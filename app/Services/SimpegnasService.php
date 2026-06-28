<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PresensiHarian;
use App\Models\PresensiSyncLog;
use App\Models\Pegawai;
use Carbon\Carbon;

class SimpegnasService
{
    protected string $url;
    protected string $token;
    protected string $kantorId;

    public function __construct()
    {
        $this->url = config('services.simpegnas.url');
        $this->token = config('services.simpegnas.token');
        $this->kantorId = config('services.simpegnas.kantor_id');
    }

    /**
     * Sinkronisasi data presensi bulanan kantor dari API Simpegnas BKN ke DB lokal
     */
    public function syncAttendance(int $month, int $year, ?string $pegawaiId = null, string $triggeredBy = 'Sistem (Otomatis)'): array
    {
        $successCount = 0;
        $failedCount = 0;

        try {
            // Request ke API Simpegnas menggunakan header presensi-key
            $response = Http::withHeaders([
                    'presensi-key' => $this->token,
                    'Accept' => 'application/json'
                ])
                ->get($this->url, [
                    'kantor_id' => $this->kantorId,
                    'tahun' => $year,
                    'bulan' => $month
                ]);

            if ($response->successful() && $response->json('status') === true) {
                $officeData = $response->json('data') ?? [];

                // Filter data jika hanya ingin menyinkronkan pegawai tertentu
                if ($pegawaiId) {
                    $targetPegawai = Pegawai::find($pegawaiId);
                    if ($targetPegawai && $targetPegawai->nip) {
                        $officeData = array_filter($officeData, function ($item) use ($targetPegawai) {
                            return $item['nip'] === $targetPegawai->nip;
                        });
                    } else {
                        return ['success' => 0, 'failed' => 1];
                    }
                }

                foreach ($officeData as $empData) {
                    $nip = $empData['nip'];
                    
                    // Cari pegawai di database lokal berdasarkan NIP
                    $pegawai = Pegawai::where('nip', $nip)->first();
                    if (!$pegawai) {
                        $failedCount++;
                        continue;
                    }

                    $presensiHarian = $empData['presensi'] ?? [];

                    foreach ($presensiHarian as $dayData) {
                        $day = $dayData['day'];
                        
                        // Menangani hari libur/akhir pekan
                        $statusHarian = $dayData['status'] ?? 'HN';
                        $isHoliday = in_array($statusHarian, ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF']);

                        // Konstruksi tanggal presensi harian
                        try {
                            $tanggal = Carbon::createFromDate($year, $month, $day);
                            if ($tanggal->isWeekend()) {
                                continue;
                            }
                            $tanggal = $tanggal->format('Y-m-d');
                        } catch (\Exception $e) {
                            // Antisipasi kesalahan format tanggal jika day melebihi batas bulan
                            continue;
                        }
                        
                        // Ekstrak status Check-In dan Check-Out
                        $checkInStatus = $dayData['checkIn']['status'] ?? '';
                        $checkOutStatus = $dayData['checkOut']['status'] ?? '';
                        
                        if ($isHoliday || in_array($statusHarian, ['DL', 'TK', 'IDLI', 'ITM'])) {
                            $checkInStatus = '';
                            $checkOutStatus = '';
                        }
                        
                        // Default potongan
                        $potonganTM = 0.00;
                        $potonganPC = 0.00;
                        $totalPotongan = 0.00;

                        // Potongan
                        if ($statusHarian === 'TK') {
                            $totalPotongan = 3.00;
                        } elseif (!$isHoliday && !in_array($statusHarian, ['DL', 'IDLI', 'ITM'])) {
                            if (!empty($checkInStatus) && $checkInStatus !== 'HN') {
                                $potonganTM = PresensiHarian::getDeductionWeight('TM', $checkInStatus);
                            }
                            if (!empty($checkOutStatus) && $checkOutStatus !== 'HN') {
                                $potonganPC = PresensiHarian::getDeductionWeight('PC', $checkOutStatus);
                            }
                            $totalPotongan = $potonganTM + $potonganPC;
                        }

                        PresensiHarian::updateOrCreate(
                            [
                                'pegawai_id' => $pegawai->id,
                                'tanggal' => $tanggal,
                            ],
                            [
                                'jam_masuk' => (!empty($dayData['checkIn']['time_with_timezone'])) ? $dayData['checkIn']['time_with_timezone'] : null,
                                'jam_keluar' => (!empty($dayData['checkOut']['time_with_timezone'])) ? $dayData['checkOut']['time_with_timezone'] : null,
                                'work_from_masuk' => $dayData['checkIn']['work_from'] ?? null,
                                'work_from_keluar' => $dayData['checkOut']['work_from'] ?? null,
                                'status_kehadiran' => $statusHarian,
                                'kategori_terlambat' => (!empty($checkInStatus) && $checkInStatus !== 'HN') ? $checkInStatus : null,
                                'menit_terlambat' => $dayData['checkIn']['late'] ?? 0,
                                'kategori_pulang_cepat' => (!empty($checkOutStatus) && $checkOutStatus !== 'HN') ? $checkOutStatus : null,
                                'menit_pulang_cepat' => $dayData['checkOut']['late'] ?? 0,
                                'potongan_terlambat' => $potonganTM,
                                'potongan_pulang_cepat' => $potonganPC,
                                'total_potongan' => $totalPotongan,
                                'keterangan' => $dayData['keterangan'] ?? ($isHoliday ? 'Hari Libur / Akhir Pekan' : ($statusHarian === 'TK' ? 'Tanpa Keterangan' : 'Presensi Sinkron BKN')),
                                'is_sync' => true,
                                'synced_at' => now(),
                            ]
                        );
                    }
                    $successCount++;
                }

                // Simpan log sinkronisasi bulanan (bukan sync per pegawai tunggal)
                if (!$pegawaiId) {
                    PresensiSyncLog::create([
                        'tahun' => $year,
                        'bulan' => $month,
                        'triggered_by' => $triggeredBy,
                        'status' => 'sukses',
                        'total_pegawai_synced' => $successCount,
                        'total_pegawai_skipped' => $failedCount,
                        'message' => 'Sinkronisasi data presensi massal berhasil diselesaikan.'
                    ]);
                }
            } else {
                $errMsg = 'API Simpegnas BKN mengembalikan status gagal atau response code non-200';
                Log::error($errMsg, ['response' => $response->body()]);
                if (!$pegawaiId) {
                    PresensiSyncLog::create([
                        'tahun' => $year,
                        'bulan' => $month,
                        'triggered_by' => $triggeredBy,
                        'status' => 'gagal',
                        'total_pegawai_synced' => 0,
                        'total_pegawai_skipped' => 0,
                        'message' => $errMsg . '. Detail: ' . substr($response->body(), 0, 500)
                    ]);
                }
                return ['success' => 0, 'failed' => 1];
            }
        } catch (\Exception $e) {
            Log::error('Kesalahan sinkronisasi presensi: ' . $e->getMessage());
            if (!$pegawaiId) {
                PresensiSyncLog::create([
                    'tahun' => $year,
                    'bulan' => $month,
                    'triggered_by' => $triggeredBy,
                    'status' => 'gagal',
                    'total_pegawai_synced' => 0,
                    'total_pegawai_skipped' => 0,
                    'message' => 'Kesalahan pengecualian: ' . $e->getMessage()
                ]);
            }
            return ['success' => 0, 'failed' => 1];
        }

        return [
            'success' => $successCount,
            'failed' => $failedCount
        ];
    }

    /**
     * Mengambil data rekapitulasi bulanan live langsung dari BKN API (tanpa simpan ke DB)
     */
    public function getLiveRekap(int $month, int $year): array
    {
        try {
            $response = Http::withHeaders([
                    'presensi-key' => $this->token,
                    'Accept' => 'application/json'
                ])
                ->get($this->url, [
                    'kantor_id' => $this->kantorId,
                    'tahun' => $year,
                    'bulan' => $month
                ]);

            if ($response->successful() && $response->json('status') === true) {
                return $response->json('data') ?? [];
            }
        } catch (\Exception $e) {
            Log::error('Kesalahan mengambil data Live Rekap BKN: ' . $e->getMessage());
        }

        return [];
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
