<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Statistik;
use Illuminate\Support\Facades\Http;

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

    try {
        $response1 = Http::timeout(30)->retry(3, 2000)->get($sources['JDIH Kabupaten Indragiri Hulu']['api']);
        if ($response1->successful()) {
            $data1 = collect($response1->json())->map(function ($item) use ($sources) {
                $item['source_name'] = 'JDIH Kabupaten Indragiri Hulu';
                $item['source_url']  = $sources['JDIH Kabupaten Indragiri Hulu']['site'];
                return $item;
            })->toArray();
        }
    } catch (\Exception $e) {
        $data1 = [];
    }

    try {
        $response2 = Http::timeout(30)->retry(3, 2000)->get($sources['JDIH DPRD Indragiri Hulu']['api']);
        if ($response2->successful()) {
            $data2 = collect($response2->json())->map(function ($item) use ($sources) {
                $item['source_name'] = 'JDIH DPRD Indragiri Hulu';
                $item['source_url']  = $sources['JDIH DPRD Indragiri Hulu']['site'];
                return $item;
            })->toArray();
        }
    } catch (\Exception $e) {
        $data2 = [];
    }

    // Gabungkan & balik urutan supaya terbaru di atas
    $apiData = array_reverse(array_merge($data1 ?? [], $data2 ?? []));

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

public function tersedia() {
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

    try {
        $response1 = Http::timeout(30)->retry(3, 2000)->get($sources['JDIH Kabupaten Indragiri Hulu']['api']);
        if ($response1->successful()) {
            $data1 = collect($response1->json())->map(function ($item) use ($sources) {
                $item['source_name'] = 'JDIH Kabupaten Indragiri Hulu';
                $item['source_url']  = $sources['JDIH Kabupaten Indragiri Hulu']['site'];
                return $item;
            })->toArray();
        }
    } catch (\Exception $e) {
        $data1 = [];
    }

    try {
        $response2 = Http::timeout(30)->retry(3, 2000)->get($sources['JDIH DPRD Indragiri Hulu']['api']);
        if ($response2->successful()) {
            $data2 = collect($response2->json())->map(function ($item) use ($sources) {
                $item['source_name'] = 'JDIH DPRD Indragiri Hulu';
                $item['source_url']  = $sources['JDIH DPRD Indragiri Hulu']['site'];
                return $item;
            })->toArray();
        }
    } catch (\Exception $e) {
        $data2 = [];
    }

    // Gabungkan & balik urutan supaya terbaru di atas
    $apiData = array_reverse(array_merge($data1 ?? [], $data2 ?? []));

    return view('frontend.informasi.tersedia', [
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

public function berkala()
{
    $data1 = [];
    $data2 = [];

    $sources = [
        'Transparansi Anggaran Kabupaten Indragiri Hulu' => [
            'api'  => 'https://api-splp.layanan.go.id/transparansi_anggaran_indragiri_hulu/1.0/api/dokumenapi',
            'site' => 'https://transparansi.inhukab.go.id/',
        ],
        'Portal Satu Data Kabupaten Indragiri Hulu' => [
            'api'  => 'https://jdihdprd.inhukab.go.id/integrasijdihdprd.php',
            'site' => 'https://pasti.inhukab.go.id/',
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | SOURCE 1 - SPLP Transparansi
    |--------------------------------------------------------------------------
    */
    try {
        $response1 = Http::timeout(30)
            ->retry(3, 2000)
            ->get($sources['Transparansi Anggaran Kabupaten Indragiri Hulu']['api']);

        if ($response1->successful()) {

            $json = $response1->json();

            $list = $json['data'] ?? [];

            $data1 = collect($list)->map(function ($item) use ($sources) {

                // decode berkas agar jadi array
                if (isset($item['berkas'])) {
                    $item['berkas'] = json_decode($item['berkas'], true);
                }

                $item['source_name'] = 'Transparansi Anggaran Kabupaten Indragiri Hulu';
                $item['source_url']  = $sources['Transparansi Anggaran Kabupaten Indragiri Hulu']['site'];

                return $item;

            })->toArray();
        }

    } catch (\Exception $e) {
        $data1 = [];
    }

    /*
    |--------------------------------------------------------------------------
    | SOURCE 2 - Portal Satu Data
    |--------------------------------------------------------------------------
    */
    try {
        $response2 = Http::timeout(30)
            ->retry(3, 2000)
            ->get($sources['Portal Satu Data Kabupaten Indragiri Hulu']['api']);

        if ($response2->successful()) {

            $json = $response2->json();

            $list = is_array($json) ? $json : ($json['data'] ?? []);

            $data2 = collect($list)->map(function ($item) use ($sources) {

                $item['source_name'] = 'Portal Satu Data Kabupaten Indragiri Hulu';
                $item['source_url']  = $sources['Portal Satu Data Kabupaten Indragiri Hulu']['site'];

                return $item;

            })->toArray();
        }

    } catch (\Exception $e) {
        $data2 = [];
    }

    /*
    |--------------------------------------------------------------------------
    | Merge & Sort
    |--------------------------------------------------------------------------
    */
    $apiData = collect(array_merge($data1, $data2))
        ->sortByDesc('created_at')
        ->values()
        ->toArray();

    return view('frontend.informasi.berkala', [
        "title"    => "Informasi Berkala - PPID Indragiri Hulu",
        "judul"    => "Informasi Berkala",
        "subjudul" => "Informasi Berkala PPID Kabupaten Indragiri Hulu",
        'apiData'  => new \Illuminate\Pagination\LengthAwarePaginator(
            collect($apiData)->forPage(request()->get('page', 1), 10)->values(),
            count($apiData),
            10,
            request()->get('page', 1),
            [
                'path'  => request()->url(),
                'query' => request()->query()
            ]
        ),
        'apiTotal' => count($apiData),
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