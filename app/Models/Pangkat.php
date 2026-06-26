<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pangkat extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pangkats';

    protected $fillable = [
        'id',
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

    public function pegawais()
    {
        return $this->hasMany(Pegawai::class, 'pangkat_id');
    }
}
