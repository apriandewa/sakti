<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tamu extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'nama',
        'alamat',
        'no_hp',
        'email',
        'jenis_kelamin',
        'pekerjaan',
        'asal',
        'keperluan',
        'pesan',
        'status',
        'tanggal_kunjungan',
        'ip_address',
        'user_id',
        'verifikator_id',
    ];

    protected $casts = [
        'tanggal_kunjungan' => 'datetime',
    ];

    protected $table = 'tamus';

    /*
    |--------------------------------------------------------------------------
    | Relasi User
    |--------------------------------------------------------------------------
    */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikator_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi Verifikasi
    |--------------------------------------------------------------------------
    */

    public function verifikasis()
    {
        return $this->morphMany(Verifikasi::class, 'verifiable');
    }

    /*
    |--------------------------------------------------------------------------
    | Relasi File (gunakan satu relasi saja: files)
    |--------------------------------------------------------------------------
    */

    public function file()
    {
        return $this->morphMany(File::class, 'fileable');
    }
    public function files()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * Ambil satu file berdasarkan alias.
     */
    public function getfilebyalias(string $alias): ?File
    {
        return $this->files()->where('alias', $alias)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | Scope Status
    |--------------------------------------------------------------------------
    */

    public function scopeDISETUJUI($query)
    {
        return $query->where('status', 'DISETUJUI');
    }

    public function scopeTerkirim($query)
    {
        return $query->where('status', 'TERKIRIM');
    }

    public function scopeDitolak($query)
    {
        return $query->where('status', 'DITOLAK');
    }
}