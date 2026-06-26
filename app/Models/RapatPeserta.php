<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RapatPeserta extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id', 'agenda_rapat_id', 'nama', 'nip', 'jabatan',
        'instansi', 'no_hp', 'tanda_tangan', 'waktu_hadir',
    ];

    protected $casts = [
        'waktu_hadir' => 'datetime',
    ];

    protected $table = 'rapat_pesertas';

    public function agendaRapat()
    {
        return $this->belongsTo(AgendaRapat::class);
    }
}
