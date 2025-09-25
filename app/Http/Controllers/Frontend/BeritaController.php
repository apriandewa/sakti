<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Unduhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BeritaController extends Controller
{
    public function index() {
       
        return view('frontend.berita.index', [
            "title" => "PPID Indragiri Hulu",
            "judul" => "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => "Website Resmi PPID Kabupaten Indragiri Hulu",
            'berita' => Berita::where('status', 'TERVERIFIKASI')->orderBy('created_at', 'desc')->filter(request(['search', 'kategori']))->paginate(12)->withQueryString(),
           
        ]);
    }

    

public function show($slug)
{
    $berita = Berita::where('slug', $slug)
        ->where('status', 'TERVERIFIKASI')
        ->firstOrFail();

    $sessionKey = 'viewed_post_' . $berita->id;

    if (!session()->has($sessionKey)) {
        DB::table('beritas')
            ->where('id', $berita->id)
            ->update([
                'view' => DB::raw('view + 1')
            ]);

        session()->put($sessionKey, true);
    }

    // ambil ulang dari DB biar sinkron
    $berita->refresh();

    return view('frontend.berita.detail', [
        "title"    => "Detail Berita",
        "judul"    => "Berita PPID Kabupaten Indragiri Hulu",
        "subjudul" => "Detail Berita PPID Kabupaten Indragiri Hulu",
        "news"     => $berita,
    ]);
}


}
