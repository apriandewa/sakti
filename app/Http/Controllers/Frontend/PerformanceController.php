<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\PerformanceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PerformanceController — Frontend Publik E-Performance (Kinerja 70% + Presensi 30%)
 *
 * Route: GET  /performance        -> performance.index
 *        POST /performance/cari   -> performance.cari
 */
class PerformanceController extends Controller
{
    public function __construct(protected PerformanceService $service)
    {
    }

    /**
     * GET /performance — Halaman form pencarian e-Performance
     */
    public function index(): View
    {
        $bulanList = collect(range(1, 12))->mapWithKeys(fn ($b) => [
            $b => Carbon::create()->month($b)->translatedFormat('F'),
        ]);

        $tahunList = collect(range(now()->year, 2023))->mapWithKeys(fn ($y) => [$y => $y]);

        return view('frontend.performance.index', compact('bulanList', 'tahunList'));
    }

    /**
     * POST /performance/cari — Submit pencarian data performance
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

        $result = $this->service->calculatePerformance(
            bulan: (int) $request->bulan,
            tahun: (int) $request->tahun,
            nama:  $request->nama,
            nip:   $request->nip
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'],
            ], 404);
        }

        return response()->json($result);
    }
}
