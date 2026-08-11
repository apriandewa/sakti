@extends('backend.main.index')
@push('title', 'Rekap e-Kinerja')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title"><i class="fa fa-line-chart"></i> Rekap Penilaian e-Kinerja Pegawai</h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                                    <li class="breadcrumb-item active">Rekap e-Kinerja</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>

            <section class="content">
                <div class="row">
                    <div class="col-12">

                        {{-- ============================================
                             2-TAB: Data Kinerja | Log & Monitoring
                             ============================================ --}}
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#tabDataKinerja">
                                        <i class="fa fa-table"></i> Data Kinerja
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#tabLogSinkronisasi" id="tabLogLink">
                                        <i class="fa fa-history"></i> Log &amp; Monitoring Sinkronisasi BKN
                                    </a>
                                </li>
                            </ul>

                            <div class="tab-content">

                                {{-- ========================
                                     TAB 1: DATA KINERJA
                                     ======================== --}}
                                <div class="tab-pane active" id="tabDataKinerja">

                                    {{-- Filter Form --}}
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <h4 class="box-title"><i class="fa fa-filter"></i> Filter Pencarian</h4>
                                        </div>
                                        <div class="box-body">
                                            <form id="formFilterRekap" class="row g-3 align-items-end">
                                                <div class="col-md-5">
                                                    <label class="control-label">Kantor / OPD (Unor)</label>
                                                    <select id="filterUnor" class="form-control select2-unor" style="width:100%;" required>
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="control-label">Periode SKP</label>
                                                    <select id="filterPeriode" class="form-control select2-periode" style="width:100%;" required>
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary btn-sm w-100">
                                                        <span class="fa fa-search"></span> Cari
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- DataTable Rekap --}}
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <h4 class="box-title">Data Penilaian e-Kinerja</h4>
                                        </div>
                                        <div class="box-body table-responsive">
                                            <table id="datatableRekap" class="table table-bordered table-striped" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:40px;">No</th>
                                                        <th>Nama Pegawai / NIP</th>
                                                        <th>Unor / OPD</th>
                                                        <th>Periode SKP</th>
                                                        <th class="text-center">Hasil Kerja</th>
                                                        <th class="text-center">Perilaku Kerja</th>
                                                        <th class="text-center">Hasil Akhir</th>
                                                        <th class="text-center" style="width:80px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                            <p class="text-muted small mt-3 mb-0" id="rekapEmptyHint">
                                                <i class="fa fa-info-circle"></i> Pilih Kantor/Unor dan Periode di atas, lalu klik <strong>Cari</strong> untuk menampilkan data.
                                            </p>
                                        </div>
                                    </div>

                                </div>{{-- /tabDataKinerja --}}

                                {{-- ========================
                                     TAB 2: LOG SINKRONISASI
                                     ======================== --}}
                                <div class="tab-pane" id="tabLogSinkronisasi">

                                    {{-- Panel Aksi Sinkronisasi --}}
                                    <div class="box box-success">
                                        <div class="box-header with-border">
                                            <h4 class="box-title"><i class="fa fa-refresh"></i> Tarik Data dari BKN</h4>
                                            <p class="text-muted small mb-0 mt-1">
                                                Pilih Kantor/OPD dan Periode, lalu klik <strong>Tarik Data BKN</strong> untuk menyinkronkan seluruh pegawai dari Unor tersebut.
                                            </p>
                                        </div>
                                        <div class="box-body">
                                            <form id="formSinkronisasi" class="row g-3 align-items-end">
                                                <div class="col-md-5">
                                                    <label class="control-label">Kantor / OPD (Unor)</label>
                                                    <select id="syncUnor" class="form-control select2-unor" style="width:100%;" required>
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-5">
                                                    <label class="control-label">Periode SKP</label>
                                                    <select id="syncPeriode" class="form-control select2-periode" style="width:100%;" required>
                                                        <option value=""></option>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" id="btnSyncRekap" class="btn btn-success btn-sm w-100">
                                                        <span class="fa fa-cloud-download"></span> Tarik Data BKN
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- DataTable Log --}}
                                    <div class="box">
                                        <div class="box-header with-border">
                                            <h4 class="box-title">Histori Sinkronisasi</h4>
                                        </div>
                                        <div class="box-body table-responsive">
                                            <table id="datatableLogs" class="table table-bordered table-striped" style="width:100%;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width:40px;">No</th>
                                                        <th>Kantor / Unor</th>
                                                        <th>Periode</th>
                                                        <th class="text-center">Status</th>
                                                        <th>Dijalankan Oleh</th>
                                                        <th>Waktu Mulai</th>
                                                        <th>Waktu Selesai</th>
                                                        <th class="text-center">Durasi</th>
                                                        <th class="text-center">Hasil</th>
                                                        <th>Catatan</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>

                                </div>{{-- /tabLogSinkronisasi --}}

                            </div>{{-- /tab-content --}}
                        </div>{{-- /nav-tabs-custom --}}

                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ url($template.'/assets/vendor_components/select2/dist/css/select2.min.css') }}">
@endpush

@push('js')
    <script src="{{ url($template.'/assets/vendor_components/select2/dist/js/select2.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/datatable/datatables.min.js') }}"></script>

    {{-- Inject config URLs ke JS (aman, tidak expose secret) --}}
    <script>
        window.EkinerjaRekapConfig = {
            urlDatatable:    '{{ route('kinerja.data') }}',
            urlLogsDatatable:'{{ route('kinerja.logs-data') }}',
            urlUnorOptions:  '{{ route('kinerja.unor') }}',
            urlPeriodeOptions:'{{ route('kinerja.periode') }}',
            urlSync:         '{{ route('kinerja.sync') }}',
            csrfToken:       '{{ csrf_token() }}'
        };
    </script>

    <script src="{{ asset('js/backend/ekinerja/datatable.js') }}"></script>
@endpush