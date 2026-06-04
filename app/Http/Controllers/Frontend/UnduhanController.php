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
        return view('frontend.unduhan.index', [
            "title" => "PPID Indragiri Hulu",
            "judul" => "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => "Website Resmi PPID Kabupaten Indragiri Hulu",
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

        return view('frontend.unduhan.detail', [
            "title"    => "Detail Unduhan",
            "judul"    => "Unduhan PPID Kabupaten Indragiri Hulu",
            "subjudul" => "Detail Unduhan PPID Kabupaten Indragiri Hulu",
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