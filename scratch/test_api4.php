<?php
$token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpbnN0YW5zaV9pZCI6IkE1RUIwM0UyM0IxQUY2QTBFMDQwNjQwQTA0MDI1MkFEIiwiYWxsIjpmYWxzZSwiaWF0IjoxNzgwNDczMTE2fQ.G029Pj7hIgHPBx1mtg4Y6fyxGUlautZUjlh4ZkyL-Tw";
$nip = "199408122020122018";
$date = "2026-06-25"; // try a day with actual attendance

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api-absensi.simpegnas.go.id/absensi/api/get/image?nip=$nip&tanggal=$date&tgl=$date");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "presensi-key: $token"
]);
$result = curl_exec($ch);
curl_close($ch);

$data = json_decode($result, true);
if (isset($data['data']['last'])) {
    foreach($data['data']['last'] as $item) {
        unset($item['image_base64']);
        print_r($item);
    }
}
