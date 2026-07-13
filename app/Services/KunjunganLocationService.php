<?php

namespace App\Services;

use App\Models\Pengaturan;

class KunjunganLocationService
{
    public function getOfficeLocation(?Pengaturan $pengaturan = null): ?array
    {
        $pengaturan ??= Pengaturan::first();

        if (! $pengaturan || blank($pengaturan->peta)) {
            return null;
        }

        $coords = $this->parseCoordinatesFromMapUrl($pengaturan->peta);

        if (! $coords) {
            return null;
        }

        return [
            'latitude'      => $coords['latitude'],
            'longitude'     => $coords['longitude'],
            'radius_meters' => (int) config('kunjungan.radius_meters', 200),
            'name'          => trim((string) $pengaturan->alamat) ?: 'Kantor',
        ];
    }

    public function parseCoordinatesFromMapUrl(string $url): ?array
    {
        $decoded = urldecode(trim($url));

        // Format embed Google Maps: !2d{lng}!3d{lat}
        if (preg_match('/!2d([\-\d.]+)!3d([\-\d.]+)/', $decoded, $matches)) {
            return $this->buildCoordinates((float) $matches[2], (float) $matches[1]);
        }

        // Format alternatif: !3d{lat}!4d{lng}
        if (preg_match('/!3d([\-\d.]+)!4d([\-\d.]+)/', $decoded, $matches)) {
            return $this->buildCoordinates((float) $matches[1], (float) $matches[2]);
        }

        // Format URL biasa: @lat,lng
        if (preg_match('/@([\-\d.]+),([\-\d.]+)/', $decoded, $matches)) {
            return $this->buildCoordinates((float) $matches[1], (float) $matches[2]);
        }

        // Query parameter: q=lat,lng atau ll=lat,lng
        if (preg_match('/[?&](?:q|ll)=([\-\d.]+),([\-\d.]+)/', $decoded, $matches)) {
            return $this->buildCoordinates((float) $matches[1], (float) $matches[2]);
        }

        return null;
    }

    public function distanceInMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000;
        $latFrom     = deg2rad($lat1);
        $lngFrom     = deg2rad($lng1);
        $latTo       = deg2rad($lat2);
        $lngTo       = deg2rad($lng2);
        $latDelta    = $latTo - $latFrom;
        $lngDelta    = $lngTo - $lngFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)
        ));

        return $angle * $earthRadius;
    }

    private function buildCoordinates(float $latitude, float $longitude): ?array
    {
        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return null;
        }

        return [
            'latitude'  => $latitude,
            'longitude' => $longitude,
        ];
    }
}
