<?php

namespace App\Http\Controllers\Frontend;
use App\Http\Controllers\Controller;
use App\Models\Statistik;
use App\Models\Informasi;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

use Illuminate\Http\Request;

class InformasiController extends Controller
{
   public function index(Request $request) {
    $sources = [
        'JDIH Kabupaten Indragiri Hulu' => [
            'api'  => 'https://api-splp.layanan.go.id/jdih-inhu/1.0/integrasijdihinhu',
            'site' => 'https://jdih.inhukab.go.id/',
            'type' => 'jdih',  // response: array of objects, field: judul, tanggal_pengundangan
        ],
        'JDIH DPRD Indragiri Hulu' => [
            'api'  => 'https://api-splp.layanan.go.id/jdih-dprd-indragiri-hulu/1.0/integrasijdihdprd',
            'site' => 'https://jdihdprd.inhukab.go.id/',
            'type' => 'jdih',
        ],
        'Transparansi Anggaran Indragiri Hulu' => [
            'api'  => 'https://api-splp.layanan.go.id/transparansi_anggaran_indragiri_hulu/1.0/api/dokumenapi',
            'site' => 'https://transparansi.inhukab.go.id/',
            'type' => 'transparansi',  // response: {data: [...]} field: name, tahun, created_at
        ],
    ];

    // Fetch & normalisasi semua sumber API ke skema yang sama
    $allApiData = collect();

    // Cache key map: reuse same keys as berkala() to avoid duplicate HTTP calls
    $cacheKeyMap = [
        'JDIH Kabupaten Indragiri Hulu'       => 'cache_jdih_inhu_data',
        'JDIH DPRD Indragiri Hulu'             => 'cache_jdih_dprd_inhu_data',
        'Transparansi Anggaran Indragiri Hulu' => 'cache_info_berkala_data',
    ];

    foreach ($sources as $sourceName => $source) {
        $cacheKey = $cacheKeyMap[$sourceName] ?? ('cache_api_' . md5($sourceName));

        // --- Coba dari cache dulu ---
        if (Cache::has($cacheKey)) {
            $cached = collect(Cache::get($cacheKey));

            // Normalisasi cache lama (dari berkala) ke skema index jika perlu
            if ($source['type'] === 'transparansi') {
                $normalized = $cached->map(function ($item) use ($sourceName, $source) {
                    // Jika sudah dinormalisasi (ada judul), langsung pakai
                    if (isset($item['judul'])) return $item;

                    // Format dari berkala: name, tahun, created_at, berkas
                    $berkas = $item['berkas'] ?? [];
                    if (is_string($berkas)) $berkas = json_decode($berkas, true) ?? [];
                    $urlFirst = '#';
                    $berkasFiles = [];
                    if (!empty($berkas)) {
                        foreach ($berkas as $file) {
                            $fileUrl = is_string($file) && str_starts_with($file, 'http')
                                ? $file
                                : $source['site'] . 'storage/' . $file;
                            $berkasFiles[] = ['name' => basename($file), 'url' => $fileUrl];
                        }
                        $urlFirst = $berkasFiles[0]['url'] ?? '#';
                    }
                    return [
                        'judul'                => $item['name'] ?? '-',
                        'tahun_pengundangan'   => $item['tahun'] ?? null,
                        'tanggal_pengundangan' => isset($item['created_at']) ? substr($item['created_at'], 0, 10) : null,
                        'created_at_raw'       => $item['created_at'] ?? null,
                        'fileDownload'         => !empty($berkasFiles) ? 'Lihat Dokumen' : 'Tidak ada file',
                        'urlDownload'          => $urlFirst,
                        'berkas_files'         => $berkasFiles,
                        'source_name'          => $sourceName,
                        'source_url'           => $source['site'],
                        'tipe_label'           => 'TRANSPARANSI',
                    ];
                });
                $allApiData = $allApiData->merge($normalized);
            } else {
                // JDIH: pastikan ada created_at_raw
                $normalized = $cached->map(function ($item) use ($sourceName, $source) {
                    if (!isset($item['created_at_raw'])) {
                        $item['created_at_raw'] = $item['tanggal_pengundangan'] ?? null;
                    }
                    if (!isset($item['source_name'])) $item['source_name'] = $sourceName;
                    if (!isset($item['source_url']))  $item['source_url']  = $source['site'];
                    if (!isset($item['tipe_label']))  $item['tipe_label']  = 'JDIH';
                    return $item;
                });
                $allApiData = $allApiData->merge($normalized);
            }
            continue;
        }

        // --- Fetch dari API ---
        try {
            $response = Http::timeout(8)
                ->withHeaders(['Accept' => 'application/json'])
                ->get($source['api']);

            $json = $response->json();

            // Ambil array data sesuai struktur response
            if ($source['type'] === 'transparansi') {
                $items = collect($json['data'] ?? []);
            } else {
                $items = is_array($json) && !isset($json['code']) ? collect($json) : collect();
            }

            if ($items->isEmpty()) continue;

            // Normalisasi ke skema seragam
            $normalized = $items->map(function ($item) use ($sourceName, $source) {
                if ($source['type'] === 'transparansi') {
                    $berkas = $item['berkas'] ?? [];
                    if (is_string($berkas)) $berkas = json_decode($berkas, true) ?? [];
                    $urlFirst = '#';
                    $berkasFiles = [];
                    if (!empty($berkas)) {
                        foreach ($berkas as $file) {
                            $fileUrl = is_string($file) && str_starts_with($file, 'http')
                                ? $file
                                : $source['site'] . 'storage/' . $file;
                            $berkasFiles[] = ['name' => basename($file), 'url' => $fileUrl];
                        }
                        $urlFirst = $berkasFiles[0]['url'] ?? '#';
                    }
                    return [
                        'judul'                => $item['name'] ?? '-',
                        'tahun_pengundangan'   => $item['tahun'] ?? null,
                        'tanggal_pengundangan' => isset($item['created_at']) ? substr($item['created_at'], 0, 10) : null,
                        'created_at_raw'       => $item['created_at'] ?? null,
                        'fileDownload'         => !empty($berkasFiles) ? 'Lihat Dokumen' : 'Tidak ada file',
                        'urlDownload'          => $urlFirst,
                        'berkas_files'         => $berkasFiles,
                        'source_name'          => $sourceName,
                        'source_url'           => $source['site'],
                        'tipe_label'           => 'TRANSPARANSI',
                    ];
                } else {
                    return array_merge($item, [
                        'source_name'    => $sourceName,
                        'source_url'     => $source['site'],
                        'tipe_label'     => 'JDIH',
                        'created_at_raw' => $item['tanggal_pengundangan'] ?? null,
                    ]);
                }
            });

            Cache::put($cacheKey, $normalized->toArray(), 3600);
            $allApiData = $allApiData->merge($normalized);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Gagal fetch API [{$sourceName}]: " . $e->getMessage());
        }
    }


    // Ambil semua data DB dengan status DITERIMA (semua tipe)
    $dbData = Informasi::with('files')
        ->where('status', 'DITERIMA')
        ->latest()
        ->get()
        ->map(function ($item) {
            $berkasFiles = $item->files->where('alias', 'berkas_informasi');
            $dbFiles = $berkasFiles->map(function($f) {
                return [
                    'name' => $f->name,
                    'url'  => url($f->public_stream)
                ];
            })->values()->toArray();

            $urlDownload = $berkasFiles->count() > 0 ? url($berkasFiles->first()->public_stream) : '#';
            $fileName    = $berkasFiles->count() > 0 ? $berkasFiles->first()->name : 'Tidak ada file';

            return [
                'judul'                => $item->nama,
                'tahun_pengundangan'   => $item->tahun,
                'tanggal_pengundangan' => $item->created_at ? $item->created_at->format('Y-m-d') : null,
                'created_at_raw'       => $item->created_at ? $item->created_at->toDateTimeString() : null,
                'fileDownload'         => $fileName,
                'urlDownload'          => $urlDownload,
                'source_name'          => 'PPID Kabupaten Indragiri Hulu',
                'source_url'           => url('/'),
                'tipe_label'           => $item->tipe,
                'is_db'                => true,
                'db_files'             => $dbFiles,
            ];
        });

    // Gabungkan API + DB, urutkan terbaru di atas
    $allData = $allApiData->merge($dbData)
        ->sortByDesc('created_at_raw')
        ->values();

    // Filter pencarian
    $search = $request->get('search');
    if (!empty($search)) {
        $keyword = strtolower($search);
        $allData = $allData->filter(function ($item) use ($keyword) {
            $inJudul = str_contains(strtolower($item['judul'] ?? ''), $keyword);
            $inTahun = str_contains(strtolower((string)($item['tahun_pengundangan'] ?? '')), $keyword);
            $inTipe  = str_contains(strtolower($item['tipe_label'] ?? ''), $keyword);
            return $inJudul || $inTahun || $inTipe;
        })->values();
    }

    $perPage     = 12;
    $currentPage = $request->get('page', 1);
    $total       = $allData->count();

    return view('frontend.informasi.index', [
        'title'    => 'Daftar Informasi Publik - PPID Indragiri Hulu',
        'judul'    => 'Website Resmi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu',
        'subjudul' => 'Website Resmi PPID Kabupaten Indragiri Hulu',
        'apiData'  => new \Illuminate\Pagination\LengthAwarePaginator(
            $allData->forPage($currentPage, $perPage)->values(),
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        ),
        'apiTotal' => $total,
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

    // Ambil data dari DB
    $dbData = Informasi::with('files')->where('tipe', 'TERSEDIA')->where('status', 'DITERIMA')->latest()->get()->map(function ($item) {
        $berkasFiles = $item->files->where('alias', 'berkas_informasi');
        $dbFiles = $berkasFiles->map(function($f) {
            return [
                'name' => $f->name,
                'url' => url($f->public_stream)
            ];
        })->values()->toArray();

        $urlDownload = $berkasFiles->count() > 0 ? url($berkasFiles->first()->public_stream) : '#';
        $fileName = $berkasFiles->count() > 0 ? $berkasFiles->first()->name : 'Tidak ada file';

        return [
            'judul' => $item->nama,
            'tahun_pengundangan' => $item->tahun,
            'tanggal_pengundangan' => $item->created_at ? $item->created_at->format('Y-m-d') : null,
            'fileDownload' => $fileName,
            'urlDownload' => $urlDownload,
            'source_name' => 'PPID Kabupaten Indragiri Hulu',
            'source_url' => url('/'),
            'is_db' => true,
            'db_files' => $dbFiles,
        ];
    });

    // Gabungkan & urutkan berdasarkan tanggal pengundangan secara descending
    $allData = $data1->merge($data2)->merge($dbData)->sortByDesc('tanggal_pengundangan')->values();

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

    // Ambil data dari DB
    $dbData = Informasi::with('files')->where('tipe', 'BERKALA')->where('status', 'DITERIMA')->latest()->get()->map(function ($item) {
        $berkasFiles = $item->files->where('alias', 'berkas_informasi');
        $berkasArray = $berkasFiles->map(function($f) {
            return url($f->public_stream);
        })->values()->toArray();
        $urlDownload = $berkasFiles->count() > 0 ? url($berkasFiles->first()->public_stream) : '#';

        return [
            'name' => $item->nama,
            'tahun' => $item->tahun,
            'created_at' => $item->created_at ? $item->created_at->toDateTimeString() : null,
            'berkas' => $berkasArray,
            'urlDownload' => $urlDownload,
            'source_name' => 'PPID Kabupaten Indragiri Hulu',
            'source_url' => url('/'),
            'is_db' => true,
        ];
    });

    $allData = $allData->merge($dbData)->sortByDesc('created_at')->values();

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

public function sertamerta(Request $request)
{
    $dbData = Informasi::with('files')->where('tipe', 'SERTA MERTA')->where('status', 'DITERIMA')->latest()->get()->map(function ($item) {
        $berkasFiles = $item->files->where('alias', 'berkas_informasi');
        $dbFiles = $berkasFiles->map(function($f) {
            return [
                'name' => $f->name,
                'url' => url($f->public_stream)
            ];
        })->values()->toArray();

        $urlDownload = $berkasFiles->count() > 0 ? url($berkasFiles->first()->public_stream) : '#';
        $fileName = $berkasFiles->count() > 0 ? $berkasFiles->first()->name : 'Tidak ada file';

        return [
            'judul' => $item->nama,
            'tahun_pengundangan' => $item->tahun,
            'fileDownload' => $fileName,
            'urlDownload' => $urlDownload,
            'source_name' => 'PPID Kabupaten Indragiri Hulu',
            'source_url' => url('/'),
            'is_db' => true,
            'db_files' => $dbFiles,
        ];
    });

    $search = $request->get('search');
    if (!empty($search)) {
        $keyword = strtolower($search);
        $dbData = $dbData->filter(function ($item) use ($keyword) {
            $inJudul = str_contains(strtolower($item['judul'] ?? ''), $keyword);
            $inTahun = str_contains(strtolower((string)($item['tahun_pengundangan'] ?? '')), $keyword);
            return $inJudul || $inTahun;
        })->values();
    }

    $perPage     = 10;
    $currentPage = $request->get('page', 1);
    $total       = $dbData->count();

    return view('frontend.informasi.sertamerta', [
        'title'    => 'Informasi Sertamerta - PPID Indragiri Hulu',
        'judul'    => 'Daftar Informasi Publik Sertamerta',
        'subjudul' => 'Informasi Sertamerta PPID Kabupaten Indragiri Hulu',
        'apiData'  => new \Illuminate\Pagination\LengthAwarePaginator(
            $dbData->forPage($currentPage, $perPage)->values(),
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

public function dikecualikan(Request $request)
{
    $dbData = Informasi::with('files')->where('tipe', 'DIKECUALIKAN')->where('status', 'DITERIMA')->latest()->get()->map(function ($item) {
        $berkasFiles = $item->files->where('alias', 'berkas_informasi');
        $dbFiles = $berkasFiles->map(function($f) {
            return [
                'name' => $f->name,
                'url' => url($f->public_stream)
            ];
        })->values()->toArray();

        $urlDownload = $berkasFiles->count() > 0 ? url($berkasFiles->first()->public_stream) : '#';
        $fileName = $berkasFiles->count() > 0 ? $berkasFiles->first()->name : 'Tidak ada file';

        return [
            'judul' => $item->nama,
            'tahun_pengundangan' => $item->tahun,
            'fileDownload' => $fileName,
            'urlDownload' => $urlDownload,
            'source_name' => 'PPID Kabupaten Indragiri Hulu',
            'source_url' => url('/'),
            'is_db' => true,
            'db_files' => $dbFiles,
        ];
    });

    $search = $request->get('search');
    if (!empty($search)) {
        $keyword = strtolower($search);
        $dbData = $dbData->filter(function ($item) use ($keyword) {
            $inJudul = str_contains(strtolower($item['judul'] ?? ''), $keyword);
            $inTahun = str_contains(strtolower((string)($item['tahun_pengundangan'] ?? '')), $keyword);
            return $inJudul || $inTahun;
        })->values();
    }

    $perPage     = 10;
    $currentPage = $request->get('page', 1);
    $total       = $dbData->count();

    return view('frontend.informasi.dikecualikan', [
        'title'    => 'Informasi Dikecualikan - PPID Indragiri Hulu',
        'judul'    => 'Daftar Informasi Publik Dikecualikan',
        'subjudul' => 'Informasi Dikecualikan PPID Kabupaten Indragiri Hulu',
        'apiData'  => new \Illuminate\Pagination\LengthAwarePaginator(
            $dbData->forPage($currentPage, $perPage)->values(),
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

}