<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Verifikasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'verifikasis';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'verifiable_type',
        'verifiable_id',
        'catatan',
        'status',
        'user_id',
    ];

    /**
     * Relasi ke model polymorphic (Berita, Galeri, Unduhan, dll).
     */
    public function verifiable()
    {
        return $this->morphTo();
    }

    /**
     * Relasi ke user yang melakukan verifikasi.
     */
    public function user()
    {
        // kalau tabel users pakai kolom id default
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

     public function notification() : object
    {
        return $this->morphOne(Notification::class, 'notifiable');
    }
}
