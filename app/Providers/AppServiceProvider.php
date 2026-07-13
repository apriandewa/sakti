<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\PersonalAccessToken;
use Laravel\Sanctum\Sanctum;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\Models\Berita;
use App\Models\Unduhan;
use App\Models\Galeri;
use App\Models\Slider;
use App\Models\Page;
use App\Models\Pengaturan;
use App\Models\Tautan;
use App\Models\Testimoni;
use App\Models\Penghargaan;

use App\Observers\FrontendCacheObserver;
use App\Services\VisitorService;
use App\Services\VerificationService;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        $this->app->singleton('verification', function ($app) {
            return new VerificationService();
        });
    }

    /**
     * Bootstrap any application services.
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);
        if (config('app.env') === 'production') {
            $this->app['request']->server->set('HTTPS', TRUE);
        }

        // Set locale Carbon ke bahasa Indonesia
        Carbon::setLocale('id');

        // ── Auto-clear cache saat ada perubahan data di admin ─────────────────
        // Setiap model berikut di-create/update/delete → cache frontend langsung
        // dihapus agar konten terbaru langsung tampil tanpa tunggu expiry.
        $models = [
            Berita::class,
            Unduhan::class,
            Galeri::class,
            Slider::class,
            Page::class,
            Pengaturan::class,
            Tautan::class,
            Testimoni::class,
            Penghargaan::class,
        ];

        foreach ($models as $model) {
            $model::observe(FrontendCacheObserver::class);
        }

        // Komposer untuk semua view — dengan CACHING agar tidak query DB berulang kali
        View::composer('*', function ($view) {
            // Cache semua data statis selama 5 menit (300 detik)
            $cachedData = \Illuminate\Support\Facades\Cache::remember('view_composer_global', 300, function () {
                $beritaList  = Berita::select('kategori')->distinct()->pluck('kategori');
                $unduhanList = Unduhan::select('kategori')->distinct()->pluck('kategori');
                $galeriList  = Galeri::select('kategori')->distinct()->pluck('kategori');
                $latestNews  = Berita::latest()->take(5)->get();
                $popularNews = Berita::orderBy('view', 'desc')->take(5)->get();
                $pengaturan  = Pengaturan::first();

                // Ticker navbar
                $navTicker = collect();

                Slider::where('status', 'aktif')->latest()->take(3)->get()
                    ->each(fn($item) => $navTicker->push([
                        'label' => '🖼 ' . $item->nama,
                        'url'   => url('/'),
                    ]));

                Berita::where('status', 'terverifikasi')->latest()->take(3)->get()
                    ->each(fn($item) => $navTicker->push([
                        'label' => '📰 ' . $item->nama,
                        'url'   => route('berita.detail', $item->slug),
                    ]));

                Galeri::where('status', 'terverifikasi')->latest()->take(2)->get()
                    ->each(fn($item) => $navTicker->push([
                        'label' => '🖼 ' . $item->nama,
                        'url'   => route('galeri.detail', $item->slug),
                    ]));

                Unduhan::where('status', 'terverifikasi')->latest()->take(2)->get()
                    ->each(fn($item) => $navTicker->push([
                        'label' => '📥 ' . $item->nama,
                        'url'   => route('unduhan.detail', $item->slug),
                    ]));

                $pagemenu    = Page::where('status', 'aktif')->where('kategori', 'profil')->orderBy('created_at', 'asc')->get();
                $saluranmenu = Page::where('status', 'aktif')->where('kategori', 'saluran')->orderBy('created_at', 'asc')->get();
                $unduhanmenu = Unduhan::select('kategori')->distinct()->pluck('kategori');

                return compact(
                    'beritaList', 'unduhanList', 'galeriList', 'latestNews', 'popularNews', 'pengaturan',
                    'navTicker', 'pagemenu', 'saluranmenu', 'unduhanmenu'
                );
            });

            // Statistik kunjungan: cache per-menit agar relatif real-time
            $visitorStats = \Illuminate\Support\Facades\Cache::remember('visitor_stats', 60, function () {
                $visitorService = new VisitorService();
                return $visitorService->getStatistik();
            });

            // Info pengunjung (IP/browser) tidak di-cache karena per-request
            $visitorService = new VisitorService();
            $visitorInfo    = $visitorService->getVisitorInfo();

            $view->with(array_merge($cachedData, compact('visitorStats', 'visitorInfo')));
        });
        
       // Bikin $template bisa dipakai di semua Blade
        View::share('template', config('master.app.web.template'));

        // Gunakan Bootstrap untuk pagination
        Paginator::useBootstrap();
    }
}
