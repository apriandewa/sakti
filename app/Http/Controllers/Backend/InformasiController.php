<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Informasi;
use App\Models\Verifikasi;
use App\Models\User;

class InformasiController extends Controller
{
    public function index()
    {
        return view($this->view.'.index');
    }

    public function data(Request $request)
    {
        $user = $request->user();

        // Jika level_id 1 atau 4, tampilkan semua
        if (in_array($user->level_id, [1, 4])) {
            $data = $this->model::all();
        } else {
            // Selain itu, hanya bisa melihat data milik user itu sendiri (termasuk level_id 2 dan 3)
            $data = $this->model::where('user_id', $user->id)->get();
        }

        return datatables()->of($data)
            ->addColumn('status_badge', function ($row) {
                return view('components.status-badge', [
                    'status' => $row->status,
                    'size' => 'xs'
                ])->render();
            })
            ->addColumn('action', function ($data) use ($user) {
                $button = '';

                $button .= '<button type="button" class="btn-action btn btn-sm btn-info btn-outline" 
                                data-title="Detail" data-action="show" data-url="' . $this->url . '" 
                                data-id="' . $data->id . '" title="Tampilkan">
                                <i class="fa fa-eye text-info"></i></button>';

                if ($data->status == 'DRAFT' || $data->status == 'REVISI') {
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

                // tombol kirim muncul hanya pada status DRAFT atau REVISI dan level_id 2, 3
                if (in_array($data->status, ['DRAFT', 'REVISI']) && in_array($user->level_id, [2, 3])) {
                    $button .= '<button type="button" class="btn-kirim btn btn-sm btn-outline" 
                                    data-title="Kirim Data" data-url="' . route('informasi.kirim', $data->id) . '" 
                                    data-id="' . $data->id . '" title="Kirim">
                                    <i class="fa fa-paper-plane text-primary"></i></button>';
                }

                // tombol verifikasi muncul hanya pada status PENGAJUAN dan level_id 1, 4
                if ($data->status == 'PENGAJUAN' && in_array($user->level_id, [1, 4])) {
                    $button .= '<button type="button" class="btn-action btn btn-sm btn-outline" 
                                    data-title="Verifikasi" data-action="show" data-url="' . $this->url . '" 
                                    data-id="' . $data->id . '" title="Verifikasi">
                                    <i class="fa fa-check text-success"></i></button>';
                }

                return "<div class='btn-group'>" . $button . "</div>";
            })
            ->addIndexColumn()
            ->rawColumns(['status_badge', 'action'])
            ->make();
    }

    public function create()
    {
        return view($this->view.'.create');
    }

    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nama' => 'required',
            'tipe' => 'required|in:BERKALA,TERSEDIA,SERTA MERTA,DIKECUALIKAN',
            'desc' => 'required',
            'tahun' => 'required|integer',
            'berkas_informasi.*' => 'nullable|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $payload = $request->except(['berkas_informasi']);
        $payload['user_id'] = $request->user()->id;
        $payload['status']  = 'DRAFT';

        if ($data = $this->model::create($payload)) {
            if ($request->hasFile('berkas_informasi')) {
                foreach ($request->file('berkas_informasi') as $file) {
                    $path = Storage::disk(config('filesystems.default'))->putFile('informasi', $file);
                    $data->files()->create([
                        'data' => [
                            'name'   => $file->getClientOriginalName(),
                            'disk'   => config('filesystems.default'),
                            'target' => $path,
                        ],
                        'alias' => 'berkas_informasi',
                    ]);
                }
            }
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan']);
        }
        return response()->json(['status' => false, 'message' => 'Data gagal disimpan']);
    }

    public function show($id)
    {
        $data = $this->model::with('files')->findOrFail($id);
        $histori_verifikasi = \App\Models\Verifikasi::with('user')
            ->where('verifiable_id', $id)
            ->orderBy('updated_at', 'asc')
            ->get();

        return view($this->view.'.show', compact('data', 'histori_verifikasi'));
    }

    public function edit($id)
    {
        $data = $this->model::findOrFail($id);
        return view($this->view.'.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'nama' => 'required',
            'tipe' => 'required|in:BERKALA,TERSEDIA,SERTA MERTA,DIKECUALIKAN',
            'desc' => 'required',
            'tahun' => 'required|integer',
            'berkas_informasi.*' => 'nullable|mimes:pdf|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'message' => $validator->errors()->first()]);
        }

        $data = $this->model::findOrFail($id);
        $payload = $request->except(['berkas_informasi']);
        $payload['user_id'] = $request->user()->id;
        $payload['status'] = 'DRAFT';

        if ($data->update($payload)) {
            if ($request->hasFile('berkas_informasi')) {
                // Upload new files
                foreach ($request->file('berkas_informasi') as $file) {
                    $path = Storage::disk(config('filesystems.default'))->putFile('informasi', $file);
                    $data->files()->create([
                        'data' => [
                            'name'   => $file->getClientOriginalName(),
                            'disk'   => config('filesystems.default'),
                            'target' => $path,
                        ],
                        'alias' => 'berkas_informasi',
                    ]);
                }
            }
            return response()->json(['status' => true, 'message' => 'Data berhasil disimpan']);
        }
        return response()->json(['status' => false, 'message' => 'Data gagal disimpan']);
    }

    public function delete($id)
    {
        $data = $this->model::findOrFail($id);
        return view($this->view.'.delete', compact('data'));
    }

    public function destroy($id)
    {
        $data = $this->model::findOrFail($id);
        if ($data->delete()) {
            return response()->json(['status' => true, 'message' => 'Data berhasil dihapus']);
        }
        return response()->json(['status' => false, 'message' => 'Data gagal dihapus']);
    }

    public function kirim(Request $request, $id)
    {
        $data = $this->model::findOrFail($id);

        $data->status = 'PENGAJUAN';

        if ($data->save()) {
            $verifikasi = Verifikasi::create([
                'id'              => \Illuminate\Support\Str::uuid()->toString(),
                'verifiable_type' => get_class($data),
                'verifiable_id'   => $data->id,
                'catatan'         => null,
                'status'          => 'PENGAJUAN',
                'user_id'         => Auth::id(),
            ]);

            $users = User::where('level_id', 4)->pluck('id')->toArray();
            if (!empty($users)) {
                $this->help::sendNotification($data, $users, [
                    'title'   => 'Data Informasi Menunggu Verifikasi',
                    'link'    => url('/admin/informasi/'),
                    'icon'    => 'fa fa-info-circle',
                    'color'   => 'text-warning',
                    'content' => $data->nama ?? 'Data informasi masuk'
                ]);
            }

            return response()->json(['status' => true, 'message' => 'Data berhasil dikirim']);
        }

        return response()->json(['status' => false, 'message' => 'Data gagal dikirim']);
    }

    public function verifikasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:DITERIMA,REVISI,DITOLAK',
            'catatan' => 'nullable|string',
        ]);

        $data = $this->model::findOrFail($id);
        $data->status = $request->status;
        $data->catatan = $request->catatan;
        $data->verifikator_id = Auth::id();

        if ($data->save()) {
            Verifikasi::create([
                'id'              => \Illuminate\Support\Str::uuid()->toString(),
                'verifiable_type' => get_class($data),
                'verifiable_id'   => $data->id,
                'catatan'         => $request->catatan,
                'status'          => $request->status,
                'user_id'         => Auth::id(),
            ]);

            $this->help::sendNotification($data, [$data->user_id], [
                'title'   => 'Verifikasi Informasi: ' . $request->status,
                'link'    => url('/admin/informasi'),
                'icon'    => 'fa fa-info-circle',
                'color'   => $request->status == 'DITERIMA' ? 'text-success' : 'text-danger',
                'content' => $data->nama
            ]);

            return response()->json(['status' => true, 'message' => 'Status berhasil diubah']);
        }

        return response()->json(['status' => false, 'message' => 'Status gagal diubah']);
    }
}
