<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Model Pegawai
 *
 * Merepresentasikan tabel pegawais yang sudah ada.
 * Kolom kantor_id & nama_kantor ditambah melalui migration alter.
 */
class Pegawai extends Model
{
    use SoftDeletes;

    protected $table = 'pegawais';

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'nama',
        'nip',
        'nik',
        'gelar_depan',
        'gelar_belakang',
        'kantor_id',
        'nama_kantor',
        'status',
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
     * Nama lengkap dengan gelar depan & belakang
     */
    public function getNamaLengkapAttribute(): string
    {
        $gelarDepan  = $this->gelar_depan  ? $this->gelar_depan . ' '  : '';
        $gelarBelak  = $this->gelar_belakang ? ', ' . $this->gelar_belakang : '';
        return trim($gelarDepan . ($this->nama ?? '') . $gelarBelak);
    }

    /**
     * Relasi ke presensi harian
     */
    public function presensiHarians(): HasMany
    {
        return $this->hasMany(PresensiHarian::class, 'pegawai_id');
    }

    /**
     * Scope untuk filter status aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    /**
     * Scope untuk filter berdasarkan kantor_id
     */
    public function scopeByKantor($query, string $kantorId)
    {
        return $query->where('kantor_id', $kantorId);
    }

    /**
     * Presensi di bulan & tahun tertentu.
     *
     * @param int|null $dayLimit Jika diisi, hanya ambil s.d. tanggal ini (untuk bulan berjalan,
     *                           dibatasi sampai hari kemarin agar tidak menghitung hari yang belum lewat).
     */
    public function presensiByBulan(int $bulan, int $tahun, ?int $dayLimit = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = $this->presensiHarians()
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun);

        if ($dayLimit !== null) {
            $query->whereDay('tanggal', '<=', $dayLimit);
        }

        return $query->orderBy('tanggal')->get();
    }
}