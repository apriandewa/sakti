<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Unduhan;
use Illuminate\Http\Request;        
use App\Models\Page;
use App\Models\User;
use App\Models\Pegawai;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $countberita = Berita::where('status','TERVERIFIKASI')->count();
        $countgaleri = Galeri::where('status','TERVERIFIKASI')->count();
        $countunduhan = Unduhan::where('status','TERVERIFIKASI')->count();
        // $counthalaman = Page::all()->count();
        $beritasaya = Berita::where('status', 'TERVERIFIKASI')
                        ->where('user_id', auth()->id())
                        ->count();
        $galerisaya = Galeri::where('status','TERVERIFIKASI')->where('user_id', auth()->id())->count();
        $unduhansaya = Unduhan::where('status','TERVERIFIKASI')->where('user_id', auth()->id())->count();
        // $totalpengunjung = User
        // pegawai statistics for charts
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

        return view($this->view . '.index', [
         'title' => 'Halaman Dahsboard',
        //  'user' => auth()->name,
         'jml_berita' => $countberita,
         'jml_galeri' => $countgaleri,
         'jml_unduhan' => $countunduhan,
        //  'jml_halaman' => $counthalaman,
        //  'jml_pengunjung' => $totalpengunjung,
         'berita_saya' => $beritasaya,
         'galeri_saya' => $galerisaya,
         'unduhan_saya' => $unduhansaya,
         // pegawai chart data (pass as arrays)
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
        ]);
    }
}

