<?php

namespace App\Console\Commands;

use App\Services\Ekinerja\BknApiException;
use App\Services\Ekinerja\EkinerjaService;
use Illuminate\Console\Command;

/**
 * php artisan ekinerja:sync-periode
 *
 * Menarik ulang referensi periode dari API BKN secara paksa (mengabaikan
 * TTL cache), lalu menyimpannya ke tabel ekinerja_referensi_periode.
 * Cocok dijadwalkan harian via Laravel Scheduler.
 */
class SyncPeriodeEkinerja extends Command
{
    protected $signature = 'ekinerja:sync-periode';

    protected $description = 'Sinkronisasi referensi periode e-Kinerja dari API BKN ke cache lokal';

    public function handle(EkinerjaService $ekinerjaService): int
    {
        $this->info('Menyinkronkan referensi periode dari BKN...');

        try {
            $ekinerjaService->ensurePeriodeSynced(force: true);
        } catch (BknApiException $e) {
            $this->error('Gagal sinkronisasi: ' . $e->getMessage());

            return self::FAILURE;
        }

        $this->info('Sinkronisasi referensi periode selesai.');

        return self::SUCCESS;
    }
}
