<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return \Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->getEmail())->first();

        if (!$user) {
            return redirect('/login')->withErrors([
                'email' => 'Anda belum terdaftar sebagai pengguna sistem ini. Mohon kontak admin untuk melanjutkan.'
            ]);
        }

        Auth::login($user);

        return redirect()->intended('/admin/dashboard');
    }
}

