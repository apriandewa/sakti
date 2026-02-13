<?php

namespace App\Actions\Fortify;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ValidateCaptcha
{
    /**
     * Validate captcha untuk semua POST request form Fortify
     * kecuali two-factor challenge.
     */
    public function __invoke(Request $request, $next)
    {
        // Jika request POST dan bukan two-factor challenge
        if ($request->isMethod('post') && ! $request->is('two-factor-challenge*')) {

            Validator::make($request->all(), [
                'captcha'     => ['required', 'captcha_api:' . $request->captcha_key],
                'captcha_key' => ['required']
            ], [
                'captcha.required'     => 'Captcha wajib diisi.',
                'captcha.captcha_api'  => 'Captcha tidak valid.',
                'captcha_key.required' => 'Captcha key tidak ditemukan.',
            ])->validate();
        }

        return $next($request);
    }
}