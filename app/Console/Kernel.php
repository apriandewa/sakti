<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Sinkronisasi otomatis rekap presensi Simpegnas BKN
        // Catatan: jam 15:13 saat ini SEMENTARA untuk keperluan testing.
        // Sesuai PRD (2.5), jadwal production seharusnya 18:00 WIB.
        $schedule->call(function () {
            $service = app(\App\Services\SimpegnasService::class);
            $now = now();

            Log::channel('scheduler')->info('[Presensi] Sinkronisasi otomatis dimulai', [
                'month' => $now->month,
                'year'  => $now->year,
            ]);

            try {
                // Tarik data bulan berjalan - loop semua kantor yang punya pegawai lokal,
                // TIDAK auto-insert pegawai baru (autoCreatePegawai: false, default).
                $service->syncAttendance(
                    month: $now->month,
                    year: $now->year,
                    triggeredBy: 'Sistem (Otomatis)',
                );

                // Tarik data bulan lalu jika berada pada minggu pertama bulan baru
                // untuk memastikan kelengkapan data.
                if ($now->day <= 7) {
                    $lastMonth = $now->copy()->subMonth();
                    $service->syncAttendance(
                        month: $lastMonth->month,
                        year: $lastMonth->year,
                        triggeredBy: 'Sistem (Otomatis)',
                    );
                }

                Log::channel('scheduler')->info('[Presensi] Sinkronisasi otomatis selesai');
            } catch (\Throwable $e) {
                Log::channel('scheduler')->error('[Presensi] Sinkronisasi otomatis gagal', [
                    'message' => $e->getMessage(),
                ]);
            }
        })
            ->dailyAt('15:13')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->name('sync-presensi-simpegnas'); // wajib diisi karena closure (bukan Artisan command)

        // Sinkronisasi referensi periode e-Kinerja dari BKN setiap hari pukul 01:00 WIB
        $schedule->command('ekinerja:sync-periode')
            ->dailyAt('01:00')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/ekinerja-sync-periode.log'));

        // Sinkronisasi penilaian e-Kinerja (seluruh Unor aktif, periode berjalan) setiap hari pukul 02:00 WIB
        $schedule->command('ekinerja:sync-penilaian')
            ->dailyAt('02:00')
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->appendOutputTo(storage_path('logs/ekinerja-sync-penilaian.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}