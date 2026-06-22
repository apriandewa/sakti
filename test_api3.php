<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
use Illuminate\Support\Facades\Http;
try {
    $startTime = microtime(true);
    $response = Http::timeout(60)->get('https://api-splp.layanan.go.id/transparansi_anggaran_indragiri_hulu/1.0/api/dokumenapi');
    $duration = microtime(true) - $startTime;
    echo "Status: " . $response->status() . "\n";
    echo "Duration: " . round($duration, 2) . "s\n";
    $json = $response->json();
    $count = 0;
    if (isset($json['data']) && is_array($json['data'])) {
        $count = count($json['data']);
    } elseif (is_array($json)) {
        $count = count($json);
    }
    echo "Count: $count\n";
} catch (\Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
}
