<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

use Laravel\Fortify\Fortify;
use Laravel\Fortify\Features;
use Laravel\Fortify\Actions\EnsureLoginIsNotThrottled;
use Laravel\Fortify\Actions\CanonicalizeUsername;
use Laravel\Fortify\Actions\RedirectIfTwoFactorAuthenticatable;
use Laravel\Fortify\Actions\AttemptToAuthenticate;
use Laravel\Fortify\Actions\PrepareAuthenticatedSession;

class FortifyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // actions default Fortify
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        // view untuk two-factor challenge (pastikan kamu punya view ini)
        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        // rate limiters
        RateLimiter::for('login', function (Request $request) {
            $throttleKey = Str::transliterate(Str::lower($request->input(Fortify::username())).'|'.$request->ip());
            return Limit::perMinute(5)->by($throttleKey);
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        // custom authentication pipeline (sesuaikan dengan contoh resmi, tambahkan ValidateCaptcha)
        Fortify::authenticateThrough(function (Request $request) {
            return array_filter([
                // default helpers (jangan hapus kalau kamu memerlukan fitur-fitur ini)
                config('fortify.limiters.login') ? null : EnsureLoginIsNotThrottled::class,
                config('fortify.lowercase_usernames') ? CanonicalizeUsername::class : null,

                // --- validasi captcha kita (jalankan sebelum pengecekan 2FA/attempt authenticate) ---
                \App\Actions\Fortify\ValidateCaptcha::class,

                // bila fitur 2FA aktif, panggil RedirectIfTwoFactorAuthenticatable (POSISI PENTING: sebelum AttemptToAuthenticate)
                Features::enabled(Features::twoFactorAuthentication()) ? RedirectIfTwoFactorAuthenticatable::class : null,

                // attempt authenticate dan siapkan session
                AttemptToAuthenticate::class,
                PrepareAuthenticatedSession::class,
            ]);
        });
    }
}
