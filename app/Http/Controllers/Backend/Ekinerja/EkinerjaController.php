<?php

namespace App\Http\Controllers\Backend\Ekinerja;

use App\Http\Controllers\Controller;
use App\Models\Ekinerja\EkinerjaMasterUnor;
use App\Services\Ekinerja\EkinerjaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Controller backend "Rekap e-Kinerja" (PRD Bab 7.2 & 10).
 * Mengikuti konvensi mvc-route project (identik dengan Presensi\PresensiController).
 *
 * Route (routes/backend.php — dalam group prefix 'ekinerja', as 'ekinerja.'):
 *   Route::get('data', 'Ekinerja\EkinerjaController@data')->name('data');
 *   Route::get('logs-data', 'Ekinerja\EkinerjaController@logsData')->name('logs-data');
 *   Route::get('unor', 'Ekinerja\EkinerjaController@unor')->name('unor');
 *   Route::get('periode', 'Ekinerja\EkinerjaController@periode')->name('periode');
 *   Route::get('{id}/show', 'Ekinerja\EkinerjaController@show')->name('show');
 *   Route::post('sync', 'Ekinerja\EkinerjaController@sync')->name('sync');
 *   Route::resource('ekinerja', ...)->only(['index']);
 *
 * Tidak ada query Eloquent / panggilan HTTP langsung di controller ini —
 * semua didelegasikan ke EkinerjaService (app/Services/Ekinerja).
 */
class EkinerjaController extends Controller
{
    public function __construct(
        protected EkinerjaService $service,
        \App\support\Helper $helper
    ) {
        parent::__construct($helper);
    }

    /* ============================================================
     *  INDEX — Halaman utama (2 Tab: Data + Log)
     * ============================================================ */

    public function index(): View
    {
        return view($this->view . '.index');
    }

    /* ============================================================
     *  DATATABLE TAB 1 — AJAX JSON rekap penilaian (Yajra)
     * ============================================================ */

    public function data(Request $request): JsonResponse
    {
        $unorId   = $request->input('unor_id');
        $periodeId = $request->input('periode_id');

        if (! $unorId || ! $periodeId) {
            return response()->json(['data' => []]);
        }

        $query = $this->service->rekapQuery($unorId, $periodeId);

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('nama_nip', function ($row) {
                $nama = htmlspecialchars($row->nama ?? '-');
                $nip  = htmlspecialchars($row->nip ?? '-');
                return "<strong>{$nama}</strong><br><small class=\"text-muted\">NIP. {$nip}</small>";
            })
            ->addColumn('unor_nama', function ($row) {
                return htmlspecialchars($row->skp_unor ?? '-');
            })
            ->addColumn('periode_skp', function ($row) {
                return optional($row->periode)->label ?? '-';
            })
            ->addColumn('hasil_kerja_badge', fn ($row) => $this->badgeHtml($row->hasil_kerja))
            ->addColumn('perilaku_kerja_badge', fn ($row) => $this->badgeHtml($row->perilaku_kerja))
            ->addColumn('hasil_akhir_badge', fn ($row) => $this->badgeHtml($row->hasil_akhir))
            ->addColumn('action', function ($row) {
                $detailUrl = route('kinerja.show', $row->id);
                return "
                    <button type='button' class='btn btn-xs btn-info btn-action'
                        data-title='Detail Penilaian e-Kinerja' data-url='{$detailUrl}'>
                        <i class='fa fa-eye'></i> Detail
                    </button>
                ";
            })
            ->rawColumns(['nama_nip', 'hasil_kerja_badge', 'perilaku_kerja_badge', 'hasil_akhir_badge', 'action'])
            ->make(true);
    }

    /* ============================================================
     *  DATATABLE TAB 2 — AJAX JSON log sinkronisasi (Yajra)
     * ============================================================ */

    public function logsData(Request $request): JsonResponse
    {
        $query = $this->service->getLogsQuery(
            unorId:   $request->input('unor_id'),
            periodeId: $request->input('periode_id'),
        );

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('status_badge', fn ($row) => $row->status_badge)
            ->addColumn('waktu_mulai_fmt', fn ($row) => $row->waktu_mulai?->format('d/m/Y H:i:s') ?? '-')
            ->addColumn('waktu_selesai_fmt', fn ($row) => $row->waktu_selesai?->format('d/m/Y H:i:s') ?? '-')
            ->addColumn('durasi_fmt', function ($row) {
                $d = $row->durasi;
                return $d !== null ? "{$d} detik" : '-';
            })
            ->addColumn('jumlah_fmt', function ($row) {
                $ok   = $row->jumlah_data_ditarik ?? 0;
                $fail = $row->jumlah_gagal ?? 0;
                return "<span class=\"text-success\">{$ok} berhasil</span> / <span class=\"text-danger\">{$fail} gagal</span>";
            })
            ->rawColumns(['status_badge', 'jumlah_fmt'])
            ->make(true);
    }

    /* ============================================================
     *  UNOR — Sumber data AJAX Select2 (kantor/Unor aktif)
     * ============================================================ */

    public function unor(Request $request): JsonResponse
    {
        $results = EkinerjaMasterUnor::query()
            ->active()
            ->when($request->input('q'), fn ($q, $term) => $q->where('nama_unor', 'like', "%{$term}%"))
            ->orderBy('nama_unor')
            ->limit(20)
            ->get()
            ->map(fn (EkinerjaMasterUnor $u) => ['id' => $u->unor_id, 'text' => $u->nama_unor])
            ->values();

        return response()->json(['results' => $results]);
    }

    /* ============================================================
     *  PERIODE — Sumber data AJAX Select2 (khusus backend)
     * ============================================================ */

    public function periode(Request $request): JsonResponse
    {
        $results = $this->service->getPeriodeOptions($request->input('q'));

        return response()->json(['results' => $results]);
    }

    /* ============================================================
     *  SHOW — Modal detail satu penilaian pegawai (PRD Bab 10.2)
     * ============================================================ */

    public function show(string $id): View
    {
        $data = $this->service->findPenilaian($id);

        abort_if(! $data, 404, 'Data penilaian tidak ditemukan.');

        return view($this->view . '.show', compact('data'));
    }

    /* ============================================================
     *  SYNC — Sinkronisasi manual dari API BKN (PRD Bab 7.2)
     * ============================================================ */

    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'unor_id'    => 'required|string',
            'periode_id' => 'required|string',
        ]);

        $triggeredBy = auth()->check()
            ? (auth()->user()->name ?? auth()->user()->email ?? 'Admin')
            : 'Admin';

        $result = $this->service->syncPenilaianByUnor(
            unorId:      $request->unor_id,
            periodeId:   $request->periode_id,
            triggeredBy: $triggeredBy,
        );

        return response()->json([
            'status'  => $result['status'] === 'sukses',
            'message' => $result['message'],
            'result'  => $result,
        ]);
    }

    /* ============================================================
     *  Helper badge warna (PRD Bab 11 — pewarnaan informatif)
     * ============================================================ */

    private function badgeHtml(?string $value): string
    {
        $label = $value ? strtoupper($value) : '-';
        $v = strtolower((string) $value);

        $color = match (true) {
            str_contains($v, 'diatas') || str_contains($v, 'di atas') || str_contains($v, 'sangat baik') => 'success',
            str_contains($v, 'sesuai') || str_contains($v, 'baik') => 'primary',
            str_contains($v, 'cukup') => 'warning',
            str_contains($v, 'bawah') || str_contains($v, 'kurang') => 'danger',
            default => 'secondary',
        };

        return "<span class=\"badge badge-{$color}\">{$label}</span>";
    }
}