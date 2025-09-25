<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\Galeri;
use App\Models\Unduhan;
use Illuminate\Http\Request;        
use App\Models\Page;
use App\Models\User;                

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
        ]);
    }
}

