<?php

namespace App\Models\Ekinerja;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class EkinerjaMasterUnor extends Model
{
    use HasUuids, Loggable, SoftDeletes;

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
