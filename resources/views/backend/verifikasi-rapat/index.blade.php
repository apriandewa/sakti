@extends('backend.main.index')
@push('title', $page->title ?? 'Verifikasi Rapat')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title"><i class="{!! $page->icon !!}"></i> {!! $page->title ?? 'Verifikasi Rapat' !!}</h3>
                        <nav><ol class="breadcrumb"><li class="breadcrumb-item">{!! $page->subtitle ?? 'Verifikasi Agenda Rapat' !!}</li></ol></nav>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header"><h4 class="box-title">Daftar Pengajuan Rapat</h4></div>
                            <div class="box-body">
                                <table id="datatable" class="table table-bordered table-striped" style="width:100%;">
                                    <thead><tr>
                                        <th class="w-0">No</th><th>Nama Agenda</th><th>Tanggal</th><th>Waktu</th><th>Tempat</th><th>Pembuat</th><th>Status</th><th class="text-center w-0">Action</th>
                                    </tr></thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ url($template.'/assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/jquery-validation-1.17.0/lib/jquery.form.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ url('/js/'.$backend.'/'.$page->code.'/datatable.js') }}"></script>
    <script src="{{ url('js/jquery-crud.js') }}"></script>
@endpush
