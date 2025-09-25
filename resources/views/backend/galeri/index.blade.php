@extends('backend.main.index')
@push('title', $page->title ?? 'Galeri')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <div class="content-header">
                <div class="d-flex align-items-center">
                    <div class="me-auto">
                        <h3 class="page-title"><i class="{!! $page->icon !!}"></i> {!! $page->title ?? 'Page Name' !!} </h3>
                        <div class="d-inline-block align-items-center">
                            <nav>
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"> {!! $page->subtitle ?? 'Welcome to '.$page->title.' page' !!}</li>
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
                                <h4 class="box-title">Content {!! $page->title ?? 'Page Name' !!}</h4>
                                @if($user->create)
                                    <button type="button" class="btn-action pull-right btn btn-success btn-sm" data-title="Tambah" data-action="create" data-url="{!! $page->url ?? '' !!}">
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
										<th>Slug</th>
										<th>Desc</th>
										<th>Kategori</th>
										<th>Keterangan</th>
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
@push('js')
    <!-- jQuery dulu -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

    <script src="{{ url($template.'/assets/vendor_components/select2/dist/js/select2.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/sweetalert/sweetalert.min.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/jquery-validation-1.17.0/lib/jquery.form.js') }}"></script>
    <script src="{{ url($template.'/assets/vendor_components/datatable/datatables.min.js') }}"></script>
     <script src="{{ url($template.'/assets/vendor_plugins/summernote/summernote-lite.min.js') }}"></script>
    <script src="{{ url('/js/'.$backend.'/'.$page->code.'/datatable.js') }}"></script>
    <script src="{{ url('js/jquery-crud.js') }}"></script>

    <script>
        $(document).on('click', '.btn-kirim[data-action="kirim"]', function(e) {
            e.preventDefault();
            let url = $(this).data('url'); // URL untuk request send
    
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Galeri akan dikirim untuk verifikasi!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, kirim!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Kirim request AJAX ke server
                    $.ajax({
                        url: url,
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status) {
                                Swal.fire(
                                    'Good job!',
                                    response.message,
                                    'success'
                                ).then(() => {
                                    window.location.reload(); // Reload the current page
                                });
                            } else {
                                Swal.fire(
                                    'Gagal!',
                                    response.message,
                                    'error'
                                );
                            }
                        },
                        error: function(xhr) {
                            Swal.fire(
                                'Gagal!',
                                'Terjadi kesalahan pada server.',
                                'error'
                            );
                        }
                    });
                }
            });
        });
    </script>
@endpush

@push('css')
    <link rel="stylesheet" href="{{ url($template.'/assets/vendor_plugins/summernote/summernote-lite.css') }}">
@endpush
