<?php
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpbnN0YW5zaV9pZCI6IkE1RUIwM0UyM0IxQUY2QTBFMDQwNjQwQTA0MDI1MkFEIiwiYWxsIjpmYWxzZSwiaWF0IjoxNzgwNDczMTE2fQ.G029Pj7hIgHPBx1mtg4Y6fyxGUlautZUjlh4ZkyL-Tw";
$nip = "199408122020122018";
$month = 6;
$year = 2026;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-absensi.simpegnas.go.id/absensi/api/get/riwayat?nip=$nip&bulan=$month&tahun=$year");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "presensi-key: $token"
]);
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);
if (isset($data['data']['riwayat']) && count($data['data']['riwayat']) > 0) {
    // print first 2 records
    print_r(array_slice($data['data']['riwayat'], 0, 2));
} else {
    echo "No riwayat found or structure different:\n";
    print_r($data);
}
