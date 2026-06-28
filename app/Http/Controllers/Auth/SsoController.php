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
        $response = Http::asForm()->post(config('services.kompass.sso_url') . '/oauth/token', [
            'grant_type' => 'authorization_code',
            'client_id' => config('services.kompass.client_id'),
            'client_secret' => config('services.kompass.client_secret'),
            'redirect_uri' => config('services.kompass.redirect_uri'),
            'code' => $code,
        ]);

        if ($response->failed()) {
            $errorMessage = $response->json('message') ?? 'Internal Server Error';
            if ($response->header('Content-Type') && !str_contains($response->header('Content-Type'), 'application/json')) {
                $errorMessage = 'Server SSO mengembalikan status ' . $response->status() . ' (' . $response->reason() . ')';
            }
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal mendapatkan access token dari server SSO: ' . $errorMessage
            ]);
        }

        $accessToken = $response->json('access_token');

        // Dapatkan data user dari API SSO
        $userResponse = Http::withToken($accessToken)
            ->get(config('services.kompass.sso_url') . '/api/user');

        if ($userResponse->failed()) {
            return redirect()->route('login')->withErrors([
                'email' => 'Gagal mengambil data profil dari server SSO.'
            ]);
        }

        $ssoUser = $userResponse->json();
        
        // Cari user lokal berdasarkan email
        $user = User::where('email', $ssoUser['email'])->first();

        if (!$user) {
            // Split name into first_name and last_name
            $parts = explode(' ', $ssoUser['name'], 2);
            $firstName = $parts[0];
            $lastName = $parts[1] ?? '';

            // Jika user belum ada di db lokal, buat baru
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $ssoUser['email'],
                'password' => bcrypt(bin2hex(random_bytes(8))),
                'level_id' => 3, // Default level code 'user' (ID 3)
                'access_group_id' => 3, // Default access group code 'user' (ID 3)
            ]);
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
