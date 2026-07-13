<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\Loggable;

class AgendaRapat extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Loggable;

    protected $fillable = [
        'id', 'nama', 'tanggal', 'jam_mulai', 'jam_selesai', 'tempat',
        'acara', 'deskripsi', 'catatan', 'status',
        'dasar_dari', 'dasar_no', 'dasar_tgl', 'dasar_hal',
        'surat_nomor', 'surat_sifat', 'surat_lampiran', 'surat_hal', 'surat_tujuan',
        'barcode_token', 'user_id', 'pegawai_id', 'jenis_tanda_tangan',
        'tipe_rapat', 'zoom_meeting_id', 'zoom_password', 'jenis_tujuan_surat', 'surat_tujuan_lampiran'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'dasar_tgl' => 'date',
    ];

    protected $table = 'agenda_rapats';

    // ===== Relationships =====

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function file()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function files()
    {
        return $this->hasMany(File::class, 'fileable_id');
    }

    public function verifikasi()
    {
        return $this->hasMany(RapatVerifikasi::class)->with('user')->oldest('created_at');
    }

    public function peserta()
    {
        return $this->hasMany(RapatPeserta::class)->oldest('waktu_hadir');
    }

    public function notulen()
    {
        return $this->hasOne(RapatNotulen::class);
    }

    public function dokumenTte()
    {
        return $this->hasMany(DokumenTte::class);
    }

    /**
     * Get dokumen TTE by jenis (undangan, daftar_hadir, notulen)
     */
    public function getDokumenTteByJenis($jenis)
    {
        return $this->dokumenTte()->where('jenis_dokumen', $jenis)->first();
    }

    // ===== File Helpers =====

    public function getfilebyalias($alias)
    {
        return $this->file()->where('alias', $alias)->first();
    }

    public function getfilesbyalias($alias)
    {
        return $this->files()->where('alias', $alias)->get();
    }

    // ===== Status Helpers =====

    public function getStatusMapAttribute()
    {
        $statusMap = [
            'DRAFT'     => ['bg' => '#f3f4f6', 'color' => '#374151', 'border' => '#d1d5db', 'desc' => 'Agenda rapat masih berupa draf.'],
            'PENGAJUAN' => ['bg' => '#e0f2fe', 'color' => '#0369a1', 'border' => '#bae6fd', 'desc' => 'Agenda rapat telah diajukan untuk verifikasi.'],
            'DITERIMA'  => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0', 'desc' => 'Agenda rapat diterima dan siap dilaksanakan.'],
            'REVISI'    => ['bg' => '#fef3c7', 'color' => '#b45309', 'border' => '#fcd34d', 'desc' => 'Agenda rapat perlu revisi sesuai catatan.'],
            'DITOLAK'   => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fca5a5', 'desc' => 'Agenda rapat ditolak.'],
        ];
        return $statusMap[strtoupper($this->status)] ?? ['bg' => '#f3f4f6', 'color' => '#374151', 'border' => '#d1d5db', 'desc' => 'Status tidak diketahui.'];
    }

    public function getBadgeStatusAttribute()
    {
        $mapped = $this->status_map;
        $status = $this->status ?: '-';
        return "<span class=\"badge fw-semibold\" style=\"background-color: {$mapped['bg']}; color: {$mapped['color']}; border: 1px solid {$mapped['border']}; font-size: 11px; padding: 4px 10px; border-radius: 12px;\">{$status}</span>";
    }

    // ===== Attendance Link =====

    public function getAbsensiUrlAttribute()
    {
        return url('/rapat/absensi/' . $this->barcode_token);
    }
}
