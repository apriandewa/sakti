<?php

namespace App\Http\Controllers\Backend\Galeri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Galeri;
use Illuminate\Support\Facades\Storage;
use App\Models\Verifikasi;  
use Illuminate\Support\Facades\Auth;
use App\Models\User;    
use Illuminate\Support\Facades\DB;

class GaleriController extends Controller
{
    public function index()
    {
        return view($this->view.'.index');
    }

    public function create()
    {
        return view($this->view.'.create');
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
                                        data-url="' . route('berita.kirim', $data->id) . '" 
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
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make();
    }


    public function store(Request $request)
{
    $request->validate([
        'nama'      => 'required',
        'slug'      => 'required',
        'desc'      => 'required',
        'kategori'  => 'required',
        'keterangan'=> 'required',
        'gambar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        'file.*'    => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',
    ]);

    // bersihkan script di desc
    $request->merge(['desc' => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->get('desc'))]);

    // simpan hanya field non-file agar tidak kebawa ke mass-assignment
    $payload = $request->except(['file', 'gambar']);
    $payload['user_id'] = $request->user()->id;
    $payload['status']  = 'DRAFT';

    if ($galeri = $this->model::create($payload)) {

        // === Multiple attachments: file[] ===
        if ($request->hasFile('file')) {
            $files = is_array($request->file('file')) ? $request->file('file') : [$request->file('file')];

            foreach ($files as $uploaded) {
                $path = Storage::disk(config('filesystems.default'))
                    ->putFile('galeri', $uploaded);

                $galeri->files()->create([
                    'data' => [
                        'name'   => $uploaded->getClientOriginalName(),
                        'disk'   => config('filesystems.default'),
                        'target' => $path,
                    ],
                    'alias' => 'gambar_galeri',
                ]);
            }
        }

        // === Cover image: gambar (single) ===
        if ($request->hasFile('gambar')) {
            $cover = $request->file('gambar');
            $path  = Storage::disk(config('filesystems.default'))
                ->putFile('galeri', $cover);

            $galeri->files()->create([
                'data' => [
                    'name'   => $cover->getClientOriginalName(),
                    'disk'   => config('filesystems.default'),
                    'target' => $path,
                ],
                'alias' => 'cover_galeri', // konsisten dengan update()
            ]);
        }

        return response()->json(['status' => true, 'message' => 'Data berhasil disimpan']);
    }

    return response()->json(['status' => false, 'message' => 'Data gagal disimpan']);
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
        return view($this->view.'.edit', compact('data'));
    }

    public function update(Request $request, $id)
{
    $request->validate([
        'nama' => 'required',
        'slug' => 'required',
        'desc' => 'required',
        'kategori' => 'required',
        'keterangan' => 'required',
        'file.*' => 'nullable|mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx|max:2048',
    ]);

    $data = $this->model::findOrFail($id);
    $data['user_id'] = $request->user()->id;
    $data['status'] = 'DRAFT';

    if ($data->update($request->all())) {

        // === Multiple attachments: file[] ===
        if ($request->hasFile('file')) {
            $files = is_array($request->file('file')) 
                ? $request->file('file') 
                : [$request->file('file')];

            foreach ($files as $file) {
                $path = Storage::disk(config('filesystems.default'))
                    ->putFile('galeri', $file);

                $data->files()->create([
                    'data' => [
                        'name'  => $file->getClientOriginalName(),
                        'disk'  => config('filesystems.default'),
                        'target'=> $path,
                    ],
                    'alias' => 'gambar_galeri',
                ]);
            }
        }

        // === Cover Image ===
        if ($request->hasFile('gambar')) {
            // hapus file lama alias 'cover_galeri'
            foreach ($data->files()->where('alias', 'cover_galeri')->get() as $file) {
                if (isset($file->data['target']) && 
                    Storage::disk($file->data['disk'] ?? config('filesystems.default'))->exists($file->data['target'])) {
                    Storage::disk($file->data['disk'] ?? config('filesystems.default'))->delete($file->data['target']);
                }
                $file->delete();
            }

            // upload cover baru
            $path = Storage::disk(config('filesystems.default'))
                ->putFile('galeri', $request->file('gambar'));

            $data->files()->create([
                'data' => [
                    'name'  => $request->file('gambar')->getClientOriginalName(),
                    'disk'  => config('filesystems.default'),
                    'target'=> $path,
                ],
                'alias' => 'cover_galeri',
            ]);
        }

        $response = [
            'status'  => TRUE,
            'message' => 'Data berhasil disimpan',
        ];
    }

    return response()->json($response ?? ['status'=>FALSE, 'message'=>'Data gagal disimpan']);
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

    public function extracted(Request $request, $galeri): void
{
    if ($request->hasFile('file')) {
        $files = is_array($request->file('file')) 
            ? $request->file('file') 
            : [$request->file('file')];

        foreach ($files as $file) {
            $path = Storage::disk(config('filesystems.default'))
                ->putFile($this->code.'/'.date('Y').'/'.date('m').'/'.date('d'), $file);

            $galeri->file()->create([
                'data'=>[
                    'name'  => $file->getClientOriginalName(),
                    'disk'  => config('filesystems.default'),
                    'target'=> $path,
                ],
                'alias' => 'cover_galeri',
            ]);
        }
    }
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
                    'icon'    => 'fa fa-image',
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
