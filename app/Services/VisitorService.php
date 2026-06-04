<?php

namespace App\Services;

use App\Models\VisitorLog;
use Carbon\Carbon;

class VisitorService
{
    /**
     * Ambil semua statistik kunjungan sekaligus.
     */
    public function getStatistik(): array
    {
        return [
            'hari_ini'    => VisitorLog::today()->count(),
            'bulan_ini'   => VisitorLog::thisMonth()->count(),
            'tahun_ini'   => VisitorLog::thisYear()->count(),
            'tahun_lalu'  => VisitorLog::lastYear()->count(),
        ];
    }

    /**
     * Ambil info pengunjung saat ini (IP & browser).
     */
    public function getVisitorInfo(): array
    {
        $request    = request();
        $userAgent  = $request->userAgent() ?? '';

        // IP Address publik
        $ip = $request->ip();
        if ($request->header('X-Forwarded-For')) {
            $ip = explode(',', $request->header('X-Forwarded-For'))[0];
        }

        $browser = $this->detectBrowser($userAgent);
        $os      = $this->detectOS($userAgent);

        return [
            'ip'      => trim($ip),
            'browser' => $browser,
            'os'      => $os,
        ];
    }

    private function detectBrowser(string $ua): string
    {
        $browsers = [
            'Brave'          => 'Brave',
            'Edg'            => 'Microsoft Edge',
            'OPR'            => 'Opera',
            'Opera'          => 'Opera',
            'SamsungBrowser' => 'Samsung Internet',
            'UCBrowser'      => 'UC Browser',
            'YaBrowser'      => 'Yandex Browser',
            'Chrome'         => 'Google Chrome',
            'Firefox'        => 'Mozilla Firefox',
            'Safari'         => 'Apple Safari',
            'MSIE'           => 'Internet Explorer',
            'Trident'        => 'Internet Explorer',
        ];

        foreach ($browsers as $key => $name) {
            if (stripos($ua, $key) !== false) {
                return $name;
            }
        }

        return 'Unknown Browser';
    }

    private function detectOS(string $ua): string
    {
        $osMap = [
            'Windows NT 10'  => 'Windows 10/11',
            'Windows NT 6.3' => 'Windows 8.1',
            'Windows NT 6.2' => 'Windows 8',
            'Windows NT 6.1' => 'Windows 7',
            'Windows'        => 'Windows',
            'Android'        => 'Android',
            'iPhone'         => 'iOS (iPhone)',
            'iPad'           => 'iOS (iPad)',
            'Mac OS X'       => 'macOS',
            'Linux'          => 'Linux',
        ];

        foreach ($osMap as $key => $name) {
            if (stripos($ua, $key) !== false) {
                return $name;
            }
        }

        return 'Unknown OS';
    }
}
