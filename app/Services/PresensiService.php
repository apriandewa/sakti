<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\PresensiHarian;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * PresensiService
 *
 * Bertanggung jawab untuk:
 * 1. Mengambil daftar kantor dari API Simpegnas BKN (untuk Select2)
 * 2. Menghitung rekap per-pegawai (TM, PC, TK, total potongan) dari DB lokal
 * 3. Menyajikan data rekap dari DB lokal ke DataTable
 * 4. Import pegawai via CSV
 *
 * CATATAN: Logika sinkronisasi presensi dari API BKN (syncFromBkn, upsert
 * presensi harian, perhitungan bobot potongan, dsb) sudah dipindahkan
 * sepenuhnya ke SimpegnasService agar tidak ada duplikasi logika antara
 * sync otomatis (cron) dan sync manual (tombol UI).
 */
class PresensiService
{
    protected string $baseUrl = 'https://api-absensi.simpegnas.go.id/absensi/api';
    protected string $token;

    public function __construct()
    {
        $this->token = config('services.simpegnas.token');
    }

    /* ============================================================
     *  DAFTAR KANTOR (untuk Select2 Autocomplete)
     * ============================================================ */

    /**
     * Ambil daftar kantor dari API Simpegnas BKN.
     * Kembalikan array [{id, text}] siap pakai untuk Select2.
     */
    public function getDaftarKantor(): array
    {
        try {
            $response = Http::withHeaders([
                'presensi-key' => $this->token,
                'Accept'       => 'application/json',
            ])->timeout(15)->get("{$this->baseUrl}/get/kantor");

            if ($response->successful() && $response->json('status') === true) {
                $kantorList = $response->json('data.kantor') ?? [];
                return collect($kantorList)
                    ->map(fn($k) => [
                        'id'   => $k['id'] ?? '',
                        'text' => $k['nama_kantor'] ?? '',
                    ])
                    ->values()
                    ->toArray();
            }
        } catch (\Throwable $e) {
            Log::error('[Presensi] Gagal ambil daftar kantor: ' . $e->getMessage());
        }

        return [];
    }

    /* ============================================================
     *  REKAP BULANAN (DataTable & Laporan)
     * ============================================================ */

    /**
     * Hitung rekap bulanan untuk semua pegawai di kantor & bulan/tahun tertentu.
     * Data diambil dari DB lokal (hasil sync).
     *
     * @return array Rekap per-pegawai dengan semua kolom DataTable
     */
    public function getRekap(string $kantorId, int $bulan, int $tahun): array
    {
        $dayLimit = $this->resolveDayLimit($bulan, $tahun);

        $pegawais = Pegawai::where('kantor_id', $kantorId)
            ->with(['presensiHarians' => function ($q) use ($bulan, $tahun, $dayLimit) {
                $q->whereMonth('tanggal', $bulan)
                  ->whereYear('tanggal', $tahun);

                if ($dayLimit !== null) {
                    $q->whereDay('tanggal', '<=', $dayLimit);
                }
            }])
            ->aktif()
            ->get();

        $hariKerja = $this->countHariKerja($bulan, $tahun, $dayLimit);

        return $pegawais->map(function (Pegawai $pegawai) use ($hariKerja, $bulan, $tahun, $dayLimit) {
            return $this->hitungRekapPegawai($pegawai, $hariKerja, $bulan, $tahun, $dayLimit);
        })->values()->toArray();
    }

    /**
     * Tentukan batas hari yang boleh dihitung untuk bulan yang diminta.
     *
     * - Jika bulan/tahun yang diminta BUKAN bulan berjalan (sudah lewat / masa depan): null (tidak dibatasi, tampilkan penuh).
     * - Jika bulan berjalan: dibatasi sampai HARI KEMARIN saja (bukan hari ini),
     *   karena presensi hari ini biasanya belum lengkap (belum checkout / belum diproses BKN).
     *   Jika hari ini tanggal 1, maka dayLimit = 0 (belum ada hari yang bisa dihitung sama sekali).
     */
    protected function resolveDayLimit(int $bulan, int $tahun): ?int
    {
        $today = Carbon::today();
        $isCurrentMonth = ($bulan == $today->month && $tahun == $today->year);

        if (! $isCurrentMonth) {
            return null;
        }

        return $today->day - 1;
    }

    /**
     * Hitung rekap untuk satu pegawai
     */
    public function hitungRekapPegawai(Pegawai $pegawai, int $hariKerja, int $bulan, int $tahun, ?int $dayLimit = null): array
    {
        $presensiList = $pegawai->presensiHarians->isNotEmpty()
            ? $pegawai->presensiHarians
            : $pegawai->presensiByBulan($bulan, $tahun, $dayLimit);

        // Hitung per-kategori
        $hadir = 0; $tk = 0; $cuti = 0; $dl = 0; $izin = 0;
        $tm1 = 0; $tm2 = 0; $tm3 = 0; $tm4 = 0; $tmm = 0;
        $pc1 = 0; $pc2 = 0; $pc3 = 0; $pc4 = 0; $pcm = 0;
        $totalPotongan = 0.0;

        foreach ($presensiList as $p) {
            $status = strtoupper($p->status_kehadiran ?? '');
            $katTM  = strtoupper($p->kategori_terlambat ?? '');
            $katPC  = strtoupper($p->kategori_pulang_cepat ?? '');

            // Status kehadiran
            match($status) {
                'HN','TM1','TM2','TM3','TM4','TMM','PC1','PC2','PC3','PC4','PCM' => $hadir++,
                'TK'   => $tk++,
                'CT'   => $cuti++,
                'DL'   => $dl++,
                'IZIN' => $izin++,
                default => null,
            };

            // Terlambat
            match($katTM) {
                'TM1' => $tm1++,
                'TM2' => $tm2++,
                'TM3' => $tm3++,
                'TM4' => $tm4++,
                'TMM' => $tmm++,
                default => null,
            };

            // Pulang cepat
            match($katPC) {
                'PC1' => $pc1++,
                'PC2' => $pc2++,
                'PC3' => $pc3++,
                'PC4' => $pc4++,
                'PCM' => $pcm++,
                default => null,
            };

            $totalPotongan += (float) $p->total_potongan;
        }

        return [
            'id'           => $pegawai->id,
            'nama'         => $pegawai->nama_lengkap,
            'nip'          => $pegawai->nip,
            'hadir'        => $hadir,
            'tk'           => $tk,
            'cuti'         => $cuti,
            'dl'           => $dl,
            'izin'         => $izin,
            'hari_kerja'   => $hariKerja,
            'tm1'          => $tm1,
            'tm2'          => $tm2,
            'tm3'          => $tm3,
            'tm4'          => $tm4,
            'tmm'          => $tmm,
            'pc1'          => $pc1,
            'pc2'          => $pc2,
            'pc3'          => $pc3,
            'pc4'          => $pc4,
            'pcm'          => $pcm,
            'total_potongan' => round($totalPotongan, 2),
        ];
    }

    /**
     * Ambil log harian satu pegawai untuk modal detail
     */
    public function getLogHarian(string $pegawaiId, int $bulan, int $tahun): array
    {
        $pegawai = Pegawai::findOrFail($pegawaiId);
        $dayLimit = $this->resolveDayLimit($bulan, $tahun);

        $presensiQuery = PresensiHarian::where('pegawai_id', $pegawaiId)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($dayLimit !== null) {
            $presensiQuery->whereDay('tanggal', '<=', $dayLimit);
        }

        $presensi = $presensiQuery->orderBy('tanggal')->get();

        return [
            'pegawai'  => $pegawai,
            'presensi' => $presensi,
            'rekap'    => $this->hitungRekapPegawai($pegawai->load(['presensiHarians' => function($q) use ($bulan, $tahun, $dayLimit) {
                $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
                if ($dayLimit !== null) {
                    $q->whereDay('tanggal', '<=', $dayLimit);
                }
            }]), $this->countHariKerja($bulan, $tahun, $dayLimit), $bulan, $tahun, $dayLimit),
        ];
    }

    /* ============================================================
     *  FOTO PRESENSI (Base64 untuk Modal Geolokasi)
     * ============================================================ */

    /**
     * Ambil foto presensi check-in/out dari BKN API (base64)
     */
    public function getFotoPresensi(string $nip, string $tanggal, string $jenis = 'in'): ?string
    {
        try {
            $response = Http::withHeaders([
                'presensi-key' => $this->token,
                'Accept'       => 'application/json',
            ])->timeout(10)->get("{$this->baseUrl}/get/image-riwayat", [
                'nip'     => $nip,
                'tanggal' => $tanggal,
                'jenis'   => $jenis,
            ]);

            if ($response->successful()) {
                return $response->json('data') ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning("[Presensi] Gagal ambil foto NIP {$nip}: " . $e->getMessage());
        }

        return null;
    }

    /* ============================================================
     *  IMPORT PEGAWAI VIA CSV
     * ============================================================ */

    /**
     * Import bulk pegawai dari array CSV
     * @param array $rows [{nama, nip, kantor_id}]
     */
    public function importPegawaiCsv(array $rows, string $kantorId): array
    {
        $imported = 0; $skipped = 0; $errors = [];

        foreach ($rows as $i => $row) {
            $nip  = trim($row['nip']  ?? '');
            $nama = trim($row['nama'] ?? '');

            if (! $nip || ! $nama) {
                $skipped++;
                continue;
            }

            try {
                Pegawai::updateOrCreate(
                    ['nip' => $nip],
                    ['nama' => $nama, 'kantor_id' => $kantorId, 'status' => 'aktif']
                );
                $imported++;
            } catch (\Throwable $e) {
                $errors[] = "Baris " . ($i + 2) . ": " . $e->getMessage();
            }
        }

        return compact('imported', 'skipped', 'errors');
    }

    /* ============================================================
     *  PRIVATE HELPERS
     * ============================================================ */

    /**
     * Hitung hari kerja dalam sebulan (Senin–Jumat, tanpa libur nasional sederhana)
     * Catatan: jika ingin akurat per kalender libur, extend dengan API kalender nasional.
     *
     * @param int|null $dayLimit Jika diisi, hanya hitung hari kerja s.d. tanggal ini (untuk bulan berjalan).
     */
    public function countHariKerja(int $bulan, int $tahun, ?int $dayLimit = null): int
    {
        $start = Carbon::create($tahun, $bulan, 1);
        $end   = $dayLimit !== null
            ? Carbon::create($tahun, $bulan, max($dayLimit, 1))
            : $start->copy()->endOfMonth();
        $count = 0;

        if ($dayLimit !== null && $dayLimit < 1) {
            return 0;
        }

        for ($d = $start->copy(); $d->lte($end); $d->addDay()) {
            if ($d->isWeekday()) $count++;
        }

        return $count;
    }
}