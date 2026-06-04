<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Testimoni;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UlasanController extends Controller
{
    public function create() {
       
        return view('frontend.ulasan.create', [
            "title" => "PPID Indragiri Hulu",
            "judul" => "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
            "subjudul" => "Website Resmi PPID Kabupaten Indragiri Hulu",
               
        ]);
    }

    

// ── Simpan data ulasan baru ─────────────────────────────────────────────────
    public function store(Request $request)
    {
        // ── Simpan file sementara SEBELUM validasi agar tidak hilang ──────────
        $fotoTmp    = null;

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

        // ── Validasi ──────────────────────────────────────────────────────────
        try {
            $validated = $request->validate([
                'nama'          => 'required|string|max:100',
                'desc'         => 'nullable|string|max:1000',
                'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                'keterangan'   => 'nullable|string|max:1000',   
                'captcha'       => ['required', 'captcha_api:' . $request->captcha_key],
                'captcha_key'   => ['required'],
            ], [
                'nama.required'          => 'Nama lengkap wajib diisi.',
                'foto.image'             => 'Foto harus berupa gambar.',
                'foto.mimes'             => 'Format foto harus berupa jpeg, png, atau jpg.',
                'foto.max'               => 'Ukuran foto maksimal 2MB.',
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

        $ulasan = Testimoni::create([
            ...$validated,
            'status'            => 'Terkirim',
        ]);

        // ── Simpan foto ───────────────────────────────────────────────────────
        if ($request->hasFile('foto') && $request->file('foto')->isValid()) {
            // File baru di-upload pada submit terakhir
            $foto      = $request->file('foto');
            $fotoDisk  = config('filesystems.default');
            $fotoPath  = Storage::disk($fotoDisk)->putFile('ulasan/foto', $foto);
            $fotoName  = $foto->getClientOriginalName();

            // Hapus tmp yang sudah disimpan sebelumnya (duplikat)
            if ($fotoTmp && $fotoTmp !== $request->foto_tmp) {
                Storage::disk('public')->delete($fotoTmp);
            }
        } elseif ($fotoTmp && Storage::disk('public')->exists($fotoTmp)) {
            // Pindahkan dari tmp ke lokasi permanen
            $fotoDisk = 'public';
            $fotoName = session('foto_tmp_name', basename($fotoTmp));
            $fotoPath = 'ulasan/foto/' . basename($fotoTmp);
            Storage::disk('public')->move($fotoTmp, $fotoPath);
        }

        if (isset($fotoPath)) {
            $ulasan->file()->create([
                'data' => [
                    'name'   => $fotoName ?? basename($fotoPath),
                    'disk'   => $fotoDisk,
                    'target' => $fotoPath,
                ],
                'alias' => 'gambar_testimoni',
            ]);
        }

        return redirect()->route('ulasan.create')
            ->with('success', 'Terima kasih sudah memberikan ulasan ke PPID Kabupaten Indragiri Hulu, Data Anda telah berhasil dikirim.');
    }


}
