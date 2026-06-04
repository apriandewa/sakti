<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PegawaiController extends Controller
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
    // 🔥 GET KANTOR
    // =========================
    private function getKantor()
    {
        return Cache::remember('kantor_list', 600, function () {
            try {
                $res = Http::withHeaders($this->headers())
                    ->timeout(10)
                    ->retry(2, 1000)
                    ->get($this->baseUrl.'/get/kantor');

                if ($res->successful()) {
                    return $res->json()['data']['kantor'] ?? [];
                }

            } catch (\Exception $e) {
                Log::error('GET KANTOR ERROR: '.$e->getMessage());
            }

            return [];
        });
    }

    // =========================
    // 🔥 REKAP GLOBAL (CACHE)
    // =========================
    private function getRekapGlobal($dates)
    {
        $key = 'rekap_global_'.md5(json_encode($dates));

        return Cache::remember($key, 300, function () use ($dates) {
            return $this->generateRekap($dates);
        });
    }

    // =========================
    // 🔥 GENERATE DATA (HEAVY)
    // =========================
    private function generateRekap($dates)
    {
        // 🔥 LIMIT BIAR GAK LEMOT (ubah kalau sudah stabil)
        $kantorList = collect($this->getKantor())->take(50);

        $rekap = [];

        foreach ($kantorList as $kantor) {

            $kantor_id  = $kantor['id'] ?? null;
            $namaKantor = $kantor['nama_kantor'] ?? $kantor['nama'] ?? 'Tanpa Nama';

            if (!$kantor_id) continue;

            // init kantor
            $rekap[$kantor_id] = [
                'nama' => $namaKantor,
                'total_asn' => 0,
                'tanggal' => []
            ];

            // init tanggal
            foreach ($dates as $tgl) {
                $rekap[$kantor_id]['tanggal'][$tgl] = [
                    'wfo' => 0,
                    'wfh' => 0,
                    'tidak_hadir' => 0
                ];
            }

            try {

                $res = Http::withHeaders($this->headers())
                    ->timeout(20)
                    ->retry(2, 1000)
                    ->get($this->baseUrl.'/get/rekap-by-kantor', [
                        'kantor_id' => $kantor_id,
                        'start_date' => min($dates),
                        'end_date'   => max($dates)
                    ]);

                if (!$res->successful()) {
                    Log::error('API ERROR', [
                        'kantor_id' => $kantor_id,
                        'status' => $res->status()
                    ]);
                    continue;
                }

                $pegawaiList = $res->json()['data'] ?? [];

                // total ASN
                $rekap[$kantor_id]['total_asn'] = count($pegawaiList);

                foreach ($pegawaiList as $p) {

                    foreach ($p['presensi'] ?? [] as $item) {

                        $tgl = $item['tgl'] ?? null;
                        if (!$tgl || !in_array($tgl, $dates)) continue;

                        $jam_pagi  = $item['jam_pagi'] ?? null;
                        $jam_siang = $item['jam_siang'] ?? null;
                        $jam_sore  = $item['jam_sore'] ?? null;

                        $isHadir = $jam_pagi || $jam_siang || $jam_sore;

                        $isWFH =
                            ($item['pagi'] ?? '') === 'WFH' ||
                            ($item['siang'] ?? '') === 'WFH' ||
                            ($item['sore'] ?? '') === 'WFH';

                        if ($isWFH) {
                            $rekap[$kantor_id]['tanggal'][$tgl]['wfh']++;
                        } elseif ($isHadir) {
                            $rekap[$kantor_id]['tanggal'][$tgl]['wfo']++;
                        } else {
                            $rekap[$kantor_id]['tanggal'][$tgl]['tidak_hadir']++;
                        }
                    }
                }

            } catch (\Exception $e) {
                Log::error('REKAP ERROR: '.$e->getMessage());
            }

            // 🔥 DELAY BIAR API GAK DROP
            usleep(200000); // 0.2 detik
        }

        return $rekap;
    }

    // =========================
    // 🔥 INDEX
    // =========================
    public function index()
    {
        $dates = [
            '2026-04-17',
            '2026-04-24'
        ];

        $rekapGlobal = $this->getRekapGlobal($dates);

        return view('frontend.informasi.bulanan', compact('rekapGlobal', 'dates'));
    }
}