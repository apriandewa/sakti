<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Unduhan;

use Illuminate\Http\Request;

class UnduhanController extends Controller
{
    public function index() {
       
        return view('frontend.unduhan.index', [
            "title" => "PPID Indragiri Hulu",
            "judul" => "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => "Website Resmi PPID Kabupaten Indragiri Hulu",
            'unduhan' => Unduhan::where('status', 'TERVERIFIKASI')->orderBy('created_at', 'desc')->filter(request(['search', 'kategori']))->paginate(12)->withQueryString(),
           
        ]);
    }

     public function show($slug)
    {

        $Unduhan = Unduhan::where('slug', $slug)->where('status', 'TERVERIFIKASI')->first();
            return view('frontend.unduhan.detail', [
            "title" => "Detail Unduhan",
            "judul" => "Unduhan PPID Kabupaten Indragiri Hulu",
            "subjudul" => "Detail Unduhan PPID Kabupaten Indragiri Hulu",
            'news' => $Unduhan,
            ]);

        

        return view($this->view . '.show', compact('data'));
    }
}
