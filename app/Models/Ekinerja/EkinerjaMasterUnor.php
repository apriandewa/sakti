<?php

namespace App\Models\Ekinerja;

use App\Traits\HasUuid;  // TODO: sesuaikan namespace trait existing project
use App\Traits\Logable;  // TODO: sesuaikan namespace trait existing project
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EkinerjaMasterUnor extends Model
{
    use HasUuid, Logable, SoftDeletes;

    protected $table = 'ekinerja_master_unor';

    protected $fillable = [
        'unor_id',
        'nama_unor',
        'unor_induk',
        'opd_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
