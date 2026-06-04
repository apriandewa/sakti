<?php

namespace App\Http\Controllers\Backend\Galeri;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use App\Models\Galeri;
use App\Models\Kategori;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\Verifikasi;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GaleriController extends Controller
{
    public function index()
    {
        return view($this->view.'.index');
    }

    public function create()
    {
        $parentKategori = Kategori::where('slug', 'galeri')->first();
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
            $data = collect();
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
                                        data-url="' . route('galeri.kirim', $data->id) . '"
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
            ->rawColumns(['action', 'status_badge'])
            ->make();
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'       => 'required',
            'slug'       => 'required',
            'desc'       => 'required',
            'kategori'   => 'required',
            'keterangan' => 'required',

            // Logo (single)
            'logo'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // Galeri (multiple)
            'galeri'     => 'nullable|array',
            'galeri.*'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();

        try {
            // Bersihkan script di desc
            $request->merge([
                'desc' => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->desc),
            ]);

            // Simpan hanya field non-file
            $payload              = $request->except(['logo', 'galeri']);
            $payload['user_id']   = $request->user()->id;
            $payload['status']    = 'DRAFT';

            $galeri = $this->model::create($payload);

            /*
            |--------------------------------------------------------------------------
            | Upload Logo (single)
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('logo')) {
                $this->uploadLogo($galeri, $request->file('logo'));
            }

            /*
            |--------------------------------------------------------------------------
            | Upload Galeri (multiple)
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('galeri')) {
                $this->uploadGaleri($galeri, $request->file('galeri'));
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil disimpan',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $data = $this->model::find($id);

        $histori_verifikasi = \App\Models\Verifikasi::with('user')
            ->where('verifiable_id', $id)
            ->orderBy('updated_at', 'asc')
            ->get();

        return view($this->view.'.show', compact('data', 'histori_verifikasi'));
    }

    public function edit($id)
    {
        $data = $this->model::find($id);
        $parentKategori = Kategori::where('slug', 'galeri')->first();
        $kategoris = $parentKategori
            ? Kategori::where('parent_id', $parentKategori->id)->pluck('nama', 'nama')
            : collect();
        return view($this->view.'.edit', compact('data', 'kategoris'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama'           => 'required',
            'slug'           => 'required',
            'desc'           => 'required',
            'kategori'       => 'required',
            'keterangan'     => 'required',

            // Logo (single)
            'logo'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // Galeri (multiple)
            'galeri'         => 'nullable|array',
            'galeri.*'       => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // Hapus galeri tertentu
            'delete_galeri'  => 'nullable|array',
            'delete_galeri.*'=> 'nullable|uuid',
        ]);

        DB::beginTransaction();

        try {
            $data = $this->model::findOrFail($id);

            // Bersihkan script di desc
            $request->merge([
                'desc' => preg_replace('#<script(.*?)>(.*?)</script>#is', '', $request->desc),
            ]);

            $data->update([
                ...$request->except(['logo', 'galeri', 'delete_galeri']),
                'user_id' => $request->user()->id,
                'status'  => 'DRAFT',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Hapus Galeri yang Dipilih
            |--------------------------------------------------------------------------
            */
            if ($request->filled('delete_galeri')) {
                foreach ($request->delete_galeri as $fileId) {
                    $fileRecord = $data->files()
                        ->where('id', $fileId)
                        ->where('alias', 'galeri_gambar')
                        ->first();

                    if ($fileRecord) {
                        $this->deleteFileRecord($fileRecord);
                    }
                }
            }

            /*
            |--------------------------------------------------------------------------
            | Replace Logo (hapus lama, upload baru)
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('logo')) {
                $oldLogo = $data->files()
                    ->where('alias', 'logo')
                    ->first();

                if ($oldLogo) {
                    $this->deleteFileRecord($oldLogo);
                }

                $this->uploadLogo($data, $request->file('logo'));
            }

            /*
            |--------------------------------------------------------------------------
            | Tambah Galeri Baru (multiple)
            |--------------------------------------------------------------------------
            */
            if ($request->hasFile('galeri')) {
                $this->uploadGaleri($data, $request->file('galeri'));
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil diperbarui',
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function delete($id)
    {
        $data = $this->model::find($id);
        return view($this->view.'.delete', compact('data'));
    }

    public function destroy($id)
    {
        $data = $this->model::findOrFail($id);

        /*
        |--------------------------------------------------------------------------
        | Hapus semua file terkait
        |--------------------------------------------------------------------------
        */
        foreach ($data->files as $file) {
            $this->deleteFileRecord($file);
        }

        $data->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Data berhasil dihapus',
        ]);
    }

    public function kirim($id)
    {
        $data = $this->model::find($id);

        if (!$data) {
            return response()->json([
                'status'  => false,
                'message' => 'Data tidak ditemukan',
            ]);
        }

        $data->status = 'VERIFIKASI';

        if ($data->save()) {

            $verifikasi = Verifikasi::create([
                'id'              => \Illuminate\Support\Str::uuid()->toString(),
                'verifiable_type' => get_class($data),
                'verifiable_id'   => $data->id,
                'catatan'         => null,
                'status'          => 'VERIFIKASI',
                'user_id'         => Auth::id(),
            ]);

            $users = User::where('level_id', 4)->pluck('id')->toArray();

            if (!empty($users)) {
                $this->help::sendNotification($data, $users, [
                    'title'   => 'Data Baru Menunggu Verifikasi',
                    'link'    => url('/admin/verifikasi/'),
                    'icon'    => 'fa fa-image',
                    'color'   => 'text-warning',
                    'content' => $data->nama ?? 'Data baru masuk',
                ]);
            }

            return response()->json([
                'status'  => true,
                'message' => 'Data berhasil dikirim dan menunggu verifikasi',
            ]);
        }

        return response()->json([
            'status'  => false,
            'message' => 'Data gagal dikirim',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PRIVATE HELPERS
    |--------------------------------------------------------------------------
    */

    /**
     * Upload logo tunggal dan simpan ke relasi file().
     */
    private function uploadLogo($galeri, UploadedFile $file)
    {
        $disk = config('filesystems.default');

        $path = Storage::disk($disk)->putFile('galeri/logo', $file);

        if (!$path) {
            return;
        }

        $galeri->files()->create([
            'alias' => 'logo',
            'name'  => $file->getClientOriginalName(),
            'data'  => [
                'name'   => $file->getClientOriginalName(),
                'disk'   => $disk,
                'target' => $path,
            ],
        ]);
    }

    /**
     * Upload banyak gambar galeri dan simpan ke relasi files().
     */
    private function uploadGaleri($galeri, $files)
    {
        if (!is_array($files)) {
            $files = [$files];
        }

        $disk = config('filesystems.default');

        foreach ($files as $file) {

            if (!$file instanceof UploadedFile) {
                continue;
            }

            if (!$file->isValid()) {
                continue;
            }

            $path = Storage::disk($disk)->putFile('galeri/gambar', $file);

            if (!$path) {
                continue;
            }

            $galeri->files()->create([
                'alias' => 'galeri_gambar',
                'name'  => $file->getClientOriginalName(),
                'data'  => [
                    'name'   => $file->getClientOriginalName(),
                    'disk'   => $disk,
                    'target' => $path,
                ],
            ]);
        }
    }

    /**
     * Hapus record file dari storage dan database.
     */
    private function deleteFileRecord($fileRecord)
    {
        if (!$fileRecord) {
            return;
        }

        $disk   = $fileRecord->data['disk']   ?? config('filesystems.default');
        $target = $fileRecord->data['target'] ?? null;

        if ($target && Storage::disk($disk)->exists($target)) {
            Storage::disk($disk)->delete($target);
        }

        $fileRecord->delete();
    }
}
