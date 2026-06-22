<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class Informasi extends Model
{
    use HasFactory, HasUuids, SoftDeletes, loggable;

    protected $fillable = ['id', 'nama', 'tipe', 'desc', 'tahun', 'status', 'catatan', 'user_id', 'verifikator_id'];
    protected $casts = [];
    protected $table = 'informasis';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    public function verifikasis()
    {
        return $this->morphMany(Verifikasi::class, 'verifiable');
    }

    public function file()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function notification() : object
    {
        return $this->morphOne(Notification::class, 'notifiable');
    }

    public function getfilebyalias($alias)
    {
        return $this->file()->where('alias', $alias)->first();
    }
}
