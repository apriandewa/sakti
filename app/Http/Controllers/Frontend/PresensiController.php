<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PresensiController extends Controller
{
    private $baseUrl = "https://api-absensi.simpegnas.go.id/absensi/api";

    private function headers()
    {
        return [
            'presensi-key' => env('API_ABSENSI_TOKEN'),
            'Accept' => 'application/json'
        ];
    }

    // =========================
    // 🔥 LIBUR NASIONAL
    // =========================
    private function getHariLibur()
    {
        try {
            $res = Http::timeout(10)
                ->get("https://date.nager.at/api/v3/PublicHolidays/" . now()->year . "/ID");

            if ($res->successful()) {
                return collect($res->json())->pluck('date')->toArray();
            }
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }

        return [];
    }

    // =========================
    // 🔥 KALENDER BULANAN (FIX ERROR)
    // =========================
    private function kalenderKerja($libur)
    {
        $start = now()->copy()->startOfMonth();
        $end   = now()->copy()->endOfMonth();

        $hariKerja = 0;
        $weekend = 0;
        $liburNasional = 0;

        while ($start <= $end) {

            if ($start->isWeekend()) {
                $weekend++;
            } elseif (in_array($start->format('Y-m-d'), $libur)) {
                $liburNasional++;
            } else {
                $hariKerja++;
            }

            $start->addDay();
        }

        return [
            'hari_kerja' => $hariKerja,
            'weekend' => $weekend,
            'libur' => $liburNasional
        ];
    }

    // =========================
    // 🔥 CEK HARI KERJA
    // =========================
    private function isHariKerja($tgl, $libur)
    {
        $date = Carbon::parse($tgl);

        return !($date->isWeekend() || in_array($date->format('Y-m-d'), $libur));
    }

    // =========================
    // 🔥 FORMAT TANGGAL
    // =========================
    private function formatTanggal($tgl)
    {
        return Carbon::parse($tgl)
            ->locale('id')
            ->translatedFormat('l, d F Y');
    }

    // =========================
    // 🔥 STATUS MAPPING
    // =========================
    private function mapStatus($status)
    {
        return match($status) {
            'TM' => ['label'=>'Telat Masuk','class'=>'warning'],
            'PC' => ['label'=>'Pulang Cepat','class'=>'secondary'],
            'HN'  => ['label'=>'Hadir','class'=>'success'],
            'TK'  => ['label'=>'Tanpa Keterangan','class'=>'danger'],
            default => ['label'=>$status ?? '-','class'=>'dark']
        };
    }

    // =========================
    // 🔥 SUMMARY GLOBAL (KONSISTEN)
    // =========================
    private function hitungSummary($presensi, $libur)
    {
        $s = [
            'hari_kerja'=>0,
            'hadir'=>0,
            'tk'=>0,
            'telat'=>0,
            'pulang_cepat'=>0
        ];

        foreach ($presensi as $item) {

            if (!isset($item['tgl'])) continue;
            if (!$this->isHariKerja($item['tgl'], $libur)) continue;

            $s['hari_kerja']++;

            $status = $item['keterangan'] ?? '';

            if ($status == 'HN') $s['hadir']++;
            if ($status == 'TK') $s['tk']++;
            if ($status == 'TM') $s['telat']++;
            if ($status == 'PC') $s['pulang_cepat']++;
        }

        return $s;
    }

    // =========================
    // =========================
    // 🔵 INDEX (DASHBOARD INSTANSI - FIX STABIL)
    // =========================
    public function index()
    {
        $kantor_id = 'fbc6ca94-d421-48f0-a6d7-d2bcf392c6f2';

        $pegawaiList = [];

        $libur = $this->getHariLibur();
        $kalender = $this->kalenderKerja($libur);

        $start = now()->startOfMonth()->format('Y-m-d');
        $end   = now()->format('Y-m-d');

        $today = now()->format('Y-m-d');

        // =========================
        // 🔥 STATISTIK HARI INI
        // =========================
        $statToday = [
            'pegawai' => 0,
            'hadir' => 0,
            'terlambat' => 0,
            'tidak_hadir' => 0,
        ];

        // =========================
        // 🔥 STATISTIK BULANAN
        // =========================
        $statMonth = [
            'kehadiran_total' => 0,
            'hadir' => 0,
            'terlambat' => 0,
            'tidak_hadir' => 0,
            'pulang_cepat' => 0,
        ];

        try {
            $res = Http::withHeaders($this->headers())
                ->get("$this->baseUrl/get/rekap-by-kantor", [
                    'kantor_id' => $kantor_id,
                    'start_date' => $start,
                    'end_date'   => $end
                ]);

            if ($res->successful()) {

                $data = $res->json();

                // 🔥 pastikan key ada
                $pegawaiList = $data['data'] ?? [];

                // 🔥 jumlah pegawai
                $statToday['pegawai'] = count($pegawaiList);

                foreach ($pegawaiList as &$p) {

                    $presensi = $p['presensi'] ?? [];

                    // 🔥 SUMMARY (biar tetap ada di blade lama)
                    $p['summary'] = $this->hitungSummary($presensi, $libur);

                    foreach ($presensi as $item) {

                        if (!isset($item['tgl'])) continue;

                        $tgl = Carbon::parse($item['tgl'])->format('Y-m-d');

                        $jam_pagi  = $item['jam_pagi'] ?? null;
                        $jam_siang = $item['jam_siang'] ?? null;
                        $jam_sore  = $item['jam_sore'] ?? null;

                        $isHadir = $jam_pagi || $jam_siang || $jam_sore;
                        $isTelat = $jam_pagi && strtotime($jam_pagi) > strtotime('08:00:00');
                        $isPulangCepat = $jam_sore && strtotime($jam_sore) < strtotime('15:59:00');
                        $isTidakHadir = !$jam_pagi && !$jam_siang && !$jam_sore;

                        // =========================
                        // 🔥 STAT TODAY (1 RECORD PER PEGAWAI)
                        // =========================
                        if ($tgl == $today) {

                            if ($isHadir) $statToday['hadir']++;
                            if ($isTelat) $statToday['terlambat']++;
                            if ($isTidakHadir) $statToday['tidak_hadir']++;
                        }

                        // =========================
                        // 🔥 BULANAN (ONLY HARI KERJA)
                        // =========================
                        if (!$this->isHariKerja($tgl, $libur)) continue;

                        $statMonth['kehadiran_total']++;

                        if ($isHadir) $statMonth['hadir']++;
                        if ($isTelat) $statMonth['terlambat']++;
                        if ($isPulangCepat) $statMonth['pulang_cepat']++;
                        if ($isTidakHadir) $statMonth['tidak_hadir']++;
                    }
                }

                // 🔥 ranking tetap jalan
                usort($pegawaiList, function ($a, $b) {
                    return ($b['summary']['hadir'] ?? 0) <=> ($a['summary']['hadir'] ?? 0);
                });
            }

        } catch (\Exception $e) {
            Log::error("ERROR INDEX PRESENSI: " . $e->getMessage());
        }

        return view('frontend.presensi.index', compact(
            'pegawaiList',
            'kalender',
            'statToday',
            'statMonth'
        ));
    }
    
   // =========================
    /// =========================
public function detail($nip)
{
    // $kantor_id = '0055a372-6b7a-4a2f-9a10-3294fd18896e';
    $kantor_id = 'fbc6ca94-d421-48f0-a6d7-d2bcf392c6f2';

    $pegawai = null;
    $libur = $this->getHariLibur();

    $start = now()->startOfMonth()->format('Y-m-d');
    $end   = now()->format('Y-m-d');

    // 🔥 kontrol hari ini
    $today = now()->format('Y-m-d');
    $batasJam = now()->setTime(18, 0, 0);

    try {
        $res = Http::withHeaders($this->headers())
            ->get("$this->baseUrl/get/rekap-by-kantor", [
                'kantor_id' => $kantor_id,
                'start_date' => $start,
                'end_date'   => $end
            ]);

        if ($res->successful()) {

            foreach ($res->json()['data'] ?? [] as $p) {

                if (($p['nip'] ?? null) == $nip) {

                    $pegawai = $p;
                    $filtered = [];

                    foreach ($p['presensi'] ?? [] as $item) {

                        if (!isset($item['tgl'])) continue;

                        // ❌ skip hari ini jika belum jam 18:00
                        if ($item['tgl'] == $today && now()->lt($batasJam)) {
                            continue;
                        }

                        if (!$this->isHariKerja($item['tgl'], $libur)) continue;

                        $map = $this->mapStatus($item['keterangan'] ?? '');

                        $item['tgl_indonesia'] = $this->formatTanggal($item['tgl']);
                        $item['status_label'] = $map['label'];
                        $item['status_class'] = $map['class'];

                        // =========================
                        // 🔥 LOGIKA WAKTU + POTONGAN
                        // =========================
                        $ket = [];
                        $potongan = 0;

                        $jam_pagi = $item['jam_pagi'] ?? null;
                        $jam_sore = $item['jam_sore'] ?? null;

                        $standarMasuk = strtotime('08:00:00');
                        $standarPulang = strtotime('15:59:00');

                        // ❌ Tidak hadir
                        if (empty($jam_pagi) && empty($jam_sore)) {

                            $ket[] = "Tidak Hadir";
                            $potongan += 3;

                        } else {

                            // 🔴 Telat
                            if (empty($jam_pagi)) {

                                $ket[] = "Tidak absen masuk";
                                $potongan += 1.5;

                                } else {


                                $jamMasuk = strtotime($jam_pagi);

                                if ($jamMasuk > $standarMasuk) {
                                    $menit = floor(($jamMasuk - $standarMasuk) / 60);
                                    $ket[] = "Telat {$menit} menit";

                                    if ($menit <= 30) $potongan += 0.5;
                                    elseif ($menit <= 60) $potongan += 1;
                                    elseif ($menit <= 90) $potongan += 1.25;
                                    else $potongan += 1.5;
                                }
                            }

                            // 🔵 Pulang cepat / tidak absen
                            if (empty($jam_sore)) {

                                $ket[] = "Tidak absen pulang";
                                $potongan += 1.5;

                            } else {

                                $jamPulang = strtotime($jam_sore);

                                if ($jamPulang < $standarPulang) {
                                    $menit = floor(($standarPulang - $jamPulang) / 60);
                                    $ket[] = "Pulang cepat {$menit} menit";

                                    if ($menit <= 30) $potongan += 0.5;
                                    elseif ($menit <= 60) $potongan += 1;
                                    elseif ($menit <= 90) $potongan += 1.25;
                                    else $potongan += 1.5;
                                }
                            }
                        }

                        $item['keterangan_waktu'] = !empty($ket) ? implode(' | ', $ket) : '-';
                        $item['potongan'] = $potongan;

                        $filtered[] = $item;
                    }

                    // 🔥 hasil filter
                    $pegawai['presensi'] = $filtered;

                    // 🔥 summary WAJIB pakai filtered
                    $pegawai['summary'] = $this->hitungSummary($filtered, $libur);

                    // 🔥 total potongan
                    $pegawai['total_potongan'] = collect($filtered)->sum('potongan');

                    // 🔥 ambil foto
                    try {
                        $imgResponse = Http::withHeaders($this->headers())
                            ->get("$this->baseUrl/get/image", ['nip' => $nip]);

                        if ($imgResponse->successful()) {
                            $pegawai['foto'] =
                                $imgResponse->json()['data']['register'][0]['image_base64'] ?? null;
                        } else {
                            $pegawai['foto'] = null;
                        }
                    } catch (\Exception $e) {
                        Log::error($e->getMessage());
                        $pegawai['foto'] = null;
                    }

                    break;
                }
            }
        }

    } catch (\Exception $e) {
        Log::error($e->getMessage());
    }

    return view('frontend.presensi.detail', compact('pegawai'));
}
}