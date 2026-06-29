<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class DokumenTte extends Model
{
    use HasUuids;

    protected $table = 'dokumen_ttes';

    protected $fillable = [
        'id', 'agenda_rapat_id', 'jenis_dokumen', 'pegawai_id',
        'signed_file', 'original_file', 'status', 'bsre_response', 'signed_at'
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    public function agendaRapat()
    {
        return $this->belongsTo(AgendaRapat::class);
    }

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    public function penandaTangan()
    {
        return $this->pegawai();
    }

    public function file()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * Badge status tanda tangan
     */
    public function getBadgeStatusAttribute()
    {
        $map = [
            'pending' => ['bg' => '#fef3c7', 'color' => '#b45309', 'icon' => 'fa-clock-o', 'label' => 'Menunggu'],
            'signed'  => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'fa-check-circle', 'label' => 'Sudah Ditandatangani'],
            'failed'  => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'icon' => 'fa-times-circle', 'label' => 'Gagal'],
        ];

        $s = $map[$this->status] ?? $map['pending'];
        return '<span class="badge fw-semibold" style="background-color: '.$s['bg'].'; color: '.$s['color'].'; border: 1px solid '.$s['color'].'; font-size: 11px; padding: 4px 10px; border-radius: 12px;"><i class="fa '.$s['icon'].'"></i> '.$s['label'].'</span>';
    }
}
