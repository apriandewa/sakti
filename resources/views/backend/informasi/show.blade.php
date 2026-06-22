<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-10">
                <div class="form-group">
                    {!! html()->span()->text("Nama Informasi")->class("control-label") !!}
                    {!! html()->p($data->nama)->class("form-control") !!}
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    {!! html()->span()->text("Status")->class("control-label") !!}
                    {!! html()->p($data->status)->class("form-control") !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span()->text("Tipe")->class("control-label") !!}
                    {!! html()->p($data->tipe)->class("form-control") !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->span()->text("Tahun")->class("control-label") !!}
                    {!! html()->p($data->tahun)->class("form-control") !!}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->span()->text("Deskripsi")->class("control-label") !!}
                    {!! html()->p($data->desc)->class("form-control") !!}
                </div>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <h5>Berkas Informasi</h5>
                <ul>
                    @foreach($data->files->where('alias', 'berkas_informasi') as $file)
                        <li><a href="{{ url($file->link_stream) }}" target="_blank">{{ $file->name }}</a></li>
                    @endforeach
                    @if($data->files->where('alias', 'berkas_informasi')->isEmpty())
                        <li>Tidak ada berkas.</li>
                    @endif
                </ul>
            </div>
        </div>

        <div class="col-md-12 mt-4">
            <x-histori-verifikasi 
                :verifiable_id="$data->id" 
                :verifiable_type="get_class($data)" 
            />
        </div>

        @if(in_array(auth()->user()->level_id, [1, 4]) && $data->status == 'PENGAJUAN')
        <hr>
        <h5>Verifikasi</h5>
        <div class="row">
            <div class="col-md-12">
                <form id="frmVerifikasi">
                    <div class="form-group">
                        <label>Status Verifikasi</label>
                        <select name="status" class="form-control" required>
                            <option value="">-- Pilih --</option>
                            <option value="DITERIMA">Terima</option>
                            <option value="REVISI">Revisi</option>
                            <option value="DITOLAK">Tolak</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <textarea name="catatan" class="form-control" rows="3"></textarea>
                    </div>
                </form>
            </div>
        </div>
        @endif

    </div>
</div>
<style>
    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.submit-data').hide();
    $('.modal-title').html('<i class="fa fa-info-circle"></i> Detail Data {!! $page->title !!}');

    // hapus tombol custom sebelumnya jika ada
    $('#btn-kirim-informasi').remove();
    $('#btn-verifikasi-informasi').remove();

    @if(in_array($data->status, ['DRAFT', 'REVISI']) && in_array(auth()->user()->level_id, [2, 3]))
        $('.modal-footer').append('<button type="button" class="btn btn-primary" id="btn-kirim-informasi"><i class="fa fa-paper-plane"></i> Kirim</button>');
        
        $('#btn-kirim-informasi').click(function() {
            swal({
                title: "Apakah anda yakin?",
                text: "Data akan dikirim untuk pengajuan",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Ya, Kirim!",
                cancelButtonText: "Batal",
                closeOnConfirm: false
            }, function(){
                $.post("{{ route('informasi.kirim', $data->id) }}", {_token: "{{ csrf_token() }}"}, function(res) {
                    if (res.status) {
                        swal("Terkirim!", res.message, "success");
                        $('#datatable').DataTable().ajax.reload();
                        $('.modal').modal('hide');
                    } else {
                        swal("Gagal!", res.message, "error");
                    }
                });
            });
        });
    @endif

    @if(in_array(auth()->user()->level_id, [1, 4]) && $data->status == 'PENGAJUAN')
        $('.modal-footer').append('<button type="button" class="btn btn-success" id="btn-verifikasi-informasi"><i class="fa fa-check"></i> Verifikasi</button>');
        
        $('#btn-verifikasi-informasi').click(function() {
            let data = $('#frmVerifikasi').serialize() + '&_token={{ csrf_token() }}';
            let status = $('#frmVerifikasi select[name=status]').val();

            if (!status) {
                swal("Peringatan", "Pilih status verifikasi terlebih dahulu", "warning");
                return;
            }

            $.post("{{ route('informasi.verifikasi', $data->id) }}", data, function(res) {
                if (res.status) {
                    swal("Berhasil!", res.message, "success");
                    $('#datatable').DataTable().ajax.reload();
                    $('.modal').modal('hide');
                } else {
                    swal("Gagal!", res.message, "error");
                }
            });
        });
    @endif
    
    // bersihkan tombol saat modal ditutup
    $('.modal').on('hidden.bs.modal', function () {
        $('#btn-kirim-informasi').remove();
        $('#btn-verifikasi-informasi').remove();
    });
</script>
