{!! html()->form('DELETE', route('agenda-rapat.peserta.destroy', $peserta->id))->id('form-delete-peserta')->class('form form-horizontal')->open() !!}
<div class="row">
    <div class="col-md-12">
        <label class="control-label h6">Apakah Anda Yakin Ingin Menghapus Peserta Ini dari Daftar Hadir?</label>
        <div class="info-data">
            <div class="panel shadow-sm">
                <div class="panel-body panel-dark bg-dark p-3 rounded">
                    <p class="mb-1"><code>Nama</code> <span class="text-danger">:</span> <span class="text-info">{{ $peserta->nama }}</span></p>
                    <p class="mb-1"><code>NIP</code> <span class="text-danger">:</span> <span class="text-info">{{ $peserta->nip ?? '-' }}</span></p>
                    <p class="mb-1"><code>Jabatan</code> <span class="text-danger">:</span> <span class="text-info">{{ $peserta->jabatan ?? '-' }}</span></p>
                    <p class="mb-1"><code>Instansi</code> <span class="text-danger">:</span> <span class="text-info">{{ $peserta->instansi ?? '-' }}</span></p>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <span class="message"></span>
        </div>
    </div>
</div>
{!! html()->hidden('function', 'refreshAbsensi')->id('function') !!}
{!! html()->form()->close() !!}
<script>
    $('.modal-title').html('<i class="fa fa-trash"></i> Hapus Peserta');
    $('.submit-data').html('<i class="fa fa-trash"></i> Hapus Peserta');
</script>
