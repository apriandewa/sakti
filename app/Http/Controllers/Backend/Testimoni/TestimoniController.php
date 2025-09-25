<?php

namespace App\Http\Controllers\Backend\Testimoni;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Testimoni;
use Illuminate\Support\Facades\Storage;

class TestimoniController extends Controller
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
        $data=$this->model::all();
        return datatables()->of($data)
            ->addColumn('action', function ($data) use ($user) {
                $button ='';
                if($user->read){
                    $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Detail" data-action="show" data-url="'.$this->url.'" data-id="'.$data->id.'" title="Tampilkan"><i class="fa fa-eye text-info"></i></button>';
                }
                if($user->update){
                    $button.='<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Edit" data-action="edit" data-url="'.$this->url.'" data-id="'.$data->id.'" title="Edit"> <i class="fa fa-edit text-warning"></i> </button> ';
                }
                if($user->delete){
                    $button.='<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Delete" data-action="delete" data-url="'.$this->url.'" data-id="'.$data->id.'" title="Delete"> <i class="fa fa-trash text-danger"></i> </button>';
                }
                return "<div class='btn-group'>".$button."</div>";
            })
            ->addIndexColumn()
            ->rawColumns(['action'])
            ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required',
			'desc' => 'required',
			'keterangan' => 'required',
			'status' => 'required',
        ]);

        $data = $request->all();
        $data['user_id'] = $request->user()->id; // Add the logged-in user's ID to the data

        if ($testimoni = $this->model::create($data)) {
            
            // Handle file upload if 'gambar' is present in the request
            if ($request->hasFile('gambar')) {
                    $testimoni->file()->create([
                        'data' => [
                            'name' => $request->file('gambar')->getClientOriginalName(),
                            'disk' => config('filesystems.default'),
                            'target' => Storage::disk(config('filesystems.default'))->putFile('testimoni', $request->file('gambar')),
                        ],
                        'alias' => 'gambar_testimoni', // Menambahkan alias untuk field gambar
                    ]);
                }
            $response=[ 'status'=>TRUE, 'message'=>'Data berhasil disimpan'];
        }
        else {
            $response = ['status' => FALSE, 'message' => 'Data gagal disimpan'];
        }
        return response()->json($response ?? ['status'=>FALSE, 'message'=>'Data gagal disimpan']);
    }

    public function show($id)
    {
        $data = $this->model::find($id);
        return view($this->view.'.show', compact('data'));
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
			'desc' => 'required',
			'keterangan' => 'required',
			'status' => 'required',
        ]);

        $data=$this->model::find($id);
        // Handle file upload if 'gambar' is present in the request
            if($data->update($request->all())){
                if ($request->hasFile('gambar')) {
                    // Hapus semua file lama dengan alias 'gambar_testimoni'
                    foreach ($data->files()->where('alias', 'gambar_testimoni')->get() as $file) {
                        // Hapus file fisik dari storage jika ada
                        if (isset($file->data['target']) && Storage::disk($file->data['disk'] ?? config('filesystems.default'))->exists($file->data['target'])) {
                            Storage::disk($file->data['disk'] ?? config('filesystems.default'))->delete($file->data['target']);
                        }
                        $file->delete();
                    }
                    // Upload file baru dengan format sama seperti store
                    $data->files()->create([
                        'data' => [
                            'name' => $request->file('gambar')->getClientOriginalName(),
                            'disk' => config('filesystems.default'),
                            'target' => Storage::disk(config('filesystems.default'))->putFile('testimoni', $request->file('gambar')),
                        ],
                        'alias' => 'gambar_testimoni',
                    ]);
                }
                $response=[
                    'status'=>TRUE, 'message'=>'Data berhasil disimpan',
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
}
