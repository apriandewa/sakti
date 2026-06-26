{!! html()->form('DELETE', route($page->code.'.destroy', $data->id))->id('form-create-'.$page->code)->class('form form-horizontal')->open() !!}
<div class="row">
    <div class="col-md-12">
        <label class="control-label h6">Apakah Anda Yakin Ingin Menghapus Agenda Rapat Ini?</label>
        <div class="info-data">
            <div class="panel">
                <div class="panel-body panel-dark bg-dark">
                    <p><code>Nama</code> <span class="text-danger">:</span> <span class="text-info">{{ $data->nama }}</span></p>
                    <p><code>Tanggal</code> <span class="text-danger">:</span> <span class="text-info">{{ $data->tanggal ? $data->tanggal->format('d/m/Y') : '-' }}</span></p>
                    <p><code>Waktu</code> <span class="text-danger">:</span> <span class="text-info">{{ substr($data->jam_mulai, 0, 5) }} - {{ substr($data->jam_selesai, 0, 5) }}</span></p>
                    <p><code>Tempat</code> <span class="text-danger">:</span> <span class="text-info">{{ $data->tempat }}</span></p>
                    <p><code>Status</code> <span class="text-danger">:</span> <span class="text-info">{{ $data->status }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <span class="message"></span>
        </div>
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{!! html()->form()->close() !!}
<script>
    $('.modal-title').html('<i class="mdi mdi-delete-forever"></i> Hapus Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-trash"></i> Hapus Data');
</script>
