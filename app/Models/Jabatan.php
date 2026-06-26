<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jabatan extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'jabatans';

    protected $fillable = [
        'id',
        'parent_id',
        'nama',
        'desc',
        'keterangan',
        'status',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(Jabatan::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Jabatan::class, 'parent_id');
    }

    public function pegawaisAsJenis()
    {
        return $this->hasMany(Pegawai::class, 'jabatan_jenis_id');
    }

    public function pegawaisAsNama()
    {
        return $this->hasMany(Pegawai::class, 'jabatan_nama_id');
    }
}
