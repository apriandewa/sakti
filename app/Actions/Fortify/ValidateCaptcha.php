<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateCaptcha
{
    /**
     * Validate captcha only for login POST (bukan untuk two-factor challenge).
     */
    public function __invoke(Request $request, $next)
    {
        // hanya jalan pada POST (login form). Abaikan route two-factor challenge.
        if ($request->isMethod('post') && ! $request->is('two-factor-challenge*')) {
            Validator::make($request->all(), [
                'captcha' => 'required|captcha',
            ], [
                'captcha.required' => 'Captcha wajib diisi.',
                'captcha.captcha'   => 'Captcha tidak valid.',
            ])->validate();
        }

        return $next($request);
    }
}
