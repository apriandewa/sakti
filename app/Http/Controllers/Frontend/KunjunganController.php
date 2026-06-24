<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Tamu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class KunjunganController extends Controller
{
    // ── Halaman utama (daftar tamu yang disetujui) ────────────────────────────
    public function index()
    {
        $pengaturan = \App\Models\Pengaturan::first();
        return view('frontend.page.index', [
            'title'    => $pengaturan->judul ?? 'Diskominfotik Indragiri Hulu',
            'judul'    => $pengaturan->subjudul ?? 'Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu',
            'subjudul' => $pengaturan->deskripsi ?? 'Website Resmi Diskominfotik Kabupaten Indragiri Hulu',
            'tamu'     => Tamu::where('status', 'disetujui')
                              ->orderBy('tanggal_kunjungan', 'desc')
                              ->paginate(12)
                              ->withQueryString(),
        ]);
    }

    // ── Form isian buku tamu ──────────────────────────────────────────────────
    public function create()
    {
        $pekerjaan = Kategori::whereHas('parent', fn($q) => $q->where('slug', 'pekerjaan'))
                        ->where('status', 'aktif')
                        ->orderBy('nama')
                        ->get();

        $keperluan = Kategori::whereHas('parent', fn($q) => $q->where('slug', 'keperluan'))
                        ->where('status', 'aktif')
                        ->orderBy('nama')
                        ->get();

        $pengaturan = \App\Models\Pengaturan::first();
        return view('frontend.tamu.create', [
            'title'     => $pengaturan->judul ?? 'Diskominfotik Indragiri Hulu',
            'judul'     => $pengaturan->subjudul ?? 'Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu',
            'subjudul'  => $pengaturan->deskripsi ?? 'Form Pengisian Buku Tamu Diskominfotik Kabupaten Indragiri Hulu',
            'pekerjaan' => $pekerjaan,
            'keperluan' => $keperluan,
        ]);
    }

    // ── Simpan data tamu baru ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        // ── Simpan file sementara SEBELUM validasi agar tidak hilang ──────────
        $fotoTmp    = null;
        $dokumenTmp = null;

        // Jika ada file baru di-upload, simpan ke tmp
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            $fotoTmp = $request->file('foto')->store('tmp', 'public');
            session([
                'foto_tmp'      => $fotoTmp,
                'foto_tmp_name' => $request->file('foto')->getClientOriginalName(),
                'foto_tmp_mime' => $request->file('foto')->getMimeType(),
            ]);
        } elseif ($request->filled('foto_tmp') && Storage::disk('public')->exists($request->foto_tmp)) {
            // Pakai file tmp yang sudah ada di sesi sebelumnya
            $fotoTmp = $request->foto_tmp;
            session([
                'foto_tmp'      => $fotoTmp,
                'foto_tmp_name' => session('foto_tmp_name'),
                'foto_tmp_mime' => session('foto_tmp_mime'),
            ]);
        }

        if ($request->hasFile('dokumen') && $request->file('dokumen')->isValid()) {
            $dokumenTmp = $request->file('dokumen')->store('tmp', 'public');
            session([
                'dokumen_tmp'      => $dokumenTmp,
                'dokumen_tmp_name' => $request->file('dokumen')->getClientOriginalName(),
            ]);
        } elseif ($request->filled('dokumen_tmp') && Storage::disk('public')->exists($request->dokumen_tmp)) {
            $dokumenTmp = $request->dokumen_tmp;
            session([
                'dokumen_tmp'      => $dokumenTmp,
                'dokumen_tmp_name' => session('dokumen_tmp_name'),
            ]);
        }

        // ── Validasi ──────────────────────────────────────────────────────────
        try {
            $validated = $request->validate([
                'nama'          => 'required|string|max:100',
                'email'         => 'nullable|email|max:100',
                'no_hp'         => 'nullable|string|max:20|regex:/^[0-9\-\+\s]+$/',
                'pekerjaan'     => 'nullable|string|max:150',
                'asal'          => 'nullable|string|max:150',
                'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
                'alamat'        => 'nullable|string|max:500',
                'keperluan'     => 'required|string|max:100',
                'pesan'         => 'nullable|string|max:1000',
                'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'dokumen'       => 'nullable|mimes:pdf,jpeg,png,jpg|max:5120',
                'captcha'       => ['required', 'captcha_api:' . $request->captcha_key],
                'captcha_key'   => ['required'],
            ], [
                'nama.required'          => 'Nama lengkap wajib diisi.',
                'email.email'            => 'Format email tidak valid.',
                'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
                'keperluan.required'     => 'Keperluan kunjungan wajib dipilih.',
                'foto.image'             => 'Foto harus berupa gambar.',
                'foto.mimes'             => 'Format foto harus berupa jpeg, png, atau jpg.',
                'foto.max'               => 'Ukuran foto maksimal 2MB.',
                'dokumen.mimes'          => 'Format dokumen harus berupa pdf, jpeg, png, atau jpg.',
                'dokumen.max'            => 'Ukuran dokumen maksimal 5MB.',
                'captcha.required'       => 'Captcha wajib diisi.',
                'captcha.captcha_api'    => 'Kode captcha tidak valid.',
                'captcha_key.required'   => 'Captcha key tidak ditemukan.',
            ]);
        } catch (ValidationException $e) {
            // Validasi gagal → session tmp sudah di-set di atas, redirect back
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        }

        // ── Validasi lolos: proses simpan permanen ────────────────────────────
        unset($validated['captcha'], $validated['captcha_key'], $validated['foto'], $validated['dokumen']);

        $tamu = Tamu::create([
            ...$validated,
            'status'            => 'Terkirim',
            'ip_address'        => $request->ip(),
            'tanggal_kunjungan' => now(),
        ]);

        // ── Simpan foto ───────────────────────────────────────────────────────
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            // File baru di-upload pada submit terakhir
            $foto      = $request->file('foto');
            $fotoDisk  = config('filesystems.default');
            $fotoPath  = Storage::disk($fotoDisk)->putFile('tamu/foto', $foto);
            $fotoName  = $foto->getClientOriginalName();

            // Hapus tmp yang sudah disimpan sebelumnya (duplikat)
            if ($fotoTmp && $fotoTmp !== $request->foto_tmp) {
                Storage::disk('public')->delete($fotoTmp);
            }
        } elseif ($fotoTmp && Storage::disk('public')->exists($fotoTmp)) {
            // Pindahkan dari tmp ke lokasi permanen
            $fotoDisk = 'public';
            $fotoName = session('foto_tmp_name', basename($fotoTmp));
            $fotoPath = 'tamu/foto/' . basename($fotoTmp);
            Storage::disk('public')->move($fotoTmp, $fotoPath);
        }

        if (isset($fotoPath)) {
            $tamu->file()->create([
                'data' => [
                    'name'   => $fotoName ?? basename($fotoPath),
                    'disk'   => $fotoDisk,
                    'target' => $fotoPath,
                ],
                'alias' => 'foto_tamu',
            ]);
        }

        // ── Simpan dokumen ────────────────────────────────────────────────────
        if ($request->hasFile('dokumen') && $request->file('dokumen')->isValid()) {
            $dokumen     = $request->file('dokumen');
            $dokumenDisk = config('filesystems.default');
            $dokumenPath = Storage::disk($dokumenDisk)->putFile('tamu/dokumen', $dokumen);
            $dokumenName = $dokumen->getClientOriginalName();

            if ($dokumenTmp && $dokumenTmp !== $request->dokumen_tmp) {
                Storage::disk('public')->delete($dokumenTmp);
            }
        } elseif ($dokumenTmp && Storage::disk('public')->exists($dokumenTmp)) {
            $dokumenDisk = 'public';
            $dokumenName = session('dokumen_tmp_name', basename($dokumenTmp));
            $dokumenPath = 'tamu/dokumen/' . basename($dokumenTmp);
            Storage::disk('public')->move($dokumenTmp, $dokumenPath);
        }

        if (isset($dokumenPath)) {
            $tamu->file()->create([
                'data' => [
                    'name'   => $dokumenName ?? basename($dokumenPath),
                    'disk'   => $dokumenDisk,
                    'target' => $dokumenPath,
                ],
                'alias' => 'dokumen_tamu',
            ]);
        }

        // ── Bersihkan semua session tmp ───────────────────────────────────────
        session()->forget([
            'foto_tmp', 'foto_tmp_name', 'foto_tmp_mime',
            'dokumen_tmp', 'dokumen_tmp_name',
        ]);

        return redirect()->route('kunjungan.create')
            ->with('success', 'Terima kasih sudah datang ke Diskominfotik Kabupaten Indragiri Hulu, Data Anda telah berhasil dikirim.');
    }
}