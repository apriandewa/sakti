<?php

namespace App\Http\Controllers\Backend\Unduhan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unduhan;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;
use App\Models\Verifikasi;  
use Illuminate\Support\Facades\Auth;
use App\Models\User;    
use Illuminate\Support\Facades\DB;

class UnduhanController extends Controller
{
    public function index()
    {
        return view($this->view.'.index');
    }

    public function create()
    {
        $parentKategori = Kategori::where('slug', 'unduhan')->first();
        $kategoris = $parentKategori
            ? Kategori::where('parent_id', $parentKategori->id)->pluck('nama', 'nama')
            : collect();
        return view($this->view.'.create', compact('kategoris'));
    }

    public function data(Request $request)
    {
        $user = $request->user();

        // Jika level_id adalah 1 atau 2, tampilkan semua data
        if (in_array($user->level_id, [1, 2])) {
            $data = $this->model::all();
        } 
        // Jika level_id adalah 3, tampilkan data berdasarkan user_id
        else if ($user->level_id == 3) {
            $data = $this->model::where('user_id', $user->id)->get();
        } 
        // Jika level_id tidak sesuai, tampilkan data kosong
        else {
            $data = collect(); // Koleksi kosong
        }

        return datatables()->of($data)
            ->addColumn('action', function ($data) use ($user) {
                $button = '';

                if ($data->status != 'DRAFT' && $data->status != 'REVISI') {
                    if ($user->read) {
                        $button .= '<button type="button" class="btn-action btn btn-sm btn-info btn-outline" 
                                        data-title="Detail" data-action="show" data-url="' . $this->url . '" 
                                        data-id="' . $data->id . '" title="Tampilkan">
                                        <i class="fa fa-eye text-info"></i></button>';
                    }
                } else {
                    if ($user->read) {
                        $button .= '<button type="button" class="btn-kirim btn btn-sm btn-outline" 
                                        data-title="Kirim Data" data-action="kirim" 
                                        data-url="' . route('unduhan.kirim', $data->id) . '" 
                                        data-id="' . $data->id . '" title="Kirim">
                                        <i class="fa fa-paper-plane text-primary"></i></button>';

                        $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" 
                                        data-title="Detail" data-action="show" data-url="' . $this->url . '" 
                                        data-id="' . $data->id . '" title="Tampilkan">
                                        <i class="fa fa-eye text-info"></i></button>';
                    }

                    if ($user->update) {
                        $button .= '<button type="button" class="btn-action btn-sm btn btn-outline" 
                                        data-title="Edit" data-action="edit" data-url="' . $this->url . '" 
                                        data-id="' . $data->id . '" title="Edit">
                                        <i class="fa fa-edit text-warning"></i></button>';
                    }

                    if ($user->delete) {
                        $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" 
                                        data-title="Delete" data-action="delete" data-url="' . $this->url . '" 
                                        data-id="' . $data->id . '" title="Delete">
                                        <i class="fa fa-trash text-danger"></i></button>';
                    }
                }

                return "<div class='btn-group'>" . $button . "</div>";
            })
            ->addColumn('status_badge', function ($row) {
                return view('components.status-badge', [
                    'status' => $row->status,
                    'size'   => 'xs',
                ])->render();
            })
            ->addIndexColumn()
            ->rawColumns(['action','status_badge'])
            ->make();
    }


    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required',
            'slug'       => 'required|unique:unduhans',
            'desc'       => 'required',
            'kategori'   => 'required',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'berkas'     => 'nullable|array',
            'berkas.*'   => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $payload = $request->except(['gambar', 'berkas']);
        $payload['user_id'] = $request->user()->id;
        $payload['status']  = 'DRAFT';

        if ($unduhan = $this->model::create($payload)) {

            // Gambar sampul (single)
            if ($request->hasFile('gambar')) {
                $unduhan->file()->create([
                    'data'  => [
                        'name'   => $request->file('gambar')->getClientOriginalName(),
                        'disk'   => config('filesystems.default'),
                        'target' => Storage::disk(config('filesystems.default'))->putFile('unduhan/gambar', $request->file('gambar')),
                    ],
                    'alias' => 'gambar_unduhan',
                ]);
            }

            // Berkas unduhan (multiple — PDF / Word)
            if ($request->hasFile('berkas')) {
                foreach ($request->file('berkas') as $berkas) {
                    if (!$berkas || !$berkas->isValid()) continue;
                    $unduhan->file()->create([
                        'data'  => [
                            'name'   => $berkas->getClientOriginalName(),
                            'disk'   => config('filesystems.default'),
                            'target' => Storage::disk(config('filesystems.default'))->putFile('unduhan/berkas', $berkas),
                        ],
                        'alias' => 'berkas_unduhan',
                    ]);
                }
            }

            $response = ['status' => TRUE, 'message' => 'Data berhasil disimpan'];
        }
        return response()->json($response ?? ['status' => FALSE, 'message' => 'Data gagal disimpan']);
    }

    public function show($id)
    {
        $data = $this->model::find($id);

        // Ambil histori verifikasi dari tabel verifikasi berdasarkan verifiable_id
        $histori_verifikasi = \App\Models\Verifikasi::with('user')
        ->where('verifiable_id', $id)
        ->orderBy('updated_at', 'asc')
        ->get();


        return view($this->view.'.show', compact('data', 'histori_verifikasi'));
    }

    public function edit($id)
    {
        $data = $this->model::find($id);
        $parentKategori = Kategori::where('slug', 'unduhan')->first();
        $kategoris = $parentKategori
            ? Kategori::where('parent_id', $parentKategori->id)->pluck('nama', 'nama')
            : collect();
        return view($this->view.'.edit', compact('data', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'       => 'required',
            'slug'       => 'required',
            'desc'       => 'required',
            'kategori'   => 'required',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'berkas'     => 'nullable|array',
            'berkas.*'   => 'nullable|file|mimes:pdf,doc,docx|max:20480',
        ]);

        $data = $this->model::findOrFail($id);
        $data['user_id'] = $request->user()->id;
        $data['status']  = 'DRAFT';

        if ($data->update($request->except(['gambar', 'berkas']))) {

            // Gambar sampul (single — ganti jika ada baru)
            if ($request->hasFile('gambar')) {
                foreach ($data->files()->where('alias', 'gambar_unduhan')->get() as $file) {
                    if (isset($file->data['target']) && Storage::disk($file->data['disk'] ?? config('filesystems.default'))->exists($file->data['target'])) {
                        Storage::disk($file->data['disk'] ?? config('filesystems.default'))->delete($file->data['target']);
                    }
                    $file->delete();
                }
                $data->files()->create([
                    'data'  => [
                        'name'   => $request->file('gambar')->getClientOriginalName(),
                        'disk'   => config('filesystems.default'),
                        'target' => Storage::disk(config('filesystems.default'))->putFile('unduhan/gambar', $request->file('gambar')),
                    ],
                    'alias' => 'gambar_unduhan',
                ]);
            }

            // Berkas unduhan (multiple — tambah ke existing)
            if ($request->hasFile('berkas')) {
                foreach ($request->file('berkas') as $berkas) {
                    if (!$berkas || !$berkas->isValid()) continue;
                    $data->files()->create([
                        'data'  => [
                            'name'   => $berkas->getClientOriginalName(),
                            'disk'   => config('filesystems.default'),
                            'target' => Storage::disk(config('filesystems.default'))->putFile('unduhan/berkas', $berkas),
                        ],
                        'alias' => 'berkas_unduhan',
                    ]);
                }
            }

            $response = ['status' => TRUE, 'message' => 'Data berhasil disimpan'];
        }
        return response()->json($response ?? ['status' => FALSE, 'message' => 'Data gagal disimpan']);
    }

    public function delete($id)
    {
        $data=$this->model::find($id);
        return view($this->view.'.delete', compact('data'));
    }

    public function destroy($id)
    {
        $data=$this->model::find($id);
        if($data->delete()){
            $response=[ 'status'=>TRUE, 'message'=>'Data berhasil dihapus'];
        }
        return response()->json($response ?? ['status'=>FALSE, 'message'=>'Data gagal dihapus']);
    }

        public function kirim($id)
    {
        // cari data (bisa berita/galeri/unduhan)
        $data = $this->model::find($id);

        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan'
            ]);
        }

        // update status data
        $data->status = 'VERIFIKASI';

        if ($data->save()) {

            // buat record di tabel verifikasi
            $verifikasi = Verifikasi::create([
                'id'              => \Illuminate\Support\Str::uuid()->toString(),
                'verifiable_type' => get_class($data),
                'verifiable_id'   => $data->id,
                'catatan'         => null,
                'status'          => 'VERIFIKASI',
                'user_id'         => Auth::id(), // user yang sedang login
            ]);

            // ambil semua user dengan level_id = 4 (verifikator)
            $users = User::where('level_id', 4)->pluck('id')->toArray();

            // kirim notifikasi
            if (!empty($users)) {
                $this->help::sendNotification($data, $users, [
                    'title'   => 'Data Baru Menunggu Verifikasi',
                    'link'    => url('/admin/verifikasi/'), // ✅ arahkan ke ID verifikasi
                    'icon'    => 'fa fa-download',
                    'color'   => 'text-warning',
                    'content' => $data->nama ?? 'Data baru masuk'
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil dikirim dan menunggu verifikasi'
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Data gagal dikirim'
        ]);
    }

}
