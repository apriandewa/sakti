<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Statistik;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;

class InformasiController extends Controller
{
   public function index() {
    $data1 = [];
    $data2 = [];
    $sources = [
        'JDIH Kabupaten Indragiri Hulu' => [
            'api' => 'https://api-splp.layanan.go.id/jdih-inhu/1.0/integrasijdihinhu',
            'site' => 'https://jdih.inhukab.go.id/',
        ],
        'JDIH DPRD Indragiri Hulu' => [
            'api' => 'https://api-splp.layanan.go.id/jdih-dprd-indragiri-hulu/1.0/integrasijdihdprd',
            'site' => 'https://jdihdprd.inhukab.go.id/',
        ],
    ];

    $fetchData = function($key, $url, $sourceName, $siteUrl) {
        if (Cache::has($key)) {
            return Cache::get($key);
        }
        try {
            $response = Http::timeout(5)->get($url);
            if ($response->successful()) {
                $data = collect($response->json())->map(function ($item) use ($sourceName, $siteUrl) {
                    $item['source_name'] = $sourceName;
                    $item['source_url']  = $siteUrl;
                    return $item;
                })->toArray();
                if (!empty($data)) {
                    Cache::put($key, $data, 3600);
                }
                return $data;
            }
        } catch (\Exception $e) {}
        return [];
    };

    $data1 = $fetchData('cache_jdih_inhu_data', $sources['JDIH Kabupaten Indragiri Hulu']['api'], 'JDIH Kabupaten Indragiri Hulu', $sources['JDIH Kabupaten Indragiri Hulu']['site']);
    $data2 = $fetchData('cache_jdih_dprd_inhu_data', $sources['JDIH DPRD Indragiri Hulu']['api'], 'JDIH DPRD Indragiri Hulu', $sources['JDIH DPRD Indragiri Hulu']['site']);

    // Gabungkan & balik urutan supaya terbaru di atas
    $apiData = array_reverse(array_merge($data1, $data2));

    return view('frontend.informasi.index', [
        "title" => "PPID Indragiri Hulu",
        "judul" => "Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu",
        "subjudul" => "Website Resmi PPID Kabupaten Indragiri Hulu",
        'apiData' => new \Illuminate\Pagination\LengthAwarePaginator(
            collect($apiData)->forPage(request()->get('page', 1), 10)->values(),
            count($apiData),
            10,
            request()->get('page', 1),
            ['path' => request()->url(), 'query' => request()->query()]
        ),
        'apiTotal' => count($apiData),
    ]);
}

public function tersedia(Request $request)
{
    $sources = [
        'JDIH Kabupaten Indragiri Hulu' => [
            'api'  => 'https://api-splp.layanan.go.id/jdih-inhu/1.0/integrasijdihinhu',
            'site' => 'https://jdih.inhukab.go.id/',
        ],
        'JDIH DPRD Indragiri Hulu' => [
            'api'  => 'https://api-splp.layanan.go.id/jdih-dprd-indragiri-hulu/1.0/integrasijdihdprd',
            'site' => 'https://jdihdprd.inhukab.go.id/',
        ],
    ];

    $fetchData = function($key, $url, $sourceName, $siteUrl) {
        if (Cache::has($key)) {
            return collect(Cache::get($key));
        }
        try {
            $response = Http::timeout(5)->get($url);
            if ($response->successful()) {
                $data = collect($response->json())->map(function ($item) use ($sourceName, $siteUrl) {
                    $item['source_name'] = $sourceName;
                    $item['source_url']  = $siteUrl;
                    return $item;
                });
                if ($data->isNotEmpty()) {
                    Cache::put($key, $data->toArray(), 3600);
                }
                return $data;
            }
        } catch (\Exception $e) {}
        return collect();
    };

    $data1 = $fetchData('cache_jdih_inhu_data', $sources['JDIH Kabupaten Indragiri Hulu']['api'], 'JDIH Kabupaten Indragiri Hulu', $sources['JDIH Kabupaten Indragiri Hulu']['site']);
    $data2 = $fetchData('cache_jdih_dprd_inhu_data', $sources['JDIH DPRD Indragiri Hulu']['api'], 'JDIH DPRD Indragiri Hulu', $sources['JDIH DPRD Indragiri Hulu']['site']);

    // Gabungkan & balik urutan supaya terbaru di atas
    $allData = $data1->merge($data2)->reverse()->values();

    // ✅ Filter pencarian
    $search = $request->get('search');
    if (!empty($search)) {
        $keyword = strtolower($search);
        $allData = $allData->filter(function ($item) use ($keyword) {
            $inJudul = str_contains(strtolower($item['judul'] ?? ''), $keyword);
            $inTahun = str_contains(strtolower((string)($item['tahun_pengundangan'] ?? '')), $keyword);
            return $inJudul || $inTahun;
        })->values();
    }

    $perPage     = 10;
    $currentPage = $request->get('page', 1);
    $total       = $allData->count();

    return view('frontend.informasi.tersedia', [
        'title'    => 'PPID Indragiri Hulu',
        'judul'    => 'Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu',
        'subjudul' => 'Website Resmi PPID Kabupaten Indragiri Hulu',
        'apiData'  => new \Illuminate\Pagination\LengthAwarePaginator(
            $allData->forPage($currentPage, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            [
                'path'  => $request->url(),
                'query' => $request->query(), // ✅ bawa ?search= saat ganti halaman
            ]
        ),
        'apiTotal' => $total,
    ]);
}

public function berkala(Request $request)
{
    $sourceName = 'Transparansi Anggaran Kabupaten Indragiri Hulu';
    $apiUrl     = 'https://api-splp.layanan.go.id/transparansi_anggaran_indragiri_hulu/1.0/api/dokumenapi';
    $siteUrl    = 'https://transparansi.inhukab.go.id/';

    if (Cache::has('cache_info_berkala_data')) {
        $allData = collect(Cache::get('cache_info_berkala_data'));
    } else {
        $allData = collect();
        try {
            $response = Http::timeout(5)->get($apiUrl);
            if ($response->successful()) {
                $fetched = collect($response->json()['data'] ?? [])
                    ->map(function ($item) use ($sourceName, $siteUrl) {
                        if (isset($item['berkas'])) {
                            $item['berkas'] = json_decode($item['berkas'], true);
                        }
                        $item['source_name'] = $sourceName;
                        $item['source_url']  = $siteUrl;
                        return $item;
                    })
                    ->sortByDesc('created_at')
                    ->values()
                    ->toArray();
                
                if (!empty($fetched)) {
                    Cache::put('cache_info_berkala_data', $fetched, 3600);
                }
                $allData = collect($fetched);
            }
        } catch (\Exception $e) {}
    }

    // Filter pencarian
    $search = $request->get('search');
    if (!empty($search)) {
        $keyword = strtolower($search);
        $allData = $allData->filter(function ($item) use ($keyword) {
            $inName  = str_contains(strtolower($item['name']  ?? ''), $keyword);
            $inTahun = str_contains(strtolower((string)($item['tahun'] ?? '')), $keyword);
            return $inName || $inTahun;
        })->values();
    }

    $perPage     = 10;
    $currentPage = $request->get('page', 1);
    $total       = $allData->count();

    return view('frontend.informasi.berkala', [
        'title'    => 'Informasi Berkala - PPID Indragiri Hulu',
        'judul'    => 'Informasi Berkala',
        'subjudul' => 'Informasi Berkala PPID Kabupaten Indragiri Hulu',
        'apiData'  => new \Illuminate\Pagination\LengthAwarePaginator(
            $allData->forPage($currentPage, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        ),
        'apiTotal' => $total,
    ]);
}

public function statistik() {
    return view('frontend.informasi.statistik', [
        "title" => "Statistik - PPID Indragiri Hulu",
        "judul" => "Statistik Layanan Informasi",
        "subjudul" => "Statistik Layanan Informasi PPID Kabupaten Indragiri Hulu",
        'statistik' => Statistik::all(),


   ]);
}

}