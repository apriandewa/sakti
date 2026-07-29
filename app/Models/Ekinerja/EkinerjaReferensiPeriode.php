<?php

namespace App\Models\Ekinerja;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class EkinerjaReferensiPeriode extends Model
{
    use HasUuids;

    protected $table = 'ekinerja_referensi_periode';

    protected $fillable = [
        'periode_id',
        'nama',
        'tahun',
        'periode_awal',
        'periode_akhir',
        'batas_pengisian',
        'jenis_periode',
        'tipe_periodik',
        'angka_periodik',
        'synced_at',
    ];

    protected $casts = [
        'synced_at' => 'datetime',
    ];

    /** Label tampilan, mis. "JANUARI 2026" */
    public function getLabelAttribute(): string
    {
        return trim($this->nama . ' ' . ($this->tahun ?? ''));
    }
}
