<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$urls = [
    'https://api-splp.layanan.go.id/jdih-inhu/1.0/integrasijdihinhu',
    'https://api-splp.layanan.go.id/transparansi_anggaran_indragiri_hulu/1.0/api/dokumenapi',
];

foreach ($urls as $url) {
    try {
        $startTime = microtime(true);
        $response = Http::timeout(30)->get($url);
        $duration = microtime(true) - $startTime;
        echo "URL: $url\n";
        echo "Status: " . $response->status() . "\n";
        echo "Duration: " . round($duration, 2) . "s\n";
        $json = $response->json();
        $count = 0;
        if (isset($json['data']) && is_array($json['data'])) {
            $count = count($json['data']);
        } elseif (is_array($json)) {
            $count = count($json);
        }
        echo "Count: $count\n\n";
    } catch (\Exception $e) {
        echo "Exception for $url: " . $e->getMessage() . "\n\n";
    }
}
