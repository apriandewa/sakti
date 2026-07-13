<?php

namespace App\Http\Requests\Ekinerja;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CariPenilaianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // halaman publik, tanpa otorisasi khusus
    }

    public function rules(): array
    {
        return [
            'periode_id' => ['required', 'string', Rule::exists('ekinerja_referensi_periode', 'periode_id')],
            'nip'        => ['required', 'digits:' . config('ekinerja.search.nip_length', 18)],
            'nama'       => ['required', 'string', 'min:3', 'max:150'],

            // TODO(backend): ganti dengan rule captcha resmi dari package "meaws captcha"
            // mis. 'captcha' => ['required', new \Meaws\Captcha\Rules\CaptchaRule()],
            'captcha' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'periode_id.required' => 'Periode wajib dipilih.',
            'periode_id.exists'   => 'Periode yang dipilih tidak valid.',
            'nip.required'        => 'NIP wajib diisi.',
            'nip.digits'          => 'NIP harus terdiri dari 18 digit angka.',
            'nama.required'       => 'Nama pegawai wajib diisi.',
            'captcha.required'    => 'Kode keamanan wajib diisi.',
        ];
    }
}
