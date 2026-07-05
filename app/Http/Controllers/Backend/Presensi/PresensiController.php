<?php

namespace App\Http\Controllers\Backend\Presensi;

use App\Http\Controllers\Controller;
use App\support\Helper;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\PresensiHarian;
use App\Models\PresensiSyncLog;
use App\Services\SimpegnasService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PresensiController extends Controller
{
    protected SimpegnasService $simpegnasService;

    public function __construct(Helper $helper, SimpegnasService $simpegnasService)
    {
        parent::__construct($helper);
        $this->simpegnasService = $simpegnasService;
        
        // Fallback jika tidak ada context menu (misalnya saat pengujian di CLI/Tinker)
        if (empty($this->code) || $this->code === 'dashboard') {
            $this->code = 'presensi';
            $this->view = config('master.app.view.backend', 'backend') . '.presensi';
        }
    }

    /**
     * Tampilan Utama Halaman Rekap Presensi
     */
    public function index(): object
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 3, $currentYear);

        return view($this->view . '.index', compact('months', 'years', 'currentMonth', 'currentYear'));
    }

    /**
     * Menyediakan data JSON untuk Yajra DataTables (Mendukung mode Live & Lokal)
     */
    public function data(Request $request): object
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $source = $request->input('source', 'local');

        // Hitung batas hari jika bulan adalah bulan sekarang
        $today = Carbon::today();
        $currentMonth = $today->month;
        $currentYear = $today->year;
        $isCurrentMonth = ($month == $currentMonth && $year == $currentYear);
        $dayLimit = $isCurrentMonth ? $today->day : null;

        if ($source === 'live') {
            // Mode 1: Real-time Live dari BKN API (tanpa membebani db lokal)
            $pegawais = Pegawai::where('status', 'aktif')
                ->select('id', 'nama', 'nip', 'gelar_depan', 'gelar_belakang')
                ->get();

            $liveOfficeData = $this->simpegnasService->getLiveRekap((int)$month, (int)$year);
            $liveOfficeDataByNip = collect($liveOfficeData)->keyBy('nip');

            $mappedData = [];
            foreach ($pegawais as $pegawai) {
                $row = [
                    'id' => $pegawai->id,
                    'nama' => $pegawai->nama,
                    'nip' => $pegawai->nip,
                    'gelar_depan' => $pegawai->gelar_depan,
                    'gelar_belakang' => $pegawai->gelar_belakang,
                    'count_hn' => 0,
                    'count_tk' => 0,
                    'count_ct' => 0,
                    'count_dl' => 0,
                    'count_izin' => 0,
                    'total_hari_kerja' => 0,
                    'count_tm1' => 0,
                    'count_tm2' => 0,
                    'count_tm3' => 0,
                    'count_tm4' => 0,
                    'count_tmm' => 0,
                    'count_pc1' => 0,
                    'count_pc2' => 0,
                    'count_pc3' => 0,
                    'count_pc4' => 0,
                    'count_pc5' => 0,
                    'total_potongan' => 0.00
                ];

                $bknData = $liveOfficeDataByNip->get($pegawai->nip);
                if ($bknData && !empty($bknData['presensi'])) {
                    $presensi = $bknData['presensi'];
                    $totalPotongan = 0.00;

                    foreach ($presensi as $dayData) {
                        $day = $dayData['day'];
                        
                        // Lewati data jika sudah melampaui hari ini (untuk bulan sekarang)
                        if ($dayLimit && $day > $dayLimit) continue;
                        
                        try {
                            if (Carbon::createFromDate($year, $month, $day)->isWeekend()) continue;
                        } catch (\Exception $e) {
                            continue;
                        }
                        
                        $statusHarian = $dayData['status'] ?? 'HN';
                        $isHoliday = in_array($statusHarian, ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF']);

                        // Hitung jumlah kehadiran
                        if ($statusHarian === 'HN' || $statusHarian === 'WFO') {
                            $row['count_hn']++;
                        } elseif ($statusHarian === 'TK') {
                            $row['count_tk']++;
                        } elseif (in_array($statusHarian, ['CT', 'CB', 'CM', 'CS', 'CKAP'])) {
                            $row['count_ct']++;
                        } elseif (in_array($statusHarian, ['DL', 'TB'])) {
                            $row['count_dl']++;
                        } elseif (in_array($statusHarian, ['IZIN', 'IDL', 'IDLI', 'IDLO', 'ITM', 'IPC', 'ITMPC'])) {
                            $row['count_izin']++;
                        }

                        // Hitung total hari kerja efektif (non-weekend/libur)
                        if (!$isHoliday) {
                            $row['total_hari_kerja']++;
                        }

                        // Hitung keterlambatan (TM)
                        $checkInStatus = $dayData['checkIn']['status'] ?? '';
                        if (!$isHoliday && !in_array($statusHarian, ['DL', 'TK', 'IDLI', 'ITM']) && !empty($checkInStatus) && $checkInStatus !== 'HN') {
                            $checkInStatusUpper = strtoupper($checkInStatus);
                            if ($checkInStatusUpper === 'TM1') $row['count_tm1']++;
                            elseif ($checkInStatusUpper === 'TM2') $row['count_tm2']++;
                            elseif ($checkInStatusUpper === 'TM3') $row['count_tm3']++;
                            elseif ($checkInStatusUpper === 'TM4') $row['count_tm4']++;
                            elseif ($checkInStatusUpper === 'TMM') $row['count_tmm']++;
                        }

                        // Hitung pulang cepat (PC)
                        $checkOutStatus = $dayData['checkOut']['status'] ?? '';
                        if (!$isHoliday && !in_array($statusHarian, ['DL', 'TK', 'IDLI', 'ITM']) && !empty($checkOutStatus) && $checkOutStatus !== 'HN') {
                            $checkOutStatusUpper = strtoupper($checkOutStatus);
                            if ($checkOutStatusUpper === 'PC1') $row['count_pc1']++;
                            elseif ($checkOutStatusUpper === 'PC2') $row['count_pc2']++;
                            elseif ($checkOutStatusUpper === 'PC3') $row['count_pc3']++;
                            elseif ($checkOutStatusUpper === 'PC4') $row['count_pc4']++;
                            elseif (in_array($checkOutStatusUpper, ['PC5', 'PCM'])) $row['count_pc5']++;
                        }

                        // Akumulasi persentase potongan
                        if ($statusHarian === 'TK') {
                            $totalPotongan += 3.00;
                        } elseif (!$isHoliday && !in_array($statusHarian, ['DL', 'IDLI', 'ITM'])) {
                            $potonganTM = 0.00;
                            $potonganPC = 0.00;
                            if (!empty($checkInStatus) && $checkInStatus !== 'HN') {
                                $potonganTM = PresensiHarian::getDeductionWeight('TM', $checkInStatus);
                            }
                            if (!empty($checkOutStatus) && $checkOutStatus !== 'HN') {
                                $potonganPC = PresensiHarian::getDeductionWeight('PC', $checkOutStatus);
                            }
                            $totalPotongan += ($potonganTM + $potonganPC);
                        }
                    }

                    $row['total_potongan'] = $totalPotongan;
                }

                $mappedData[] = (object) $row;
            }

            $query = collect($mappedData);
        } else {
            // Mode 2: Dari Database Lokal
            // Helper function untuk tambahkan kondisi day limit jika diperlukan
            $addDayLimitCondition = function($q) use ($dayLimit) {
                if ($dayLimit) {
                    $q->whereDay('tanggal', '<=', $dayLimit);
                }
            };
            
            $query = Pegawai::query()
                ->where('status', 'aktif')
                ->select('id', 'nama', 'nip', 'gelar_depan', 'gelar_belakang')
                ->withCount([
                    'presensiHarian as count_hn' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereIn('status_kehadiran', ['HN', 'WFO']);
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_tk' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->where('status_kehadiran', 'TK');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_ct' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereIn('status_kehadiran', ['CT', 'CB', 'CM', 'CS', 'CKAP']);
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_dl' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereIn('status_kehadiran', ['DL', 'TB']);
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_izin' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereIn('status_kehadiran', ['IZIN', 'IDL', 'IDLI', 'IDLO', 'ITM', 'IPC', 'ITMPC']);
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as total_hari_kerja' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')
                          ->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF']);
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_tm1' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_terlambat', 'TM1');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_tm2' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_terlambat', 'TM2');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_tm3' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_terlambat', 'TM3');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_tm4' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_terlambat', 'TM4');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_tmm' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_terlambat', 'TMM');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_pc1' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_pulang_cepat', 'PC1');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_pc2' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_pulang_cepat', 'PC2');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_pc3' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_pulang_cepat', 'PC3');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_pc4' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_pulang_cepat', 'PC4');
                        $addDayLimitCondition($q);
                    },
                    'presensiHarian as count_pc5' => function ($q) use ($month, $year, $addDayLimitCondition) {
                        $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)')->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->where('kategori_pulang_cepat', 'PC5');
                        $addDayLimitCondition($q);
                    }
                ])
                ->selectRaw("(
                    SELECT SUM(total_potongan) 
                    FROM presensi_harians 
                    WHERE presensi_harians.pegawai_id = pegawais.id 
                    AND MONTH(tanggal) = ? 
                    AND YEAR(tanggal) = ?
                    AND DAYOFWEEK(tanggal) NOT IN (1, 7)
                    " . ($dayLimit ? "AND DAY(tanggal) <= ?" : "") . "
                ) as total_potongan", ($dayLimit ? [$month, $year, $dayLimit] : [$month, $year]));
        }

        return datatables()->of($query)
            ->addIndexColumn()
            ->addColumn('nama_nip', function ($row) {
                $gelarDepan = $row->gelar_depan ? $row->gelar_depan . ' ' : '';
                $gelarBelakang = $row->gelar_belakang ? ', ' . $row->gelar_belakang : '';
                $namaLengkap = "<strong>" . $gelarDepan . $row->nama . $gelarBelakang . "</strong>";
                $nipStr = $row->nip ? 'NIP. ' . $row->nip : 'NIK. -';
                return $namaLengkap . "<br><small class='text-muted'>" . $nipStr . "</small>";
            })
            ->editColumn('total_potongan', function ($row) {
                $total = $row->total_potongan ?? 0.00;
                return $total > 0 
                    ? "<span class='badge badge-warning text-white fw-bold'>-" . number_format($total, 2) . "%</span>"
                    : "<span class='badge badge-success fw-bold'>0.00%</span>";
            })
            ->addColumn('action', function ($row) use ($month, $year, $source) {
                $btnDetail = '<button type="button" class="btn btn-xs btn-info btn-action" data-title="Log Kehadiran Pegawai" data-action="show" data-size="modal-xl" data-url="presensi/show/'.$row->id.'/'.$month.'/'.$year.'?source='.$source.'"><i class="fa fa-eye"></i> Detail</button>';
                $btnSync = '<button type="button" class="btn btn-xs btn-success btn-sync-single ms-1" data-id="'.$row->id.'"><i class="fa fa-refresh"></i> Sync</button>';
                return $btnDetail . ' ' . $btnSync;
            })
            ->rawColumns(['nama_nip', 'total_potongan', 'action'])
            ->make(true);
    }

    /**
     * Memicu sinkronisasi data presensi dari API Simpegnas BKN ke DB Lokal (Manual)
     */
    public function sync(Request $request): object
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $pegawaiId = $request->input('pegawai_id'); // Nullable

        $adminName = $request->user() ? ($request->user()->first_name . ' ' . $request->user()->last_name) : 'Admin Backend';

        $result = $this->simpegnasService->syncAttendance((int)$month, (int)$year, $pegawaiId, $adminName);

        return response()->json([
            'status' => true,
            'title' => 'Sinkronisasi Selesai',
            'message' => "Berhasil menyinkronkan data presensi. Sukses: {$result['success']} pegawai. Gagal: {$result['failed']} pegawai.",
        ]);
    }

    /**
     * Menampilkan Modal Detail Log Kehadiran Harian Pegawai
     */
    public function showDetail($pegawaiId, $month, $year): object
    {
        $pegawai = Pegawai::findOrFail($pegawaiId);
        $source = request()->query('source', 'local');

        // Hitung batas hari jika bulan adalah bulan sekarang
        $today = Carbon::today();
        $currentMonth = $today->month;
        $currentYear = $today->year;
        $isCurrentMonth = ($month == $currentMonth && $year == $currentYear);
        $dayLimit = $isCurrentMonth ? $today->day : null;

        if ($source === 'live') {
            $liveOfficeData = $this->simpegnasService->getLiveRekap((int)$month, (int)$year);
            $liveOfficeDataByNip = collect($liveOfficeData)->keyBy('nip');
            $bknData = $liveOfficeDataByNip->get($pegawai->nip);

            $logs = collect();
            if ($bknData && !empty($bknData['presensi'])) {
                foreach ($bknData['presensi'] as $dayData) {
                    $day = $dayData['day'];
                    
                    // Lewati data jika sudah melampaui hari ini (untuk bulan sekarang)
                    if ($dayLimit && $day > $dayLimit) continue;
                    
                    $statusHarian = $dayData['status'] ?? 'HN';
                    $isHoliday = in_array($statusHarian, ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF']);
                    
                    try {
                        $tanggal = Carbon::createFromDate($year, $month, $day);
                        if ($tanggal->isWeekend()) continue;
                    } catch (\Exception $e) {
                        continue;
                    }

                    $checkInStatus = $dayData['checkIn']['status'] ?? '';
                    $checkOutStatus = $dayData['checkOut']['status'] ?? '';
                    
                    if ($isHoliday || in_array($statusHarian, ['DL', 'TK', 'IDLI', 'ITM'])) {
                        $checkInStatus = '';
                        $checkOutStatus = '';
                    }
                    
                    $potonganTM = 0.00;
                    $potonganPC = 0.00;
                    $totalPotongan = 0.00;

                    if ($statusHarian === 'TK') {
                        $totalPotongan = 3.00;
                    } elseif (!$isHoliday && !in_array($statusHarian, ['DL', 'IDLI', 'ITM'])) {
                        if (!empty($checkInStatus) && $checkInStatus !== 'HN') {
                            $potonganTM = PresensiHarian::getDeductionWeight('TM', $checkInStatus);
                        }
                        if (!empty($checkOutStatus) && $checkOutStatus !== 'HN') {
                            $potonganPC = PresensiHarian::getDeductionWeight('PC', $checkOutStatus);
                        }
                        $totalPotongan = $potonganTM + $potonganPC;
                    }

                    $logs->push(new PresensiHarian([
                        'pegawai_id' => $pegawai->id,
                        'tanggal' => $tanggal,
                        'jam_masuk' => (!empty($dayData['checkIn']['time_with_timezone'])) ? $dayData['checkIn']['time_with_timezone'] : null,
                        'jam_keluar' => (!empty($dayData['checkOut']['time_with_timezone'])) ? $dayData['checkOut']['time_with_timezone'] : null,
                        'work_from_masuk' => $dayData['checkIn']['work_from'] ?? null,
                        'work_from_keluar' => $dayData['checkOut']['work_from'] ?? null,
                        'status_kehadiran' => $statusHarian,
                        'kategori_terlambat' => (!empty($checkInStatus) && $checkInStatus !== 'HN') ? $checkInStatus : null,
                        'menit_terlambat' => $dayData['checkIn']['late'] ?? 0,
                        'kategori_pulang_cepat' => (!empty($checkOutStatus) && $checkOutStatus !== 'HN') ? $checkOutStatus : null,
                        'menit_pulang_cepat' => $dayData['checkOut']['late'] ?? 0,
                        'potongan_terlambat' => $potonganTM,
                        'potongan_pulang_cepat' => $potonganPC,
                        'total_potongan' => $totalPotongan,
                        'keterangan' => $dayData['keterangan'] ?? ($isHoliday ? 'Hari Libur / Akhir Pekan' : ($statusHarian === 'TK' ? 'Tanpa Keterangan' : 'Presensi Live BKN')),
                    ]));
                }
            }
        } else {
            $query = PresensiHarian::where('pegawai_id', $pegawaiId)
                ->whereMonth('tanggal', $month)
                ->whereYear('tanggal', $year)
                ->whereRaw('DAYOFWEEK(tanggal) NOT IN (1, 7)');
            
            // Tambahkan kondisi day limit jika bulan adalah bulan sekarang
            if ($dayLimit) {
                $query->whereDay('tanggal', '<=', $dayLimit);
            }
            
            $logs = $query->orderBy('tanggal', 'asc')->get();
        }

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F');

        // Kalkulasi Statistik
        $countHn = $logs->whereIn('status_kehadiran', ['HN', 'WFO'])->count();
        $countTk = $logs->where('status_kehadiran', 'TK')->count();
        $countCt = $logs->whereIn('status_kehadiran', ['CT', 'CB', 'CM', 'CS', 'CKAP'])->count();
        $countDl = $logs->whereIn('status_kehadiran', ['DL', 'TB'])->count();
        $countIzin = $logs->whereIn('status_kehadiran', ['IZIN', 'IDL', 'IDLI', 'IDLO', 'ITM', 'IPC', 'ITMPC'])->count();
        $totalHariKerja = $logs->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF'])->count();
        $countTm = $logs->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->whereNotNull('kategori_terlambat')->count();
        $countPc = $logs->whereNotIn('status_kehadiran', ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF', 'DL', 'TK', 'IDLI', 'ITM'])->whereNotNull('kategori_pulang_cepat')->count();

        $percentHn = $totalHariKerja > 0 ? round(($countHn / $totalHariKerja) * 100) : 0;
        $percentTk = $totalHariKerja > 0 ? round(($countTk / $totalHariKerja) * 100) : 0;
        $countLain = $countCt + $countDl + $countIzin;
        $percentLain = $totalHariKerja > 0 ? round(($countLain / $totalHariKerja) * 100) : 0;

        // Logika Chart Donut Presensi (Kehadiran Efektif & Persentase)
        $daysTelatAtauCepat = $logs->whereIn('status_kehadiran', ['HN', 'WFO'])
                                   ->filter(function($log) { 
                                       return !empty($log->kategori_terlambat) || !empty($log->kategori_pulang_cepat); 
                                   })->count();
        $daysTepatWaktu = $countHn - $daysTelatAtauCepat;
        
        $kehadiranEfektif = $daysTepatWaktu + $countCt + $countDl + $countIzin;
        $persentaseEfektif = $totalHariKerja > 0 ? round(($kehadiranEfektif / $totalHariKerja) * 100) : 0;

        $totalGraphic = $daysTepatWaktu + $countTm + $countPc + $countTk + $countDl + $countCt + $countIzin;

        $pctHn   = $totalGraphic > 0 ? ($daysTepatWaktu / $totalGraphic) * 100 : 0;
        $pctTm   = $totalGraphic > 0 ? ($countTm / $totalGraphic) * 100 : 0;
        $pctPc   = $totalGraphic > 0 ? ($countPc / $totalGraphic) * 100 : 0;
        $pctTk   = $totalGraphic > 0 ? ($countTk / $totalGraphic) * 100 : 0;
        $pctDl   = $totalGraphic > 0 ? ($countDl / $totalGraphic) * 100 : 0;
        $pctCt   = $totalGraphic > 0 ? ($countCt / $totalGraphic) * 100 : 0;
        $pctIzin = $totalGraphic > 0 ? ($countIzin / $totalGraphic) * 100 : 0;

        $offsetHn   = 0;
        $offsetTm   = -($pctHn);
        $offsetPc   = -($pctHn + $pctTm);
        $offsetTk   = -($pctHn + $pctTm + $pctPc);
        $offsetDl   = -($pctHn + $pctTm + $pctPc + $pctTk);
        $offsetCt   = -($pctHn + $pctTm + $pctPc + $pctTk + $pctDl);
        $offsetIzin = -($pctHn + $pctTm + $pctPc + $pctTk + $pctDl + $pctCt);

        return view($this->view . '.show', compact(
            'pegawai', 'logs', 'monthName', 'year', 'month', 'source',
            'countHn', 'countTk', 'countCt', 'countDl', 'countIzin',
            'totalHariKerja', 'countTm', 'countPc',
            'percentHn', 'percentTk', 'countLain', 'percentLain',
            'daysTepatWaktu', 'kehadiranEfektif', 'persentaseEfektif',
            'pctHn', 'pctTm', 'pctPc', 'pctTk', 'pctDl', 'pctCt', 'pctIzin',
            'offsetHn', 'offsetTm', 'offsetPc', 'offsetTk', 'offsetDl', 'offsetCt', 'offsetIzin'
        ));
    }

    /**
     * Proxy untuk gambar profile pegawai langsung dari API Simpegnas BKN (Mencegah masalah CORS/Token Leak)
     */
    public function image($nip)
    {
        $response = $this->simpegnasService->getEmployeeImage($nip);
        if ($response && $response->successful()) {
            $data = $response->json();
            
            if (isset($data['status']) && $data['status'] === true && isset($data['data']['register']) && is_array($data['data']['register']) && count($data['data']['register']) > 0) {
                // Ambil foto terbaru / paling terakhir
                $latestPhoto = end($data['data']['register']);
                
                if (!empty($latestPhoto['image_base64'])) {
                    $base64 = $latestPhoto['image_base64'];
                    
                    // Hilangkan prefix data:image/...;base64, jika ada
                    if (strpos($base64, 'base64,') !== false) {
                        $base64 = explode('base64,', $base64)[1];
                    }
                    
                    $imageContent = base64_decode($base64);
                    
                    return response($imageContent, 200)
                        ->header('Content-Type', 'image/jpeg');
                }
            }
        }

        // Redirect ke avatar default jika gagal mengambil dari BKN
        return redirect(asset(config('master.app.web.template') . '/images/avatar/avatar-1.png'));
    }

    /**
     * Menampilkan Lokasi Map & Log Presensi Detil Harian Pegawai (Level 2 Detail)
     */
    public function riwayatDetail($nip, $date): object
    {
        $carbonDate = Carbon::parse($date);
        $month = $carbonDate->month;
        $year = $carbonDate->year;

        $riwayatData = $this->simpegnasService->getEmployeeHistory($nip, $month, $year);

        $targetDate = $carbonDate->format('Y-m-d');
        $filteredLogs = collect($riwayatData)->filter(function ($item) use ($targetDate) {
            return Str::startsWith($item['tgl'] ?? '', $targetDate);
        });

        // Tipe check_in_type: 1 = Masuk/CheckIn, 3 = Pulang/CheckOut
        $checkInLog = $filteredLogs->firstWhere('check_in_type', '1');
        $checkOutLog = $filteredLogs->firstWhere('check_in_type', '3');

        return view($this->view . '.riwayat', compact('checkInLog', 'checkOutLog', 'targetDate', 'nip'));
    }


    /**
     * Mengambil log sinkronisasi berkala untuk DataTable
     */
    public function syncLogs(Request $request): object
    {
        $query = PresensiSyncLog::query()->orderBy('created_at', 'desc');
        return datatables()->of($query)
            ->addIndexColumn()
            ->editColumn('created_at', function ($row) {
                return $row->created_at->format('d-m-Y H:i:s');
            })
            ->editColumn('periode', function ($row) {
                $months = [
                    1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                    5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                    9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                ];
                return ($months[$row->bulan] ?? $row->bulan) . ' ' . $row->tahun;
            })
            ->editColumn('status', function ($row) {
                return $row->status === 'sukses'
                    ? '<span class="badge badge-success">Sukses</span>'
                    : '<span class="badge badge-danger">Gagal</span>';
            })
            ->rawColumns(['status'])
            ->make(true);
    }
}
