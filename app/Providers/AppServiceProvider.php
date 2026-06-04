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
use App\Models\Page;
use App\Models\Pengaturan;
use App\Models\Profil;
use App\Services\VisitorService;
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

        // Komposer untuk semua view
        View::composer('*', function ($view) {
            $beritaList  = Berita::select('kategori')->distinct()->pluck('kategori');
            $unduhanList = Unduhan::select('kategori')->distinct()->pluck('kategori');
            $galeriList  = Galeri::select('kategori')->distinct()->pluck('kategori');
            $latestNews  = Berita::latest()->take(5)->get();
            $popularNews = Berita::orderBy('view', 'desc')->take(5)->get();
            $pengaturan  = Pengaturan::first();

            // Statistik kunjungan
            $visitorService  = new VisitorService();
            $visitorStats    = $visitorService->getStatistik();
            $visitorInfo     = $visitorService->getVisitorInfo();

            $view->with(compact(
                'beritaList', 'unduhanList', 'galeriList', 'latestNews', 'popularNews', 'pengaturan',
                'visitorStats', 'visitorInfo'
            ));
        });

        View::share('pagemenu', Page::where('status', 'aktif')->where('kategori', 'profil')->orderBy('created_at', 'asc')->get());
        View::share('saluranmenu', Page::where('status', 'aktif')->where('kategori', 'saluran')->orderBy('created_at', 'asc')->get());
        View::share('unduhanmenu', Unduhan::select('kategori')->distinct()->pluck('kategori'));
        
       // Bikin $template bisa dipakai di semua Blade
        View::share('template', config('master.app.web.template'));

        // Gunakan Bootstrap untuk pagination
        Paginator::useBootstrap();
    }
}
