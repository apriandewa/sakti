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
        $pengaturan = \App\Models\Pengaturan::first();
        return view('frontend.berita.index', [
            "title" => $pengaturan->judul ?? "Diskominfotik Indragiri Hulu",
            "judul" => $pengaturan->subjudul ?? "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => $pengaturan->deskripsi ?? "Website Resmi Diskominfotik Kabupaten Indragiri Hulu",
            'berita' => Berita::with('user')
            ->where('status', 'TERVERIFIKASI')
            ->latest()
            ->filter(request(['search', 'kategori']))
            ->paginate(12)
            ->withQueryString(),

        ]);
    }

    

public function show($slug)
{
    $berita = Berita::where('slug', $slug)
        ->where('status', 'TERVERIFIKASI')
        ->firstOrFail();

    $sessionKey = 'viewed_post_' . $berita->id;

    // Jika belum pernah dilihat dalam session
    if (!session()->has($sessionKey)) {

        $berita->increment('view');

        session()->put($sessionKey, true);
    }

    $pengaturan = \App\Models\Pengaturan::first();

    return view('frontend.berita.detail', [
        "title"    => $pengaturan->judul ?? "Detail Berita",
        "judul"    => $pengaturan->subjudul ?? "Berita Diskominfotik Kabupaten Indragiri Hulu",
        "subjudul" => $pengaturan->deskripsi ?? "Detail Berita Diskominfotik Kabupaten Indragiri Hulu",
        "news"     => $berita,
    ]);
}



}
