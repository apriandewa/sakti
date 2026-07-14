<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PresensiHarian;
use App\Services\PresensiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * CekKehadiranController — Frontend Publik Cek Kehadiran Pegawai
 *
 * Route: GET  /cekkehadiran
 *        POST /cekkehadiran/cari
 */
class CekKehadiranController extends Controller
{
    public function __construct(protected PresensiService $service)
    {
    }

    /**
     * GET /cekkehadiran — Halaman form pencarian
     */
    public function index(): View
    {
        $bulanList = collect(range(1, 12))->mapWithKeys(fn($b) => [
            $b => \Carbon\Carbon::create()->month($b)->translatedFormat('F')
        ]);

        $tahunList = collect(range(now()->year, 2023))->mapWithKeys(fn($y) => [$y => $y]);

        return view('frontend.cekkehadiran.index', compact('bulanList', 'tahunList'));
    }

    /**
     * POST /cekkehadiran/cari — Pencarian data kehadiran
     */
    public function cari(Request $request): JsonResponse
    {
        $request->validate([
            'bulan'   => 'required|integer|min:1|max:12',
            'tahun'   => 'required|integer|min:2020|max:2030',
            'nama'    => 'required|string|min:2',
            'nip'     => 'required|string|min:6',
            'captcha' => 'required|captcha',
        ], [
            'captcha.captcha' => 'Kode keamanan tidak sesuai. Silakan coba lagi.',
            'nama.required'   => 'Nama pegawai wajib diisi.',
            'nip.required'    => 'NIP pegawai wajib diisi.',
            'bulan.required'  => 'Pilih bulan terlebih dahulu.',
            'tahun.required'  => 'Pilih tahun terlebih dahulu.',
        ]);

        $bulan = (int) $request->bulan;
        $tahun = (int) $request->tahun;
        $nip   = trim($request->nip);
        $nama  = trim($request->nama);

        // Cari pegawai berdasarkan NIP
        $pegawai = Pegawai::where('nip', $nip)->first();

        if (! $pegawai) {
            return response()->json([
                'success' => false,
                'message' => 'Nama dan NIP tidak sesuai dengan data yang terdaftar.',
            ]);
        }

        // Validasi kesesuaian nama (case-insensitive, partial match)
        $namaDb    = strtolower(preg_replace('/\s+/', ' ', $pegawai->nama));
        $namaInput = strtolower(preg_replace('/\s+/', ' ', $nama));

        // Cek kemiripan nama: minimal nama input terkandung dalam nama DB atau sebaliknya
        $namaCocok = str_contains($namaDb, $namaInput) || str_contains($namaInput, $namaDb)
            || similar_text($namaDb, $namaInput, $pct) && $pct >= 60;

        if (! $namaCocok) {
            return response()->json([
                'success' => false,
                'message' => 'Nama dan NIP tidak sesuai. Pastikan nama yang Anda masukkan sesuai dengan data kepegawaian.',
            ]);
        }

        // Batasi bulan berjalan sampai HARI KEMARIN saja - presensi hari ini
        // biasanya belum lengkap (belum checkout / belum diproses BKN), jadi
        // jika ikut dihitung bisa membuat total potongan tampak lebih besar dari seharusnya.
        // Sama seperti pembatasan di PresensiController (backend).
        $today = \Carbon\Carbon::today();
        $isCurrentMonth = ($bulan == $today->month && $tahun == $today->year);
        $dayLimit = $isCurrentMonth ? ($today->day - 1) : null;

        // Ambil data presensi bulanan
        $presensiQuery = PresensiHarian::where('pegawai_id', $pegawai->id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($dayLimit !== null) {
            $presensiQuery->whereDay('tanggal', '<=', $dayLimit);
        }

        $presensiList = $presensiQuery->orderBy('tanggal')->get();

        if ($presensiList->isEmpty()) {
            $pesanKosong = ($dayLimit !== null && $dayLimit < 1)
                ? 'Data kehadiran bulan ini belum tersedia karena belum ada hari yang lewat untuk disinkronkan.'
                : 'Data kehadiran belum tersedia untuk periode tersebut. Pastikan data sudah disinkronisasi oleh administrator.';

            return response()->json([
                'success' => false,
                'message' => $pesanKosong,
            ]);
        }

        // Hitung rekap (dengan dayLimit yang sama supaya konsisten dengan data harian di atas)
        $pegawaiWithPresensi = $pegawai->load(['presensiHarians' => function ($q) use ($bulan, $tahun, $dayLimit) {
            $q->whereMonth('tanggal', $bulan)->whereYear('tanggal', $tahun);
            if ($dayLimit !== null) {
                $q->whereDay('tanggal', '<=', $dayLimit);
            }
        }]);

        $rekap = $this->service->hitungRekapPegawai(
            $pegawaiWithPresensi,
            $this->service->countHariKerja($bulan, $tahun, $dayLimit),
            $bulan,
            $tahun,
            $dayLimit
        );

        // Format data harian untuk tampilan
        $harian = $presensiList->map(function ($p) {
            return [
                'tanggal'           => \Carbon\Carbon::parse($p->tanggal)->translatedFormat('l, d F Y'),
                'tanggal_raw'       => $p->tanggal->toDateString(),
                'status'            => $p->status_kehadiran,
                'label_status'      => $p->label_status,
                'badge_class'       => $p->badge_class,
                'jam_masuk'         => $p->jam_masuk  ?? '-',
                'jam_keluar'        => $p->jam_keluar ?? '-',
                'kategori_terlambat'    => $p->kategori_terlambat,
                'menit_terlambat'       => $p->menit_terlambat,
                'kategori_pulang_cepat' => $p->kategori_pulang_cepat,
                'menit_pulang_cepat'    => $p->menit_pulang_cepat,
                'total_potongan'    => (float) $p->total_potongan,
                'keterangan'        => $p->keterangan,
            ];
        })->values()->toArray();

        return response()->json([
            'success' => true,
            'pegawai' => [
                'nama'        => $pegawai->nama_lengkap,
                'nip'         => $pegawai->nip,
                'nama_kantor' => $pegawai->nama_kantor,
            ],
            'rekap'   => $rekap,
            'harian'  => $harian,
            'periode' => [
                'bulan'      => $bulan,
                'tahun'      => $tahun,
                'nama_bulan' => \Carbon\Carbon::create($tahun, $bulan)->translatedFormat('F Y'),
            ],
        ]);
    }
}