<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

use Illuminate\Http\Request;

class InformasiController extends Controller
{
   public function index() {
    $data1 = [];
    $data2 = [];
    $sources = [
        'JDIH Kabupaten Indragiri Hulu' => [
            'api' => 'https://jdih.inhukab.go.id/integrasijdihinhu.php',
            'site' => 'https://jdih.inhukab.go.id/',
        ],
        'JDIH DPRD Indragiri Hulu' => [
            'api' => 'https://jdihdprd.inhukab.go.id/integrasijdihdprd.php',
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


}
