<?php

namespace App\Http\Controllers\Backend\Tamu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tamu;
use App\Models\Kategori;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TamuController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $statistik = [
            'total'      => Tamu::disetujui()->count(),
            'hari_ini'   => Tamu::disetujui()->whereDate('tanggal_kunjungan', today())->count(),
            'bulan_ini'  => Tamu::disetujui()
                                ->whereMonth('tanggal_kunjungan', now()->month)
                                ->whereYear('tanggal_kunjungan', now()->year)
                                ->count(),
            'minggu_ini' => Tamu::disetujui()
                                ->whereBetween('tanggal_kunjungan', [
                                    now()->startOfWeek(),
                                    now()->endOfWeek(),
                                ])
                                ->count(),
        ];

        return view($this->view . '.index', compact('statistik'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $listPekerjaan = $this->getKategori('pekerjaan');
        $listKeperluan = $this->getKategori('keperluan');

        return view($this->view . '.create', compact('listPekerjaan', 'listKeperluan'));
    }

    /*
    |--------------------------------------------------------------------------
    | DATATABLES
    |--------------------------------------------------------------------------
    */

    public function data(Request $request)
    {
        $user = $request->user();
        $data = Tamu::query()->latest();

        return datatables()->of($data)
            ->addColumn('tanggal_kunjungan', function ($row) {
                return $row->tanggal_kunjungan
                    ? Carbon::parse($row->tanggal_kunjungan)->translatedFormat('d F Y H:i')
                    : '-';
            })
            ->addColumn('status_badge', function ($row) {
                return view('components.status-badge', [
                    'status' => $row->status,
                    'size'   => 'xs',
                ])->render();
            })
            ->addColumn('action', function ($row) use ($user) {
                $btn = '';

                if ($user->read) {
                    $btn .= '<button type="button" class="btn-action btn btn-sm btn-outline"
                                data-title="Detail" data-action="show"
                                data-url="' . $this->url . '" data-id="' . $row->id . '"
                                title="Tampilkan">
                                <i class="fa fa-eye text-info"></i>
                             </button>';
                }

                if ($user->update) {
                    $btn .= '<button type="button" class="btn-action btn btn-sm btn-outline"
                                data-title="Edit" data-action="edit"
                                data-url="' . $this->url . '" data-id="' . $row->id . '"
                                title="Edit">
                                <i class="fa fa-edit text-warning"></i>
                             </button>';
                }

                if ($user->delete) {
                    $btn .= '<button type="button" class="btn-action btn btn-sm btn-outline"
                                data-title="Delete" data-action="delete"
                                data-url="' . $this->url . '" data-id="' . $row->id . '"
                                title="Hapus">
                                <i class="fa fa-trash text-danger"></i>
                             </button>';
                }

                return "<div class='btn-group'>{$btn}</div>";
            })
            ->addIndexColumn()
            ->rawColumns(['action', 'status_badge'])
            ->make();
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $request->validate([
            'nama'          => 'required',
            'alamat'        => 'required',
            'no_hp'         => 'required',
            'email'         => 'required|email',
            'jenis_kelamin' => 'required',
            'pekerjaan'     => 'required',
            'asal'          => 'required',
            'keperluan'     => 'required',
            'pesan'         => 'required',
            'foto'          => 'nullable|image|mimes:jpeg,png|max:2048',
            'dokumen'       => 'nullable|mimes:jpeg,png,pdf,doc,docx|max:5120',
        ]);

        // Sanitasi script pada pesan
        $request->merge([
            'pesan' => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->pesan),
        ]);

        $payload = $request->except(['foto', 'dokumen']);
        $payload['user_id']           = $request->user()->id;
        $payload['status']            = 'TERKIRIM';
        $payload['ip_address']        = $request->ip();
        $payload['tanggal_kunjungan'] = now();

        try {
            $tamu = Tamu::create($payload);

            if ($request->hasFile('foto')) {
                $this->uploadFile($tamu, $request->file('foto'), 'foto_tamu');
            }

            if ($request->hasFile('dokumen')) {
                $this->uploadFile($tamu, $request->file('dokumen'), 'dokumen_tamu');
            }

            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil disimpan.',
            ]);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status'  => false,
                'message' => 'Data gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $data        = Tamu::findOrFail($id);
        $fotoTamu    = $data->getfilebyalias('foto_tamu');
        $dokumenTamu = $data->getfilebyalias('dokumen_tamu');

        $statusClass = match (strtoupper($data->status ?? '')) {
            'DISETUJUI' => 'success',
            'DITOLAK'   => 'danger',
            default     => 'info',
        };

        $bisaDiproses = strtoupper($data->status ?? '') === 'TERKIRIM';

        return view($this->view . '.show', compact(
            'data',
            'fotoTamu',
            'dokumenTamu',
            'statusClass',
            'bisaDiproses'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $data          = Tamu::findOrFail($id);
        $listPekerjaan = $this->getKategori('pekerjaan');
        $listKeperluan = $this->getKategori('keperluan');
        $fotoTamu      = $data->getfilebyalias('foto_tamu');
        $dokumenTamu   = $data->getfilebyalias('dokumen_tamu');

        return view($this->view . '.edit', compact(
            'data',
            'listPekerjaan',
            'listKeperluan',
            'fotoTamu',
            'dokumenTamu'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'              => 'required',
            'alamat'            => 'required',
            'no_hp'             => 'required',
            'email'             => 'required|email',
            'jenis_kelamin'     => 'required',
            'pekerjaan'         => 'required',
            'asal'              => 'required',
            'keperluan'         => 'required',
            'pesan'             => 'required',
            'status'            => 'required',
            'tanggal_kunjungan' => 'required',
            'foto'              => 'nullable|image|mimes:jpeg,png|max:2048',
            'dokumen'           => 'nullable|mimes:jpeg,png,pdf,doc,docx|max:5120',
        ]);

        try {
            $tamu = Tamu::findOrFail($id);

            $tamu->update($request->except(['foto', 'dokumen']));

            if ($request->hasFile('foto')) {
                $this->replaceFile($tamu, $request->file('foto'), 'foto_tamu');
            }

            if ($request->hasFile('dokumen')) {
                $this->replaceFile($tamu, $request->file('dokumen'), 'dokumen_tamu');
            }

            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil disimpan.',
            ]);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status'  => false,
                'message' => 'Data gagal disimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE (konfirmasi)
    |--------------------------------------------------------------------------
    */

    public function delete($id)
    {
        $data = Tamu::findOrFail($id);

        return view($this->view . '.delete', compact('data'));
    }

    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        try {
            $tamu = Tamu::findOrFail($id);
            $tamu->delete();

            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil dihapus.',
            ]);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status'  => false,
                'message' => 'Data gagal dihapus: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE FILE (foto atau dokumen via AJAX)
    |--------------------------------------------------------------------------
    */

    public function deleteFile(Request $request, $id)
    {
        $request->validate([
            'alias' => 'required|in:foto_tamu,dokumen_tamu',
        ]);

        try {
            $tamu = Tamu::findOrFail($id);
            $file = $tamu->getfilebyalias($request->alias);

            if (! $file) {
                return response()->json([
                    'status'  => false,
                    'message' => 'File tidak ditemukan.',
                ], 404);
            }

            $file->delete();

            return response()->json([
                'status'  => true,
                'message' => 'File berhasil dihapus.',
            ]);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'status'  => false,
                'message' => 'File gagal dihapus: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS  (TERKIRIM → DISETUJUI / DITOLAK)
    |--------------------------------------------------------------------------
    */

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:DISETUJUI,DITOLAK',
        ]);

        try {
            $tamu = Tamu::findOrFail($id);

            if (strtoupper($tamu->status) !== 'TERKIRIM') {
                return response()->json([
                    'success' => false,
                    'message' => 'Status hanya dapat diubah dari kondisi TERKIRIM.',
                ], 422);
            }

            $tamu->status         = strtoupper($request->status);
            $tamu->verifikator_id = $request->user()->id;
            $tamu->save();

            return response()->json([
                'success' => true,
                'message' => 'Status berhasil diperbarui menjadi ' . $tamu->status,
                'status'  => $tamu->status,
            ]);

        } catch (\Throwable $e) {
            Log::error($e);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | GRAFIK
    |--------------------------------------------------------------------------
    */

    public function grafik(Request $request)
    {
        $kategori = $request->kategori ?? 'jenis_kelamin';
        $periode  = $request->periode  ?? 'bulanan';
        $tahun    = $request->tahun    ?? now()->year;
        $bulan    = $request->bulan    ?? now()->month;

        $query = Tamu::query();

        if ($tahun) {
            $query->whereYear('tanggal_kunjungan', $tahun);
        }

        if ($periode === 'bulanan' && $bulan) {
            $query->whereMonth('tanggal_kunjungan', $bulan);
        }

        $data = $query
            ->select($kategori . ' as label', DB::raw('COUNT(*) as total'))
            ->groupBy($kategori)
            ->orderByDesc('total')
            ->get();

        return response()->json($data);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Upload file baru dan simpan relasi.
     */
    private function uploadFile(Tamu $tamu, $file, string $alias): void
    {
        $path = Storage::disk(config('filesystems.default'))
            ->putFile('tamu', $file);

        $tamu->files()->create([
            'data' => [
                'name'   => $file->getClientOriginalName(),
                'disk'   => config('filesystems.default'),
                'target' => $path,
            ],
            'alias' => $alias,
        ]);
    }

    /**
     * Hapus file lama (jika ada) lalu upload file baru.
     */
    private function replaceFile(Tamu $tamu, $file, string $alias): void
    {
        $lama = $tamu->getfilebyalias($alias);

        if ($lama) {
            $lama->delete();
        }

        $this->uploadFile($tamu, $file, $alias);
    }

    /**
     * Ambil list kategori berdasarkan slug parent.
     */
    private function getKategori(string $slug): \Illuminate\Support\Collection
    {
        $parent = Kategori::where('slug', $slug)->first();

        if (! $parent) {
            return collect();
        }

        return Kategori::where('parent_id', $parent->id)
            ->orderBy('nama')
            ->pluck('nama', 'nama');
    }
}