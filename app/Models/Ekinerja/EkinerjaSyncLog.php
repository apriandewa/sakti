<?php

namespace App\Models\Ekinerja;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Model;

/**
 * Model log sinkronisasi e-Kinerja (PRD Bab 8.4).
 * Format disamakan dengan PresensiSyncLog.
 *
 * @property string  $id
 * @property string|null $unor_id
 * @property string|null $nama_unor
 * @property string|null $periode_id
 * @property string  $sync_by
 * @property string  $status  — 'berjalan', 'sukses', 'gagal'
 * @property \Carbon\Carbon|null $waktu_mulai
 * @property \Carbon\Carbon|null $waktu_selesai
 * @property int|null $jumlah_data_ditarik
 * @property int|null $jumlah_gagal
 * @property string|null $catatan_pesan
 */
class EkinerjaSyncLog extends Model
{
    use HasUuid;

    protected $table = 'ekinerja_sync_logs';

    protected $fillable = [
        'unor_id',
        'nama_unor',
        'periode_id',
        'sync_by',
        'status',
        'waktu_mulai',
        'waktu_selesai',
        'jumlah_data_ditarik',
        'jumlah_gagal',
        'catatan_pesan',
    ];

    protected $casts = [
        'waktu_mulai'   => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    /** Relasi ke master unor */
    public function unor()
    {
        return $this->belongsTo(EkinerjaMasterUnor::class, 'unor_id', 'unor_id');
    }

    /** Durasi sinkronisasi (detik) */
    public function getDurasiAttribute(): ?int
    {
        if ($this->waktu_mulai && $this->waktu_selesai) {
            return (int) $this->waktu_mulai->diffInSeconds($this->waktu_selesai);
        }

        return null;
    }

    /** Badge warna status */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'sukses'   => '<span class="badge badge-success">Sukses</span>',
            'gagal'    => '<span class="badge badge-danger">Gagal</span>',
            'berjalan' => '<span class="badge badge-warning">Berjalan</span>',
            default    => '<span class="badge badge-secondary">' . htmlspecialchars($this->status) . '</span>',
        };
    }
}
