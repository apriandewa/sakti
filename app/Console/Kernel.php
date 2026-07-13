<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
        // Sinkronisasi otomatis rekap presensi Simpegnas BKN setiap hari jam 14:00 WIB
        $schedule->call(function () {
            $service = app(\App\Services\SimpegnasService::class);
            $now = now();

            // Tarik data bulan berjalan - loop semua kantor yang punya pegawai lokal,
            // TIDAK auto-insert pegawai baru (autoCreatePegawai: false, default).
            $service->syncAttendance(
                month: $now->month,
                year: $now->year,
                triggeredBy: 'Sistem (Otomatis)',
            );

            // Tarik data bulan lalu jika berada pada minggu pertama bulan baru untuk memastikan kelengkapan data
            if ($now->day <= 7) {
                $lastMonth = $now->copy()->subMonth();
                $service->syncAttendance(
                    month: $lastMonth->month,
                    year: $lastMonth->year,
                    triggeredBy: 'Sistem (Otomatis)',
                );
            }
        })->dailyAt('15:13');
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