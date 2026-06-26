<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RapatVerifikasi extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = ['id', 'agenda_rapat_id', 'user_id', 'status', 'catatan'];
    protected $table = 'rapat_verifikasis';

    public function agendaRapat()
    {
        return $this->belongsTo(AgendaRapat::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
