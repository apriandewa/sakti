<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Statistik;
use App\Models\Tautan;
use App\Models\Slider;
use App\Models\Struktur;
use App\Models\Testimoni;
use App\Models\Berita;
use App\Models\Unduhan;
use App\Models\Galeri;
use App\Models\Page;
use App\Models\Penghargaan;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        /*
        |--------------------------------------------------------------------------
        | STATISTIK INTERNAL DATABASE
        |--------------------------------------------------------------------------
        */
        $totalPemohon   = Statistik::sum('pemohon');
        $totalDiminta   = Statistik::sum('diminta');
        $totalDiberikan = Statistik::sum('diberikan');
        $totalDitolak   = Statistik::sum('ditolak');

        /*
        |--------------------------------------------------------------------------
        | FUNCTION AMBIL DATA API (STABIL + FALLBACK CACHE)
        |--------------------------------------------------------------------------
        */
        $getApiCount = function ($url, $cacheKey) {

            try {

                $response = Http::timeout(8)->get($url);

                if ($response->successful()) {

                    $json = $response->json();

                    if (isset($json['data']) && is_array($json['data'])) {
                        $count = count($json['data']);
                    } elseif (is_array($json)) {
                        $count = count($json);
                    } else {
                        return Cache::get($cacheKey);
                    }

                    // Simpan ke cache 1 jam
                    Cache::put($cacheKey, $count, 3600);

                    return $count;
                }

            } catch (\Exception $e) {
                // Jika gagal, ambil data lama dari cache
                return Cache::get($cacheKey);
            }

            return Cache::get($cacheKey);
        };

        /*
        |--------------------------------------------------------------------------
        | INFORMASI TERSEDIA (JDIH)
        |--------------------------------------------------------------------------
        */
        $sourcesInfotersedia = [
            'https://api-splp.layanan.go.id/jdih-inhu/1.0/integrasijdihinhu',
            'https://api-splp.layanan.go.id/jdih-dprd-indragiri-hulu/1.0/integrasijdihdprd',
        ];

        $infotersedia = 0;

        foreach ($sourcesInfotersedia as $url) {
            $result = $getApiCount($url, 'cache_infotersedia_' . md5($url));

            if (!is_null($result)) {
                $infotersedia += $result;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | INFORMASI BERKALA
        |--------------------------------------------------------------------------
        */
        $sourcesInfoberkala = [
            'https://api-splp.layanan.go.id/transparansi_anggaran_indragiri_hulu/1.0/api/dokumenapi',
        ];

        $infoberkala = 0;

        foreach ($sourcesInfoberkala as $url) {
            $result = $getApiCount($url, 'cache_infoberkala_' . md5($url));

            if (!is_null($result)) {
                $infoberkala += $result;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        return view('frontend.home', [

            "title"    => "PPID Indragiri Hulu",
            "judul"    => "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => "Website Resmi PPID Kabupaten Indragiri Hulu",

            'berita'      => Berita::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),
            'unduhan'     => Unduhan::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),
            'galeri'      => Galeri::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),

            'client'      => Tautan::where('status', 'aktif')->oldest()->limit(20)->get(),
            'slider'      => Slider::where('status', 'aktif')->oldest()->limit(10)->get(),
            'struktur'    => Struktur::where('status', 'aktif')->oldest()->limit(15)->get(),
            'testimoni'   => Testimoni::where('status', 'aktif')->oldest()->limit(10)->get(),
            'penghargaan' => Penghargaan::where('status', 'aktif')->oldest()->limit(10)->get(),

            'welcome' => Page::where('status', 'aktif')->where('kategori', 'welcome')->first(),
            'profil'  => Page::where('status', 'aktif')->where('kategori', 'profil')->get(),
            'saluran' => Page::where('status', 'aktif')->where('kategori', 'saluran')->get(),

            // Statistik internal
            'pemohon'   => $totalPemohon,
            'diminta'   => $totalDiminta,
            'diberikan' => $totalDiberikan,
            'ditolak'   => $totalDitolak,

            // Statistik API
            'infotersedia' => $infotersedia,
            'infoberkala'  => $infoberkala,
        ]);
    }
}
