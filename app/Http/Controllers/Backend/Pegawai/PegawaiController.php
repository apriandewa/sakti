<?php

namespace App\Http\Controllers\Backend\Pegawai;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Pangkat;
use App\Models\StatusPegawai;
use App\Models\Jabatan;
use App\Models\Page;
use App\Models\Pegawai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PegawaiController extends Controller
{
    public function index() : object
    {
        $gender = Pegawai::select('jenis_kelamin', DB::raw('count(*) as total'))
            ->groupBy('jenis_kelamin')
            ->get();

        $agama = Pegawai::select('agama', DB::raw('count(*) as total'))
            ->groupBy('agama')
            ->get();

        $status = Pegawai::join('statuses', 'pegawais.status_id', '=', 'statuses.id')
            ->select('statuses.nama as label', DB::raw('count(*) as total'))
            ->groupBy('statuses.nama')
            ->get();

        $totalPegawai = Pegawai::count();
        $totalPegawaiLaki = Pegawai::where('jenis_kelamin', 'Laki-laki')->count();
        $totalPegawaiPerempuan = Pegawai::where('jenis_kelamin', 'Perempuan')->count();
        $statusCounts = $status->pluck('total', 'label');
        $totalPNS = $statusCounts->get('PNS', 0);
        $totalCPNS = $statusCounts->get('CPNS', 0);
        $totalPPPK = $statusCounts->get('PPPK', 0);
        $totalPPPKPW = $statusCounts->get('PPPK-PW', 0);

        $pangkat = Pegawai::join('pangkats', 'pegawais.pangkat_id', '=', 'pangkats.id')
            ->select('pangkats.nama as label', DB::raw('count(*) as total'))
            ->groupBy('pangkats.nama')
            ->get();

        $jabatanJenis = Pegawai::join('jabatans as jj', 'pegawais.jabatan_jenis_id', '=', 'jj.id')
            ->select('jj.nama as label', DB::raw('count(*) as total'))
            ->groupBy('jj.nama')
            ->get();

        $jabatanNama = Pegawai::join('jabatans as jn', 'pegawais.jabatan_nama_id', '=', 'jn.id')
            ->select('jn.nama as label', DB::raw('count(*) as total'))
            ->groupBy('jn.nama')
            ->get();

        $bidang = Pegawai::join('pages', 'pegawais.bidang_id', '=', 'pages.id')
            ->select('pages.nama as label', DB::raw('count(*) as total'))
            ->groupBy('pages.nama')
            ->get();

        $pendidikan = Pegawai::select('pendidikan_terakhir', DB::raw('count(*) as total'))
            ->groupBy('pendidikan_terakhir')
            ->get();

        return view($this->view.'.index', [
            'totalPegawai' => $totalPegawai,
            'totalPegawaiLaki' => $totalPegawaiLaki,
            'totalPegawaiPerempuan' => $totalPegawaiPerempuan,
            'totalPNS' => $totalPNS,
            'totalCPNS' => $totalCPNS,
            'totalPPPK' => $totalPPPK,
            'totalPPPKPW' => $totalPPPKPW,
            'pegawaiGenderLabels' => $gender->pluck('jenis_kelamin')->toArray(),
            'pegawaiGenderData' => $gender->pluck('total')->toArray(),
            'pegawaiAgamaLabels' => $agama->pluck('agama')->toArray(),
            'pegawaiAgamaData' => $agama->pluck('total')->toArray(),
            'pegawaiStatusLabels' => $status->pluck('label')->toArray(),
            'pegawaiStatusData' => $status->pluck('total')->toArray(),
            'pegawaiPangkatLabels' => $pangkat->pluck('label')->toArray(),
            'pegawaiPangkatData' => $pangkat->pluck('total')->toArray(),
            'pegawaiJabatanJenisLabels' => $jabatanJenis->pluck('label')->toArray(),
            'pegawaiJabatanJenisData' => $jabatanJenis->pluck('total')->toArray(),
            'pegawaiJabatanNamaLabels' => $jabatanNama->pluck('label')->toArray(),
            'pegawaiJabatanNamaData' => $jabatanNama->pluck('total')->toArray(),
            'pegawaiBidangLabels' => $bidang->pluck('label')->toArray(),
            'pegawaiBidangData' => $bidang->pluck('total')->toArray(),
            'pegawaiPendidikanLabels' => $pendidikan->pluck('pendidikan_terakhir')->toArray(),
            'pegawaiPendidikanData' => $pendidikan->pluck('total')->toArray(),
        ]);
    }

    public function create() : object
    {
        $users = User::select('id', 'first_name', 'last_name')->get()->pluck('name', 'id');
        $pangkats = Pangkat::where('status', 'aktif')->pluck('nama', 'id');
        $statuses = StatusPegawai::where('status', 'aktif')->pluck('nama', 'id');
        $jabatanJenis = Jabatan::whereNull('parent_id')->where('status', 'aktif')->pluck('nama', 'id');
        $bidangs = Page::where('kategori', 'bidang')->where('status', 'aktif')->pluck('nama', 'id');

        return view($this->view.'.create', compact('users', 'pangkats', 'statuses', 'jabatanJenis', 'bidangs'));
    }

    public function getJabatanNama($parent_id) : object
    {
        $jabatans = Jabatan::where('parent_id', $parent_id)->where('status', 'aktif')->pluck('nama', 'id');
        return response()->json($jabatans);
    }

    public function data(Request $request) : object
    {
        $data = $this->model::with(['user', 'statusPegawai', 'pangkat', 'jabatanJenis', 'jabatanNama', 'bidang']);
        $user = $request->user();
        return datatables()->of($data)
            ->editColumn('status', function ($data) {
                return $data->status == 'aktif' ? '<span class="badge badge-success">Aktif</span>' : '<span class="badge badge-danger">Tidak Aktif</span>';
            })
            ->addColumn('action', function ($data) use ($user) {
                $button ='';
                if($user->read){
                    $button .= '<a href="'.url(config('master.app.url.backend').'/'.$this->url.'/'.$data->id).'" class="btn btn-sm btn-outline" title="Detail"><i class="fa fa-eye text-info"></i></a>';
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
            ->rawColumns(['action','status'])
            ->make();
    }

    public function store(Request $request) : object
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'nip' => 'nullable|string|max:50|unique:pegawais,nip',
            'nik' => 'nullable|string|max:50|unique:pegawais,nik',
            'status_id' => 'required|exists:statuses,id',
            'pangkat_id' => 'required|exists:pangkats,id',
            'jabatan_jenis_id' => 'required|exists:jabatans,id',
            'jabatan_nama_id' => 'required|exists:jabatans,id',
            'bidang_id' => 'required|exists:pages,id',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|string|max:50',
            'pendidikan_terakhir' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telpon' => 'required|string|max:50',
            'foto_pegawai' => 'nullable|image|mimes:jpg,png|max:1024',
            'spesimen_tte' => 'nullable|image|mimes:jpg,png|max:1024',
            'status' => 'required|in:aktif,tidak aktif',
            'periode' => 'required|string|max:10',
        ]);

        if ($pegawai = $this->model::create($request->all())) {
            // handle foto_pegawai
            if ($request->hasFile('foto_pegawai')) {
                $pegawai->file()->create([
                    'data' => [
                        'name' => $request->file('foto_pegawai')->getClientOriginalName(),
                        'disk' => config('filesystems.default'),
                        'target' => Storage::disk(config('filesystems.default'))->putFile('pegawai/foto', $request->file('foto_pegawai')),
                    ],
                    'alias' => 'foto_pegawai'
                ]);
            }
            // handle spesimen_tte
            if ($request->hasFile('spesimen_tte')) {
                $pegawai->file()->create([
                    'data' => [
                        'name' => $request->file('spesimen_tte')->getClientOriginalName(),
                        'disk' => config('filesystems.default'),
                        'target' => Storage::disk(config('filesystems.default'))->putFile('pegawai/tte', $request->file('spesimen_tte')),
                    ],
                    'alias' => 'spesimen_tte'
                ]);
            }
            $response = ['status' => TRUE, 'message' => 'Data berhasil disimpan'];
        }
        return response()->json($response ?? ['status' => FALSE, 'message' => 'Data gagal disimpan']);
    }

    public function show($id) : object
    {
        $data = $this->model::with(['user', 'statusPegawai', 'pangkat', 'jabatanJenis', 'jabatanNama', 'bidang'])->findOrFail($id);
        $historyTte = \App\Models\DokumenTte::with('agendaRapat')
            ->where('pegawai_id', $id)
            ->latest()
            ->paginate(10);
        return view($this->view.'.show', compact('data', 'historyTte'));
    }

    public function edit($id) : object
    {
        $data = $this->model::find($id);
        $users = User::select('id', 'first_name', 'last_name')->get()->pluck('name', 'id');
        $pangkats = Pangkat::where('status', 'aktif')->pluck('nama', 'id');
        $statuses = StatusPegawai::where('status', 'aktif')->pluck('nama', 'id');
        $jabatanJenis = Jabatan::whereNull('parent_id')->where('status', 'aktif')->pluck('nama', 'id');
        $jabatanNama = Jabatan::where('parent_id', $data->jabatan_jenis_id)->where('status', 'aktif')->pluck('nama', 'id');
        $bidangs = Page::where('kategori', 'bidang')->where('status', 'aktif')->pluck('nama', 'id');

        return view($this->view.'.edit', compact('data', 'users', 'pangkats', 'statuses', 'jabatanJenis', 'jabatanNama', 'bidangs'));
    }

    public function update(Request $request, $id) : object
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'gelar_depan' => 'nullable|string|max:50',
            'gelar_belakang' => 'nullable|string|max:50',
            'nip' => 'nullable|string|max:50|unique:pegawais,nip,'.$id,
            'nik' => 'nullable|string|max:50|unique:pegawais,nik,'.$id,
            'status_id' => 'required|exists:statuses,id',
            'pangkat_id' => 'required|exists:pangkats,id',
            'jabatan_jenis_id' => 'required|exists:jabatans,id',
            'jabatan_nama_id' => 'required|exists:jabatans,id',
            'bidang_id' => 'required|exists:pages,id',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'agama' => 'required|string|max:50',
            'pendidikan_terakhir' => 'required|string|max:255',
            'alamat' => 'required|string',
            'telpon' => 'required|string|max:50',
            'foto_pegawai' => 'nullable|image|mimes:jpg,png|max:1024',
            'spesimen_tte' => 'nullable|image|mimes:jpg,png|max:1024',
            'status' => 'required|in:aktif,tidak aktif',
            'periode' => 'required|string|max:10',
        ]);

        $data = $this->model::find($id);
        if ($data->update($request->all())) {
            // handle foto_pegawai
            if ($request->hasFile('foto_pegawai')) {
                foreach ($data->file()->where('alias', 'foto_pegawai')->get() as $f) {
                    $f->delete();
                }
                $data->file()->create([
                    'data' => [
                        'name' => $request->file('foto_pegawai')->getClientOriginalName(),
                        'disk' => config('filesystems.default'),
                        'target' => Storage::disk(config('filesystems.default'))->putFile('pegawai/foto', $request->file('foto_pegawai')),
                    ],
                    'alias' => 'foto_pegawai'
                ]);
            }
            // handle spesimen_tte
            if ($request->hasFile('spesimen_tte')) {
                foreach ($data->file()->where('alias', 'spesimen_tte')->get() as $f) {
                    $f->delete();
                }
                $data->file()->create([
                    'data' => [
                        'name' => $request->file('spesimen_tte')->getClientOriginalName(),
                        'disk' => config('filesystems.default'),
                        'target' => Storage::disk(config('filesystems.default'))->putFile('pegawai/tte', $request->file('spesimen_tte')),
                    ],
                    'alias' => 'spesimen_tte'
                ]);
            }
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
        // Delete related files
        foreach ($data->file as $f) {
            $f->delete();
        }
        if($data->delete()){
            $response = ['status' => TRUE, 'message' => 'Data berhasil dihapus'];
        }
        return response()->json($response ?? ['status' => FALSE, 'message' => 'Data gagal dihapus']);
    }
}
