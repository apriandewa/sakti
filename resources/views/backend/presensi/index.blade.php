@extends('backend.main.index')
@push('title', $page->title ?? 'Presensi Pegawai')

@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title"><i class="{{ $page->icon ?? 'fa fa-calendar-check-o' }}"></i> {{ $page->title ?? 'Presensi Pegawai' }}</h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item">{{ $page->subtitle ?? 'Rekap Presensi & Potongan BKN' }}</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            
            <section class="content">
                <!-- Nav Tabs Navigasi Utama -->
                <div class="row">
                    <div class="col-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active font-weight-bold" data-bs-toggle="tab" href="#tab-rekap" role="tab">
                                        <i class="fa fa-calendar-check-o me-5"></i> Rekapitulasi Kehadiran
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link font-weight-bold" data-bs-toggle="tab" href="#tab-sync-monitoring" role="tab">
                                        <i class="fa fa-cloud-download me-5"></i> Log & Monitoring Sinkronisasi BKN
                                    </a>
                                </li>
                            </ul>
                            
                            <div class="tab-content bg-white p-15">
                                <!-- TAB 1: REKAPITULASI KEHADIRAN PEGAWAI -->
                                <div class="tab-pane active" id="tab-rekap" role="tabpanel">
                                    <!-- Panel Filter & Sinkronisasi Cepat -->
                                    <div class="box border shadow-none mb-20">
                                        <div class="box-header with-border py-10">
                                            <h5 class="box-title"><i class="fa fa-filter text-info"></i> Filter Data Pencarian</h5>
                                        </div>
                                        <div class="box-body">
                                            <form id="form-filter" class="">
                                                <div class="row align-items-end">
                                                    <div class="col-md-3 col-sm-6 col-12 form-group mb-10">
                                                        <label for="month-filter" class="form-label">Pilih Bulan</label>
                                                        <select name="month" id="month-filter" class="form-control select2 w-100">
                                                            @foreach($months as $key => $name)
                                                                <option value="{{ $key }}" {{ $key == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-2 col-sm-6 col-12 form-group mb-10">
                                                        <label for="year-filter" class="form-label">Pilih Tahun</label>
                                                        <select name="year" id="year-filter" class="form-control select2 w-100">
                                                            @foreach($years as $year)
                                                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3 col-sm-6 col-12 form-group mb-10">
                                                        <label for="source-filter" class="form-label">Sumber Data Tampilan</label>
                                                        <select name="source" id="source-filter" class="form-control select2 w-100">
                                                            <option value="local" selected>Database Lokal (Sync)</option>
                                                            <option value="live">Live Server BKN (Real-time)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4 col-12 form-group mb-10 text-md-end text-start">
                                                        <button type="submit" class="btn btn-primary" id="btn-cari">
                                                            <i class="fa fa-search text-white me-5"></i> Cari Data
                                                        </button>
                                                        <button type="button" class="btn btn-success ms-5 text-white" id="btn-sync-bkn">
                                                            <i class="fa fa-cloud-download text-white me-5"></i> Tarik Data BKN
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Panel Tabel Utama -->
                                    <div class="box border shadow-none mb-0">
                                        <div class="box-body">
                                            <div class="table-responsive">
                                                <table id="datatable" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th rowspan="2" class="align-middle text-center w-0">No</th>
                                                            <th rowspan="2" class="align-middle" style="min-width: 220px;">Nama Pegawai / NIP</th>
                                                            <th colspan="6" class="text-center bg-light font-weight-bold">Status Kehadiran (Hari)</th>
                                                            <th colspan="5" class="text-center bg-warning-light font-weight-bold">Terlambat (Kali)</th>
                                                            <th colspan="5" class="text-center bg-danger-light font-weight-bold">Pulang Cepat (Kali)</th>
                                                            <th rowspan="2" class="align-middle text-center" style="min-width: 100px;">Total Potongan</th>
                                                            <th rowspan="2" class="align-middle text-center w-0">Aksi</th>
                                                        </tr>
                                                        <tr>
                                                            <!-- Kehadiran -->
                                                            <th class="text-center bg-success-light" title="Hadir Normal">HN</th>
                                                            <th class="text-center bg-danger-light" title="Tanpa Keterangan">TK</th>
                                                            <th class="text-center bg-info-light" title="Cuti">CT</th>
                                                            <th class="text-center bg-primary-light" title="Dinas Luar">DL</th>
                                                            <th class="text-center bg-secondary-light" title="Izin">IZ</th>
                                                            <th class="text-center bg-light" title="Hari Kerja Efektif">HK</th>
                                                            
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

                                <!-- TAB 2: MONITORING SINKRONISASI BKN -->
                                <div class="tab-pane" id="tab-sync-monitoring" role="tabpanel">
                                    <div class="row">
                                        <!-- Form Pemicu Sync Manual -->
                                        <div class="col-md-4 col-12">
                                            <div class="box border shadow-none mb-md-0 mb-20">
                                                <div class="box-header with-border bg-light py-10">
                                                    <h5 class="box-title font-weight-bold text-dark"><i class="fa fa-refresh text-success"></i> Tarik Data Manual</h5>
                                                </div>
                                                <div class="box-body">
                                                    <p class="text-muted font-size-13">
                                                        Sinkronisasi massal akan memperbarui rekapitulasi kehadiran seluruh pegawai dinas yang terdaftar berdasarkan database lokal dengan server API BKN.
                                                    </p>
                                                    <form id="form-manual-sync">
                                                        <div class="form-group mb-15">
                                                            <label for="sync-month" class="form-label">Tarik Bulan</label>
                                                            <select id="sync-month" class="form-control select2 w-100">
                                                                @foreach($months as $key => $name)
                                                                    <option value="{{ $key }}" {{ $key == $currentMonth ? 'selected' : '' }}>{{ $name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="form-group mb-20">
                                                            <label for="sync-year" class="form-label">Tarik Tahun</label>
                                                            <select id="sync-year" class="form-control select2 w-100">
                                                                @foreach($years as $year)
                                                                    <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-success text-white w-100" id="btn-start-sync">
                                                            <i class="fa fa-cloud-download me-5"></i> Mulai Sinkronisasi Manual
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tabel Log Histori Sync -->
                                        <div class="col-md-8 col-12">
                                            <div class="box border shadow-none mb-0">
                                                <div class="box-header with-border bg-light py-10">
                                                    <h5 class="box-title font-weight-bold text-dark"><i class="fa fa-list text-info"></i> Log Histori Sinkronisasi Berkala BKN</h5>
                                                </div>
                                                <div class="box-body">
                                                    <div class="table-responsive">
                                                        <table id="sync-logs-table" class="table table-bordered table-striped table-hover" style="width: 100%;">
                                                            <thead class="thead-light">
                                                                <tr>
                                                                    <th class="text-center" style="width: 50px;">No</th>
                                                                    <th class="text-center" style="width: 140px;">Waktu Sync</th>
                                                                    <th class="text-center" style="width: 100px;">Periode</th>
                                                                    <th>Ditarik Oleh</th>
                                                                    <th class="text-center" style="width: 80px;">Sukses (Pgw)</th>
                                                                    <th class="text-center" style="width: 80px;">Gagal (Pgw)</th>
                                                                    <th class="text-center" style="width: 80px;">Status</th>
                                                                    <th>Pesan Log</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody></tbody>
                                                        </table>
                                                    </div>
                                                </div>
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
@endsection
@push('js')
    <script src="{{ url($template.'/assets/vendor_components/select2/dist/js/select2.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/jquery-validation-1.17.0/lib/jquery.form.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ url('/js/'.$backend.'/'.$page->code.'/datatable.js') }}"></script>
    <script src="{{ url('js/jquery-crud.js') }}"></script>
@endpush
