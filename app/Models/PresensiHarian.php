<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiHarian extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'presensi_harians';

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'work_from_masuk',
        'work_from_keluar',
        'status_kehadiran',
        'kategori_terlambat',
        'menit_terlambat',
        'kategori_pulang_cepat',
        'menit_pulang_cepat',
        'potongan_terlambat',
        'potongan_pulang_cepat',
        'total_potongan',
        'keterangan',
        'is_sync',
        'synced_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_sync' => 'boolean',
        'synced_at' => 'datetime',
        'potongan_terlambat' => 'decimal:2',
        'potongan_pulang_cepat' => 'decimal:2',
        'total_potongan' => 'decimal:2'
    ];

    /**
     * Relasi ke model Pegawai
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Mengambil bobot potongan berdasarkan tipe (TM/PC) dan kategori
     */
    public static function getDeductionWeight(string $type, ?string $category): float
    {
        if (empty($category)) {
            return 0.00;
        }

        $category = strtoupper(trim($category));

        if ($type === 'TM') {
            return match ($category) {
                'TM1' => 0.50,
                'TM2' => 1.00,
                'TM3' => 1.25,
                'TM4' => 1.50,
                'TM5', 'TM6', 'TMM' => 0.00,
                default => 0.00
            };
        }

        if ($type === 'PC') {
            return match ($category) {
                'PC1' => 1.50,
                'PC2' => 1.25,
                'PC3' => 1.00,
                'PC4' => 0.50,
                'PC5', 'PC6', 'PCM' => 0.00,
                default => 0.00
            };
        }

        return 0.00;
    }

    /**
     * Mengambil class badge untuk UI berdasarkan status kehadiran
     */
    public static function getStatusBadgeClass(?string $status): string
    {
        if (empty($status)) {
            return 'badge-secondary';
        }
        
        return match(strtoupper(trim($status))) {
            'HN', 'WFO' => 'badge-success',
            'TK' => 'badge-danger',
            'CT', 'CB', 'CM', 'CS', 'CKAP' => 'badge-info',
            'DL', 'TB' => 'badge-primary',
            'IZIN', 'IDL', 'IDLI', 'IDLO', 'ITM', 'IPC', 'ITMPC' => 'badge-warning text-dark',
            'LN', 'LJ', 'LS', 'LM', 'L', 'OFF' => 'badge-light text-dark',
            default => 'badge-secondary'
        };
    }
}
