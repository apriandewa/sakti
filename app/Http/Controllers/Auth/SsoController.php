<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class SsoController extends Controller
{
    /**
     * Redirect ke SSO Server.
     */
    public function redirectToSso(Request $request)
    {
        $state = bin2hex(random_bytes(16));
        $request->session()->put('sso_state', $state);

        $query = http_build_query([
            'client_id' => config('services.kompass.client_id'),
            'redirect_uri' => config('services.kompass.redirect_uri'),
            'response_type' => 'code',
            'state' => $state,
        ]);

        return redirect(config('services.kompass.sso_url') . '/oauth/authorize?' . $query);
    }

    /**
     * Tangani callback dari SSO Server.
     */
    public function handleSsoCallback(Request $request)
    {
        $state = $request->session()->pull('sso_state');

        // Jika state ada di session (SP-initiated), wajib sama dengan state di request.
        // Jika tidak ada di session (IdP-initiated), abaikan pengecekan state.
        if ($state && $state !== $request->state) {
            return redirect()->route('login')->withErrors([
                'email' => 'Validasi state CSRF gagal. Silakan coba lagi.'
            ]);
        }

        if ($request->has('error')) {
            return redirect()->route('login')->withErrors([
                'email' => 'Otorisasi ditolak oleh server SSO: ' . $request->error_description
            ]);
        }

        $code = $request->code;

        // Exchange Authorization Code for Access Token
        $response = Http::withoutVerifying()->asForm()->post(config('services.kompass.sso_url') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.kompass.client_id'),
            'client_secret' => config('services.kompass.client_secret'),
            'redirect_uri' => config('services.kompass.redirect_uri'),
            'code' => $code,
        ]);

        if ($response->failed()) {
            $errorMessage = $response->json('message') ?? $response->json('error_description') ?? '';
            
            if ($response->status() === 401) {
                $errorMessage = 'Client ID atau Client Secret salah / tidak terdaftar di server SSO.';
            } elseif (empty($errorMessage)) {
                $errorMessage = 'Server SSO mengembalikan status ' . $response->status() . ' (' . $response->reason() . ')';
            }

            $ssoErrorUrl = rtrim(config('services.kompass.sso_url'), '/') . '/sso/error';
            return redirect($ssoErrorUrl . '?' . http_build_query([
                'app' => config('app.name'),
                'error' => 'Gagal Pertukaran Token (Token Exchange Failed)',
                'message' => $errorMessage,
                'solution' => 'Periksa kembali variabel KOMPASS_CLIENT_ID dan KOMPASS_CLIENT_SECRET di file .env aplikasi client Anda, lalu pastikan nilainya sesuai dengan yang terdaftar di admin portal Kompas SSO.'
            ]));
        }

        $accessToken = $response->json('access_token');

        // Dapatkan data user dari API SSO
        $userResponse = Http::withoutVerifying()->withToken($accessToken)
            ->get(config('services.kompass.sso_url') . '/api/user');

        if ($userResponse->failed()) {
            $ssoErrorUrl = rtrim(config('services.kompass.sso_url'), '/') . '/sso/error';
            return redirect($ssoErrorUrl . '?' . http_build_query([
                'app' => config('app.name'),
                'error' => 'Gagal Mengambil Data User (User Profile Failed)',
                'message' => 'Token berhasil didapatkan, tetapi gagal mengambil data profil dari server SSO.',
                'solution' => 'Pastikan token tidak kedaluwarsa dan server SSO Kompas dalam kondisi berjalan normal.'
            ]));
        }

        $ssoUser = $userResponse->json();
        
        // Cari user lokal berdasarkan email
        $user = User::where('email', $ssoUser['email'])->first();

        if (!$user) {
            $ssoErrorUrl = rtrim(config('services.kompass.sso_url'), '/') . '/sso/error';
            return redirect($ssoErrorUrl . '?' . http_build_query([
                'app' => config('app.name'),
                'error' => 'Akun Belum Terdaftar (User Not Registered)',
                'message' => 'Akun Anda (' . $ssoUser['email'] . ') terdaftar di Kompas SSO, tetapi tidak ditemukan di database pengguna lokal aplikasi ' . config('app.name') . '.',
                'solution' => 'Silakan hubungi administrator aplikasi ' . config('app.name') . ' untuk mendaftarkan email Anda (' . $ssoUser['email'] . ') ke database setempat agar mendapatkan hak akses masuk.'
            ]));
        }

        // Sinkronisasi/Update role dari SSO ke Level & Access Group lokal
        if (isset($ssoUser['role'])) {
            $ssoRole = $ssoUser['role']; // e.g. 'admin', 'verifikator', 'user'
            $level = \App\Models\Level::where('code', $ssoRole)->first();
            $accessGroup = \App\Models\AccessGroup::where('code', $ssoRole)->first();
            
            if ($level) {
                $user->level_id = $level->id;
            }
            if ($accessGroup) {
                $user->access_group_id = $accessGroup->id;
            }
            $user->save();
        }

        Auth::login($user);

        return redirect()->intended('/admin/dashboard');
    }
}
