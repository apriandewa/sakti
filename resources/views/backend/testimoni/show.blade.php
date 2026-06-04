<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Nama")->class("control-label") !!}
					{!! html()->p($data->nama)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Desc")->class("control-label") !!}
					{!! html()->p($data->desc)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Keterangan")->class("control-label") !!}
					{!! html()->p($data->keterangan)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group mb-3">
					{!! html()->span()->text("Status")->class("control-label fw-semibold d-block mb-1") !!}
					<div class="detail-box">
						<span class="badge bg-{{ $statusClass ?? 'info' }}" id="badgeStatus">
							{{ strtoupper($data->status ?? 'TERKIRIM') }}
						</span>
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group mb-3">
					{!! html()->span()->text("Gambar")->class("control-label fw-semibold d-block mb-1") !!}
					<div class="detail-box" style="min-height: 160px; align-items: flex-start; padding: 10px;">
						@if(!is_null($data->getfilebyalias('gambar_testimoni')))
							@php
								$file = $data->getfilebyalias('gambar_testimoni');
							@endphp
							@if($file)
								<div>
									{!! html()->img(url($file->link_stream), $file->name)->class('img-fluid img-thumbnail mb-2')->style('max-height: 150px; border-radius: 8px; display: block;') !!}
									<div class="d-flex gap-2 flex-wrap">
										<a href="{{ $file->link_stream }}" target="_blank" class="btn btn-sm btn-outline-primary">
											<i class="fa fa-eye"></i> Lihat
										</a>
										<a href="{{ $file->link_download }}" class="btn btn-sm btn-outline-secondary">
											<i class="fa fa-download"></i> Unduh
										</a>
									</div>
									<small class="text-muted d-block mt-1">
										{{ $file->name }} &bull; {{ $file->size }}
									</small>
								</div>
							@endif
						@else
							<span class="text-muted fst-italic">Tidak ada gambar</span>
						@endif
					</div>
				</div>
			</div>
		</div>
    </div>
</div>
<style>
    .modal-lg {
        max-width: 1000px !important;
    }
    .detail-box {
        min-height: 46px;
        padding: 10px 14px;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        background: #f8f9fa;
        display: flex;
        align-items: center;
        font-size: 14px;
        line-height: 1.5;
    }
</style>
<script>
    $('.submit-data').hide();
    $('.modal-title').html('<i class="fa fa-search"></i> Detail Data {!! $page->title !!}');

    var $footer = $('.modal-footer');

    @if(isset($bisaDiproses) && $bisaDiproses)
        $('.btn-terima, .btn-tolak').remove();

        $footer.append(
            '<button type="button" class="btn btn-sm btn-success btn-terima me-auto" data-id="{{ $data->id }}">' +
                '<i class="fa fa-check-circle"></i> Terima' +
            '</button>' +
            '<button type="button" class="btn btn-sm btn-danger btn-tolak me-auto" data-id="{{ $data->id }}">' +
                '<i class="fa fa-times-circle"></i> Tolak' +
            '</button>'
        );
    @endif

    function kirimUpdateStatus(id, status, $btn, labelAsli) {
        var url   = '{{ route("testimoni.update-status", ":id") }}'.replace(':id', id);
        var token = '{{ csrf_token() }}';

        $.ajax({
            url  : url,
            type : 'PUT',
            data : { _token: token, status: status },
            beforeSend: function () {
                $btn.prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Memproses...');
            },
            success: function (res) {
                if (res.success) {
                    var warna = (status === 'DISETUJUI') ? 'success' : 'danger';

                    $('#badgeStatus')
                        .removeClass('bg-success bg-danger bg-info bg-warning bg-secondary')
                        .addClass('bg-' + warna)
                        .text(status);

                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
                        $('#datatable').DataTable().ajax.reload(null, true);
                    }

                    $('.btn-terima, .btn-tolak').remove();

                    swal('Berhasil!', res.message, 'success');
                } else {
                    swal('Gagal!', res.message || 'Terjadi kesalahan.', 'error');
                    $btn.prop('disabled', false).html(labelAsli);
                }
            },
            error: function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message)
                    ? xhr.responseJSON.message
                    : 'Terjadi kesalahan pada server.';
                swal('Gagal!', msg, 'error');
                $btn.prop('disabled', false).html(labelAsli);
            }
        });
    }

    $(document).on('click', '.btn-terima', function () {
        var $btn      = $(this);
        var id        = $btn.data('id');
        var labelAsli = '<i class="fa fa-check-circle"></i> Terima';

        swal({
            title              : 'Konfirmasi',
            text               : 'Apakah Anda yakin ingin MENERIMA data ini?',
            type               : 'warning',
            showCancelButton   : true,
            confirmButtonColor : '#28a745',
            confirmButtonText  : 'Ya, Terima',
            cancelButtonText   : 'Batal',
            closeOnConfirm     : false,
        }, function () {
            kirimUpdateStatus(id, 'DISETUJUI', $btn, labelAsli);
        });
    });

    $(document).on('click', '.btn-tolak', function () {
        var $btn      = $(this);
        var id        = $btn.data('id');
        var labelAsli = '<i class="fa fa-times-circle"></i> Tolak';

        swal({
            title              : 'Konfirmasi',
            text               : 'Apakah Anda yakin ingin MENOLAK data ini?',
            type               : 'warning',
            showCancelButton   : true,
            confirmButtonColor : '#dc3545',
            confirmButtonText  : 'Ya, Tolak',
            cancelButtonText   : 'Batal',
            closeOnConfirm     : false,
        }, function () {
            kirimUpdateStatus(id, 'DITOLAK', $btn, labelAsli);
        });
    });
</script>
