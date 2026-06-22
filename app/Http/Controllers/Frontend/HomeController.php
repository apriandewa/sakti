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
use App\Models\Informasi;
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
            // Cek cache terlebih dahulu agar tidak request ke API setiap kali halam dibuka
            if (Cache::has($cacheKey)) {
                return Cache::get($cacheKey);
            }

            try {
                // Set timeout sedikit lebih lama agar API yang lambat tidak langsung timeout
                $response = Http::timeout(15)->get($url);

                if ($response->successful()) {
                    $json = $response->json();
                    $count = 0;

                    if (isset($json['data']) && is_array($json['data'])) {
                        $count = count($json['data']);
                    } elseif (is_array($json)) {
                        $count = count($json);
                    }

                    if ($count > 0) {
                        // Simpan ke cache 1 jam (3600 detik)
                        Cache::put($cacheKey, $count, 3600);
                        return $count;
                    }
                }
            } catch (\Exception $e) {
                // Jika gagal, biarkan me-return nilai fallback atau 0
            }

            return 0; // Return 0 jika API gagal dan cache belum ada
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

        $dbInfotersedia = Informasi::where('tipe', 'TERSEDIA')->where('status', 'DITERIMA')->count();
        $infotersedia += $dbInfotersedia;

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

        $dbInfoberkala = Informasi::where('tipe', 'BERKALA')->where('status', 'DITERIMA')->count();
        $infoberkala += $dbInfoberkala;

        /*
        |--------------------------------------------------------------------------
        | INFORMASI SERTA MERTA & DIKECUALIKAN (INTERNAL DATABASE)
        |--------------------------------------------------------------------------
        */
        $infosetiapsaat = Informasi::where('tipe', 'SERTA MERTA')->where('status', 'DITERIMA')->count();
        $infodikecualikan = Informasi::where('tipe', 'DIKECUALIKAN')->where('status', 'DITERIMA')->count();

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
            'slider'      => Slider::where('status', 'aktif')->latest()->limit(6)->get(),
            'struktur'    => Struktur::where('status', 'aktif')->oldest()->limit(15)->get(),
            'testimoni'   => Testimoni::where('status', 'DISETUJUI')->oldest()->limit(10)->get(),
            'penghargaan' => Penghargaan::where('status', 'aktif')->oldest()->limit(10)->get(),

            'welcome' => Page::where('status', 'aktif')->where('kategori', 'welcome')->first(),
            'profil'  => Page::where('status', 'aktif')->where('kategori', 'profil')->get(),
            'saluran' => Page::where('status', 'aktif')->where('kategori', 'saluran')->get(),

            // Statistik internal
            'pemohon'   => $totalPemohon,
            'diminta'   => $totalDiminta,
            'diberikan' => $totalDiberikan,
            'ditolak'   => $totalDitolak,

            // Statistik API & Internal Informasi
            'infotersedia' => $infotersedia,
            'infoberkala'  => $infoberkala,
            'infosetiapsaat' => $infosetiapsaat,
            'infodikecualikan' => $infodikecualikan,
        ]);
    }
}
