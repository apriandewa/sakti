<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ekinerja\CariPenilaianRequest;
use App\Models\Ekinerja\EkinerjaReferensiPeriode;
use App\Services\Ekinerja\BknApiException;
use App\Services\Ekinerja\EkinerjaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * EkinerjaController — Frontend Publik Pencarian e-Kinerja (PRD Bab 7)
 *
 * Route (routes/web.php):
 *   Route::prefix('kinerja')->name('ekinerja.')->group(function () {
 *       Route::get('/', [EkinerjaController::class, 'index'])->name('index');
 *       Route::get('/periode', [EkinerjaController::class, 'periode'])->name('periode');
 *       Route::post('/cari', [EkinerjaController::class, 'cari'])
 *           ->middleware('throttle:10,1')->name('cari');
 *   });
 *
 * Tidak ada query Eloquent langsung di sini — semua delegasi ke EkinerjaService.
 */
class EkinerjaController extends Controller
{
    public function __construct(protected EkinerjaService $service)
    {
    }

    /* ============================================================
     *  INDEX — Halaman utama pencarian publik
     * ============================================================ */

    public function index(): View
    {
        return view('frontend.ekinerja.index');
    }

    /* ============================================================
     *  PERIODE — Daftar periode untuk Select2 (publik)
     * ============================================================ */

    public function periode(Request $request): JsonResponse
    {
        $results = $this->service->getPeriodeOptions($request->query('q'));

        return response()->json(['results' => $results]);
    }

    /* ============================================================
     *  CARI — Submit form pencarian (POST + throttle)
     * ============================================================ */

    public function cari(CariPenilaianRequest $request): JsonResponse
    {
        $result = $this->service->cariPenilaian(
            periodeId: $request->validated('periode_id'),
            nip:       $request->validated('nip'),
            namaInput: $request->validated('nama'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        if (! $result['success']) {
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'Data tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success'     => true,
            'data'        => $result['data'],
            'nama_cocok'  => $result['nama_cocok'],
        ]);
    }
}