<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiSyncLog extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'presensi_sync_logs';

    protected $fillable = [
        'tahun',
        'bulan',
        'triggered_by',
        'status',
        'total_pegawai_synced',
        'total_pegawai_skipped',
        'message',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'bulan' => 'integer',
        'total_pegawai_synced' => 'integer',
        'total_pegawai_skipped' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
