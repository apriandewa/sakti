<?php

namespace App\Models\Ekinerja;

use App\Traits\HasUuid; // TODO: sesuaikan namespace trait existing project
use Illuminate\Database\Eloquent\Model;

class EkinerjaPenilaian extends Model
{
    use HasUuid;

    protected $table = 'ekinerja_penilaian';

    protected $fillable = [
        'bkn_id', 'jenis', 'nip', 'nama',
        'periode_awal_skp', 'periode_akhir_skp',
        'skp_unor_id', 'skp_unor', 'skp_unor_induk',
        'skp_jabatan', 'skp_jenis_jabatan', 'is_skp_plt_plh_pjb',
        'hasil_kerja', 'perilaku_kerja', 'hasil_akhir',
        'pegawai_atasan_id', 'pegawai_atasan_nip', 'pegawai_atasan_nama',
        'pegawai_atasan_unor_id', 'pegawai_atasan_unor',
        'pegawai_atasan_jabatan', 'pegawai_atasan_golru',
        'waktu_dinilai', 'pegawai_penilai_id', 'tahun_skp',
        'skp_id', 'periode_id', 'skp_penilaian_id',
        'golru', 'jenis_pegawai', 'raw_response', 'source', 'synced_at',
    ];

    protected $casts = [
        'periode_awal_skp'   => 'date',
        'periode_akhir_skp'  => 'date',
        'waktu_dinilai'      => 'datetime',
        'is_skp_plt_plh_pjb' => 'boolean',
        'raw_response'       => 'array',
        'synced_at'          => 'datetime',
    ];

    public function periode()
    {
        return $this->belongsTo(EkinerjaReferensiPeriode::class, 'periode_id', 'periode_id');
    }

    public function unor()
    {
        return $this->belongsTo(EkinerjaMasterUnor::class, 'skp_unor_id', 'unor_id');
    }
}
