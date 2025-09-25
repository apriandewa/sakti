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

use Illuminate\Http\Request;

class HomeController extends Controller
{

public function index()
{

    // Hitung statistik
    $totalPemohon   = Statistik::sum('pemohon');
    $totalDiminta   = Statistik::sum('diminta');
    $totalDiberikan = Statistik::sum('diberikan');
    $totalDitolak   = Statistik::sum('ditolak');

    return view('frontend.home', [
        "title"      => "PPID Indragiri Hulu",
        "judul"      => "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
        "subjudul"   => "Website Resmi PPID Kabupaten Indragiri Hulu",
        'berita'     => Berita::where('status', 'TERVERIFIKASI')->orderBy('created_at', 'desc')->limit(12)->get(),
        'unduhan'    => Unduhan::where('status', 'TERVERIFIKASI')->orderBy('created_at', 'desc')->limit(12)->get(),
        'galeri'     => Galeri::where('status', 'TERVERIFIKASI')->orderBy('created_at', 'desc')->limit(12)->get(),
        'client'     => Tautan::where('status', 'aktif')->orderBy('created_at', 'asc')->get(),
        'slider'     => Slider::where('status', 'aktif')->orderBy('created_at', 'asc')->get(),
        'struktur'   => Struktur::where('status', 'aktif')->orderBy('created_at', 'asc')->get(),
        'testimoni'  => Testimoni::where('status', 'aktif')->orderBy('created_at', 'asc')->get(),
        'penghargaan'=> Penghargaan::where('status', 'aktif')->orderBy('created_at', 'asc')->get(),
        'profil'    => Page::where('status', 'aktif')->where('kategori', 'profil')->get(),
        'saluran'    => Page::where('status', 'aktif')->where('kategori', 'saluran')->get(),
        'pemohon'    => $totalPemohon,
        'diminta'    => $totalDiminta,
        'diberikan'  => $totalDiberikan,
        'ditolak'    => $totalDitolak,

        
    ]);
}

}
