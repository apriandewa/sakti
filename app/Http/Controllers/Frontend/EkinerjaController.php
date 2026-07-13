<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ekinerja\CariPenilaianRequest;
use App\Services\Ekinerja\EkinerjaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller publik untuk halaman pengecekan mandiri Penilaian e-Kinerja.
 * Route prefix: /sakti/kinerja
 *
 * Tidak ada query Eloquent atau panggilan HTTP di sini — semuanya
 * didelegasikan ke EkinerjaService (lihat app/Services/Ekinerja).
 */
class EkinerjaController extends Controller
{
    public function __construct(protected EkinerjaService $ekinerjaService)
    {
    }

    /** GET /sakti/kinerja */
    public function index(): View
    {
        return view('frontend.ekinerja.index');
    }

    /** GET /sakti/kinerja/periode — sumber data AJAX Select2 */
    public function periode(Request $request): JsonResponse
    {
        $results = $this->ekinerjaService->getPeriodeOptions($request->query('q'));

        return response()->json(['results' => $results]);
    }

    /** POST /sakti/kinerja/cari */
    public function cari(CariPenilaianRequest $request): JsonResponse
    {
        // TODO(backend): validasi nilai captcha terlebih dahulu di sini
        // menggunakan helper/facade resmi package "meaws captcha" sebelum
        // melanjutkan ke pencarian data, contoh:
        // if (! Meaws::validate($request->input('captcha'))) { ... }

        $result = $this->ekinerjaService->cariPenilaian(
            periodeId: $request->validated('periode_id'),
            nip: $request->validated('nip'),
            namaInput: $request->validated('nama'),
            ipAddress: $request->ip(),
            userAgent: $request->userAgent(),
        );

        return response()->json($result);
    }
}
