{!! html()->form('PUT', route('agenda-rapat.peserta.update', $peserta->id))->id('form-edit-peserta')->class('form form-horizontal')->open() !!}

<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <h5 class="text-muted border-bottom pb-1 mb-3"><i class="fa fa-user"></i> Edit Data Peserta</h5>
            </div>

            <div class="col-md-12">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('nama')->text('Nama Lengkap') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('nama', $peserta->nama)->placeholder('Nama Lengkap')->class('form-control')->id('nama')->required() !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('nip')->text('NIP') !!}
                    {!! html()->text('nip', $peserta->nip)->placeholder('NIP (jika ada)')->class('form-control only-number')->id('nip') !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('no_hp')->text('No HP') !!}
                    {!! html()->text('no_hp', $peserta->no_hp)->placeholder('Nomor Handphone')->class('form-control only-number')->id('no_hp') !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('jabatan')->text('Jabatan') !!}
                    {!! html()->text('jabatan', $peserta->jabatan)->placeholder('Jabatan')->class('form-control')->id('jabatan') !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label()->class('control-label')->for('instansi')->text('Instansi') !!}
                    {!! html()->text('instansi', $peserta->instansi)->placeholder('Instansi/Unit Kerja')->class('form-control')->id('instansi') !!}
                </div>
            </div>
        </div>
    </div>
</div>

{!! html()->hidden('function', 'refreshAbsensi')->id('function') !!}
{!! html()->form()->close() !!}

<script>
    $(document).ready(function() {
        $('.modal-title').html('<i class="fa fa-edit"></i> Edit Peserta Rapat');
        $('.submit-data').html('<i class="fa fa-save"></i> Simpan Perubahan');
    });
</script>
