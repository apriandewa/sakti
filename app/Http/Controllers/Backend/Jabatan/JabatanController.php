<?php

namespace App\Http\Controllers\Backend\Jabatan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class JabatanController extends Controller
{
    public function index() : object
    {
        return view($this->view.'.index');
    }

    public function create() : object
    {
        $parent = $this->model::whereNull('parent_id')->pluck('nama', 'id');
        return view($this->view.'.create', compact('parent'));
    }

    public function data(Request $request) : object
    {
        $data = $this->model::with('parent');
        $user = $request->user();
        return datatables()->of($data)
            ->editColumn('parent_id', function ($data) {
                return $data->parent ? $data->parent->nama : '<span class="badge bg-purple">Jenis Jabatan (Parent)</span>';
            })
            ->editColumn('status', function ($data) {
                return $data->status == 'aktif' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>';
            })
            ->addColumn('action', function ($data) use ($user) {
                $button ='';
                if($user->read){
                    $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" data-title="Detail" data-action="show" data-url="'.$this->url.'" data-id="'.$data->id.'" title="Tampilkan"><i class="fa fa-eye text-info"></i></button>';
                }
                if($user->create || $user->update){
                    $button.='<button class="btn-action btn btn-sm btn-outline" data-title="Edit" data-action="edit" data-url="'.$this->url.'" data-id="'.$data->id.'" title="Edit"> <i class="fa fa-edit text-warning"></i> </button> ';
                }
                if($user->delete){
                    $button.='<button class="btn-action btn btn-sm btn-outline" data-title="Delete" data-action="delete" data-url="'.$this->url.'" data-id="'.$data->id.'" title="Delete"> <i class="fa fa-trash text-danger"></i> </button>';
                }
                return "<div class='btn-group'>".$button."</div>";
            })
            ->addIndexColumn()
            ->rawColumns(['action','status','parent_id'])
            ->make();
    }

    public function store(Request $request) : object
    {
        $request->validate([
            'parent_id' => 'nullable|exists:jabatans,id',
            'nama'   => 'required|string|max:255',
            'desc'   => 'nullable|string',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        $input = $request->all();
        $input['user_id'] = $request->user()->id;

        if ($this->model::create($input)) {
            $response = ['status' => TRUE, 'message' => 'Data berhasil disimpan'];
        }
        return response()->json($response ?? ['status' => FALSE, 'message' => 'Data gagal disimpan']);
    }

    public function show($id) : object
    {
        $data = $this->model::with('parent')->find($id);
        return view($this->view.'.show', compact('data'));
    }

    public function edit($id) : object
    {
        $parent = $this->model::whereNull('parent_id')->where('id', '!=', $id)->pluck('nama', 'id');
        $data = $this->model::find($id);
        return view($this->view.'.edit', compact('data', 'parent'));
    }

    public function update(Request $request, $id) : object
    {
        $request->validate([
            'parent_id' => 'nullable|exists:jabatans,id',
            'nama'   => 'required|string|max:255',
            'desc'   => 'nullable|string',
            'keterangan' => 'nullable|string',
            'status' => 'required|in:aktif,tidak aktif',
        ]);

        $data = $this->model::find($id);
        if ($data->update($request->all())) {
            $response = ['status' => TRUE, 'message' => 'Data berhasil disimpan'];
        }
        return response()->json($response ?? ['status' => FALSE, 'message' => 'Data gagal disimpan']);
    }

    public function delete($id) : object
    {
        $data = $this->model::find($id);
        return view($this->view.'.delete', compact('data'));
    }

    public function destroy($id) : object
    {
        $data = $this->model::find($id);
        if($data->delete()){
            $response = ['status' => TRUE, 'message' => 'Data berhasil dihapus'];
        }
        return response()->json($response ?? ['status' => FALSE, 'message' => 'Data gagal dihapus']);
    }
}
