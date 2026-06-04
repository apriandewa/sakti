<?php

namespace App\Http\Controllers\Backend\Kategori;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $data=$this->model::find($request->id);
        return view($this->view.'.index', ['data'=>$data]);
    }


    public function create(Request $request)
{
    $parent_id = $request->parent_id;

    return view('backend.kategori.create', compact('parent_id'));
}

public function getSub(Request $request)
    {
        $data = Kategori::where('parent_id', $request->id)->get();
        return response()->json($data);
    }


    public function data(Request $request)
    {
        $user = $request->user();
        $data=$this->model::whereParentId($request->id);
        return datatables()->of($data)
            ->addColumn('action', function ($data) use ($user) {
                $button ='';
                if($user->read){
                    $button .= '<a href="'.$this->url.'?id='.$data->id.'" class= "btn btn-sm btn-outline"> <i class="fa fa-external-link"></i></a>';
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
            'slug' => 'required|unique:kategoris,slug',
			'desc' => 'required',
			'ikon' => 'required',
			'status' => 'required',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // bersihkan script di desc
        $request->merge(['desc' => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->get('desc'))]);


        $data = $request->all();
        $data['user_id'] = $request->user()->id; // Add the logged-in user's ID to the data
        $data['parent_id']  = $request->parent_id; //untuk menambahkan parent_id saat berada di child

        if ($kategori = $this->model::create($data)) {
            
            // Handle file upload if 'gambar' is present in the request
            if ($request->hasFile('gambar')) {
                    $kategori->file()->create([
                        'data' => [
                            'name' => $request->file('gambar')->getClientOriginalName(),
                            'disk' => config('filesystems.default'),
                            'target' => Storage::disk(config('filesystems.default'))->putFile('kategori', $request->file('gambar')),
                        ],
                        'alias' => 'gambar_kategori', // Menambahkan alias untuk field gambar
                    ]);
                }
            $response=[ 'status'=>TRUE, 'message'=>'Data berhasil disimpan'];
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
			'ikon' => 'required',
			'status' => 'required',
            'gambar'    => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

       $data=$this->model::find($id);
       $data['user_id'] = $request->user()->id; // Add the logged-in user's ID to the data
        // Handle file upload if 'gambar' is present in the request
            if($data->update($request->all())){
                if ($request->hasFile('gambar')) {
                    // Hapus semua file lama dengan alias 'gambar_kategori'
                    foreach ($data->files()->where('alias', 'gambar_kategori')->get() as $file) {
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
                            'target' => Storage::disk(config('filesystems.default'))->putFile('kategori', $request->file('gambar')),
                        ],
                        'alias' => 'gambar_kategori',
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
