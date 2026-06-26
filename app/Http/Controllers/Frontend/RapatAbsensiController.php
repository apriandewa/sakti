<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\AgendaRapat;
use App\Models\RapatPeserta;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RapatAbsensiController extends Controller
{
    /**
     * Override constructor karena ini controller frontend tanpa menu
     */
    public function __construct()
    {
        // Tidak memanggil parent::__construct() karena ini publik
    }

    // =====================================================
    // SHOW - Form Daftar Hadir Online
    // =====================================================
    public function show($token)
    {
        $agenda = AgendaRapat::where('barcode_token', $token)->firstOrFail();

        // Cek apakah rapat sudah diterima
        if ($agenda->status !== 'DITERIMA') {
            return view('frontend.rapat.absensi', [
                'agenda'  => $agenda,
                'allowed' => false,
                'message' => 'Daftar hadir tidak tersedia karena agenda rapat belum diterima/disetujui.',
            ]);
        }

        // Cek apakah waktu saat ini dalam rentang rapat
        $now = Carbon::now();
        $rapatMulai = Carbon::parse($agenda->tanggal->format('Y-m-d') . ' ' . $agenda->jam_mulai);
        $rapatSelesai = Carbon::parse($agenda->tanggal->format('Y-m-d') . ' ' . $agenda->jam_selesai);

        // Toleransi 30 menit sebelum dan sesudah
        $mulaiToleransi = $rapatMulai->copy()->subMinutes(30);
        $selesaiToleransi = $rapatSelesai->copy()->addMinutes(30);

        if ($now->lt($mulaiToleransi)) {
            return view('frontend.rapat.absensi', [
                'agenda'  => $agenda,
                'allowed' => false,
                'message' => 'Daftar hadir belum dibuka. Rapat dijadwalkan pada ' . $agenda->tanggal->format('d/m/Y') . ' pukul ' . substr($agenda->jam_mulai, 0, 5) . ' WIB. Anda bisa mengisi daftar hadir 30 menit sebelum rapat dimulai.',
            ]);
        }

        if ($now->gt($selesaiToleransi)) {
            return view('frontend.rapat.absensi', [
                'agenda'  => $agenda,
                'allowed' => false,
                'message' => 'Daftar hadir sudah ditutup. Rapat telah berakhir pada ' . $agenda->tanggal->format('d/m/Y') . ' pukul ' . substr($agenda->jam_selesai, 0, 5) . ' WIB.',
            ]);
        }

        // Ambil peserta yang sudah hadir
        $pesertaHadir = $agenda->peserta()->get();

        return view('frontend.rapat.absensi', [
            'agenda'       => $agenda,
            'allowed'      => true,
            'pesertaHadir' => $pesertaHadir,
            'message'      => null,
        ]);
    }

    // =====================================================
    // STORE - Simpan Kehadiran
    // =====================================================
    public function store(Request $request, $token)
    {
        $agenda = AgendaRapat::where('barcode_token', $token)->firstOrFail();

        // Re-validasi waktu
        $now = Carbon::now();
        $rapatMulai = Carbon::parse($agenda->tanggal->format('Y-m-d') . ' ' . $agenda->jam_mulai);
        $rapatSelesai = Carbon::parse($agenda->tanggal->format('Y-m-d') . ' ' . $agenda->jam_selesai);
        $mulaiToleransi = $rapatMulai->copy()->subMinutes(30);
        $selesaiToleransi = $rapatSelesai->copy()->addMinutes(30);

        if ($now->lt($mulaiToleransi) || $now->gt($selesaiToleransi)) {
            return response()->json([
                'status'  => false,
                'message' => 'Daftar hadir tidak bisa digunakan diluar waktu rapat.',
            ], 403);
        }

        $request->validate([
            'nama'           => 'required|string|max:255',
            'nip'            => 'nullable|string|max:50',
            'jabatan'        => 'nullable|string|max:255',
            'instansi'       => 'nullable|string|max:255',
            'no_hp'          => 'nullable|string|max:20',
            'tanda_tangan'   => 'required|string',
        ]);

        $nipVal = $request->nip ?: null;
        $existing = RapatPeserta::where('agenda_rapat_id', $agenda->id)
            ->where('nama', $request->nama)
            ->where(function ($q) use ($nipVal) {
                if (is_null($nipVal)) {
                    $q->whereNull('nip')->orWhere('nip', '');
                } else {
                    $q->where('nip', $nipVal);
                }
            })
            ->exists();

        if ($existing) {
            return response()->json([
                'status'  => false,
                'message' => 'Mohon maaf tidak bisa rekam kehadiran karena anda sudah mengisi kehadiran sebelumnya.',
            ]);
        }

        $peserta = RapatPeserta::create([
            'agenda_rapat_id' => $agenda->id,
            'nama'            => $request->nama,
            'nip'             => $request->nip,
            'jabatan'         => $request->jabatan,
            'instansi'        => $request->instansi,
            'no_hp'           => $request->no_hp,
            'tanda_tangan'    => $request->tanda_tangan,
            'waktu_hadir'     => now(),
        ]);

        $materi = $agenda->getfilesbyalias('rapat_materi');
        $materiData = $materi->map(function ($file) {
            return [
                'name' => $file->name,
                'link_stream' => route('file.stream', ['id' => $file->id, 'name' => $file->name_alias]),
                'link_download' => route('file.download', ['id' => $file->id, 'name' => $file->name_alias]),
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Kehadiran berhasil dicatat. Terima kasih, ' . $request->nama . '!',
            'materi'  => $materiData,
        ]);
    }
}
