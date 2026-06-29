<?php

namespace App\Observers;

use Illuminate\Support\Facades\Cache;

/**
 * FrontendCacheObserver
 *
 * Otomatis menghapus cache frontend setiap kali ada
 * operasi create / update / delete pada model yang didaftarkan.
 *
 * Cache yang dibersihkan:
 *  - home_page_data      → data halaman utama (HomeController)
 *  - view_composer_global → data navbar, ticker, menu (AppServiceProvider)
 *  - visitor_stats        → statistik kunjungan (opsional)
 */
class FrontendCacheObserver
{
    /** Hapus cache setelah data baru dibuat */
    public function created($model): void
    {
        $this->clearCache();
    }

    /** Hapus cache setelah data diperbarui */
    public function updated($model): void
    {
        $this->clearCache();
    }

    /** Hapus cache setelah data dihapus */
    public function deleted($model): void
    {
        $this->clearCache();
    }

    /** Hapus cache setelah data dipulihkan (soft delete) */
    public function restored($model): void
    {
        $this->clearCache();
    }

    /**
     * Hapus semua cache yang berhubungan dengan frontend.
     */
    private function clearCache(): void
    {
        Cache::forget('home_page_data');
        Cache::forget('view_composer_global');
    }
}
