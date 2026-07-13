<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class PresensiSyncLog extends Model
{
    protected $table = 'presensi_sync_logs';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'kantor_id',
        'bulan',
        'tahun',
        'sync_by',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'jumlah_data_ditarik',
        'catatan_pesan',
    ];

    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }
}
