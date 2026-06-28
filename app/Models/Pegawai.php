<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pegawai extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $table = 'pegawais';

    protected $fillable = [
        'id',
        'user_id',
        'nama',
        'gelar_depan',
        'gelar_belakang',
        'nip',
        'nik',
        'status_id',
        'pangkat_id',
        'jabatan_jenis_id',
        'jabatan_nama_id',
        'bidang_id',
        'jenis_kelamin',
        'agama',
        'pendidikan_terakhir',
        'alamat',
        'telpon',
        'status',
        'periode'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function statusPegawai()
    {
        return $this->belongsTo(StatusPegawai::class, 'status_id');
    }

    public function pangkat()
    {
        return $this->belongsTo(Pangkat::class, 'pangkat_id');
    }

    public function jabatanJenis()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_jenis_id');
    }

    public function jabatanNama()
    {
        return $this->belongsTo(Jabatan::class, 'jabatan_nama_id');
    }

    public function bidang()
    {
        return $this->belongsTo(Page::class, 'bidang_id');
    }

    public function file()
    {
        return $this->morphMany(File::class, 'fileable');
    }

    public function presensiHarian()
    {
        return $this->hasMany(PresensiHarian::class, 'pegawai_id');
    }

    public function getfilebyalias($alias)
    {
        return $this->file()->where('alias', $alias)->first();
    }

    public function getFotoUrlAttribute()
    {
        $fotoFile = $this->getfilebyalias('foto_pegawai');
        if ($fotoFile && $fotoFile->exists) {
            return url($fotoFile->public_stream);
        }

        // Default avatar based on gender (jenis_kelamin)
        if ($this->jenis_kelamin === 'Perempuan') {
            return asset('eduadmin/images/avatar-perempuan.svg');
        }

        return asset('eduadmin/images/avatar-laki.svg');
    }

    public function getJabatanStylingAttribute()
    {
        $parentNama = $this->jabatanJenis ? $this->jabatanJenis->nama : '';

        switch ($parentNama) {
            case 'Pejabat Struktural':
                return [
                    'gradient' => 'linear-gradient(135deg, rgba(0, 242, 254, 0.15) 0%, rgba(0, 114, 255, 0.1) 100%)',
                    'border' => '#00f2fe',
                    'glow' => 'rgba(0, 242, 254, 0.25)',
                    'badge_class' => 'badge-struktural',
                    'badge_bg' => '#00f2fe',
                    'badge_text' => '#0f172a',
                    'theme_color' => '#00f2fe',
                    'text_class' => 'text-cyan'
                ];
            case 'Pejabat Fungsional':
                return [
                    'gradient' => 'linear-gradient(135deg, rgba(168, 85, 247, 0.15) 0%, rgba(126, 34, 206, 0.1) 100%)',
                    'border' => '#a855f7',
                    'glow' => 'rgba(168, 85, 247, 0.25)',
                    'badge_class' => 'badge-fungsional',
                    'badge_bg' => '#a855f7',
                    'badge_text' => '#ffffff',
                    'theme_color' => '#a855f7',
                    'text_class' => 'text-purple'
                ];
            case 'Staf Pelaksana':
                return [
                    'gradient' => 'linear-gradient(135deg, rgba(16, 185, 129, 0.15) 0%, rgba(5, 150, 105, 0.1) 100%)',
                    'border' => '#10b981',
                    'glow' => 'rgba(16, 185, 129, 0.25)',
                    'badge_class' => 'badge-pelaksana',
                    'badge_bg' => '#10b981',
                    'badge_text' => '#ffffff',
                    'theme_color' => '#10b981',
                    'text_class' => 'text-success'
                ];
            case 'PPPK':
                return [
                    'gradient' => 'linear-gradient(135deg, rgba(245, 158, 11, 0.15) 0%, rgba(217, 119, 6, 0.1) 100%)',
                    'border' => '#f59e0b',
                    'glow' => 'rgba(245, 158, 11, 0.25)',
                    'badge_class' => 'badge-pppk',
                    'badge_bg' => '#f59e0b',
                    'badge_text' => '#0f172a',
                    'theme_color' => '#f59e0b',
                    'text_class' => 'text-warning'
                ];
            default:
                return [
                    'gradient' => 'linear-gradient(135deg, rgba(100, 116, 139, 0.15) 0%, rgba(71, 85, 105, 0.1) 100%)',
                    'border' => '#64748b',
                    'glow' => 'rgba(100, 116, 139, 0.2)',
                    'badge_class' => 'badge-default',
                    'badge_bg' => '#64748b',
                    'badge_text' => '#ffffff',
                    'theme_color' => '#64748b',
                    'text_class' => 'text-secondary'
                ];
        }
    }
}

