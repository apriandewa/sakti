<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class PresensiHarian extends Model
{
    protected $table = 'presensi_harians';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran',
        'kategori_terlambat',
        'menit_terlambat',
        'potongan_terlambat',
        'kategori_pulang_cepat',
        'menit_pulang_cepat',
        'potongan_pulang_cepat',
        'total_potongan',
        'work_from_masuk',
        'work_from_keluar',
        'keterangan',
        'is_sync',
        'synced_at',
    ];

    protected $casts = [
        'tanggal'           => 'date',
        'menit_terlambat'   => 'integer',
        'menit_pulang_cepat'=> 'integer',
        'potongan_terlambat'    => 'decimal:2',
        'potongan_pulang_cepat' => 'decimal:2',
        'total_potongan'        => 'decimal:2',
        'is_sync'           => 'boolean',
        'synced_at'         => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    /**
     * Relasi ke pegawai
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Badge class warna berdasarkan status kehadiran (untuk view)
     */
    public function getBadgeClassAttribute(): string
    {
        return match(strtoupper($this->status_kehadiran ?? '')) {
            'HN'                        => 'badge-success',
            'TK'                        => 'badge-danger',
            'CT', 'CUTI'                => 'badge-info',
            'DL', 'DINAS LUAR'         => 'badge-primary',
            'TM1','TM2','TM3','TM4','TMM','PC1','PC2','PC3','PC4','PCM' => 'badge-warning',
            default                     => 'badge-secondary',
        };
    }

    /**
     * Label status yang ramah ditampilkan
     */
    public function getLabelStatusAttribute(): string
    {
        return match(strtoupper($this->status_kehadiran ?? '')) {
            'HN'   => 'Hadir Normal',
            'TK'   => 'Tanpa Keterangan',
            'CT'   => 'Cuti',
            'DL'   => 'Dinas Luar',
            'IZIN' => 'Izin',
            'LN'   => 'Libur Nasional',
            'WFH'  => 'Work From Home',
            'TM1'  => 'Terlambat 1',
            'TM2'  => 'Terlambat 2',
            'TM3'  => 'Terlambat 3',
            'TM4'  => 'Terlambat 4',
            'TMM'  => 'Terlambat Tanpa Ket.',
            'PC1'  => 'Pulang Cepat 1',
            'PC2'  => 'Pulang Cepat 2',
            'PC3'  => 'Pulang Cepat 3',
            'PC4'  => 'Pulang Cepat 4',
            'PCM'  => 'Pulang Cepat Tanpa Ket.',
            default => $this->status_kehadiran ?? '-',
        };
    }
}
