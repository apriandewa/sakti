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
        // Sinkronisasi otomatis rekap presensi Simpegnas BKN setiap Sabtu jam 10:00 pagi
        $schedule->call(function () {
            $service = app(\App\Services\SimpegnasService::class);
            $now = now();
            
            // Tarik data bulan berjalan
            $service->syncAttendance($now->month, $now->year, null, 'Sistem (Otomatis)');
            
            // Tarik data bulan lalu jika berada pada minggu pertama bulan baru untuk memastikan kelengkapan data
            if ($now->day <= 7) {
                $lastMonth = $now->copy()->subMonth();
                $service->syncAttendance($lastMonth->month, $lastMonth->year, null, 'Sistem (Otomatis)');
            }
        })->weeklyOn(6, '10:00');
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
