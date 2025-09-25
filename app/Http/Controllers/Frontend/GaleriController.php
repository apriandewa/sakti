<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Galeri;

use Illuminate\Http\Request;

class GaleriController extends Controller
{
    public function index() {
       
        return view('frontend.galeri.index', [
            "title" => "PPID Indragiri Hulu",
            "judul" => "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => "Website Resmi PPID Kabupaten Indragiri Hulu",
            'galeri' => Galeri::where('status', 'TERVERIFIKASI')->orderBy('created_at', 'desc')->filter(request(['search', 'kategori']))->paginate(12)->withQueryString(),
           
        ]);
    }

     public function show($slug)
    {

        $Galeri = Galeri::where('slug', $slug)->where('status', 'TERVERIFIKASI')->first();
        
            return view('frontend.galeri.detail', [
            "title" => "Detail Galeri",
            "judul" => "Galeri PPID Kabupaten Indragiri Hulu",
            "subjudul" => "Detail Galeri PPID Kabupaten Indragiri Hulu",
            'news' => $Galeri,
            ]);

    }
}
