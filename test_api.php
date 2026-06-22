<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;

$getApiCount = function ($url) {
    try {
        $startTime = microtime(true);
        $response = Http::timeout(10)->get($url); // increased timeout for testing
        $duration = microtime(true) - $startTime;
        echo "URL: $url\n";
        echo "Status: " . $response->status() . "\n";
        echo "Duration: " . round($duration, 2) . "s\n";

        if ($response->successful()) {
            $json = $response->json();
            $count = 0;

            if (isset($json['data']) && is_array($json['data'])) {
                $count = count($json['data']);
                echo "Found 'data' array, count: $count\n";
            } elseif (is_array($json)) {
                $count = count($json);
                echo "Found plain array, count: $count\n";
            } else {
                echo "No valid JSON structure found.\n";
            }

            if ($count > 0) {
                return $count;
            }
        } else {
            echo "Failed response: " . substr($response->body(), 0, 100) . "...\n";
        }
    } catch (\Exception $e) {
        echo "Exception for $url: " . $e->getMessage() . "\n";
    }

    return 0;
};

$urls = [
    'https://api-splp.layanan.go.id/jdih-inhu/1.0/integrasijdihinhu',
    'https://api-splp.layanan.go.id/jdih-dprd-indragiri-hulu/1.0/integrasijdihdprd',
    'https://api-splp.layanan.go.id/transparansi_anggaran_indragiri_hulu/1.0/api/dokumenapi',
];

foreach ($urls as $url) {
    $count = $getApiCount($url);
    echo "Result for $url: $count\n\n";
}
