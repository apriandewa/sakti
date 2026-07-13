<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi Modul Integrasi e-Kinerja BKN
|--------------------------------------------------------------------------
| File ini dipakai bersama oleh Controller Frontend & Backend melalui
| app\Services\Ekinerja\* . Jangan hardcode nilai apapun di controller —
| semua nilai konfigurasi/koneksi harus lewat file ini + .env.
*/

return [

    // Base URL API e-Kinerja BKN
    'base_url' => env('EKINERJA_BKN_BASE_URL', 'https://kinerja.bkn.go.id'),

    // Bearer token JWT (lihat catatan masa berlaku token di README modul)
    'token' => env('EKINERJA_BKN_TOKEN'),

    // Timeout HTTP (detik)
    'timeout' => (int) env('EKINERJA_BKN_TIMEOUT', 15),
    'connect_timeout' => (int) env('EKINERJA_BKN_CONNECT_TIMEOUT', 10),

    // Retry request bila gagal (bukan pada error 401/403/404)
    'retry' => [
        'times' => (int) env('EKINERJA_BKN_RETRY_TIMES', 2),
        'sleep' => (int) env('EKINERJA_BKN_RETRY_SLEEP', 500), // ms
    ],

    // Path endpoint API BKN (relatif terhadap base_url)
    'endpoints' => [
        'referensi_periode' => '/api_kinerja/referensi/periode',
        'laporan_penilaian' => '/api_kinerja/laporan/penilaian/{tahun}/{periode_id}',
    ],

    // Masa berlaku cache lokal sebelum re-fetch ke API BKN (detik)
    'cache_ttl' => [
        'periode'   => (int) env('EKINERJA_CACHE_TTL_PERIODE', 60 * 60 * 12), // 12 jam
        'penilaian' => (int) env('EKINERJA_CACHE_TTL_PENILAIAN', 60 * 60 * 1), // 1 jam
    ],

    // Aturan validasi & pembatasan pencarian publik
    'search' => [
        'nip_length'            => 18,
        'rate_limit_per_minute' => (int) env('EKINERJA_SEARCH_RATE_LIMIT', 10),
    ],

];
