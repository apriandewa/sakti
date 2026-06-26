<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\Pengaturan;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    public function index(Request $request)
    {
        $pengaturan = Pengaturan::first();
        $query = Pegawai::with(['pangkat', 'statusPegawai', 'jabatanNama', 'bidang'])
            ->where('status', 'aktif');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nip', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('jabatanNama', function($jq) use ($search) {
                      $jq->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('bidang_id')) {
            $query->where('bidang_id', $request->input('bidang_id'));
        }

        $pegawai = $query->oldest()->paginate(12);
        $bidangs = \App\Models\Page::where('status', 'aktif')->where('kategori', 'bidang')->get();

        return view('frontend.pegawai.index', [
            'title' => 'Daftar Pegawai - ' . ($pengaturan->judul ?? 'Diskominfotik'),
            'judul' => 'Daftar Pegawai',
            'subjudul' => 'Aparatur Sipil Negara & Tenaga Kerja di lingkungan Diskominfotik',
            'pegawai' => $pegawai,
            'bidangs' => $bidangs,
        ]);
    }

    public function show($id)
    {
        $pengaturan = Pengaturan::first();
        $pegawai = Pegawai::with(['user', 'pangkat', 'statusPegawai', 'jabatanJenis', 'jabatanNama', 'bidang'])
            ->where('status', 'aktif')
            ->findOrFail($id);

        $dep = $pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '';
        $bel = $pegawai->gelar_belakang ? ', ' . $pegawai->gelar_belakang : '';
        $fullName = $dep . $pegawai->nama . $bel;

        return view('frontend.pegawai.show', [
            'title' => $fullName . ' - Profil Pegawai',
            'judul' => 'Profil Pegawai',
            'subjudul' => 'Informasi detail aparatur',
            'pegawai' => $pegawai,
            'fullName' => $fullName,
        ]);
    }
}
