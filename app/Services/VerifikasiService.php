<?php

namespace App\Services;

use App\Models\Verifikasi;

class VerifikasiService
{
    /**
     * Ambil histori verifikasi berdasarkan level user.
     */
   public function getHistori($user, $verifiable_id = null, $verifiable_type = null)
{
    // Jika tidak ada ID, kembalikan collection kosong agar tidak muncul semua data
    if (!$verifiable_id) {
        return collect();
    }

    $query = Verifikasi::with('user')
        ->where('verifiable_id', $verifiable_id)
        ->orderBy('updated_at', 'DESC');

    if ($verifiable_type) {
        $query->where('verifiable_type', $verifiable_type);
    }

    return $query->get();
}

    /**
     * Mendapatkan class badge status.
     */
    public function badgeStatus($status)
    {
        return match (strtoupper($status)) {
            'DRAFT'    => 'bg-warning',
            'TERVERIFIKASI' => 'bg-success',
            'REVISI'    => 'bg-warning',
            'DITOLAK'   => 'bg-danger',
            'DITERIMA'   => 'bg-success',
            'DISETUJUI'   => 'bg-success',
            'TERKIRIM'  => 'bg-success',
            'SANGGAH'   => 'bg-danger',
            'SANGGAH DITERIMA'   => 'bg-success',
            'SANGGAH DITOLAK'   => 'bg-danger',
            'VERIFIKASI SANGGAH'   => 'bg-info',
            'REVISI SANGGAH'   => 'bg-warning',
            'PASCA SANGGAH'   => 'bg-success',
            'SELESAI'   => 'bg-success',
            default     => 'bg-secondary'
        };
    }
}
