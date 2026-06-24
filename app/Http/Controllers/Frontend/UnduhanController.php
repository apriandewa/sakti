<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Unduhan;
use App\Models\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UnduhanController extends Controller
{
    public function index() {
        $pengaturan = \App\Models\Pengaturan::first();
        return view('frontend.unduhan.index', [
            "title" => $pengaturan->judul ?? "Diskominfotik Indragiri Hulu",
            "judul" => $pengaturan->subjudul ?? "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => $pengaturan->deskripsi ?? "Website Resmi Diskominfotik Kabupaten Indragiri Hulu",
            'unduhan' => Unduhan::where('status', 'TERVERIFIKASI')
                ->orderBy('created_at', 'desc')
                ->filter(request(['search', 'kategori']))
                ->paginate(12)
                ->withQueryString(),
        ]);
    }

    public function show($slug)
    {
        $Unduhan = Unduhan::where('slug', $slug)
            ->where('status', 'TERVERIFIKASI')
            ->firstOrFail();

        $Unduhan->view = (int) $Unduhan->view + 1;
        $Unduhan->save();

        $pengaturan = \App\Models\Pengaturan::first();
        return view('frontend.unduhan.detail', [
            "title"    => $pengaturan->judul ?? "Detail Unduhan",
            "judul"    => $pengaturan->subjudul ?? "Unduhan Diskominfotik Kabupaten Indragiri Hulu",
            "subjudul" => $pengaturan->deskripsi ?? "Detail Unduhan Diskominfotik Kabupaten Indragiri Hulu",
            'news'     => $Unduhan,
        ]);
    }

    /**
     * Catat unduhan & redirect ke file
     */
    public function download($slug, $fileId)
    {
        $unduhan = Unduhan::where('slug', $slug)
            ->where('status', 'TERVERIFIKASI')
            ->firstOrFail();

        $file = $unduhan->file()->where('id', $fileId)->firstOrFail();

        $unduhan->download = (int) $unduhan->download + 1;
        $unduhan->save();

        return Storage::disk($file->disk)->download($file->target, $file->name);
    }

    public function view($slug, $fileId)
    {
        $unduhan = Unduhan::where('slug', $slug)
            ->where('status', 'TERVERIFIKASI')
            ->firstOrFail();

        $file = $unduhan->file()->where('id', $fileId)->firstOrFail();

        $unduhan->download = (int) $unduhan->download + 1;
        $unduhan->save();

        return response()->json([
            'url' => url($file->public_stream),
        ]);
    }
}