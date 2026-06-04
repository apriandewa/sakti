{{--
    Variabel yang dikirim dari controller:
    - $data          : model Tamu
    - $fotoTamu      : file foto (atau null)
    - $dokumenTamu   : file dokumen (atau null)
    - $statusClass   : 'success' | 'danger' | 'info'
    - $bisaDiproses  : boolean (true jika status masih TERKIRIM)
--}}

<div class="panel shadow-sm border-0">
    <div class="panel-body">
        <div class="row">

            {{-- Nama --}}
            <div class="col-md-7">
                <div class="form-group mb-3">
                    {!! html()->span('Nama Lengkap')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">{{ $data->nama ?? '-' }}</div>
                </div>
            </div>

            {{-- Jenis Kelamin --}}
            <div class="col-md-5">
                <div class="form-group mb-3">
                    {!! html()->span('Jenis Kelamin')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">
                        @if($data->jenis_kelamin === 'Laki-laki') 👨 Laki-laki
                        @elseif($data->jenis_kelamin === 'Perempuan') 👩 Perempuan
                        @else -
                        @endif
                    </div>
                </div>
            </div>

            {{-- Email --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->span('Email')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">{{ $data->email ?? '-' }}</div>
                </div>
            </div>

            {{-- No HP --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->span('No. HP / Telepon')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">{{ $data->no_hp ?? '-' }}</div>
                </div>
            </div>

            {{-- Pekerjaan --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->span('Pekerjaan / Instansi')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">{{ $data->pekerjaan ?? '-' }}</div>
                </div>
            </div>

            {{-- Asal --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->span('Asal Instansi / Sekolah / Universitas / Daerah')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">{{ $data->asal ?? '-' }}</div>
                </div>
            </div>

            {{-- Alamat --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->span('Alamat Lengkap')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box text-wrap">{{ $data->alamat ?? '-' }}</div>
                </div>
            </div>

            {{-- Keperluan --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->span('Keperluan Kunjungan')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">{{ $data->keperluan ?? '-' }}</div>
                </div>
            </div>

            {{-- Pesan --}}
            <div class="col-md-12">
                <div class="form-group mb-3">
                    {!! html()->span('Pesan / Kesan & Saran')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box text-wrap" style="min-height: 100px;">
                        {{ $data->pesan ?? '-' }}
                    </div>
                </div>
            </div>

            {{-- Foto Tamu --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->span('Foto Tamu')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box" style="min-height: 160px; align-items: flex-start; padding: 10px;">
                        @if($fotoTamu && $fotoTamu->exists)
                            <div>
                                <img src="{{ $fotoTamu->link_stream }}"
                                     alt="Foto Tamu"
                                     class="img-thumbnail mb-2"
                                     style="max-height: 150px; border-radius: 8px; display: block;">
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ $fotoTamu->link_stream }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-eye"></i> Lihat
                                    </a>
                                    <a href="{{ $fotoTamu->link_download }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-download"></i> Unduh
                                    </a>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    {{ $fotoTamu->name }} &bull; {{ $fotoTamu->size }}
                                </small>
                            </div>
                        @else
                            <span class="text-muted fst-italic">Tidak ada foto</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Dokumen Tamu --}}
            <div class="col-md-6">
                <div class="form-group mb-3">
                    {!! html()->span('Dokumen Tamu')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box" style="min-height: 160px; align-items: flex-start; padding: 10px;">
                        @if($dokumenTamu && $dokumenTamu->exists)
                            <div>
                                @if(in_array(strtolower($dokumenTamu->extension ?? ''), ['jpg','jpeg','png']))
                                    <img src="{{ $dokumenTamu->link_stream }}"
                                         alt="Dokumen Tamu"
                                         class="img-thumbnail mb-2"
                                         style="max-height: 150px; border-radius: 8px; display: block;">
                                @else
                                    <div class="mb-2 p-3 bg-light border rounded text-center" style="min-width: 120px;">
                                        <i class="fa fa-file fa-3x text-secondary"></i>
                                        <div class="mt-1 small text-muted">{{ strtoupper($dokumenTamu->extension ?? 'FILE') }}</div>
                                    </div>
                                @endif
                                <div class="d-flex gap-2 flex-wrap">
                                    <a href="{{ $dokumenTamu->link_stream }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                        <i class="fa fa-eye"></i> Lihat
                                    </a>
                                    <a href="{{ $dokumenTamu->link_download }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa fa-download"></i> Unduh
                                    </a>
                                </div>
                                <small class="text-muted d-block mt-1">
                                    {{ $dokumenTamu->name }} &bull; {{ $dokumenTamu->size }}
                                </small>
                            </div>
                        @else
                            <span class="text-muted fst-italic">Tidak ada dokumen</span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Status --}}
            <div class="col-md-4">
                <div class="form-group mb-3">
                    {!! html()->span('Status')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">
                        <span class="badge bg-{{ $statusClass }}" id="badgeStatus">
                            {{ strtoupper($data->status ?? 'TERKIRIM') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Tanggal Kunjungan --}}
            <div class="col-md-4">
                <div class="form-group mb-3">
                    {!! html()->span('Tanggal Kunjungan')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">
                        {{ $data->tanggal_kunjungan
                            ? $data->tanggal_kunjungan->translatedFormat('d F Y H:i')
                            : '-' }}
                    </div>
                </div>
            </div>

            {{-- IP Address --}}
            <div class="col-md-4">
                <div class="form-group mb-3">
                    {!! html()->span('IP Address')->class('control-label fw-semibold d-block mb-1') !!}
                    <div class="detail-box">{{ $data->ip_address ?? '-' }}</div>
                </div>
            </div>

        </div>
    </div>
</div>

<style>
    .modal-lg { max-width: 1000px !important; }
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
    .text-wrap { white-space: pre-line; }
    .form-group { margin-bottom: 0; }
</style>

<script>
    /* Sembunyikan tombol submit bawaan modal */
    $('.submit-data').hide();
    $('.modal-title').html('<i class="fa fa-search"></i> Detail Data {!! $page->title !!}');

    /*
    |--------------------------------------------------------------------------
    | Tombol Tutup (kiri) + Terima / Tolak (kanan) — hanya jika TERKIRIM
    |--------------------------------------------------------------------------
    | Struktur footer modal umumnya:
    |   <div class="modal-footer">
    |       <button class="btn btn-secondary" data-dismiss="modal">Tutup</button>
    |       <button class="submit-data …">…</button>   ← sudah di-hide
    |   </div>
    |
    | Kita pastikan tombol Tutup ada & di kiri,
    | lalu inject tombol aksi di sisi kanan.
    |--------------------------------------------------------------------------
    */

    // Pastikan tombol Tutup ada dan berada paling kiri
    var $footer = $('.modal-footer');


    @if($bisaDiproses)
        // Hapus duplikat jika ada (reload ajax)
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

    /*
    |--------------------------------------------------------------------------
    | Helper: kirim request update status via AJAX
    |--------------------------------------------------------------------------
    */
    function kirimUpdateStatus(id, status, $btn, labelAsli) {
        var url   = '{{ route("tamu.update-status", ":id") }}'.replace(':id', id);
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

                    // Reload tabel utama tanpa pindah halaman
                    if ($.fn.DataTable && $.fn.DataTable.isDataTable('#datatable')) {
                        $('#datatable').DataTable().ajax.reload(null, true);
                    }

                    // Hapus kedua tombol karena sudah diproses
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

    /*
    |--------------------------------------------------------------------------
    | Tombol TERIMA
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.btn-terima', function () {
        var $btn      = $(this);
        var id        = $btn.data('id');
        var labelAsli = '<i class="fa fa-check-circle"></i> Terima';

        swal({
            title              : 'Konfirmasi',
            text               : 'Apakah Anda yakin ingin MENERIMA kunjungan ini?',
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

    /*
    |--------------------------------------------------------------------------
    | Tombol TOLAK
    |--------------------------------------------------------------------------
    */
    $(document).on('click', '.btn-tolak', function () {
        var $btn      = $(this);
        var id        = $btn.data('id');
        var labelAsli = '<i class="fa fa-times-circle"></i> Tolak';

        swal({
            title              : 'Konfirmasi',
            text               : 'Apakah Anda yakin ingin MENOLAK kunjungan ini?',
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