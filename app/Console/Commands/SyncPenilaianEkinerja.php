<?php

namespace App\Console\Commands;

use App\Models\Ekinerja\EkinerjaMasterUnor;
use App\Models\Ekinerja\EkinerjaReferensiPeriode;
use App\Services\Ekinerja\BknApiException;
use App\Services\Ekinerja\EkinerjaService;
use Illuminate\Console\Command;

/**
 * php artisan ekinerja:sync-penilaian [--unor_id=] [--periode_id=] [--semua-periode]
 *
 * Menarik data penilaian seluruh pegawai per Unor + Periode dari API BKN
 * dan menyimpannya ke tabel ekinerja_penilaian (cache lokal).
 *
 * Perilaku default (tanpa opsi):
 * - Loop semua Unor aktif di tabel ekinerja_master_unor.
 * - Untuk setiap Unor, tarik periode berjalan (bulan & tahun sekarang).
 * - Gunakan --semua-periode untuk menarik semua periode yang ada.
 * - Gunakan --unor_id + --periode_id untuk sinkronisasi satu Unor saja.
 *
 * PRD Bab 7.2: Sinkronisasi Otomatis.
 */
class SyncPenilaianEkinerja extends Command
{
    protected $signature = 'ekinerja:sync-penilaian
                            {--unor_id= : Sinkronisasi hanya Unor tertentu (opsional)}
                            {--periode_id= : Sinkronisasi hanya periode tertentu (opsional)}
                            {--semua-periode : Sinkronisasi semua periode yang tersedia (bukan hanya berjalan)}';

    protected $description = 'Sinkronisasi penilaian e-Kinerja per Unor dari API BKN (PRD Bab 7.2)';

    public function handle(EkinerjaService $ekinerjaService): int
    {
        $this->info('[Ekinerja] Memulai sinkronisasi penilaian...');

        // Pastikan referensi periode sudah tersedia
        $ekinerjaService->ensurePeriodeSynced();

        // Tentukan Unor yang akan disinkronisasi
        $unorQuery = EkinerjaMasterUnor::query()->active();
        if ($unorId = $this->option('unor_id')) {
            $unorQuery->where('unor_id', $unorId);
        }
        $unorList = $unorQuery->get();

        if ($unorList->isEmpty()) {
            $this->warn('Tidak ada Unor aktif yang ditemukan. Sinkronisasi dibatalkan.');
            $this->warn('Tip: Pastikan tabel ekinerja_master_unor sudah terisi (terisi otomatis setelah ada data kinerja pertama kali).');
            return self::SUCCESS;
        }

        // Tentukan daftar periode
        if ($periodeId = $this->option('periode_id')) {
            $periodeList = EkinerjaReferensiPeriode::where('periode_id', $periodeId)->get();
        } elseif ($this->option('semua-periode')) {
            $periodeList = EkinerjaReferensiPeriode::orderByDesc('tahun')->get();
        } else {
            // Default: periode berjalan (nama bulan saat ini)
            $bulanIni = now()->translatedFormat('F'); // 'JANUARI', 'FEBRUARI', dst
            $tahunIni = now()->year;
            $periodeList = EkinerjaReferensiPeriode::where('tahun', $tahunIni)
                ->where('nama', 'like', '%' . strtoupper($bulanIni) . '%')
                ->get();
        }

        if ($periodeList->isEmpty()) {
            $this->warn('Tidak ada periode yang cocok untuk disinkronisasi. Pastikan referensi periode sudah tersinkron dari BKN.');
            return self::SUCCESS;
        }

        $totalBerhasil = 0;
        $totalGagal    = 0;

        foreach ($unorList as $unor) {
            foreach ($periodeList as $periode) {
                $this->line("  → Unor: {$unor->nama_unor} | Periode: {$periode->label}");

                try {
                    $result = $ekinerjaService->syncPenilaianByUnor(
                        unorId:      $unor->unor_id,
                        periodeId:   $periode->periode_id,
                        triggeredBy: 'Sistem (Otomatis)',
                    );

                    $totalBerhasil += $result['total_berhasil'];
                    $totalGagal    += $result['total_gagal'];

                    $icon = $result['total_gagal'] === 0 ? '✓' : '⚠';
                    $this->line("    {$icon} {$result['message']}");

                } catch (BknApiException $e) {
                    $this->error("    ✗ Gagal: " . $e->getMessage());
                    $totalGagal++;
                }
            }
        }

        $this->newLine();
        $this->info("[Ekinerja] Sinkronisasi selesai. Total: {$totalBerhasil} berhasil, {$totalGagal} gagal.");

        return self::SUCCESS;
    }
}
