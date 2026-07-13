<?php

namespace App\Services;

use App\Models\Pegawai;
use App\Models\PresensiHarian;
use App\Models\PresensiSyncLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SimpegnasService
 *
 * SATU-SATUNYA sumber logika integrasi dengan API Simpegnas BKN.
 * Dipakai bersama oleh:
 * - Scheduler harian (Kernel.php) -> autoCreatePegawai: false (konservatif, tanpa insert pegawai baru tanpa review)
 * - Tombol manual "Tarik Data BKN" di PresensiController -> autoCreatePegawai: true (admin sadar & memilih kantor secara aktif)
 * - Tombol "Sync Pegawai" (satu pegawai) di PresensiController -> autoCreatePegawai: false (pegawai harus sudah ada untuk punya ID yang diklik)
 *
 * Tanggung jawab lain (daftar kantor untuk Select2, rekap tampilan DataTable,
 * import CSV, ambil foto presensi) tetap di PresensiService.
 */
class SimpegnasService
{
    protected string $url;
    protected string $token;

    /**
     * Bobot potongan per kategori keterlambatan / pulang cepat / alpa,
     * mengacu pada PRD Modul Presensi Pegawai bagian 3.
     */
    protected const BOBOT_TERLAMBAT = [
        'TM1' => 0.5,
        'TM2' => 1.0,
        'TM3' => 1.25,
        'TM4' => 1.5,
        'TMM' => 1.5,
    ];

    protected const BOBOT_PULANG_CEPAT = [
        'PC1' => 0.5,
        'PC2' => 1.0,
        'PC3' => 1.25,
        'PC4' => 1.5,
        'PCM' => 1.5,
    ];

    protected const BOBOT_TK = 3.0;

    public function __construct()
    {
        $this->url = config('services.simpegnas.url');
        $this->token = config('services.simpegnas.token');
    }

    /**
     * Entry point tunggal untuk semua skenario sinkronisasi presensi.
     *
     * @param int $month
     * @param int $year
     * @param string|null $kantorId Jika diisi: hanya sync kantor ini. Jika null & $pegawaiId null: sync SEMUA kantor yang punya pegawai lokal.
     * @param string|null $pegawaiId Jika diisi: hanya sync satu pegawai ini (kantor & NIP diambil dari data lokal pegawai tsb).
     * @param string $triggeredBy Nama/ID admin, atau 'Sistem (Otomatis)' untuk cron.
     * @param bool $autoCreatePegawai Jika true: pegawai yang belum ada lokal akan DIBUAT otomatis dari data API.
     *                                Jika false: pegawai yang belum ada lokal akan DILEWATI (dihitung sebagai "dilewati").
     *
     * @return array{status: string, kantor_diproses: int, kantor_gagal: int, pegawai_disync: int, pegawai_baru: int, pegawai_dilewati: int, hari_records: int, messages: string[]}
     */
    public function syncAttendance(
        int $month,
        int $year,
        ?string $kantorId = null,
        ?string $pegawaiId = null,
        string $triggeredBy = 'Sistem (Otomatis)',
        bool $autoCreatePegawai = false
    ): array {
        // --- Skenario 1: sync satu pegawai spesifik ---
        if ($pegawaiId !== null) {
            $pegawai = Pegawai::find($pegawaiId);

            if (! $pegawai || empty($pegawai->kantor_id)) {
                Log::warning("syncAttendance: pegawai {$pegawaiId} tidak ditemukan atau belum punya kantor_id");

                PresensiSyncLog::create([
                    'kantor_id' => $pegawai->kantor_id ?? null,
                    'bulan' => (string) $month,
                    'tahun' => (string) $year,
                    'sync_by' => $triggeredBy,
                    'status' => 'gagal',
                    'waktu_mulai' => now(),
                    'waktu_selesai' => now(),
                    'jumlah_data_ditarik' => 0,
                    'catatan_pesan' => 'Pegawai tidak ditemukan atau belum memiliki kantor_id.',
                ]);

                return $this->buildAggregateResult('gagal', 0, 1, 0, 0, 0, 0, ['Pegawai tidak ditemukan atau belum memiliki kantor_id.']);
            }

            $detail = $this->syncKantor($pegawai->kantor_id, $month, $year, $triggeredBy, autoCreatePegawai: false, onlyNip: $pegawai->nip);

            return $this->buildAggregateResult(
                $detail['status'], 1, $detail['status'] === 'gagal' ? 1 : 0,
                $detail['pegawai_disync'], $detail['pegawai_baru'], $detail['pegawai_dilewati'],
                $detail['hari_records'], $detail['messages']
            );
        }

        // --- Skenario 2: sync satu kantor spesifik (dipilih admin secara manual) ---
        if ($kantorId !== null) {
            $detail = $this->syncKantor($kantorId, $month, $year, $triggeredBy, $autoCreatePegawai);

            return $this->buildAggregateResult(
                $detail['status'], 1, $detail['status'] === 'gagal' ? 1 : 0,
                $detail['pegawai_disync'], $detail['pegawai_baru'], $detail['pegawai_dilewati'],
                $detail['hari_records'], $detail['messages']
            );
        }

        // --- Skenario 3: sync SEMUA kantor yang punya pegawai lokal (cron harian) ---
        $kantorIds = Pegawai::query()
            ->whereNotNull('kantor_id')
            ->distinct()
            ->pluck('kantor_id');

        if ($kantorIds->isEmpty()) {
            Log::warning('syncAttendance: tidak ada kantor_id ditemukan di tabel pegawais');

            PresensiSyncLog::create([
                'kantor_id' => null,
                'bulan' => (string) $month,
                'tahun' => (string) $year,
                'sync_by' => $triggeredBy,
                'status' => 'gagal',
                'waktu_mulai' => now(),
                'waktu_selesai' => now(),
                'jumlah_data_ditarik' => 0,
                'catatan_pesan' => 'Tidak ada kantor_id yang terdaftar pada tabel pegawais.',
            ]);

            return $this->buildAggregateResult('gagal', 0, 1, 0, 0, 0, 0, ['Tidak ada kantor_id yang terdaftar pada tabel pegawais.']);
        }

        $kantorSukses = 0;
        $kantorGagal = 0;
        $totalPegawaiDisync = 0;
        $totalPegawaiBaru = 0;
        $totalPegawaiDilewati = 0;
        $totalHariRecords = 0;
        $allMessages = [];

        foreach ($kantorIds as $id) {
            $detail = $this->syncKantor($id, $month, $year, $triggeredBy, $autoCreatePegawai);

            $detail['status'] === 'sukses' ? $kantorSukses++ : $kantorGagal++;
            $totalPegawaiDisync += $detail['pegawai_disync'];
            $totalPegawaiBaru += $detail['pegawai_baru'];
            $totalPegawaiDilewati += $detail['pegawai_dilewati'];
            $totalHariRecords += $detail['hari_records'];
            $allMessages[] = "[{$id}] " . implode(' ', $detail['messages']);
        }

        return $this->buildAggregateResult(
            $kantorGagal === 0 ? 'sukses' : ($kantorSukses === 0 ? 'gagal' : 'sebagian'),
            $kantorSukses + $kantorGagal, $kantorGagal,
            $totalPegawaiDisync, $totalPegawaiBaru, $totalPegawaiDilewati,
            $totalHariRecords, $allMessages
        );
    }

    protected function buildAggregateResult(
        string $status, int $kantorDiproses, int $kantorGagal,
        int $pegawaiDisync, int $pegawaiBaru, int $pegawaiDilewati,
        int $hariRecords, array $messages
    ): array {
        return [
            'status' => $status,
            'kantor_diproses' => $kantorDiproses,
            'kantor_gagal' => $kantorGagal,
            'pegawai_disync' => $pegawaiDisync,
            'pegawai_baru' => $pegawaiBaru,
            'pegawai_dilewati' => $pegawaiDilewati,
            'hari_records' => $hariRecords,
            'messages' => $messages,
        ];
    }

    /**
     * Sinkronisasi satu kantor: tarik data dari API, upsert ke presensi_harians,
     * dan catat satu baris log ke presensi_sync_logs.
     *
     * Pencocokan pegawai lokal SELALU pakai kombinasi (nip + kantor_id) -
     * ini penting supaya NIP yang sama di kantor berbeda (mis. pegawai pindah tugas)
     * tidak saling tertukar/nyasar data presensinya.
     */
    protected function syncKantor(
        string $kantorId, int $month, int $year, string $triggeredBy,
        bool $autoCreatePegawai, ?string $onlyNip = null
    ): array {
        $waktuMulai = now();

        try {
            $response = Http::withHeaders([
                    'presensi-key' => $this->token,
                    'Accept' => 'application/json',
                ])
                ->timeout(120)
                ->retry(2, 3000, throw: false)
                ->get($this->url, [
                    'kantor_id' => $kantorId,
                    'tahun' => $year,
                    'bulan' => $month,
                ]);

            if (! $response->successful() || $response->json('status') !== true) {
                $pesan = 'Response API tidak sukses: ' . $response->status() . ' - ' . $response->body();

                Log::error("syncAttendance gagal untuk kantor {$kantorId}: {$pesan}");

                PresensiSyncLog::create([
                    'kantor_id' => $kantorId,
                    'bulan' => (string) $month,
                    'tahun' => (string) $year,
                    'sync_by' => $triggeredBy,
                    'status' => 'gagal',
                    'waktu_mulai' => $waktuMulai,
                    'waktu_selesai' => now(),
                    'jumlah_data_ditarik' => 0,
                    'catatan_pesan' => Str::limit($pesan, 500),
                ]);

                return [
                    'status' => 'gagal', 'pegawai_disync' => 0, 'pegawai_baru' => 0,
                    'pegawai_dilewati' => 0, 'hari_records' => 0, 'messages' => [$pesan],
                ];
            }

            $pegawaiData = $response->json('data') ?? [];
            $hariRecords = 0;
            $pegawaiDisync = 0;
            $pegawaiBaru = 0;
            $pegawaiDilewati = 0;

            foreach ($pegawaiData as $item) {
                $nip = $item['nip'] ?? null;
                $nama = $item['nama'] ?? null;

                if (! $nip) {
                    continue;
                }

                if ($onlyNip !== null && $nip !== $onlyNip) {
                    continue;
                }

                $pegawai = Pegawai::query()
                    ->where('nip', $nip)
                    ->where('kantor_id', $kantorId)
                    ->first();

                if (! $pegawai) {
                    if (! $autoCreatePegawai) {
                        // Cron/mode konservatif: pegawai belum diimpor via CSV, lewati.
                        $pegawaiDilewati++;
                        continue;
                    }

                    // Mode manual: admin sadar memilih kantor ini, buat pegawai baru.
                    $pegawai = Pegawai::create([
                        'id' => (string) Str::uuid(),
                        'nama' => $nama ?? '(Tanpa Nama)',
                        'nip' => $nip,
                        'kantor_id' => $kantorId,
                        'status' => 'aktif',
                    ]);
                    $pegawaiBaru++;
                }

                $pegawaiDisync++;

                foreach ($item['presensi'] ?? [] as $hari) {
                    $this->upsertPresensiHarian($pegawai, $month, $year, $hari);
                    $hariRecords++;
                }
            }

            $ringkasan = "Pegawai disync: {$pegawaiDisync}, pegawai baru diinsert: {$pegawaiBaru}, "
                . "dilewati: {$pegawaiDilewati}, total baris presensi harian: {$hariRecords}.";

            PresensiSyncLog::create([
                'kantor_id' => $kantorId,
                'bulan' => (string) $month,
                'tahun' => (string) $year,
                'sync_by' => $triggeredBy,
                'status' => 'sukses',
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => now(),
                'jumlah_data_ditarik' => $hariRecords,
                'catatan_pesan' => $ringkasan,
            ]);

            return [
                'status' => 'sukses', 'pegawai_disync' => $pegawaiDisync, 'pegawai_baru' => $pegawaiBaru,
                'pegawai_dilewati' => $pegawaiDilewati, 'hari_records' => $hariRecords, 'messages' => [$ringkasan],
            ];
        } catch (\Throwable $e) {
            Log::error("syncAttendance exception untuk kantor {$kantorId}: " . $e->getMessage());

            PresensiSyncLog::create([
                'kantor_id' => $kantorId,
                'bulan' => (string) $month,
                'tahun' => (string) $year,
                'sync_by' => $triggeredBy,
                'status' => 'gagal',
                'waktu_mulai' => $waktuMulai,
                'waktu_selesai' => now(),
                'jumlah_data_ditarik' => 0,
                'catatan_pesan' => Str::limit($e->getMessage(), 500),
            ]);

            return [
                'status' => 'gagal', 'pegawai_disync' => 0, 'pegawai_baru' => 0,
                'pegawai_dilewati' => 0, 'hari_records' => 0, 'messages' => [$e->getMessage()],
            ];
        }
    }

    /**
     * Konversi satu entri harian dari respons API BKN menjadi baris presensi_harians,
     * termasuk perhitungan kategori & potongan terlambat/pulang cepat.
     */
    protected function upsertPresensiHarian(Pegawai $pegawai, int $month, int $year, array $hari): void
    {
        $day = $hari['day'] ?? null;

        if (! $day) {
            return;
        }

        $tanggal = Carbon::createFromDate($year, $month, $day)->toDateString();

        $checkIn = $hari['checkIn'] ?? [];
        $checkOut = $hari['checkOut'] ?? [];
        $statusHarian = strtoupper($hari['status'] ?? '');

        $kategoriTerlambat = $this->extractKategori($checkIn['status'] ?? null, prefix: 'TM');
        $kategoriPulangCepat = $this->extractKategori($checkOut['status'] ?? null, prefix: 'PC');

        $menitTerlambat = (int) ($checkIn['late'] ?? 0);
        $menitPulangCepat = (int) ($checkOut['late'] ?? 0);

        if ($statusHarian === 'TK') {
            // Sesuai PRD 3.3: status TK murni pakai bobot TK, TM/PC diabaikan.
            $potonganTerlambat = 0;
            $potonganPulangCepat = 0;
            $totalPotongan = self::BOBOT_TK;
        } else {
            $potonganTerlambat = $kategoriTerlambat ? (self::BOBOT_TERLAMBAT[$kategoriTerlambat] ?? 0) : 0;
            $potonganPulangCepat = $kategoriPulangCepat ? (self::BOBOT_PULANG_CEPAT[$kategoriPulangCepat] ?? 0) : 0;
            $totalPotongan = $potonganTerlambat + $potonganPulangCepat;
        }

        PresensiHarian::updateOrCreate(
            [
                'pegawai_id' => $pegawai->id,
                'tanggal' => $tanggal,
            ],
            [
                'jam_masuk' => $this->extractJam($checkIn['time_with_timezone'] ?? null),
                'jam_keluar' => $this->extractJam($checkOut['time_with_timezone'] ?? null),
                'status_kehadiran' => $statusHarian ?: null,
                'kategori_terlambat' => $kategoriTerlambat,
                'menit_terlambat' => $menitTerlambat,
                'potongan_terlambat' => $potonganTerlambat,
                'kategori_pulang_cepat' => $kategoriPulangCepat,
                'menit_pulang_cepat' => $menitPulangCepat,
                'potongan_pulang_cepat' => $potonganPulangCepat,
                'total_potongan' => $totalPotongan,
                'work_from_masuk' => $checkIn['work_from'] ?? null,
                'work_from_keluar' => $checkOut['work_from'] ?? null,
                'is_sync' => true,
                'synced_at' => now(),
            ]
        );
    }

    protected function extractKategori(?string $status, string $prefix): ?string
    {
        if (! $status) {
            return null;
        }

        $status = strtoupper($status);

        return str_starts_with($status, $prefix) ? $status : null;
    }

    protected function extractJam(?string $timeWithTimezone): ?string
    {
        return $timeWithTimezone !== null && $timeWithTimezone !== '' ? $timeWithTimezone : null;
    }

    /**
     * Mengambil file foto profil pegawai dari server BKN
     */
    public function getEmployeeImage(string $nip)
    {
        try {
            $response = Http::withHeaders([
                'presensi-key' => $this->token
            ])->get("https://api-absensi.simpegnas.go.id/absensi/api/get/image", [
                'nip' => $nip
            ]);

            if ($response->successful()) {
                return $response;
            }
        } catch (\Exception $e) {
            Log::error('Kesalahan mengambil foto pegawai NIP ' . $nip . ': ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Mengambil riwayat detail check-in & check-out harian pegawai (GPS lokasi, device, dsb)
     */
    public function getEmployeeHistory(string $nip, int $month, int $year): array
    {
        try {
            $response = Http::withHeaders([
                'presensi-key' => $this->token,
                'Accept' => 'application/json'
            ])->get("https://api-absensi.simpegnas.go.id/absensi/api/get/riwayat", [
                'nip' => $nip,
                'tahun' => $year,
                'bulan' => $month
            ]);

            if ($response->successful() && $response->json('status') === true) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Kesalahan mengambil riwayat NIP ' . $nip . ': ' . $e->getMessage());
        }

        return [];
    }
}