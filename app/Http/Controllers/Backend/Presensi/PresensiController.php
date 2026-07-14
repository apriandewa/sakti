<?php

namespace App\Http\Controllers\Backend\Presensi;

use App\Http\Controllers\Controller;
use App\Models\Pegawai;
use App\Models\PresensiHarian;
use App\Models\PresensiSyncLog;
use App\Services\PresensiService;
use App\Services\SimpegnasService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * PresensiController — Backend Rekap Presensi Pegawai
 *
 * Route prefix: /admin/presensi
 */
class PresensiController extends Controller
{
    public function __construct(
        protected PresensiService $service,
        protected SimpegnasService $simpegnasService,
        \App\support\Helper $helper
    ) {
        parent::__construct($helper);
    }

    /* ============================================================
     *  INDEX — Halaman utama rekapitulasi
     * ============================================================ */

    public function index(): View
    {
        $bulanList = collect(range(1, 12))->mapWithKeys(fn($b) => [
            $b => \Carbon\Carbon::create()->month($b)->translatedFormat('F')
        ]);

        $tahunList = collect(range(now()->year, 2023))->mapWithKeys(fn($y) => [$y => $y]);

        return view($this->view . '.index', compact('bulanList', 'tahunList'));
    }

    /* ============================================================
     *  DATATABLE — AJAX JSON rekap pegawai
     * ============================================================ */

    public function data(Request $request): JsonResponse
    {
        $kantorId = $request->input('kantor_id');
        $bulan    = (int) $request->input('bulan', now()->month);
        $tahun    = (int) $request->input('tahun', now()->year);

        if (! $kantorId) {
            return response()->json(['data' => []]);
        }

        $rekap = $this->service->getRekap($kantorId, $bulan, $tahun);

        return datatables()->of($rekap)
            ->addIndexColumn()
            ->addColumn('nama_nip', function ($row) {
                $nama = htmlspecialchars($row['nama'] ?? '-');
                $nip  = htmlspecialchars($row['nip'] ?? '-');
                return "<strong>{$nama}</strong><br><small class=\"text-muted\">NIP. {$nip}</small>";
            })
            ->addColumn('total_potongan_fmt', function ($row) {
                $val = number_format((float)($row['total_potongan'] ?? 0), 2, ',', '.');
                $color = ($row['total_potongan'] ?? 0) >= 10 ? 'danger' : (($row['total_potongan'] ?? 0) >= 5 ? 'warning' : 'success');
                return "<span class=\"badge badge-{$color}\">{$val}%</span>";
            })
            ->addColumn('action', function ($row) use ($bulan, $tahun) {
                $id = $row['id'] ?? '';
                $detailUrl = 'presensi/' . $id . '/show?bulan=' . $bulan . '&tahun=' . $tahun;

                return "
                    <button type='button' class='btn btn-xs btn-info btn-action'
                        data-title='Detail Log Harian' data-size='modal-xl' data-url='{$detailUrl}'>
                        <i class='fa fa-eye'></i> Detail
                    </button>
                    <button type='button' class='btn btn-xs btn-warning btn-sync-pegawai ms-1'
                        data-id='{$id}' title='Sinkronisasi Data Pegawai Ini'>
                        <i class='fa fa-sync-alt'></i>
                    </button>
                ";
            })
            ->rawColumns(['nama_nip', 'total_potongan_fmt', 'action'])
            ->make(true);
    }

    public function logsData(Request $request): JsonResponse
    {
        $logs = PresensiSyncLog::orderBy('waktu_mulai', 'desc')->get();
        return datatables()->of($logs)
            ->addIndexColumn()
            ->addColumn('kantor', function ($row) {
                if($row->kantor_id) {
                     $pegawai = Pegawai::where('kantor_id', $row->kantor_id)->first();
                     return $pegawai ? $pegawai->nama_kantor : $row->kantor_id;
                }
                return 'Semua OPD';
            })
            ->addColumn('waktu_mulai_fmt', function ($row) {
                return $row->waktu_mulai ? $row->waktu_mulai->format('d/m/Y H:i:s') : '-';
            })
            ->addColumn('waktu_selesai_fmt', function ($row) {
                return $row->waktu_selesai ? $row->waktu_selesai->format('d/m/Y H:i:s') : '-';
            })
            ->addColumn('status_badge', function ($row) {
                $color = $row->status == 'sukses' ? 'success' : ($row->status == 'gagal' ? 'danger' : 'warning');
                return "<span class=\"badge badge-{$color}\">" . ucfirst($row->status) . "</span>";
            })
            ->rawColumns(['status_badge'])
            ->make(true);
    }

    /* ============================================================
     *  SHOW — Modal detail log harian & statistik satu pegawai
     * ============================================================ */

    public function show(Request $request, string $id): View
    {
        $bulan = (int) $request->input('bulan', now()->month);
        $tahun = (int) $request->input('tahun', now()->year);

        $pegawai = Pegawai::findOrFail($id);

        // Batasi bulan berjalan sampai HARI KEMARIN saja - presensi hari ini
        // biasanya belum lengkap (belum checkout / belum diproses BKN), jadi
        // jika ikut dihitung bisa membuat total potongan tampak lebih besar dari seharusnya.
        $today = \Carbon\Carbon::today();
        $isCurrentMonth = ($bulan == $today->month && $tahun == $today->year);
        $dayLimit = $isCurrentMonth ? ($today->day - 1) : null;

        $logsQuery = PresensiHarian::where('pegawai_id', $id)
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)'); // exclude Sabtu-Minggu

        if ($dayLimit !== null) {
            $logsQuery->whereDay('tanggal', '<=', $dayLimit);
        }

        $logs = $logsQuery->orderBy('tanggal')->get();

        $monthName = \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F');

        // --- Hitung statistik dasar ---
        $hadirStatuses = ['HN', 'TM1', 'TM2', 'TM3', 'TM4', 'TMM', 'PC1', 'PC2', 'PC3', 'PC4', 'PCM'];

        $countHn = $logs->whereIn('status_kehadiran', $hadirStatuses)->count();
        $countTk = $logs->where('status_kehadiran', 'TK')->count();
        $countCt = $logs->where('status_kehadiran', 'CT')->count();
        $countDl = $logs->where('status_kehadiran', 'DL')->count();
        $countIzin = $logs->where('status_kehadiran', 'IZIN')->count();
        $totalHariKerja = $logs->count();
        $countTm = $logs->whereNotNull('kategori_terlambat')->count();
        $countPc = $logs->whereNotNull('kategori_pulang_cepat')->count();
        $totalPotongan = $logs->sum('total_potongan');

        // Hari hadir yang benar-benar tepat waktu (tanpa TM/PC)
        $daysBermasalah = $logs->whereIn('status_kehadiran', $hadirStatuses)
            ->filter(fn($log) => $log->kategori_terlambat || $log->kategori_pulang_cepat)
            ->count();
        $daysTepatWaktu = $countHn - $daysBermasalah;

        $kehadiranEfektif = $daysTepatWaktu + $countCt + $countDl + $countIzin;
        $persentaseEfektif = $totalHariKerja > 0 ? round(($kehadiranEfektif / $totalHariKerja) * 100) : 0;

        // --- Persentase & offset untuk donut chart ---
        $totalGraphic = $daysTepatWaktu + $countTm + $countPc + $countTk + $countDl + $countCt + $countIzin;

        $pctHn   = $totalGraphic > 0 ? ($daysTepatWaktu / $totalGraphic) * 100 : 0;
        $pctTm   = $totalGraphic > 0 ? ($countTm / $totalGraphic) * 100 : 0;
        $pctPc   = $totalGraphic > 0 ? ($countPc / $totalGraphic) * 100 : 0;
        $pctTk   = $totalGraphic > 0 ? ($countTk / $totalGraphic) * 100 : 0;
        $pctDl   = $totalGraphic > 0 ? ($countDl / $totalGraphic) * 100 : 0;
        $pctCt   = $totalGraphic > 0 ? ($countCt / $totalGraphic) * 100 : 0;
        $pctIzin = $totalGraphic > 0 ? ($countIzin / $totalGraphic) * 100 : 0;

        $offsetHn   = 0;
        $offsetTm   = -($pctHn);
        $offsetPc   = -($pctHn + $pctTm);
        $offsetTk   = -($pctHn + $pctTm + $pctPc);
        $offsetDl   = -($pctHn + $pctTm + $pctPc + $pctTk);
        $offsetCt   = -($pctHn + $pctTm + $pctPc + $pctTk + $pctDl);
        $offsetIzin = -($pctHn + $pctTm + $pctPc + $pctTk + $pctDl + $pctCt);

        return view($this->view . '.show', compact(
            'pegawai', 'logs', 'monthName', 'bulan',
            'countHn', 'countTk', 'countCt', 'countDl', 'countIzin',
            'totalHariKerja', 'countTm', 'countPc', 'totalPotongan',
            'daysTepatWaktu', 'kehadiranEfektif', 'persentaseEfektif',
            'pctHn', 'pctTm', 'pctPc', 'pctTk', 'pctDl', 'pctCt', 'pctIzin',
            'offsetHn', 'offsetTm', 'offsetPc', 'offsetTk', 'offsetDl', 'offsetCt', 'offsetIzin'
        ))->with('year', $tahun);
    }

    /**
     * Proxy foto profil pegawai dari API Simpegnas BKN (mencegah CORS/token leak).
     *
     * Parsing dibuat toleran terhadap beberapa kemungkinan bentuk respons API,
     * karena struktur pastinya belum terkonfirmasi 100% - kalau semua percobaan
     * gagal, responsnya di-log supaya mudah dicek strukturnya dari log Laravel.
     */
    public function image(string $nip)
    {
        $response = $this->simpegnasService->getEmployeeImage($nip);

        if ($response && $response->successful()) {
            $data = $response->json();
            $base64 = null;

            // Percobaan 1: base64 langsung di 'data' (string)
            if (is_string($data['data'] ?? null) && $data['data'] !== '') {
                $base64 = $data['data'];
            }
            // Percobaan 2: 'data.image' atau 'data.image_base64' (string langsung)
            elseif (! empty($data['data']['image_base64'] ?? null)) {
                $base64 = $data['data']['image_base64'];
            } elseif (! empty($data['data']['image'] ?? null)) {
                $base64 = $data['data']['image'];
            }
            // Percobaan 3: nested 'data.register[]' - ambil foto terbaru
            elseif (
                isset($data['data']['register']) && is_array($data['data']['register'])
                && count($data['data']['register']) > 0
            ) {
                $latestPhoto = end($data['data']['register']);
                $base64 = $latestPhoto['image_base64'] ?? null;
            }

            if (! empty($base64)) {
                if (strpos($base64, 'base64,') !== false) {
                    $base64 = explode('base64,', $base64)[1];
                }

                $decoded = base64_decode($base64, true);

                if ($decoded !== false) {
                    return response($decoded, 200)->header('Content-Type', 'image/jpeg');
                }
            }

            \Illuminate\Support\Facades\Log::warning(
                "Gagal parse foto profil NIP {$nip} - struktur respons tidak dikenali",
                ['response' => $data]
            );
        }

        return redirect(asset(config('master.app.web.template') . '/images/avatar/avatar-1.png'));
    }

    /* ============================================================
     *  SYNC — Sinkronisasi dari BKN API
     *  (memakai SimpegnasService - satu-satunya sumber logika sync,
     *   dipakai bersama dengan cron di Kernel.php)
     * ============================================================ */

    /**
     * Sync seluruh pegawai di satu kantor (dipilih manual oleh admin).
     * autoCreatePegawai: true - karena admin secara sadar memilih kantor ini,
     * pegawai yang belum terdaftar lokal akan otomatis dibuat.
     */
    public function sync(Request $request): JsonResponse
    {
        $request->validate([
            'kantor_id' => 'required|string',
            'bulan'     => 'required|integer|min:1|max:12',
            'tahun'     => 'required|integer|min:2020|max:2030',
        ]);

        $syncBy = auth()->user() ? (string) auth()->user()->id : 'System';

        $result = $this->simpegnasService->syncAttendance(
            month: $request->bulan,
            year: $request->tahun,
            kantorId: $request->kantor_id,
            triggeredBy: $syncBy,
            autoCreatePegawai: true,
        );

        return response()->json([
            'status'  => true,
            'message' => "Sinkronisasi selesai. Pegawai disync: {$result['pegawai_disync']}, "
                . "pegawai baru: {$result['pegawai_baru']}, dilewati: {$result['pegawai_dilewati']}.",
            'result'  => $result,
        ]);
    }

    /**
     * Sync satu pegawai berdasarkan ID lokal (dipanggil dari tombol "Sync Pegawai" per baris).
     * autoCreatePegawai selalu false di sini - pegawai wajib sudah ada lokal untuk punya ID yang diklik.
     */
    public function syncPegawai(Request $request): JsonResponse
    {
        $request->validate([
            'pegawai_id' => 'required|string',
            'bulan'      => 'required|integer|min:1|max:12',
            'tahun'      => 'required|integer|min:2020|max:2030',
        ]);

        $syncBy = auth()->user() ? (string) auth()->user()->id : 'System';

        $result = $this->simpegnasService->syncAttendance(
            month: $request->bulan,
            year: $request->tahun,
            pegawaiId: $request->pegawai_id,
            triggeredBy: $syncBy,
        );

        return response()->json([
            'status'  => true,
            'message' => "Sync pegawai selesai.",
            'result'  => $result,
        ]);
    }

    /* ============================================================
     *  KANTOR — Daftar kantor untuk Select2
     * ============================================================ */

    public function kantor(Request $request): JsonResponse
    {
        $daftar = $this->service->getDaftarKantor();

        // Filter by search term (Select2 AJAX)
        $term = strtolower(trim($request->input('q', '')));
        if ($term) {
            $daftar = array_values(array_filter($daftar, fn($k) => str_contains(strtolower($k['text']), $term)));
        }

        return response()->json(['results' => $daftar]);
    }

    /* ============================================================
     *  FOTO — Ambil foto presensi (Base64)
     * ============================================================ */

    public function fotoPresensi(Request $request): JsonResponse
    {
        $request->validate([
            'nip'     => 'required|string',
            'tanggal' => 'required|date',
            'jenis'   => 'nullable|in:in,out',
        ]);

        $foto = $this->service->getFotoPresensi(
            $request->nip,
            $request->tanggal,
            $request->input('jenis', 'in')
        );

        if ($foto) {
            return response()->json(['status' => true, 'base64' => $foto]);
        }

        return response()->json(['status' => false, 'message' => 'Foto tidak tersedia.'], 404);
    }

    /* ============================================================
     *  IMPORT CSV PEGAWAI
     * ============================================================ */

    public function importCsv(Request $request): JsonResponse
    {
        $request->validate([
            'file'      => 'required|file|mimes:csv,txt|max:2048',
            'kantor_id' => 'required|string',
        ]);

        $path = $request->file('file')->getRealPath();
        $rows = [];

        if (($handle = fopen($path, 'r')) !== false) {
            $header = null;
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                if (! $header) {
                    $header = array_map('strtolower', array_map('trim', $data));
                    continue;
                }
                $rows[] = array_combine($header, $data);
            }
            fclose($handle);
        }

        $result = $this->service->importPegawaiCsv($rows, $request->kantor_id);

        return response()->json([
            'status'  => true,
            'message' => "Import selesai. Berhasil: {$result['imported']}, Dilewati: {$result['skipped']}.",
            'result'  => $result,
        ]);
    }
}