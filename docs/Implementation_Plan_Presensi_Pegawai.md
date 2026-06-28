# Rencana Implementasi (Implementation Plan)
## Modul Presensi Pegawai Terintegrasi API Simpegnas BKN

> [!NOTE]
> Dokumen ini memetakan rencana tahapan kerja, struktur database (migration), model Eloquent, kelas layanan (service class), pengontrol (controller), konfigurasi rute, dan kode antarmuka pengguna (Blade & Datatable JS) untuk pembangunan Modul Presensi Pegawai BKN Simpegnas.

---

## 1. Persiapan Environment & Dependensi

1. **Konfigurasi API Simpegnas BKN di `.env`**:
   Tambahkan kredensial berikut untuk menghubungkan aplikasi ke Gateway API Simpegnas BKN:
   ```env
   API_ABSENSI_TOKEN=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpbnN0YW5zaV9pZCI6IkE1RUIwM0UyM0IxQUY2QTBFMDQwNjQwQTA0MDI1MkFEIiwiYWxsIjpmYWxzZSwiaWF0IjoxNzgwNDczMTE2fQ.G029Pj7hIgHPBx1mtg4Y6fyxGUlautZUjlh4ZkyL-Tw
   API_ID_KANTOR="fbc6ca94-d421-48f0-a6d7-d2bcf392c6f2"
   API_NAMA_KANTOR="Dinas Komunikasi, Informatika, dan Statistik"
   ```

2. **Integrasi ke `config/services.php`**:
   Daftarkan variabel lingkungan di atas ke berkas konfigurasi layanan Laravel:
   ```php
   'simpegnas' => [
       'url'         => 'https://api-absensi.simpegnas.go.id/absensi/api/get/rekap-bulanan-by-kantor',
       'token'       => env('API_ABSENSI_TOKEN'),
       'kantor_id'   => env('API_ID_KANTOR'),
       'nama_kantor' => env('API_NAMA_KANTOR'),
   ],
   ```

---

## 2. Struktur Migrasi Database

Untuk merekam riwayat kehadiran harian pegawai secara detail dan melakukan kalkulasi dinamis potongan, dibuat tabel `presensi_harians`:

```php
// database/migrations/2026_06_26_000001_create_presensi_harians_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presensi_harians', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('pegawai_id');
            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_keluar')->nullable();
            
            // HN = Hadir, TK = Tanpa Keterangan, CT = Cuti, DL = Dinas Luar, IZIN = Izin
            $table->string('status_kehadiran', 10)->default('HN'); 
            
            // Kategori Keterlambatan: TM1, TM2, TM3, TM4, TMM
            $table->string('kategori_terlambat', 10)->nullable();
            $table->integer('menit_terlambat')->default(0);
            
            // Kategori Pulang Cepat: PC1, PC2, PC3, PC4, PC5
            $table->string('kategori_pulang_cepat', 10)->nullable();
            $table->integer('menit_pulang_cepat')->default(0);
            
            // Bobot Potongan dalam Persen (%) atau Poin
            $table->decimal('potongan_terlambat', 5, 2)->default(0.00);
            $table->decimal('potongan_pulang_cepat', 5, 2)->default(0.00);
            $table->decimal('total_potongan', 5, 2)->default(0.00); // Penjumlahan potongan
            
            $table->text('keterangan')->nullable();
            $table->boolean('is_sync')->default(true);
            $table->timestamp('synced_at')->nullable();
            $table->timestamps();

            // Relasi dan Constraint
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('cascade');
            $table->unique(['pegawai_id', 'tanggal']); // Menghindari duplikasi tanggal per pegawai
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('presensi_harians');
    }
};
```

---

## 3. Model & Relasi Eloquent

Buat model `PresensiHarian` lengkap dengan penanganan UUID dan relasi ke tabel `Pegawai`.

```php
// app/Models/PresensiHarian.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PresensiHarian extends Model
{
    use HasUuids;

    protected $table = 'presensi_harians';

    protected $fillable = [
        'pegawai_id',
        'tanggal',
        'jam_masuk',
        'jam_keluar',
        'status_kehadiran',
        'kategori_terlambat',
        'menit_terlambat',
        'kategori_pulang_cepat',
        'menit_pulang_cepat',
        'potongan_terlambat',
        'potongan_pulang_cepat',
        'total_potongan',
        'keterangan',
        'is_sync',
        'synced_at'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_sync' => 'boolean',
        'synced_at' => 'datetime',
    ];

    /**
     * Relasi ke model Pegawai
     */
    public function pegawai(): BelongsTo
    {
        return $this->belongsTo(Pegawai::class, 'pegawai_id');
    }

    /**
     * Hitung bobot potongan berdasarkan kategori secara statis
     */
    public static function getDeductionWeight(string $type, ?string $category): float
    {
        if (empty($category)) return 0.00;

        if ($type === 'TM') {
            return match ($category) {
                'TM1' => 0.50,
                'TM2' => 1.00,
                'TM3' => 1.25,
                'TM4', 'TMM' => 1.50,
                default => 0.00
            };
        }

        if ($type === 'PC') {
            return match ($category) {
                'PC1' => 1.50,
                'PC2' => 1.25,
                'PC3' => 1.00,
                'PC4', 'PCM', 'PC5' => 0.50,
                default => 0.00
            };
        }

        return 0.00;
    }
}
```

---

## 4. Implementasi Layanan (Service Class)

Layanan `SimpegnasService` menangani otentikasi API, pengambilan data presensi dari BKN, parsing berkas JSON, pengkalkulasian potongan kedisiplinan, dan penyimpanan data ke basis data lokal.

```php
// app/Services/SimpegnasService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\PresensiHarian;
use App\Models\Pegawai;
use Carbon\Carbon;

class SimpegnasService
{
    protected string $url;
    protected string $token;
    protected string $kantorId;

    public function __construct()
    {
        $this->url = config('services.simpegnas.url');
        $this->token = config('services.simpegnas.token');
        $this->kantorId = config('services.simpegnas.kantor_id');
    }

    /**
     * Sinkronisasi data presensi bulanan kantor dari API Simpegnas BKN
     */
    public function syncAttendance(int $month, int $year, ?string $pegawaiId = null): array
    {
        $successCount = 0;
        $failedCount = 0;

        try {
            // Request ke API Simpegnas menggunakan Token Bearer
            $response = Http::withToken($this->token)
                ->get($this->url, [
                    'kantor_id' => $this->kantorId,
                    'tahun' => $year,
                    'bulan' => $month // Bulan dikirimkan tanpa lead zero (e.g. 6)
                ]);

            if ($response->successful() && $response->json('status') === true) {
                $officeData = $response->json('data') ?? [];

                // Filter data jika hanya ingin menyinkronkan pegawai tertentu
                if ($pegawaiId) {
                    $targetPegawai = Pegawai::find($pegawaiId);
                    if ($targetPegawai && $targetPegawai->nip) {
                        $officeData = array_filter($officeData, function ($item) use ($targetPegawai) {
                            return $item['nip'] === $targetPegawai->nip;
                        });
                    } else {
                        return ['success' => 0, 'failed' => 1];
                    }
                }

                foreach ($officeData as $empData) {
                    $nip = $empData['nip'];
                    
                    // Cari pegawai di database lokal berdasarkan NIP
                    $pegawai = Pegawai::where('nip', $nip)->first();
                    if (!$pegawai) {
                        $failedCount++;
                        continue;
                    }

                    $presensiHarian = $empData['presensi'] ?? [];

                    foreach ($presensiHarian as $dayData) {
                        $day = $dayData['day'];
                        $tanggal = Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
                        
                        $statusHarian = $dayData['status'] ?? 'HN';
                        
                        // Ekstrak status Check-In dan Check-Out
                        $checkInStatus = $dayData['checkIn']['status'] ?? '';
                        $checkOutStatus = $dayData['checkOut']['status'] ?? '';
                        
                        // Default potongan
                        $potonganTM = 0.00;
                        $potonganPC = 0.00;
                        $totalPotongan = 0.00;

                        // Periksa apakah ini hari libur/weekend
                        $isHoliday = in_array($statusHarian, ['LN', 'LJ', 'LS', 'LM', 'L', 'OFF']);

                        // Potongan hanya berlaku pada hari kerja aktif (bukan libur dan bukan Alpa/TK)
                        // Untuk Alpa (TK), statusnya diproses terpisah bukan sebagai terlambat/pulang cepat
                        if (!$isHoliday && $statusHarian !== 'TK') {
                            if (!empty($checkInStatus) && $checkInStatus !== 'HN') {
                                $potonganTM = PresensiHarian::getDeductionWeight('TM', $checkInStatus);
                            }
                            if (!empty($checkOutStatus) && $checkOutStatus !== 'HN') {
                                $potonganPC = PresensiHarian::getDeductionWeight('PC', $checkOutStatus);
                            }
                            $totalPotongan = $potonganTM + $potonganPC;
                        }

                        PresensiHarian::updateOrCreate(
                            [
                                'pegawai_id' => $pegawai->id,
                                'tanggal' => $tanggal,
                            ],
                            [
                                'jam_masuk' => $dayData['checkIn']['time'] ?: null,
                                'jam_keluar' => $dayData['checkOut']['time'] ?: null,
                                'status_kehadiran' => $statusHarian,
                                'kategori_terlambat' => (!empty($checkInStatus) && $checkInStatus !== 'HN') ? $checkInStatus : null,
                                'menit_terlambat' => $dayData['checkIn']['late'] ?? 0,
                                'kategori_pulang_cepat' => (!empty($checkOutStatus) && $checkOutStatus !== 'HN') ? $checkOutStatus : null,
                                'menit_pulang_cepat' => $dayData['checkOut']['late'] ?? 0,
                                'potongan_terlambat' => $potonganTM,
                                'potongan_pulang_cepat' => $potonganPC,
                                'total_potongan' => $totalPotongan,
                                'keterangan' => $dayData['keterangan'] ?? ($isHoliday ? 'Hari Libur / Akhir Pekan' : ($statusHarian === 'TK' ? 'Tanpa Keterangan' : 'Presensi Sinkron BKN')),
                                'is_sync' => true,
                                'synced_at' => now(),
                            ]
                        );
                    }
                    $successCount++;
                }
            } else {
                Log::error('API Simpegnas BKN mengembalikan status gagal', ['response' => $response->body()]);
                return ['success' => 0, 'failed' => 1];
            }
        } catch (\Exception $e) {
            Log::error('Kesalahan sinkronisasi presensi: ' . $e->getMessage());
            return ['success' => 0, 'failed' => 1];
        }

        return [
            'success' => $successCount,
            'failed' => $failedCount
        ];
    }
}
```

---

## 5. Implementasi Controller

Pengontrol `PresensiController` bertugas menyajikan halaman index, memproses pencarian filter, menyuplai data tabular berformat JSON untuk Yajra DataTables, memicu sinkronisasi, dan menampilkan detail log.

```php
// app/Http/Controllers/Backend/Presensi/PresensiController.php
namespace App\Http\Controllers\Backend\Presensi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Pegawai;
use App\Models\PresensiHarian;
use App\Services\SimpegnasService;
use Carbon\Carbon;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    protected SimpegnasService $simpegnasService;

    public function __construct(SimpegnasService $simpegnasService)
    {
        $this->simpegnasService = $simpegnasService;
    }

    /**
     * Tampilan Halaman Utama Index Presensi
     */
    public function index()
    {
        $page = (object)[
            'title' => 'Presensi Pegawai',
            'subtitle' => 'Kelola rekapitulasi kehadiran terintegrasi Simpegnas BKN',
            'icon' => 'fa fa-calendar-check-o',
            'code' => 'presensi',
            'url' => 'presensi'
        ];
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        
        // Buat range tahun dinamis
        $years = range($currentYear - 3, $currentYear);

        return view('backend.presensi.index', compact('page', 'months', 'years', 'currentMonth', 'currentYear'));
    }

    /**
     * Menyediakan data JSON untuk Yajra DataTables
     */
    public function data(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // Raw aggregation query untuk rekapitulasi performa bulanan
        $query = Pegawai::query()
            ->where('status', 'aktif')
            ->select('pegawais.id', 'pegawais.nama', 'pegawais.nip', 'pegawais.gelar_depan', 'pegawais.gelar_belakang')
            ->withCount([
                'presensiHarian as count_hn' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status_kehadiran', 'HN');
                },
                'presensiHarian as count_tk' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status_kehadiran', 'TK');
                },
                'presensiHarian as count_ct' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status_kehadiran', 'CT');
                },
                'presensiHarian as count_dl' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status_kehadiran', 'DL');
                },
                'presensiHarian as count_izin' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('status_kehadiran', 'IZIN');
                },
                'presensiHarian as total_hari_kerja' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
                },
                'presensiHarian as count_tm1' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_terlambat', 'TM1');
                },
                'presensiHarian as count_tm2' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_terlambat', 'TM2');
                },
                'presensiHarian as count_tm3' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_terlambat', 'TM3');
                },
                'presensiHarian as count_tm4' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_terlambat', 'TM4');
                },
                'presensiHarian as count_tmm' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_terlambat', 'TMM');
                },
                'presensiHarian as count_pc1' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_pulang_cepat', 'PC1');
                },
                'presensiHarian as count_pc2' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_pulang_cepat', 'PC2');
                },
                'presensiHarian as count_pc3' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_pulang_cepat', 'PC3');
                },
                'presensiHarian as count_pc4' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_pulang_cepat', 'PC4');
                },
                'presensiHarian as count_pc5' => function ($q) use ($month, $year) {
                    $q->whereMonth('tanggal', $month)->whereYear('tanggal', $year)->where('kategori_pulang_cepat', 'PC5');
                }
            ])
            ->selectRaw("(
                SELECT SUM(total_potongan) 
                FROM presensi_harians 
                WHERE presensi_harians.pegawai_id = pegawais.id 
                AND MONTH(tanggal) = ? 
                AND YEAR(tanggal) = ?
            ) as total_potongan", [$month, $year]);

        return DataTables::of($query)
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
            ->addColumn('action', function ($row) use ($month, $year) {
                $btnDetail = '<button type="button" class="btn btn-xs btn-info btn-action" data-title="Log Kehadiran Pegawai" data-action="show" data-size="modal-xl" data-url="presensi/show/'.$row->id.'/'.$month.'/'.$year.'"><i class="fa fa-eye"></i> Detail</button>';
                $btnSync = '<button type="button" class="btn btn-xs btn-success btn-sync-single ms-1" data-id="'.$row->id.'"><i class="fa fa-refresh"></i> Sync</button>';
                return $btnDetail . ' ' . $btnSync;
            })
            ->rawColumns(['nama_nip', 'total_potongan', 'action'])
            ->make(true);
    }

    /**
     * Memicu sinkronisasi data dari BKN API (Massal)
     */
    public function sync(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $pegawaiId = $request->input('pegawai_id'); // nullable

        $result = $this->simpegnasService->syncAttendance($month, $year, $pegawaiId);

        return response()->json([
            'status' => true,
            'title' => 'Sinkronisasi Selesai',
            'message' => "Berhasil menyinkronkan data presensi. Sukses: {$result['success']} pegawai. Gagal: {$result['failed']} pegawai.",
        ]);
    }

    /**
     * Menampilkan Modal Detail Riwayat Presensi Pegawai per Bulan
     */
    public function showDetail($pegawaiId, $month, $year)
    {
        $pegawai = Pegawai::findOrFail($pegawaiId);
        
        $logs = PresensiHarian::where('pegawai_id', $pegawaiId)
            ->whereMonth('tanggal', $month)
            ->whereYear('tanggal', $year)
            ->orderBy('tanggal', 'asc')
            ->get();

        $monthName = Carbon::createFromDate($year, $month, 1)->translatedFormat('F');

        // Render HTML view partial untuk disematkan dalam modal AJAX
        return view('backend.presensi.show', compact('pegawai', 'logs', 'monthName', 'year'));
    }
}
```

---

## 6. Penambahan Rute Baru

Tambahkan pendefinisian rute backend pada berkas `routes/backend.php` di dalam grup middleware `userRoles`:

```php
// routes/backend.php (Tambahkan di bawah modul pegawai / di dalam grup middleware 'userRoles')

// ===== Presensi Pegawai terintegrasi BKN =====
Route::prefix('presensi')->as('presensi.')->group(function () {
    Route::get('data', 'Presensi\PresensiController@data')->name('data');
    Route::post('sync', 'Presensi\PresensiController@sync')->name('sync');
    Route::get('show/{pegawai_id}/{month}/{year}', 'Presensi\PresensiController@showDetail')->name('show-detail');
});
Route::resource('presensi', 'Presensi\PresensiController');
```

---

## 7. Pembuatan Struktur View (Blade Templates)

Sistem rendering memproses file blade.php bertipe `.js` dinamis, maka view dibagi menjadi tiga bagian:

### 7.1 Halaman Index View (`resources/views/backend/presensi/index.blade.php`)

```html
@extends('backend.main.index')
@push('title', $page->title ?? 'Presensi Pegawai')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title"><i class="{{ $page->icon }}"></i> {{ $page->title ?? 'Presensi Pegawai' }}</h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">{{ $page->subtitle }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            
            <section class="content">
                <!-- Filter Panel -->
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header">
                                <h4 class="box-title"><i class="fa fa-filter"></i> Filter Data</h4>
                            </div>
                            <div class="box-body">
                                <form id="form-filter" class="form-row align-items-end">
                                    <div class="col-md-3 col-sm-6 form-group">
                                        <label for="month-filter">Pilih Bulan</label>
                                        <select name="month" id="month-filter" class="form-control select2">
                                            @foreach($months as $key => $name)
                                                <option value="{{ $key }}" {{ $key == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 col-sm-6 form-group">
                                        <label for="year-filter">Pilih Tahun</label>
                                        <select name="year" id="year-filter" class="form-control select2">
                                            @foreach($years as $year)
                                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6 col-sm-12 form-group text-md-end">
                                        <button type="submit" class="btn btn-primary" id="btn-cari">
                                            <i class="fa fa-search"></i> Cari
                                        </button>
                                        <button type="button" class="btn btn-success" id="btn-sync-bkn">
                                            <i class="fa fa-cloud-download"></i> Tarik Data BKN
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Datatable Panel -->
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header">
                                <h4 class="box-title">Daftar Rekapitulasi Presensi Pegawai</h4>
                            </div>
                            <div class="box-body">
                                <div class="table-responsive">
                                    <table id="datatable" class="table table-bordered table-striped" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th rowspan="2" class="align-middle text-center w-0">No</th>
                                                <th rowspan="2" class="align-middle">Nama Pegawai / NIP</th>
                                                <th colspan="6" class="text-center bg-light font-weight-bold">Status Kehadiran (Hari)</th>
                                                <th colspan="5" class="text-center bg-warning-light">Terlambat (Kali)</th>
                                                <th colspan="5" class="text-center bg-danger-light">Pulang Cepat (Kali)</th>
                                                <th rowspan="2" class="align-middle text-center">Total Potongan</th>
                                                <th rowspan="2" class="align-middle text-center w-0">Aksi</th>
                                            </tr>
                                            <tr>
                                                <!-- Kehadiran -->
                                                <th class="text-center bg-success-light" title="Hadir Normal">HN</th>
                                                <th class="text-center bg-danger-light" title="Tanpa Keterangan">TK</th>
                                                <th class="text-center bg-info-light" title="Cuti">CT</th>
                                                <th class="text-center bg-primary-light" title="Dinas Luar">DL</th>
                                                <th class="text-center bg-secondary-light" title="Izin">IZ</th>
                                                <th class="text-center" title="Hari Kerja Efektif">HK</th>
                                                
                                                <!-- Terlambat -->
                                                <th class="text-center" title="Terlambat 1 - 30 menit">TM1</th>
                                                <th class="text-center" title="Terlambat 31 - 60 menit">TM2</th>
                                                <th class="text-center" title="Terlambat 61 - 90 menit">TM3</th>
                                                <th class="text-center" title="Terlambat > 90 menit">TM4</th>
                                                <th class="text-center" title="Terlambat Tanpa Ket">TMM</th>
                                                
                                                <!-- Pulang Cepat -->
                                                <th class="text-center" title="Pulang Cepat > 90 menit">PC1</th>
                                                <th class="text-center" title="Pulang Cepat 61 - 90 menit">PC2</th>
                                                <th class="text-center" title="Pulang Cepat 31 - 60 menit">PC3</th>
                                                <th class="text-center" title="Pulang Cepat 1 - 30 menit">PC4</th>
                                                <th class="text-center" title="Pulang Cepat Kategori 5">PC5</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ url($template.'/assets/vendor_components/select2/dist/js/select2.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/jquery-validation-1.17.0/lib/jquery.form.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ url('/js/'.$backend.'/'.$page->code.'/datatable.js') }}"></script>
    <script src="{{ url('js/jquery-crud.js') }}"></script>
@endpush
```

### 7.2 Berkas Definisi DataTable (`resources/views/backend/presensi/datatable.blade.php`)

```javascript
$(document).ready(function () {
    const table = $('#datatable').DataTable({
        searchDelay: 1000,
        responsive: true,
        lengthChange: true,
        searching: true,
        processing: true,
        serverSide: true,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Semua"]],
        ajax: {
            url: "{{ url(config('master.app.url.backend').'/'.$url.'/data') }}",
            data: function (d) {
                d.month = $('#month-filter').val();
                d.year = $('#year-filter').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, className: 'text-center' },
            { data: 'nama_nip', name: 'nama', orderable: true, searchable: true },
            
            // Kehadiran (HN, TK, CT, DL, Izin, Hari Kerja)
            { data: 'count_hn', name: 'count_hn', className: 'text-center font-weight-bold text-success' },
            { data: 'count_tk', name: 'count_tk', className: 'text-center font-weight-bold text-danger' },
            { data: 'count_ct', name: 'count_ct', className: 'text-center font-weight-bold text-info' },
            { data: 'count_dl', name: 'count_dl', className: 'text-center font-weight-bold text-primary' },
            { data: 'count_izin', name: 'count_izin', className: 'text-center font-weight-bold text-secondary' },
            { data: 'total_hari_kerja', name: 'total_hari_kerja', className: 'text-center font-weight-bold' },
            
            // Terlambat
            { data: 'count_tm1', name: 'count_tm1', className: 'text-center text-warning' },
            { data: 'count_tm2', name: 'count_tm2', className: 'text-center text-warning' },
            { data: 'count_tm3', name: 'count_tm3', className: 'text-center text-warning' },
            { data: 'count_tm4', name: 'count_tm4', className: 'text-center text-warning' },
            { data: 'count_tmm', name: 'count_tmm', className: 'text-center text-warning' },
            
            // Pulang Cepat
            { data: 'count_pc1', name: 'count_pc1', className: 'text-center text-warning' },
            { data: 'count_pc2', name: 'count_pc2', className: 'text-center text-warning' },
            { data: 'count_pc3', name: 'count_pc3', className: 'text-center text-warning' },
            { data: 'count_pc4', name: 'count_pc4', className: 'text-center text-warning' },
            { data: 'count_pc5', name: 'count_pc5', className: 'text-center text-warning' },
            
            // Potongan & Aksi
            { data: 'total_potongan', name: 'total_potongan', className: 'text-center' },
            { data: 'action', name: 'action', orderable: false, searchable: false, className: 'text-center w-0' }
        ],
        dom: 'lBfrtip',
        buttons: [
            { extend: 'excel', text: '<i class="fa fa-file-excel-o"></i> Excel', className: 'btn btn-info btn-xs', exportOptions: { columns: ':visible' } },
            { extend: 'pdf', text: '<i class="fa fa-file-pdf-o"></i> PDF', className: 'btn btn-warning btn-xs', exportOptions: { columns: ':visible' } },
            { extend: 'print', text: '<i class="fa fa-print"></i> Print', className: 'btn btn-danger btn-xs me-10', exportOptions: { columns: ':visible' } }
        ]
    });

    // Memicu form filter pencarian
    $('#form-filter').on('submit', function (e) {
        e.preventDefault();
        table.ajax.reload();
    });

    // Event handler tombol sinkronisasi massal
    $('#btn-sync-bkn').on('click', function () {
        const month = $('#month-filter').val();
        const year = $('#year-filter').val();

        swal({
            title: "Tarik Data Simpegnas?",
            text: "Sistem akan menarik data kehadiran pegawai dari API BKN untuk bulan " + month + " / " + year + ".",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#059669",
            confirmButtonText: "Ya, Tarik!",
            cancelButtonText: "Batal",
            closeOnConfirm: false,
            showLoaderOnConfirm: true
        }, function () {
            $.ajax({
                url: "{{ url(config('master.app.url.backend').'/presensi/sync') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    month: month,
                    year: year
                },
                success: function (res) {
                    if (res.status === true) {
                        swal("Berhasil!", res.message, "success");
                        table.ajax.reload();
                    } else {
                        swal("Gagal!", res.message, "error");
                    }
                },
                error: function (xhr) {
                    swal("Error!", "Gagal menghubungi server API. Kode: " + xhr.status, "error");
                }
            });
        });
    });

    // Event handler tombol sinkronisasi per pegawai
    $(document).on('click', '.btn-sync-single', function () {
        const id = $(this).data('id');
        const month = $('#month-filter').val();
        const year = $('#year-filter').val();
        const btn = $(this);

        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');

        $.ajax({
            url: "{{ url(config('master.app.url.backend').'/presensi/sync') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                month: month,
                year: year,
                pegawai_id: id
            },
            success: function (res) {
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i>');
                if (res.status === true) {
                    swal({
                        title: "Sukses!",
                        text: "Data pegawai berhasil diperbarui.",
                        type: "success",
                        timer: 1500
                    });
                    table.ajax.reload();
                } else {
                    swal("Gagal!", res.message, "error");
                }
            },
            error: function (xhr) {
                btn.prop('disabled', false).html('<i class="fa fa-refresh"></i>');
                swal("Error!", "Gagal menghubungi server API.", "error");
            }
        });
    });
});
```

### 7.3 Modal Log Kehadiran Harian (`resources/views/backend/presensi/show.blade.php`)

```html
<div class="modal-header">
    <h4 class="modal-title" id="modal-title-label">
        <i class="fa fa-calendar-check-o"></i> Log Kehadiran Pegawai
    </h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-25">
    <!-- Profil Singkat Pegawai -->
    <div class="row mb-15">
        <div class="col-md-6">
            <table class="table table-borderless table-sm">
                <tr>
                    <td class="font-weight-bold" style="width: 150px;">Nama Pegawai</td>
                    <td>: {{ ($pegawai->gelar_depan ? $pegawai->gelar_depan . ' ' : '') . $pegawai->nama . ($pegawai->gelar_belakang ? ', ' . $pegawai->gelar_belakang : '') }}</td>
                </tr>
                <tr>
                    <td class="font-weight-bold">NIP / NIK</td>
                    <td>: {{ $pegawai->nip ?? $pegawai->nik ?? '-' }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="table table-borderless table-sm">
                <tr>
                    <td class="font-weight-bold" style="width: 150px;">Bulan / Tahun</td>
                    <td>: <strong>{{ $monthName }} {{ $year }}</strong></td>
                </tr>
                <tr>
                    <td class="font-weight-bold">Total Potongan</td>
                    <td>: 
                        @php
                            $totalPotongan = $logs->sum('total_potongan');
                        @endphp
                        @if($totalPotongan > 0)
                            <span class="badge badge-warning text-white fw-bold">-{{ number_format($totalPotongan, 2) }}%</span>
                        @else
                            <span class="badge badge-success fw-bold">0.00%</span>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Tabel Riwayat Kehadiran Harian -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="text-center" style="width: 50px;">No</th>
                    <th class="text-center" style="width: 120px;">Tanggal</th>
                    <th class="text-center" style="width: 150px;">Status</th>
                    <th class="text-center">Jam Masuk</th>
                    <th class="text-center">Jam Pulang</th>
                    <th class="text-center">Terlambat</th>
                    <th class="text-center">Pulang Cepat</th>
                    <th class="text-center" style="width: 100px;">Potongan</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $index => $log)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td class="text-center">{{ $log->tanggal->translatedFormat('d-m-Y') }}</td>
                        <td class="text-center">
                            @php
                                $badgeClass = match($log->status_kehadiran) {
                                    'HN' => 'badge-success',
                                    'TK' => 'badge-danger',
                                    'CT' => 'badge-info',
                                    'DL' => 'badge-primary',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $log->status_kehadiran }}</span>
                        </td>
                        <td class="text-center font-mono">{{ $log->jam_masuk ?? '-' }}</td>
                        <td class="text-center font-mono">{{ $log->jam_keluar ?? '-' }}</td>
                        <td class="text-center">
                            @if($log->kategori_terlambat)
                                <span class="badge badge-warning text-white">{{ $log->kategori_terlambat }}</span>
                                <small class="text-muted d-block">({{ $log->menit_terlambat }} menit)</small>
                            @else
                                <span class="text-success"><i class="fa fa-check-circle"></i> Tepat Waktu</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($log->kategori_pulang_cepat)
                                <span class="badge badge-warning text-white">{{ $log->kategori_pulang_cepat }}</span>
                                <small class="text-muted d-block">({{ $log->menit_pulang_cepat }} menit)</small>
                            @else
                                <span class="text-success"><i class="fa fa-check-circle"></i> Tidak PC</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($log->total_potongan > 0)
                                <span class="text-danger font-weight-bold">-{{ number_format($log->total_potongan, 2) }}%</span>
                            @else
                                <span class="text-success">0.00%</span>
                            @endif
                        </td>
                        <td>{{ $log->keterangan ?? '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-20 text-muted">
                            <i class="fa fa-exclamation-triangle fa-2x d-block mb-10"></i>
                            Belum ada riwayat kehadiran harian yang disinkronkan untuk bulan ini.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
</div>
```
