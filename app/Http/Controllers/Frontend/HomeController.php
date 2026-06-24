<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
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
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */
        $pengaturan = \App\Models\Pengaturan::first();
        return view('frontend.home', [

            "title"    => $pengaturan->judul,
            "judul"    => $pengaturan->subjudul,
            "subjudul" => $pengaturan->deskripsi,

            'berita'      => Berita::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),
            'unduhan'     => Unduhan::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),
            'galeri'      => Galeri::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),

            'client'      => Tautan::where('status', 'aktif')->oldest()->limit(20)->get(),
            'slider'      => Slider::where('status', 'aktif')->latest()->limit(6)->get(),
            'struktur'    => Struktur::where('status', 'aktif')->oldest()->limit(15)->get(),
            'testimoni'   => Testimoni::where('status', 'DISETUJUI')->oldest()->limit(10)->get(),
            'penghargaan' => Penghargaan::where('status', 'aktif')->oldest()->limit(10)->get(),

            'welcome' => Page::where('status', 'aktif')->where('kategori', 'welcome')->first(),
            'bidang'  => Page::where('status', 'aktif')->where('kategori', 'bidang')->get(),
            'program' => Page::where('status', 'aktif')->where('kategori', 'program')->get(),
        ]);
    }
}
