<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RapatNotulen extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id', 'agenda_rapat_id', 'isi_notulen', 'pimpinan_rapat',
        'notulis', 'pimpinan_rapat_id', 'notulis_id', 'hasil_rapat', 
        'user_id', 'status', 'catatan_revisi'
    ];

    protected $table = 'rapat_notulens';

    public function agendaRapat()
    {
        return $this->belongsTo(AgendaRapat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
