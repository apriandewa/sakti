@extends('backend.main.index')
@push('title', $page->title ?? 'Rekap Presensi')
@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h3 class="page-title">
                        <i class="mdi mdi-calendar-clock me-1"></i>
                        {!! $page->title ?? 'Rekap Presensi Pegawai' !!}
                    </h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ url(config('master.app.url.backend').'/dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active">Presensi Pegawai</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="row">
                <div class="col-12">
                    <ul class="nav nav-tabs customtab2" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#tab_1" role="tab">
                                <span class="hidden-sm-up"><i class="ion-person"></i></span> 
                                <span class="hidden-xs-down">Data Presensi</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#tab_2" role="tab" id="tabLogTrigger">
                                <span class="hidden-sm-up"><i class="ion-settings"></i></span> 
                                <span class="hidden-xs-down">Log & Monitoring Sinkronisasi BKN</span>
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- TAB 1: DATA PRESENSI --}}
                        <div class="tab-pane active" id="tab_1" role="tabpanel">
                            <div class="p-15">
                                {{-- ===== FILTER CARD ===== --}}
                                <div class="box mb-4">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <i class="fa fa-filter me-1"></i> Filter Rekapitulasi Presensi
                                        </h4>
                                    </div>
                                    <div class="box-body">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-5">
                                                <label for="selectKantor">Pilih Kantor / OPD</label>
                                                <select id="selectKantor" name="kantor_id" class="form-control select2-kantor" style="width:100%">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="selectBulan">Bulan</label>
                                                <select id="selectBulan" class="form-control">
                                                    @foreach($bulanList as $num => $label)
                                                        <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>
                                                            {{ $label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="selectTahun">Tahun</label>
                                                <select id="selectTahun" class="form-control">
                                                    @foreach($tahunList as $y => $_)
                                                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="d-flex gap-2">
                                                    <button id="btnCari" class="btn btn-primary btn-sm flex-grow-1">
                                                        <i class="fa fa-search me-1"></i> Cari
                                                    </button>
                                                    <button id="btnImportCsv" class="btn btn-secondary btn-sm" title="Import Pegawai via CSV" data-toggle="modal" data-target="#modalImportCsv">
                                                        <i class="fa fa-file-csv"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- ===== DATATABLE CARD ===== --}}
                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title" id="judulRekap">
                                            Rekapitulasi Presensi &amp; Potongan Pegawai
                                        </h4>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table id="datatablePresensi" class="table table-bordered table-striped table-hover" style="width:100%">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th rowspan="2" class="align-middle text-center" style="width:40px">No</th>
                                                        <th rowspan="2" class="align-middle" style="min-width:220px">Nama Pegawai / NIP</th>
                                                        <th colspan="6" class="text-center bg-light font-weight-bold">Status Kehadiran (Hari)</th>
                                                        <th colspan="5" class="text-center bg-warning-light font-weight-bold">Terlambat (Kali)</th>
                                                        <th colspan="5" class="text-center bg-danger-light font-weight-bold">Pulang Cepat (Kali)</th>
                                                        <th rowspan="2" class="align-middle text-center" style="min-width:100px">Total Potongan</th>
                                                        <th rowspan="2" class="align-middle text-center" style="min-width:80px">Aksi</th>
                                                    </tr>
                                                    <tr>
                                                        <th class="text-center bg-success-light" title="Hadir Normal">HN</th>
                                                        <th class="text-center bg-danger-light" title="Tanpa Keterangan">TK</th>
                                                        <th class="text-center bg-info-light" title="Cuti">CT</th>
                                                        <th class="text-center bg-primary-light" title="Dinas Luar">DL</th>
                                                        <th class="text-center bg-secondary-light" title="Izin">IZ</th>
                                                        <th class="text-center bg-light" title="Hari Kerja Efektif">HK</th>
                                                        <th class="text-center" title="Terlambat 1 - 30 menit">TM1</th>
                                                        <th class="text-center" title="Terlambat 31 - 60 menit">TM2</th>
                                                        <th class="text-center" title="Terlambat 61 - 90 menit">TM3</th>
                                                        <th class="text-center" title="Terlambat > 90 menit">TM4</th>
                                                        <th class="text-center" title="Terlambat Tanpa Ket">TMM</th>
                                                        <th class="text-center" title="Pulang Cepat 1 - 30 menit">PC1</th>
                                                        <th class="text-center" title="Pulang Cepat 31 - 60 menit">PC2</th>
                                                        <th class="text-center" title="Pulang Cepat 61 - 90 menit">PC3</th>
                                                        <th class="text-center" title="Pulang Cepat > 90 menit">PC4</th>
                                                        <th class="text-center" title="Pulang Cepat Tanpa Ket">PCM</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td colspan="20" class="text-center text-muted py-4">
                                                            <i class="fa fa-info-circle me-1"></i>
                                                            Pilih kantor, bulan, dan tahun lalu klik <strong>Cari</strong>.
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: LOG & MONITORING --}}
                        <div class="tab-pane" id="tab_2" role="tabpanel">
                            <div class="p-15">
                                <div class="box mb-4">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <i class="fa fa-cloud-download-alt me-1"></i> Sinkronisasi Manual BKN
                                        </h4>
                                    </div>
                                    <div class="box-body">
                                        <div class="row g-3 align-items-end">
                                            <div class="col-md-5">
                                                <label for="selectKantorSync">Pilih Kantor / OPD</label>
                                                <select id="selectKantorSync" class="form-control select2-kantor" style="width:100%">
                                                    <option value=""></option>
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="selectBulanSync">Bulan</label>
                                                <select id="selectBulanSync" class="form-control">
                                                    @foreach($bulanList as $num => $label)
                                                        <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>{{ $label }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-2">
                                                <label for="selectTahunSync">Tahun</label>
                                                <select id="selectTahunSync" class="form-control">
                                                    @foreach($tahunList as $y => $_)
                                                        <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-3">
                                                <button id="btnSync" class="btn btn-success btn-sm w-100">
                                                    <i class="fa fa-sync-alt me-1"></i> Tarik Data BKN
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="box">
                                    <div class="box-header with-border">
                                        <h4 class="box-title">
                                            <i class="fa fa-history me-1"></i> Riwayat Sinkronisasi
                                        </h4>
                                    </div>
                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table id="datatableLog" class="table table-bordered table-striped table-hover" style="width:100%">
                                                <thead class="table-dark">
                                                    <tr>
                                                        <th class="text-center">No</th>
                                                        <th>Kantor/OPD</th>
                                                        <th>Bulan</th>
                                                        <th>Tahun</th>
                                                        <th>Eksekutor</th>
                                                        <th>Waktu Mulai</th>
                                                        <th>Waktu Selesai</th>
                                                        <th>Jml Data</th>
                                                        <th>Status</th>
                                                        <th>Keterangan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>

{{-- Modal detail log harian sekarang dirender otomatis oleh jquery-crud.js
     (pola btn-action generik: data-title/data-url/data-size), tidak perlu markup manual di sini lagi. --}}

{{-- ===== MODAL IMPORT CSV ===== --}}
<div class="modal fade" id="modalImportCsv" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-file-csv me-1"></i> Import Pegawai via CSV</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formImportCsv" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted small">Format CSV: kolom <code>nama</code> dan <code>nip</code> (header wajib di baris pertama).</p>
                    <div class="mb-3">
                        <label>File CSV</label>
                        <input type="file" name="file" id="fileCsv" class="form-control" accept=".csv" required>
                    </div>
                    <input type="hidden" name="kantor_id" id="csvKantorId">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success"><i class="fa fa-upload me-1"></i> Import</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="{{ url($template.'/assets/vendor_components/select2/dist/js/select2.js') }}"></script>
<script src="{{ url($template.'/assets/vendor_components/datatable/datatables.min.js') }}"></script>
<script src="{{ url($template.'/assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
<script src="{{ url('js/jquery-crud.js') }}"></script>
<script>
    // Konfigurasi URL & CSRF token untuk dipakai oleh datatable.js (file eksternal statis).
    window.PresensiConfig = {
        urls: {
            kantor:      '{{ route('presensi.kantor') }}',
            data:        '{{ route('presensi.data') }}',
            logsData:    '{{ route('presensi.logs-data') }}',
            sync:        '{{ route('presensi.sync') }}',
            syncPegawai: '{{ route('presensi.sync-pegawai') }}',
            importCsv:   '{{ route('presensi.import-csv') }}',
            showBase:    '{{ url(config('master.app.url.backend').'/presensi') }}'
        },
        csrfToken: '{{ csrf_token() }}'
    };
</script>
<script src="{{ url('js/'.$backend.'/'.$page->code.'/datatable.js') }}"></script>
@endpush