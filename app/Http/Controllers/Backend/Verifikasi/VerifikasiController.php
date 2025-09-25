<?php

namespace App\Http\Controllers\Backend\Verifikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Berita;
use Illuminate\Support\Facades\Storage;
use App\Models\Verifikasi;  
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class VerifikasiController extends Controller
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

    $data = Verifikasi::with('verifiable')
        ->join(DB::raw('(SELECT verifiable_id, MIN(created_at) as created_at 
                         FROM verifikasis 
                         WHERE status = "verifikasi"
                         GROUP BY verifiable_id) as uniq'),
            function ($join) {
                $join->on('verifikasis.verifiable_id', '=', 'uniq.verifiable_id')
                     ->on('verifikasis.created_at', '=', 'uniq.created_at');
            })
        ->where('verifikasis.status', 'verifikasi') // pastikan hanya status verifikasi
        ->whereHas('verifiable', function ($q) {
            $q->where('status', 'verifikasi'); // filter status pada model utama juga
        })
        ->select('verifikasis.*')
        ->get();

    return datatables()->of($data)
        ->addIndexColumn()
        ->addColumn('nama', function ($row) {
            return $row->verifiable->judul 
                ?? $row->verifiable->nama 
                ?? '-';
        })
        ->addColumn('kategori', function ($row) {
            return class_basename($row->verifiable_type); 
        })
        ->addColumn('action', function ($row) use ($user) {
            $button = '';

            if ($user->read) {
                $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" 
                    data-title="Detail" data-action="show" data-url="'.$this->url.'" data-id="'.$row->id.'" 
                    title="Tampilkan"><i class="fa fa-eye text-info"></i></button>';
            }
            if ($user->update) {
                $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" 
                    data-title="Edit" data-action="edit" data-url="'.$this->url.'" data-id="'.$row->id.'" 
                    title="Edit"><i class="fa fa-edit text-warning"></i></button>';
            }
            if ($user->delete) {
                $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" 
                    data-title="Delete" data-action="delete" data-url="'.$this->url.'" data-id="'.$row->id.'" 
                    title="Delete"><i class="fa fa-trash text-danger"></i></button>';
            }

            return "<div class='btn-group'>".$button."</div>";
        })
        ->rawColumns(['action'])
        ->make(true);
}



    public function store(Request $request)
    {
        $request->validate([
            'catatan' => 'required',
			'status' => 'required',
        ]);

        if ($this->model::create($request->all())) {
            $response=[ 'status'=>TRUE, 'message'=>'Data berhasil disimpan'];
        }
        return response()->json($response ?? ['status'=>FALSE, 'message'=>'Data gagal disimpan']);
    }

    public function show($id)
    {
        // Ambil data verifikasi berdasarkan id
        $verifikasi = $this->model::findOrFail($id);

        // Ambil data asli dari relasi morph (Berita, Galeri, Unduhan, dll)
        $data = $verifikasi->verifiable;

        // Ambil histori verifikasi berdasarkan verifiable_id
        $histori_verifikasi = \App\Models\Verifikasi::with('user')
            ->where('verifiable_id', $verifikasi->verifiable_id)
            ->orderBy('updated_at', 'asc')
            ->get();

        return view($this->view . '.show', compact('data', 'verifikasi', 'histori_verifikasi'));
    }



    public function edit($id)
    {
        $data = $this->model::find($id);
        return view($this->view.'.edit', compact('data'));
    }

public function update(Request $request, $id)
{
    $request->validate([
        'catatan' => 'required',
        'status'  => 'required',
    ]);

    DB::beginTransaction();
    try {
        // Ambil data verifikasi lama
        $verifikasiLama = Verifikasi::find($id);

        if (!$verifikasiLama) {
            return response()->json([
                'status'  => false,
                'message' => 'Data verifikasi tidak ditemukan'
            ]);
        }

        // Ambil model utama berdasarkan verifiable_type & verifiable_id
        $modelClass = $verifikasiLama->verifiable_type;
        $data = $modelClass::find($verifikasiLama->verifiable_id);

        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'Data utama tidak ditemukan'
            ]);
        }

        // Buat record baru di tabel verifikasi
        $verifikasi = Verifikasi::create([
            'id'              => \Illuminate\Support\Str::uuid()->toString(),
            'verifiable_type' => $modelClass,
            'verifiable_id'   => $data->id,
            'catatan'         => $request->catatan,
            'status'          => $request->status,
            'user_id'         => $request->user()->id,
        ]);

        // Update status pada model utama
        $data->update([
            'status'         => $request->status,
            'verifikator_id' => $request->user()->id
        ]);

        // Tentukan link berdasarkan jenis model (tanpa id)
        $link = match ($modelClass) {
            \App\Models\Berita::class   => url('/admin/berita'),
            \App\Models\Unduhan::class => url('/admin/unduhan'),
            \App\Models\Galeri::class  => url('/admin/galeri'),
            default => url('/verifikasi'), // fallback umum
        };

        // Ambil semua user dengan level_id = 3 (user)
        $users = User::where('level_id', 3)->pluck('id')->toArray();

        // Kirim notifikasi
        if (!empty($users)) {
            $this->help::sendNotification($data, $users, [
                'title'   => 'Data Sudah di Verifikasi',
                'link'    => $link,
                'icon'    => 'fa fa-check-circle',
                'color'   => 'text-warning',
                'content' => $data->nama ?? 'Data sudah diverifikasi'
            ]);
        }

        DB::commit();

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil diverifikasi dan status model utama diperbarui',
            'data'    => $verifikasi
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'status'  => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ]);
    }
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
