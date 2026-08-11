<?php

namespace App\Services\Ekinerja;

use App\Models\Ekinerja\EkinerjaLogPencarian;
use App\Models\Ekinerja\EkinerjaMasterUnor;
use App\Models\Ekinerja\EkinerjaPenilaian;
use App\Models\Ekinerja\EkinerjaReferensiPeriode;
use App\Models\Ekinerja\EkinerjaSyncLog;
use App\Models\Pegawai;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Service global modul e-Kinerja (PRD Bab 5.1 & 7).
 *
 * Dipakai oleh:
 *  - App\Http\Controllers\Frontend\EkinerjaController (pencarian publik, PRD 7.1)
 *  - App\Http\Controllers\Backend\Ekinerja\EkinerjaController (rekap admin, PRD 7.2)
 *  - App\Console\Commands\SyncPenilaianEkinerja (cron job, PRD 7.2)
 *
 * Prinsip: Controller TIDAK query ke model / panggil API langsung.
 * Semua logika (cache TTL, upsert, panggil API BKN, audit log) ada di sini.
 */
class EkinerjaService
{
    public function __construct(protected BknEkinerjaClient $client)
    {
    }

    /* =====================================================================
     * PERIODE
     * ===================================================================*/

    /**
     * Opsi periode untuk Select2 (dipakai frontend & backend).
     * @return array<int, array{id:string,text:string}>
     */
    public function getPeriodeOptions(?string $search = null): array
    {
        $this->ensurePeriodeSynced();

        return EkinerjaReferensiPeriode::query()
            ->when($search, fn (Builder $q, $term) => $q->where('nama', 'like', "%{$term}%"))
            ->orderByDesc('tahun')
            ->orderByRaw('CAST(angka_periodik AS UNSIGNED) DESC')
            ->limit(50)
            ->get()
            ->map(fn (EkinerjaReferensiPeriode $p) => [
                'id'   => $p->periode_id,
                'text' => $p->label,
            ])
            ->values()
            ->all();
    }

    /**
     * Tarik & simpan referensi periode dari BKN jika cache lokal kosong/kedaluwarsa.
     */
    public function ensurePeriodeSynced(bool $force = false): void
    {
        $ttl = (int) config('ekinerja.cache_ttl.periode');
        $latest = EkinerjaReferensiPeriode::max('synced_at');

        if (! $force && $latest && now()->diffInSeconds($latest) < $ttl) {
            return;
        }

        try {
            foreach ($this->client->getReferensiPeriode() as $row) {
                EkinerjaReferensiPeriode::updateOrCreate(
                    ['periode_id' => $row['id']],
                    [
                        'nama'            => $row['nama'] ?? null,
                        'tahun'           => $row['tahun'] ?? null,
                        'periode_awal'    => $row['periode_awal'] ?? null,
                        'periode_akhir'   => $row['periode_akhir'] ?? null,
                        'batas_pengisian' => $row['batas_pengisian'] ?? null,
                        'jenis_periode'   => $row['jenis_periode'] ?? null,
                        'tipe_periodik'   => $row['tipe_periodik'] ?? null,
                        'angka_periodik'  => $row['angka_periodik'] ?? null,
                        'synced_at'       => now(),
                    ]
                );
            }
        } catch (BknApiException $e) {
            // Jangan lempar exception saat ensure otomatis — fallback ke data lokal
            if ($force) {
                throw $e;
            }
        }
    }

    /* =====================================================================
     * PENCARIAN (Frontend Publik — PRD Bab 7.1)
     * ===================================================================*/

    /**
     * Cari penilaian per NIP + periode.
     * Alur: cek cache lokal (TTL) → jika kedaluwarsa/kosong, panggil API BKN
     * → upsert ke cache → catat log pencarian → kembalikan hasil.
     *
     * @return array{success:bool, data:?array, message:?string, nama_cocok:?bool}
     */
    public function cariPenilaian(
        string $periodeId,
        string $nip,
        ?string $namaInput = null,
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): array {
        $periode = EkinerjaReferensiPeriode::where('periode_id', $periodeId)->first();
        $tahun = (int) ($periode?->tahun ?? now()->year);

        $cache = EkinerjaPenilaian::where('nip', $nip)->where('periode_id', $periodeId)->first();
        $ttl = (int) config('ekinerja.cache_ttl.penilaian');
        $expired = ! $cache || ! $cache->synced_at || now()->diffInSeconds($cache->synced_at) > $ttl;

        if ($expired) {
            try {
                $apiData = $this->client->getPenilaian($tahun, $periodeId, $nip);

                if ($apiData) {
                    $cache = $this->upsertPenilaian($apiData, 'frontend_search', $periodeId);
                }
            } catch (BknApiException $e) {
                // API BKN gagal: fallback ke cache lama bila ada, kalau tidak ada → gagal
                if (! $cache) {
                    $this->logPencarian($nip, $namaInput, $periodeId, $ipAddress, $userAgent, 'error', $e->getMessage());

                    return ['success' => false, 'data' => null, 'message' => $e->getMessage(), 'nama_cocok' => null];
                }
            }
        }

        if (! $cache) {
            $this->logPencarian($nip, $namaInput, $periodeId, $ipAddress, $userAgent, 'not_found');

            return [
                'success'    => false,
                'data'       => null,
                'message'    => 'Data penilaian e-Kinerja untuk NIP dan periode tersebut tidak ditemukan.',
                'nama_cocok' => null,
            ];
        }

        $namaCocok = $namaInput
            ? Str::contains(Str::lower($cache->nama ?? ''), Str::lower($namaInput))
            : null;

        $this->logPencarian($nip, $namaInput, $periodeId, $ipAddress, $userAgent, 'success');

        return ['success' => true, 'data' => $cache->toArray(), 'message' => null, 'nama_cocok' => $namaCocok];
    }

    /* =====================================================================
     * REKAP (Backend Admin — PRD Bab 7.2)
     * ===================================================================*/

    /**
     * Query builder rekap per Unor + Periode, dipakai server-side Yajra DataTable.
     */
    public function rekapQuery(string $unorId, string $periodeId): Builder
    {
        return EkinerjaPenilaian::query()
            ->where('skp_unor_id', $unorId)
            ->where('periode_id', $periodeId)
            ->orderBy('nama');
    }

    public function findPenilaian(string $id): ?EkinerjaPenilaian
    {
        return EkinerjaPenilaian::find($id);
    }

    /**
     * Log DataTable — riwayat sinkronisasi (Tab 2 halaman admin).
     */
    public function getLogsQuery(?string $unorId = null, ?string $periodeId = null): Builder
    {
        return EkinerjaSyncLog::query()
            ->when($unorId, fn (Builder $q, $id) => $q->where('unor_id', $id))
            ->when($periodeId, fn (Builder $q, $id) => $q->where('periode_id', $id))
            ->orderByDesc('waktu_mulai');
    }

    /* =====================================================================
     * SINKRONISASI (Backend Admin & Scheduler — PRD Bab 7.2 & 7.3)
     * ===================================================================*/

    /**
     * Sinkronisasi penilaian untuk Unor + Periode tertentu.
     *
     * Strategi (PRD Bab 7.2):
     * 1. Tarik daftar NIP dari tabel master `pegawais` (berdasarkan `unor_id`).
     * 2. Fallback: jika tidak ada NIP di master pegawai, gunakan NIP yang sudah
     *    pernah ada di `ekinerja_penilaian` (agar backward-compatible).
     * 3. Loop per NIP → panggil API BKN → upsert ke cache lokal.
     * 4. Catat hasil ke `ekinerja_sync_logs` (PRD Bab 7.4 & 8.4).
     *
     * @return array{status:string, total_berhasil:int, total_gagal:int, message:string, log_id:string}
     */
    public function syncPenilaianByUnor(
        string $unorId,
        string $periodeId,
        string $triggeredBy = 'Admin'
    ): array {
        $periode  = EkinerjaReferensiPeriode::where('periode_id', $periodeId)->first();
        $tahun    = (int) ($periode?->tahun ?? now()->year);
        $masterUnor = EkinerjaMasterUnor::where('unor_id', $unorId)->first();

        // Buat log entry dengan status "berjalan"
        $syncLog = EkinerjaSyncLog::create([
            'unor_id'     => $unorId,
            'nama_unor'   => $masterUnor?->nama_unor,
            'periode_id'  => $periodeId,
            'sync_by'     => $triggeredBy,
            'status'      => 'berjalan',
            'waktu_mulai' => now(),
        ]);

        // 1. Sumber NIP dari master pegawais (PRD 7.2 — strategi utama)
        $nipList = Pegawai::byUnor($unorId)->pluck('nip');

        // 2. Fallback: NIP dari cache penilaian yang sudah ada
        if ($nipList->isEmpty()) {
            $nipList = EkinerjaPenilaian::where('skp_unor_id', $unorId)->pluck('nip')->unique();
        }

        $berhasil = 0;
        $gagal    = 0;
        $pesan    = [];

        foreach ($nipList as $nip) {
            try {
                $apiData = $this->client->getPenilaian($tahun, $periodeId, $nip);

                if ($apiData) {
                    $this->upsertPenilaian($apiData, 'backend_sync', $periodeId);
                    $berhasil++;
                } else {
                    $gagal++;
                    $pesan[] = "NIP {$nip}: Data tidak ditemukan di BKN.";
                }
            } catch (BknApiException $e) {
                $gagal++;
                $pesan[] = "NIP {$nip}: " . $e->getMessage();
            }
        }

        // Update log dengan hasil akhir
        $status = $gagal === 0 ? 'sukses' : ($berhasil === 0 ? 'gagal' : 'sukses');
        $syncLog->update([
            'status'               => $status,
            'waktu_selesai'        => now(),
            'jumlah_data_ditarik'  => $berhasil,
            'jumlah_gagal'         => $gagal,
            'catatan_pesan'        => implode("\n", $pesan) ?: null,
        ]);

        return [
            'status'         => $status,
            'total_berhasil' => $berhasil,
            'total_gagal'    => $gagal,
            'message'        => "Sinkronisasi selesai: {$berhasil} berhasil, {$gagal} gagal.",
            'log_id'         => $syncLog->id,
        ];
    }

    /* =====================================================================
     * INTERNAL HELPERS
     * ===================================================================*/

    /**
     * Upsert satu record penilaian dari data API BKN.
     * Juga melakukan On-the-fly Upsert master Unor & update pegawai.unor_id (PRD 7.3).
     */
    protected function upsertPenilaian(array $row, string $source, ?string $fallbackPeriodeId = null): EkinerjaPenilaian
    {
        $periodeId = ! empty($row['periode_id']) ? $row['periode_id'] : $fallbackPeriodeId;

        // 1. On-the-fly Upsert Master Unor (PRD 7.3)
        if (! empty($row['skp_unor_id'])) {
            EkinerjaMasterUnor::updateOrCreate(
                ['unor_id' => $row['skp_unor_id']],
                ['nama_unor' => $row['skp_unor'] ?? null]
            );
        }

        // 2. On-the-fly Update Pegawai.unor_id — HANYA field unor_id yang diperbarui,
        //    TIDAK menyentuh kantor_id (ranah Presensi). PRD Bab 7.2 "Pencegahan Konflik".
        if (! empty($row['nip'])) {
            Pegawai::where('nip', $row['nip'])->update([
                'unor_id' => $row['skp_unor_id'] ?? null,
            ]);
        }

        // 3. Upsert Ekinerja Penilaian
        return EkinerjaPenilaian::updateOrCreate(
            ['nip' => $row['nip'], 'periode_id' => $periodeId],
            [
                'bkn_id'                 => $row['id'] ?? null,
                'jenis'                  => $row['jenis'] ?? null,
                'nama'                   => $row['nama'] ?? null,
                'periode_awal_skp'       => $row['periode_awal_skp'] ?? null,
                'periode_akhir_skp'      => $row['periode_akhir_skp'] ?? null,
                'skp_unor_id'            => $row['skp_unor_id'] ?? null,
                'skp_unor'               => $row['skp_unor'] ?? null,
                'skp_unor_induk'         => $row['skp_unor_induk'] ?? null,
                'skp_jabatan'            => $row['skp_jabatan'] ?? null,
                'skp_jenis_jabatan'      => $row['skp_jenis_jabatan'] ?? null,
                'is_skp_plt_plh_pjb'     => (bool) ($row['is_skp_plt_plh_pjb'] ?? false),
                'hasil_kerja'            => $row['hasil_kerja'] ?? null,
                'perilaku_kerja'         => $row['perilaku_kerja'] ?? null,
                'hasil_akhir'            => $row['hasil_akhir'] ?? null,
                'pegawai_atasan_id'      => $row['pegawai_atasan_id'] ?? null,
                'pegawai_atasan_nip'     => $row['pegawai_atasan_nip'] ?? null,
                'pegawai_atasan_nama'    => $row['pegawai_atasan_nama'] ?? null,
                'pegawai_atasan_unor_id' => $row['pegawai_atasan_unor_id'] ?? null,
                'pegawai_atasan_unor'    => $row['pegawai_atasan_unor'] ?? null,
                'pegawai_atasan_jabatan' => $row['pegawai_atasan_jabatan'] ?? null,
                'pegawai_atasan_golru'   => $row['pegawai_atasan_golru'] ?? null,
                'waktu_dinilai'          => $row['waktu_dinilai'] ?? null,
                'pegawai_penilai_id'     => $row['pegawai_penilai_id'] ?? null,
                'tahun_skp'              => $row['tahun_skp'] ?? null,
                'skp_id'                 => $row['skp_id'] ?? null,
                'skp_penilaian_id'       => $row['skp_penilaian_id'] ?? null,
                'golru'                  => $row['golru'] ?? null,
                'jenis_pegawai'          => $row['jenis_pegawai'] ?? null,
                'raw_response'           => $row,
                'source'                 => $source,
                'synced_at'              => now(),
            ]
        );
    }

    protected function logPencarian(
        string $nip,
        ?string $nama,
        string $periodeId,
        ?string $ip,
        ?string $ua,
        string $status,
        ?string $message = null,
    ): void {
        EkinerjaLogPencarian::create([
            'nip'              => $nip,
            'nama_input'       => $nama,
            'periode_id'       => $periodeId,
            'ip_address'       => $ip,
            'user_agent'       => $ua,
            'status'           => $status,
            'response_message' => $message,
        ]);
    }
}
