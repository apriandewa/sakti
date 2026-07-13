<?php

namespace App\Http\Requests\Ekinerja;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class FilterRekapRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // otorisasi ditangani middleware route (auth/permission)
    }

    public function rules(): array
    {
        return [
            'unor_id'    => ['required', 'string', Rule::exists('ekinerja_master_unor', 'unor_id')],
            'periode_id' => ['required', 'string', Rule::exists('ekinerja_referensi_periode', 'periode_id')],
        ];
    }

    public function messages(): array
    {
        return [
            'unor_id.required'    => 'Kantor/Unor wajib dipilih.',
            'unor_id.exists'      => 'Kantor/Unor yang dipilih tidak valid.',
            'periode_id.required' => 'Periode wajib dipilih.',
            'periode_id.exists'   => 'Periode yang dipilih tidak valid.',
        ];
    }
}
