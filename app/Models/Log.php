<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Log extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'logs';

    protected $casts = [
        'id'   => 'string',
        'data' => 'array', // 🔥 cukup ini saja
    ];

    protected $fillable = [
        'id',
        'loggable_type',
        'loggable_id',
        'ip',
        'user_agent',
        'data'
    ];

    // 🔥 morph relation
    public function loggable()
    {
        return $this->morphTo();
    }

    // 🔥 ambil user dari JSON
    public function user()
    {
        return \App\Models\User::find($this->data['user_id'] ?? null);
    }

    // 🔥 format tanggal
    public function getDateAttribute()
    {
        return $this->created_at?->format('d-m-Y');
    }

    public function getTimeAttribute()
    {
        return $this->created_at?->format('H:i:s');
    }
}