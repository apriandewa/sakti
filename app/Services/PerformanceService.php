<?php

namespace App\Services;

use App\Models\Ekinerja\EkinerjaPenilaian;
use App\Models\Ekinerja\EkinerjaReferensiPeriode;
use App\Models\Pegawai;
use App\Services\Ekinerja\EkinerjaService;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * PerformanceService
 *
 * Menggabungkan data Penilaian SKP (e-Kinerja BKN - Bobot 70%)
 * dan Rekapitulasi Presensi / Potongan TPP (Simpegnas BKN - Bobot 30%)
 * menjadi Nilai Performance Pegawai.
 */
class PerformanceService
{
    public function __construct(
        protected PresensiService $presensiService,
        protected EkinerjaService $ekinerjaService
    ) {
    }

    /**
     * Hitung nilai performance gabungan per NIP, bulan, dan tahun.
     *
     * @return array{
     *   success: bool,
     *   message: string|null,
     *   pegawai: array|null,
     *   periode: array,
     *   performance: array,
     *   kinerja: array,
     *   presensi: array
     * }
     */
    public function calculatePerformance(int $bulan, int $tahun, string $nama, string $nip): array
    {
        $nip  = trim($nip);
        $nama = trim($nama);

        // 1. Validasi Pegawai di database lokal
        $pegawai = Pegawai::where('nip', $nip)->first();

        if (! $pegawai) {
            return [
                'success' => false,
                'message' => 'NIP pegawai tidak ditemukan dalam sistem.',
            ];
        }

        // Cek kemiripan nama
        $namaDb    = strtolower(preg_replace('/\s+/', ' ', $pegawai->nama));
        $namaInput = strtolower(preg_replace('/\s+/', ' ', $nama));
        $namaCocok = str_contains($namaDb, $namaInput) || str_contains($namaInput, $namaDb)
            || (similar_text($namaDb, $namaInput, $pct) && $pct >= 60);

        if (! $namaCocok) {
            return [
                'success' => false,
                'message' => 'Nama dan NIP tidak sesuai dengan data kepegawaian terdaftar.',
            ];
        }

        // 2. Resolve Day Limit untuk Presensi (Bulan berjalan dibatasi sampai kemarin)
        $today = Carbon::today();
        $isCurrentMonth = ($bulan == $today->month && $tahun == $today->year);
        $dayLimit = $isCurrentMonth ? max(0, $today->day - 1) : null;

        // 3. Rekap Presensi & Kehadiran (Bobot 30%)
        $pegawaiWithPresensi = $pegawai->load(['presensiHarians' => function ($q) use ($bulan, $tahun, $dayLimit) {
            $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            if ($dayLimit !== null) {
                $q->whereDay('tanggal', '<=', $dayLimit);
            }
        }]);

        $hariKerja = $this->presensiService->countHariKerja($bulan, $tahun, $dayLimit);
        $rekapPresensi = $this->presensiService->hitungRekapPegawai(
            $pegawaiWithPresensi,
            $hariKerja,
            $bulan,
            $tahun,
            $dayLimit
        );

        $totalPotongan = (float) ($rekapPresensi['total_potongan'] ?? 0);
        $scoreKehadiran = max(0.0, min(100.0, 100.0 - $totalPotongan));
        $weightedKehadiran = $scoreKehadiran * 0.30;

        // 4. Data Kinerja SKP (Bobot 70%)
        $namaBulan = Carbon::create($tahun, $bulan)->translatedFormat('F');
        
        // Cari referensi periode e-Kinerja BKN
        $periodeEkinerja = EkinerjaReferensiPeriode::where('tahun', $tahun)
            ->where('nama', 'like', '%' . strtoupper($namaBulan) . '%')
            ->first();

        $penilaianKinerja = null;
        if ($periodeEkinerja) {
            $penilaianKinerja = EkinerjaPenilaian::where('nip', $nip)
                ->where('periode_id', $periodeEkinerja->periode_id)
                ->first();
        }

        if (! $penilaianKinerja) {
            $penilaianKinerja = EkinerjaPenilaian::where('nip', $nip)
                ->where('tahun_skp', $tahun)
                ->orderByDesc('synced_at')
                ->first();
        }

        // Jika belum ada di cache, coba hit API BKN jika periode_id tersedia
        if (! $penilaianKinerja && $periodeEkinerja) {
            $searchResult = $this->ekinerjaService->cariPenilaian(
                periodeId: $periodeEkinerja->periode_id,
                nip: $nip,
                namaInput: $nama
            );
            if ($searchResult['success'] && ! empty($searchResult['data'])) {
                $penilaianKinerja = EkinerjaPenilaian::where('nip', $nip)
                    ->where('periode_id', $periodeEkinerja->periode_id)
                    ->first();
            }
        }

        // Tentukan Score Kinerja (0-100) dari Predikat Hasil Akhir SKP
        $hasilAkhir = strtoupper(trim((string) ($penilaianKinerja?->hasil_akhir ?? '')));
        $scoreKinerjaRaw = $this->mapHasilAkhirToScore($hasilAkhir, $penilaianKinerja);
        $weightedKinerja = $scoreKinerjaRaw['score'] * 0.70;

        // 5. Total Score Performance Gabungan (70% Kinerja + 30% Presensi)
        $finalScore = round($weightedKinerja + $weightedKehadiran, 2);
        $grade = $this->determineGrade($finalScore);

        return [
            'success' => true,
            'message' => null,
            'pegawai' => [
                'id'          => $pegawai->id,
                'nama'        => $pegawai->nama_lengkap,
                'nip'         => $pegawai->nip,
                'nama_kantor' => $pegawai->nama_kantor,
                'jabatan'     => $pegawai->jabatan ?? ($penilaianKinerja->skp_jabatan ?? '-'),
                'unor'        => $penilaianKinerja->skp_unor ?? ($pegawai->nama_kantor ?? '-'),
            ],
            'periode' => [
                'bulan'      => $bulan,
                'tahun'      => $tahun,
                'nama_bulan' => Carbon::create($tahun, $bulan)->translatedFormat('F Y'),
                'hari_kerja' => $hariKerja,
            ],
            'performance' => [
                'final_score'        => $finalScore,
                'grade_label'        => $grade['label'],
                'grade_category'     => $grade['category'],
                'badge_class'        => $grade['badge_class'],
                'color'              => $grade['color'],
                'weighted_kinerja'   => round($weightedKinerja, 2),
                'weighted_kehadiran' => round($weightedKehadiran, 2),
            ],
            'kinerja' => [
                'raw_score'      => $scoreKinerjaRaw['score'],
                'hasil_akhir'    => $penilaianKinerja?->hasil_akhir ?? 'BELUM ADA PENILAIAN BKN',
                'hasil_kerja'    => $penilaianKinerja?->hasil_kerja ?? '-',
                'perilaku_kerja' => $penilaianKinerja?->perilaku_kerja ?? '-',
                'status_note'    => $scoreKinerjaRaw['note'],
                'weighted_score' => round($weightedKinerja, 2),
                'pejabat_penilai'=> $penilaianKinerja?->pegawai_atasan_nama ? ($penilaianKinerja->pegawai_atasan_nama . ' (' . ($penilaianKinerja->pegawai_atasan_jabatan ?? '-') . ')') : '-',
                'waktu_dinilai'  => $penilaianKinerja?->waktu_dinilai ? Carbon::parse($penilaianKinerja->waktu_dinilai)->translatedFormat('d F Y H:i') : '-',
            ],
            'presensi' => [
                'raw_score'         => round($scoreKehadiran, 2),
                'total_potongan'    => round($totalPotongan, 2),
                'weighted_score'    => round($weightedKehadiran, 2),
                'count_tl1'         => (int) ($rekapPresensi['tm1'] ?? 0),
                'count_tl2'         => (int) ($rekapPresensi['tm2'] ?? 0),
                'count_tl3'         => (int) ($rekapPresensi['tm3'] ?? 0),
                'count_tl4'         => (int) (($rekapPresensi['tm4'] ?? 0) + ($rekapPresensi['tmm'] ?? 0)),
                'count_psw1'        => (int) ($rekapPresensi['pc1'] ?? 0),
                'count_psw2'        => (int) ($rekapPresensi['pc2'] ?? 0),
                'count_psw3'        => (int) ($rekapPresensi['pc3'] ?? 0),
                'count_psw4'        => (int) (($rekapPresensi['pc4'] ?? 0) + ($rekapPresensi['pcm'] ?? 0)),
                'count_alpa'        => (int) ($rekapPresensi['tk'] ?? 0),
                'count_hadir'       => (int) ($rekapPresensi['hadir'] ?? 0),
                'count_cuti'        => (int) ($rekapPresensi['cuti'] ?? 0),
                'count_dl'          => (int) ($rekapPresensi['dl'] ?? 0),
                'count_izin'        => (int) ($rekapPresensi['izin'] ?? 0),
                'count_sakit'       => (int) ($rekapPresensi['sakit'] ?? 0),
            ],
        ];
    }

    /**
     * Konversi Predikat SKP e-Kinerja ke Nilai Numerik (0 - 100).
     *
     * @return array{score: float, note: string}
     */
    protected function mapHasilAkhirToScore(string $hasilAkhir, ?EkinerjaPenilaian $penilaian): array
    {
        if (is_numeric($hasilAkhir) && (float) $hasilAkhir > 0) {
            $score = min(100.0, (float) $hasilAkhir);
            return ['score' => $score, 'note' => 'Nilai SKP Numerik BKN'];
        }

        return match ($hasilAkhir) {
            'SANGAT BAIK', 'SANGATBAIK' => [
                'score' => 100.0,
                'note'  => 'Predikat SKP Sangat Baik (100%)',
            ],
            'BAIK' => [
                'score' => 90.0,
                'note'  => 'Predikat SKP Baik (90%)',
            ],
            'BUTUH PERBAIKAN', 'BUTUHPERBAIKAN' => [
                'score' => 70.0,
                'note'  => 'Predikat SKP Butuh Perbaikan (70%)',
            ],
            'KURANG' => [
                'score' => 50.0,
                'note'  => 'Predikat SKP Kurang (50%)',
            ],
            'SANGAT KURANG', 'SANGATKURANG' => [
                'score' => 30.0,
                'note'  => 'Predikat SKP Sangat Kurang (30%)',
            ],
            default => [
                'score' => 80.0,
                'note'  => $penilaian ? 'Predikat SKP Belum Ditentukan (Default 80%)' : 'Belum Ada Data e-Kinerja BKN (Estimasi 80%)',
            ],
        };
    }

    /**
     * Tentukan Grade & Kategori Performance berdasarkan total score.
     *
     * @return array{label: string, category: string, badge_class: string, color: string}
     */
    protected function determineGrade(float $score): array
    {
        if ($score >= 90.0) {
            return [
                'label'       => 'SANGAT OPTIMAL',
                'category'    => 'Kinerja & Kehadiran Istimewa',
                'badge_class' => 'bg-success',
                'color'       => '#10b981', // Emerald / Green
            ];
        }

        if ($score >= 80.0) {
            return [
                'label'       => 'OPTIMAL',
                'category'    => 'Kinerja & Kehadiran Baik',
                'badge_class' => 'bg-primary',
                'color'       => '#3b82f6', // Blue
            ];
        }

        if ($score >= 70.0) {
            return [
                'label'       => 'CUKUP OPTIMAL',
                'category'    => 'Kinerja & Kehadiran Memenuhi Standar',
                'badge_class' => 'bg-info text-dark',
                'color'       => '#06b6d4', // Cyan
            ];
        }

        if ($score >= 50.0) {
            return [
                'label'       => 'KURANG OPTIMAL',
                'category'    => 'Kinerja / Kehadiran Perlu Ditingkatkan',
                'badge_class' => 'bg-warning text-dark',
                'color'       => '#f59e0b', // Amber / Warning
            ];
        }

        return [
            'label'       => 'PERLU EVALUASI CRITICAL',
            'category'    => 'Kinerja & Kehadiran Dibawah Standar Minimal',
            'badge_class' => 'bg-danger',
            'color'       => '#ef4444', // Red
        ];
    }
}
