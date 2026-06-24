<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Galeri;

use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index() {
        $pengaturan = \App\Models\Pengaturan::first();
        return view('frontend.galeri.index', [
            "title" => $pengaturan->judul ?? "Diskominfotik Indragiri Hulu",
            "judul" => $pengaturan->subjudul ?? "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => $pengaturan->deskripsi ?? "Website Resmi Diskominfotik Kabupaten Indragiri Hulu",
            'galeri' => Galeri::where('status', 'TERVERIFIKASI')->orderBy('created_at', 'desc')->filter(request(['search', 'kategori']))->paginate(12)->withQueryString(),
           
        ]);
    }

     public function show($slug)
    {

        $Galeri = Galeri::where('slug', $slug)->where('status', 'TERVERIFIKASI')->first();
        $pengaturan = \App\Models\Pengaturan::first();
            return view('frontend.galeri.detail', [
            "title" => $pengaturan->judul ?? "Detail Galeri",
            "judul" => $pengaturan->subjudul ?? "Galeri Diskominfotik Kabupaten Indragiri Hulu",
            "subjudul" => $pengaturan->deskripsi ?? "Detail Galeri Diskominfotik Kabupaten Indragiri Hulu",
            'news' => $Galeri,
            ]);

    }
}
