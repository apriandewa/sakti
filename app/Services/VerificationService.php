<?php

namespace App\Services;

class VerificationService
{
    /**
     * Get verification history with formatted data
     *
     * @param mixed $model (Model yang memiliki relasi verifikasi)
     * @return \Illuminate\Support\Collection
     */
    public function getHistory($model)
    {
        if (!$model || !$model->verifikasi) {
            return collect([]);
        }

        return $model->verifikasi->map(function ($verif) {
            return [
                'id'              => $verif->id,
                'status'          => $verif->status,
                'status_badge'    => $this->getStatusBadgeClass($verif->status),
                'status_color'    => $this->getStatusColor($verif->status),
                'status_icon'     => $this->getStatusIcon($verif->status),
                'status_icon_bi'  => $this->getBootstrapStatusIcon($verif->status),
                'user_name'       => $verif->user->name ?? 'N/A',
                'user_level'      => $verif->user->level->name ?? null,
                'catatan'         => $verif->catatan,
                'created_at'      => $verif->created_at,
                'formatted_date'  => $verif->created_at->format('d M Y H:i'),
            ];
        });
    }

    /**
     * Get status badge class
     *
     * @param string $status
     * @return string
     */
    public function getStatusBadgeClass($status)
    {
        $status = strtoupper(trim($status));

        $badges = [
            'DRAFT'        => 'badge-secondary',
            'PENGAJUAN'    => 'badge-info',
            'VERIFIKASI'   => 'badge-primary',
            'REVISI'       => 'badge-warning',
            'DITOLAK'      => 'badge-danger',
            'DITERIMA'     => 'badge-success',
            'PROSES'       => 'badge-warning',
            'DEPLOY'       => 'badge-primary',
            'UJI COBA'     => 'badge-info',
            'RILIS'        => 'badge-success',
            'SELESAI'      => 'badge-success',
            'APPROVED'     => 'badge-success',
            'REJECTED'     => 'badge-danger',
            'AKTIF'        => 'badge-success',
            'NONAKTIF'     => 'badge-danger',
            'MAINTENANCE'  => 'badge-warning',
            'PENGEMBANGAN' => 'badge-info',
            'SUSPEND'      => 'badge-danger',
        ];

        return $badges[$status] ?? 'badge-secondary';
    }

    public function getStatusStyle($status)
    {
        $status = strtoupper(trim($status));
        
        $styles = [
            'DRAFT'        => ['bg' => '#f3f4f6', 'color' => '#374151', 'border' => '#d1d5db'],
            'PENGAJUAN'    => ['bg' => '#e0f2fe', 'color' => '#0369a1', 'border' => '#bae6fd'],
            'VERIFIKASI'   => ['bg' => '#e0e7ff', 'color' => '#4338ca', 'border' => '#c7d2fe'],
            'REVISI'       => ['bg' => '#fef3c7', 'color' => '#b45309', 'border' => '#fcd34d'],
            'DITOLAK'      => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fca5a5'],
            'DITERIMA'     => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0'],
            'PROSES'       => ['bg' => '#ffedd5', 'color' => '#c2410c', 'border' => '#fed7aa'],
            'DEPLOY'       => ['bg' => '#f3e8ff', 'color' => '#6b21a8', 'border' => '#e9d5ff'],
            'UJI COBA'     => ['bg' => '#ecfeff', 'color' => '#0891b2', 'border' => '#cffafe'],
            'RILIS'        => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0'],
            'SELESAI'      => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0'],
            'APPROVED'     => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0'],
            'REJECTED'     => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fca5a5'],
            'AKTIF'        => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0'],
            'NONAKTIF'     => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fca5a5'],
            'MAINTENANCE'  => ['bg' => '#fef3c7', 'color' => '#b45309', 'border' => '#fcd34d'],
            'PENGEMBANGAN' => ['bg' => '#e0f2fe', 'color' => '#0369a1', 'border' => '#bae6fd'],
            'SUSPEND'      => ['bg' => '#fee2e2', 'color' => '#b91c1c', 'border' => '#fca5a5'],
        ];

        return $styles[$status] ?? ['bg' => '#f3f4f6', 'color' => '#374151', 'border' => '#d1d5db'];
    }

    public function renderStatusBadge($status, $extraClass = '')
    {
        $style = $this->getStatusStyle($status);
        $statusText = strtoupper($status);
        return "<span class=\"badge fw-semibold {$extraClass}\" style=\"background-color: {$style['bg']}; color: {$style['color']}; border: 1px solid {$style['border']}; font-size: 11px; padding: 4px 10px; border-radius: 12px;\">{$statusText}</span>";
    }

    /**
     * Get status color for timeline marker
     *
     * @param string $status
     * @return string
     */
    public function getStatusColor($status)
    {
        $status = strtoupper(trim($status));
        $colors = [
            'DRAFT'        => '#6c757d',
            'PENGAJUAN'    => '#17a2b8',
            'VERIFIKASI'   => '#007bff',
            'REVISI'       => '#ffc107',
            'DITOLAK'      => '#dc3545',
            'DITERIMA'     => '#28a745',
            'PROSES'       => '#fd7e14',
            'DEPLOY'       => '#007bff',
            'UJI COBA'     => '#17a2b8',
            'RILIS'        => '#28a745',
            'SELESAI'      => '#28a745',
            'APPROVED'     => '#28a745',
            'REJECTED'     => '#dc3545',
            'AKTIF'        => '#28a745',
            'NONAKTIF'     => '#dc3545',
            'MAINTENANCE'  => '#ffc107',
            'PENGEMBANGAN' => '#17a2b8',
            'SUSPEND'      => '#dc3545',
        ];

        return $colors[$status] ?? '#6c757d';
    }

    /**
     * Get status icon
     *
     * @param string $status
     * @return string
     */
    public function getStatusIcon($status)
    {
        $status = strtoupper(trim($status));
        $icons = [
            'DRAFT'        => 'fa-file-o',
            'PENGAJUAN'    => 'fa-paper-plane',
            'VERIFIKASI'   => 'fa-search',
            'REVISI'       => 'fa-edit',
            'DITOLAK'      => 'fa-times-circle',
            'DITERIMA'     => 'fa-check',
            'PROSES'       => 'fa-cogs',
            'DEPLOY'       => 'fa-cloud-upload',
            'UJI COBA'     => 'fa-flask',
            'RILIS'        => 'fa-flag-checkered',
            'SELESAI'      => 'fa-check-circle',
            'APPROVED'     => 'fa-check',
            'REJECTED'     => 'fa-times',
            'AKTIF'        => 'fa-check-circle',
            'NONAKTIF'     => 'fa-times-circle',
            'MAINTENANCE'  => 'fa-wrench',
            'PENGEMBANGAN' => 'fa-code',
            'SUSPEND'      => 'fa-ban',
        ];

        return $icons[$status] ?? 'fa-circle';
    }

    /**
     * Get bootstrap status icon
     *
     * @param string $status
     * @return string
     */
    public function getBootstrapStatusIcon($status)
    {
        $status = strtoupper(trim($status));
        $icons = [
            'DRAFT'        => 'bi-file-earmark-text',
            'PENGAJUAN'    => 'bi-send',
            'VERIFIKASI'   => 'bi-search',
            'REVISI'       => 'bi-pencil-square',
            'DITOLAK'      => 'bi-x-circle',
            'DITERIMA'     => 'bi-check-circle',
            'PROSES'       => 'bi-gear',
            'DEPLOY'       => 'bi-cloud-arrow-up',
            'UJI COBA'     => 'bi-bug',
            'RILIS'        => 'bi-rocket',
            'SELESAI'      => 'bi-check-circle',
            'APPROVED'     => 'bi-check',
            'REJECTED'     => 'bi-x-circle',
            'AKTIF'        => 'bi-check-circle',
            'NONAKTIF'     => 'bi-x-circle',
            'MAINTENANCE'  => 'bi-wrench',
            'PENGEMBANGAN' => 'bi-code-slash',
            'SUSPEND'      => 'bi-ban',
        ];

        return $icons[$status] ?? 'bi-circle';
    }

    /**
     * Check if history is empty
     *
     * @param \Illuminate\Support\Collection $history
     * @return bool
     */
    public function isEmpty($history)
    {
        return $history->isEmpty();
    }

    /**
     * Get documents grouped by tahapan for a given permohonan
     *
     * @param \App\Models\Permohonan|null $permohonan
     * @return array
     */
    public function getDokumenTahapanData($permohonan)
    {
        if (!$permohonan) {
            return [
                'tahapan_list' => collect(),
                'currentTahapanNama' => 'PERMOHONAN',
                'dokumen_per_tahapan' => [],
            ];
        }

        $tahapan_list = \App\Models\Tahapan::whereNull('tahapans.deleted_at')
            ->orderBy('tahapans.created_at')
            ->get();

        $map = [
            'DRAFT'      => 'PERMOHONAN',
            'PENGAJUAN'  => 'PERMOHONAN',
            'REVISI'     => 'PERMOHONAN',
            'VERIFIKASI' => 'VERIFIKASI',
            'DITOLAK'    => 'VERIFIKASI',
            'PROSES'     => 'PROSES',
            'DEPLOY'     => 'PROSES',
            'UJI COBA'   => 'UJICOBA',
            'RILIS'      => 'RILIS',
            'SELESAI'    => 'SELESAI',
        ];
        $currentTahapanNama = $map[strtoupper($permohonan->status)] ?? 'PERMOHONAN';
        
        $tahapanColors = [
            'permohonan' => '#17a2b8',
            'verifikasi' => '#6f42c1',
            'proses'     => '#007bff',
            'deploy'     => '#343a40',
            'ujicoba'    => '#fd7e14',
            'rilis'      => '#28a745',
        ];
        $tahapanIcons = [
            'permohonan' => 'bi-file-earmark-text',
            'verifikasi' => 'bi-check-circle',
            'proses'     => 'bi-gear',
            'deploy'     => 'bi-cloud-arrow-up',
            'ujicoba'    => 'bi-bug',
            'rilis'      => 'bi-rocket-takeoff',
        ];

        $dokumen_per_tahapan = [];
        foreach ($tahapan_list as $tahapan) {
            $tahapanKey = strtolower(str_replace(' ', '', $tahapan->nama));

            if (method_exists($tahapan, 'berkas') && !$tahapan->relationLoaded('berkas')) {
                $tahapan->load('berkas');
            }
            $berkasItems = $tahapan->berkas ?? collect();

            $items = [];
            foreach ($berkasItems as $berkas) {
                $allFiles = $permohonan->file()
                    ->withTrashed()
                    ->where('alias', $berkas->alias)
                    ->where('fileable_id', $permohonan->id)
                    ->orderBy('created_at', 'asc')
                    ->get();

                $activeFile = $allFiles->whereNull('deleted_at')->last();

                $items[] = [
                    'label'      => $berkas->nama,
                    'alias'      => $berkas->alias,
                    'wajib'      => $berkas->pivot->wajib ?? false,
                    'file'       => $activeFile,
                    'exists'     => $activeFile && $activeFile->exists(),
                    'all_files'  => $allFiles,
                ];
            }

            $dokumen_per_tahapan[$tahapan->nama] = [
                'items' => $items,
                'color' => $tahapanColors[$tahapanKey] ?? '#6c757d',
                'icon'  => $tahapanIcons[$tahapanKey] ?? 'bi-folder',
            ];
        }

        return [
            'tahapan_list' => $tahapan_list,
            'currentTahapanNama' => $currentTahapanNama,
            'dokumen_per_tahapan' => $dokumen_per_tahapan,
        ];
    }
}
