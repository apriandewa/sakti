@extends('backend.main.index')
@push('title', $page->title ?? 'Informasi')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title"><i class="{!! $page->icon ?? 'fa fa-info' !!}"></i> {!! $page->title ?? 'Informasi' !!} </h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"> Welcome to {!! $page->title ?? 'Informasi' !!} page</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="row">
                    <div class="col-12">
                        <div class="box">
                            <div class="box-header">
                                <h4 class="box-title">Data {!! $page->title ?? 'Informasi' !!}</h4>
                                @if(isset($user) && $user->create)
                                    <button type="button" class="btn-action pull-right btn btn-success btn-sm" data-title="Tambah" data-action="create" data-url="{!! $page->url ?? 'informasi' !!}">
                                        <span class="fa fa-plus-circle"></span> Tambah
                                    </button>
                                @endif
                            </div>
                            <div class="box-body">
                                <table id="datatable" class="table table-bordered table-striped" style="width: 100%;">
									<thead>
									<tr>
										<th class="w-0">No</th>
										<th>Nama</th>
										<th>Tipe</th>
										<th>Tahun</th>
										<th>Status</th>
										<th class="text-center w-0">Action</th>
									</tr>
									</thead>
									<tbody>
									</tbody>
								</table>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
@endsection

@push('css')
    <link rel="stylesheet" href="{{ url($template.'/assets/vendor_plugins/summernote/summernote-lite.css') }}">
@endpush

@push('js')
    <script src="{{ url($template.'/assets/vendor_components/select2/dist/js/select2.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/jquery-validation-1.17.0/lib/jquery.form.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/datatable/datatables.min.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_plugins/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ url('/js/'.($backend ?? 'backend').'/'.($page->code ?? 'informasi').'/datatable.js') }}"></script>
    <script src="{{ url('js/jquery-crud.js') }}"></script>
@endpush
