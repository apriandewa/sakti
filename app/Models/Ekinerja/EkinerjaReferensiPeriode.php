<?php

namespace App\Models\Ekinerja;

use App\Traits\HasUuid; // TODO: sesuaikan namespace trait HasUuid yang sudah ada di project
use Illuminate\Database\Eloquent\Model;

class EkinerjaReferensiPeriode extends Model
{
    use HasUuid;

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
