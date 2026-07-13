<?php

namespace App\Services\Ekinerja;

use App\Models\Ekinerja\EkinerjaLogPencarian;
use App\Models\Ekinerja\EkinerjaPenilaian;
use App\Models\Ekinerja\EkinerjaReferensiPeriode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Service global modul e-Kinerja.
 *
 * Dipakai oleh:
 *  - App\Http\Controllers\Frontend\EkinerjaController (pencarian publik)
 *  - App\Http\Controllers\Backend\RekapEkinerjaController (rekap admin)
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
    }

    /* =====================================================================
     * PENCARIAN (Frontend Publik)
     * ===================================================================*/

    /**
     * Cari penilaian per NIP + periode.
     * Alur: cek cache lokal (TTL) -> jika kedaluwarsa/kosong, panggil API BKN
     * -> upsert ke cache -> catat log pencarian -> kembalikan hasil.
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
        $tahun = (int) ($periode->tahun ?? now()->year);

        $cache = EkinerjaPenilaian::where('nip', $nip)->where('periode_id', $periodeId)->first();
        $ttl = (int) config('ekinerja.cache_ttl.penilaian');
        $expired = ! $cache || ! $cache->synced_at || now()->diffInSeconds($cache->synced_at) > $ttl;

        if ($expired) {
            try {
                $apiData = $this->client->getPenilaian($tahun, $periodeId, $nip);

                if ($apiData) {
                    $cache = $this->upsertPenilaian($apiData, 'frontend_search');
                }
            } catch (BknApiException $e) {
                // API BKN gagal: fallback ke cache lama bila ada, kalau tidak ada -> gagal
                if (! $cache) {
                    $this->logPencarian($nip, $namaInput, $periodeId, $ipAddress, $userAgent, 'error', $e->getMessage());

                    return ['success' => false, 'data' => null, 'message' => $e->getMessage(), 'nama_cocok' => null];
                }
            }
        }

        if (! $cache) {
            $this->logPencarian($nip, $namaInput, $periodeId, $ipAddress, $userAgent, 'not_found');

            return [
                'success' => false,
                'data' => null,
                'message' => 'Data penilaian e-Kinerja untuk NIP dan periode tersebut tidak ditemukan.',
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
     * REKAP (Backend Admin)
     * ===================================================================*/

    /** Query builder rekap per Unor + Periode, dipakai server-side DataTable. */
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
     * Data rekap siap-pakai untuk protokol server-side jQuery DataTables
     * (tanpa dependensi ke package pihak ketiga seperti Yajra).
     *
     * @return array{recordsTotal:int, recordsFiltered:int, data: \Illuminate\Support\Collection}
     */
    public function rekapDatatable(string $unorId, string $periodeId, ?string $search, int $start, int $length): array
    {
        $base = $this->rekapQuery($unorId, $periodeId);
        $recordsTotal = (clone $base)->count();

        if ($search) {
            $base->where(function (Builder $q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $recordsFiltered = (clone $base)->count();

        $data = $base->skip($start)->take($length > 0 ? $length : 10)->get();

        return compact('recordsTotal', 'recordsFiltered', 'data');
    }

    /**
     * Sinkronisasi ulang seluruh NIP yang sudah pernah tercatat pada
     * kombinasi Unor + Periode tsb (MVP — lihat PRD Bab 7.3 "Opsi B").
     *
     * TODO(next iteration): ganti sumber NIP dari tabel master pegawai
     * (mis. modul Kepegawaian/Simpeg) agar cakupan sinkronisasi lengkap,
     * bukan hanya NIP yang sudah pernah dicari sebelumnya.
     *
     * @return array{status:string, total_berhasil:int, total_gagal:int, message:string}
     */
    public function syncPenilaianByUnor(string $unorId, string $periodeId): array
    {
        $periode = EkinerjaReferensiPeriode::where('periode_id', $periodeId)->first();
        $tahun = (int) ($periode->tahun ?? now()->year);

        $nipList = EkinerjaPenilaian::where('skp_unor_id', $unorId)->pluck('nip')->unique();

        $berhasil = 0;
        $gagal = 0;

        foreach ($nipList as $nip) {
            try {
                $apiData = $this->client->getPenilaian($tahun, $periodeId, $nip);

                if ($apiData) {
                    $this->upsertPenilaian($apiData, 'backend_sync');
                    $berhasil++;
                } else {
                    $gagal++;
                }
            } catch (BknApiException) {
                $gagal++;
            }
        }

        return [
            'status' => 'success',
            'total_berhasil' => $berhasil,
            'total_gagal' => $gagal,
            'message' => "Sinkronisasi selesai: {$berhasil} berhasil, {$gagal} gagal.",
        ];
    }

    /* =====================================================================
     * INTERNAL HELPERS
     * ===================================================================*/

    protected function upsertPenilaian(array $row, string $source): EkinerjaPenilaian
    {
        return EkinerjaPenilaian::updateOrCreate(
            ['nip' => $row['nip'], 'periode_id' => $row['periode_id']],
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
            'nip' => $nip,
            'nama_input' => $nama,
            'periode_id' => $periodeId,
            'ip_address' => $ip,
            'user_agent' => $ua,
            'status' => $status,
            'response_message' => $message,
        ]);
    }
}
