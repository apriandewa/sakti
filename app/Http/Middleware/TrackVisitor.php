<?php

namespace App\Http\Middleware;

use App\Models\VisitorLog;
use Closure;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;
use Symfony\Component\HttpFoundation\Response;

class TrackVisitor
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Hanya tracking halaman frontend (bukan admin/api)
        $path = $request->path();
        $skipPrefixes = ['admin', 'api', 'js', 'file', 'auth', 'profile'];

        foreach ($skipPrefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return $next($request);
            }
        }

        // Abaikan bot / crawler
        $userAgent = $request->userAgent() ?? '';
        $bots = ['bot', 'crawler', 'spider', 'slurp', 'baidu', 'yandex', 'bing', 'googlebot', 'facebookexternalhit', 'curl', 'wget'];
        foreach ($bots as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return $next($request);
            }
        }

        try {
            // Parse User-Agent
            $agent = new \stdClass();
            $agent->browser   = $this->detectBrowser($userAgent);
            $agent->os        = $this->detectOS($userAgent);
            $agent->device    = $this->detectDevice($userAgent);

            // Ambil IP
            $ip = $request->ip();
            if ($request->header('X-Forwarded-For')) {
                $ip = explode(',', $request->header('X-Forwarded-For'))[0];
            }

            // Gunakan session ID untuk mencegah duplikat per session
            $sessionId = $request->session()->getId();

            // Simpan ke database (satu record per session per hari)
            $today    = now()->toDateString();
            $existing = VisitorLog::where('session_id', $sessionId)
                ->whereDate('created_at', $today)
                ->exists();

            if (!$existing) {
                VisitorLog::create([
                    'ip_address' => $ip,
                    'browser'    => $agent->browser,
                    'os'         => $agent->os,
                    'device'     => $agent->device,
                    'url'        => $request->fullUrl(),
                    'referer'    => $request->header('referer'),
                    'session_id' => $sessionId,
                ]);
            }
        } catch (\Exception $e) {
            // Silent fail — jangan ganggu user experience
        }

        return $next($request);
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: Detect Browser
    |--------------------------------------------------------------------------
    */
    private function detectBrowser(string $ua): string
    {
        $browsers = [
            'Brave'              => 'Brave',
            'Edg'                => 'Microsoft Edge',
            'OPR'                => 'Opera',
            'Opera'              => 'Opera',
            'SamsungBrowser'     => 'Samsung Internet',
            'UCBrowser'          => 'UC Browser',
            'YaBrowser'          => 'Yandex Browser',
            'Chrome'             => 'Google Chrome',
            'Firefox'            => 'Mozilla Firefox',
            'Safari'             => 'Apple Safari',
            'MSIE'               => 'Internet Explorer',
            'Trident'            => 'Internet Explorer',
        ];

        foreach ($browsers as $key => $name) {
            if (stripos($ua, $key) !== false) {
                return $name;
            }
        }

        return 'Unknown Browser';
    }

    /*
    |--------------------------------------------------------------------------
    | Helper: Detect OS
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Helper: Detect Device
    |--------------------------------------------------------------------------
    */
    private function detectDevice(string $ua): string
    {
        if (stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false || stripos($ua, 'iPhone') !== false) {
            return 'Mobile';
        }
        if (stripos($ua, 'Tablet') !== false || stripos($ua, 'iPad') !== false) {
            return 'Tablet';
        }
        return 'Desktop';
    }
}
