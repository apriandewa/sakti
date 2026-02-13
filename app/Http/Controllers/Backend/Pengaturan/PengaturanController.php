<?php

namespace App\Http\Controllers\Backend\Pengaturan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PengaturanController extends Controller
{
    public function index()
    {
        return view($this->view.'.index');
    }

    public function data(Request $request)
    {
        $user = $request->user();
        $data=$this->model::all();
        return datatables()->of($data)
            ->addColumn('action', function ($data) use ($user) {
                $button ='';
                
                if($user->update){
                    $button.='<button type="button" class="btn-action btn btn-info data-title="Edit" data-action="edit" data-url="'.$this->url.'" data-id="'.$data->id.'" title="Edit"> <i class="fa fa-cog text-warning"></i> Pengaturan</button> ';
                }
                
                return "<div class='btn-group'>".$button."</div>";
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make();
    }

    public function edit($id)
    {
        $data = $this->model::find($id);
        return view($this->view.'.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required',
			'subjudul' => 'required',
			'deskripsi' => 'required',
			'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:1024', 
            'ikon' => 'nullable|image|mimes:jpg,jpeg,png|max:1024',
			'alamat' => 'required',
			'telepon' => 'required',
			'email' => 'required',
			'peta' => 'required',
			'facebook' => 'required',
			'instagram' => 'required',
			'twiter' => 'required',
			'tiktok' => 'required',
			'youtube' => 'required',
			'call_center' => 'required',
        ]);

        $data=$this->model::find($id);
        // Handle file upload if 'logo' is present in the request
            if($data->update($request->all())){
                if ($request->hasFile('logo')) {
                    // Hapus semua file lama dengan alias 'logo_app'
                    foreach ($data->files()->where('alias', 'logo_app')->get() as $file) {
                        // Hapus file fisik dari storage jika ada
                        if (isset($file->data['target']) && Storage::disk($file->data['disk'] ?? config('filesystems.default'))->exists($file->data['target'])) {
                            Storage::disk($file->data['disk'] ?? config('filesystems.default'))->delete($file->data['target']);
                        }
                        $file->delete();
                    }
                    // Upload file baru dengan format sama seperti store
                    $data->files()->create([
                        'data' => [
                            'name' => $request->file('logo')->getClientOriginalName(),
                            'disk' => config('filesystems.default'),
                            'target' => Storage::disk(config('filesystems.default'))->putFile('page', $request->file('logo')),
                        ],
                        'alias' => 'logo_app',
                    ]);
                }

                if ($request->hasFile('ikon')) {
                    // Hapus semua file lama dengan alias 'ikon_app'
                    foreach ($data->files()->where('alias', 'ikon_app')->get() as $file) {
                        // Hapus file fisik dari storage jika ada
                        if (isset($file->data['target']) && Storage::disk($file->data['disk'] ?? config('filesystems.default'))->exists($file->data['target'])) {
                            Storage::disk($file->data['disk'] ?? config('filesystems.default'))->delete($file->data['target']);
                        }
                        $file->delete();
                    }
                    // Upload file baru dengan format sama seperti store
                    $data->files()->create([
                        'data' => [
                            'name' => $request->file('ikon')->getClientOriginalName(),
                            'disk' => config('filesystems.default'),
                            'target' => Storage::disk(config('filesystems.default'))->putFile('page', $request->file('ikon')),
                        ],
                        'alias' => 'ikon_app',
                    ]);
                }

                $response=[
                    'status'=>TRUE, 'message'=>'Data berhasil disimpan',
                ];
            }
        return response()->json($response ?? ['status'=>FALSE, 'message'=>'Data gagal disimpan']);
    }

}
