<?php
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpbnN0YW5zaV9pZCI6IkE1RUIwM0UyM0IxQUY2QTBFMDQwNjQwQTA0MDI1MkFEIiwiYWxsIjpmYWxzZSwiaWF0IjoxNzgwNDczMTE2fQ.G029Pj7hIgHPBx1mtg4Y6fyxGUlautZUjlh4ZkyL-Tw";
$nip = "199408122020122018";
$date = "2026-06-25";

$endpoints = [
    "https://api-absensi.simpegnas.go.id/absensi/api/get/riwayat/image?nip=$nip&tanggal=$date",
    "https://api-absensi.simpegnas.go.id/absensi/api/get/image/riwayat?nip=$nip&tanggal=$date",
    "https://api-absensi.simpegnas.go.id/absensi/api/get/presensi/image?nip=$nip&tanggal=$date"
];

foreach($endpoints as $url) {
    echo "Testing $url\n";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "presensi-key: $token"
    ]);
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    echo "HTTP Code: $http_code\n";
    echo substr($result, 0, 200) . "\n\n";
}
