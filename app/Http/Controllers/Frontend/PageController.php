<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Galeri;
use App\Models\Unduhan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function index() {
        $pengaturan = \App\Models\Pengaturan::first();
        return view('frontend.page.index', [
            "title" => $pengaturan->judul ?? "Diskominfotik Indragiri Hulu",
            "judul" => $pengaturan->subjudul ?? "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => $pengaturan->deskripsi ?? "Website Resmi Diskominfotik Kabupaten Indragiri Hulu",
            'page' => Page::where('status', 'aktif')->orderBy('created_at', 'desc')->filter(request(['search', 'kategori']))->paginate(12)->withQueryString(),     
        ]);
    }

    

public function show($slug)
{
    $page = Page::where('slug', $slug)
        ->where('status', 'aktif')
        ->firstOrFail();

    $sessionKey = 'viewed_post_' . $page->id;

    if (!session()->has($sessionKey)) {
        DB::table('pages')
            ->where('id', $page->id)
            ->update([
                'view' => DB::raw('view + 1')
            ]);

        session()->put($sessionKey, true);
    }

    // ambil ulang dari DB biar sinkron
    $page->refresh();

    $pengaturan = \App\Models\Pengaturan::first();
    return view('frontend.page.detail', [
        "title"    => $pengaturan->judul ?? "Detail Page",
        "judul"    => $pengaturan->subjudul ?? "Page Diskominfotik Kabupaten Indragiri Hulu",
        "subjudul" => $pengaturan->deskripsi ?? "Detail Page Diskominfotik Kabupaten Indragiri Hulu",
        "news"     => $page,
    ]);
}


}
