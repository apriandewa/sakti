<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Tautan;
use App\Models\Slider;
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
        | RETURN VIEW — dengan CACHE untuk semua data agar load pertama cepat
        |--------------------------------------------------------------------------
        */
        $homeData = Cache::remember('home_page_data', 300, function () {
            $pengaturan = \App\Models\Pengaturan::first();

            return [
                "title"    => $pengaturan->judul,
                "judul"    => $pengaturan->subjudul,
                "subjudul" => $pengaturan->deskripsi,

                'berita'      => Berita::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),
                'unduhan'     => Unduhan::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),
                'galeri'      => Galeri::where('status', 'TERVERIFIKASI')->latest()->limit(12)->get(),

                'client'      => Tautan::where('status', 'aktif')->oldest()->limit(20)->get(),
                'slider'      => Slider::where('status', 'aktif')->latest()->limit(6)->get(),
                'struktur'    => \App\Models\Pegawai::with(['pangkat', 'statusPegawai', 'jabatanNama', 'bidang'])
                                    ->where('status', 'aktif')->oldest()->limit(15)->get(),
                'testimoni'   => Testimoni::where('status', 'DISETUJUI')->oldest()->limit(10)->get(),
                'penghargaan' => Penghargaan::where('status', 'aktif')->oldest()->limit(10)->get(),

                'welcome' => Page::where('status', 'aktif')->where('kategori', 'welcome')->first(),
                'bidang'  => Page::where('status', 'aktif')->where('kategori', 'bidang')->get(),
                'program' => Page::where('status', 'aktif')->where('kategori', 'program')->get(),
            ];
        });

        return view('frontend.home', $homeData);
    }

    public function verifikasiUndangan($token)
    {
        $data = \App\Models\AgendaRapat::with(['pegawai', 'notulen', 'peserta', 'dokumenTte'])->where('barcode_token', $token)->firstOrFail();
        
        $title = "Verifikasi Dokumen Rapat";
        $subjudul = "Cek Keaslian dan Status Tanda Tangan Elektronik (TTE)";
        $jenis = request('jenis');

        // Ambil dokumen TTE masing-masing jenis
        $tteUndangan = $data->getDokumenTteByJenis('undangan');
        $tteDaftarHadir = $data->getDokumenTteByJenis('daftar_hadir');
        $tteNotulenNotulis = $data->getDokumenTteByJenis('notulen_notulis');
        $tteNotulenPimpinan = $data->getDokumenTteByJenis('notulen_pimpinan');
        
        // Load pimpinan dan notulis dari rapat notulen
        $pimpinan = $data->notulen ? \App\Models\Pegawai::with('jabatanNama')->find($data->notulen->pimpinan_rapat_id) : null;
        $notulis = $data->notulen ? \App\Models\Pegawai::with('jabatanNama')->find($data->notulen->notulis_id) : null;

        return view('frontend.rapat.verifikasi', compact(
            'data',
            'title',
            'subjudul',
            'tteUndangan',
            'tteDaftarHadir',
            'tteNotulenNotulis',
            'tteNotulenPimpinan',
            'pimpinan',
            'notulis',
            'jenis'
        ));
    }

    public function viewSignedPdf($token, $jenis)
    {
        $agenda = \App\Models\AgendaRapat::where('barcode_token', $token)->firstOrFail();

        // Cari signed PDF
        $dokumen = \App\Models\DokumenTte::where('agenda_rapat_id', $agenda->id)
            ->where('jenis_dokumen', $jenis)
            ->where('status', 'signed')
            ->first();

        if ($dokumen && $dokumen->signed_file) {
            $filePath = storage_path('app/public/dokumen-tte/' . $dokumen->signed_file);
            if (file_exists($filePath)) {
                return response()->file($filePath, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'inline; filename="' . $jenis . '_' . \Illuminate\Support\Str::slug($agenda->nama) . '.pdf"'
                ]);
            }
        }

        // Fallback jika belum di-TTE (untuk stream PDF dinamis)
        if ($jenis === 'undangan') {
            $data = $agenda;
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
                ->loadView('backend.agenda-rapat.pdf.undangan', compact('data'));
        } elseif ($jenis === 'daftar_hadir') {
            $data = $agenda;
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
                ->loadView('backend.agenda-rapat.pdf.daftar-hadir', compact('data'));
        } else { // notulen, notulen_notulis, notulen_pimpinan
            $data = $agenda;
            if (!$agenda->notulen) {
                abort(404, 'Notulen belum dibuat');
            }
            $pimpinan = \App\Models\Pegawai::find($agenda->notulen->pimpinan_rapat_id ?? null);
            $notulis = \App\Models\Pegawai::find($agenda->notulen->notulis_id ?? null);
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::setOptions(['isRemoteEnabled' => true])
                ->loadView('backend.agenda-rapat.pdf.notulen', compact('data', 'pimpinan', 'notulis'));
        }

        $pdf->setPaper('A4', 'portrait');
        return response($pdf->output(), 200)->header('Content-Type', 'application/pdf');
    }
}
