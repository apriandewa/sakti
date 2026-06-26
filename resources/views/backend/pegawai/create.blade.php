{{ html()->form('POST', route($page->url.'.store'))->id('form-create-'.$page->code)->acceptsFiles()->class('form form-horizontal')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Nama Lengkap','nama')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('nama')->placeholder('Ketik nama lengkap di sini')->class('form-control')->id('nama')->required() !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Akun User (Opsional)','user_id')->class('control-label') !!}
                    {!! html()->select('user_id', $users)->placeholder('Hubungkan dengan akun login')->class('form-control select2')->id('user_id') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Gelar Depan','gelar_depan')->class('control-label') !!}
                    {!! html()->text('gelar_depan')->placeholder('Contoh: H., Dr.')->class('form-control')->id('gelar_depan') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Gelar Belakang','gelar_belakang')->class('control-label') !!}
                    {!! html()->text('gelar_belakang')->placeholder('Contoh: S.Kom, M.Si')->class('form-control')->id('gelar_belakang') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('NIP','nip')->class('control-label') !!}
                    {!! html()->text('nip')->placeholder('Ketik NIP pegawai')->class('form-control')->id('nip') !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('NIK','nik')->class('control-label') !!}
                    {!! html()->text('nik')->placeholder('Ketik NIK pegawai')->class('form-control')->id('nik') !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('Status Kerja','status_id')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->select('status_id', $statuses)->placeholder('Pilih Status Kerja')->class('form-control select2')->id('status_id')->required() !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('Pangkat / Golongan','pangkat_id')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->select('pangkat_id', $pangkats)->placeholder('Pilih Pangkat')->class('form-control select2')->id('pangkat_id')->required() !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('Bidang','bidang_id')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->select('bidang_id', $bidangs)->placeholder('Pilih Bidang')->class('form-control select2')->id('bidang_id')->required() !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Jenis Jabatan','jabatan_jenis_id')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->select('jabatan_jenis_id', $jabatanJenis)->placeholder('Pilih Jenis Jabatan')->class('form-control select2')->id('jabatan_jenis_id')->required() !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Nama Jabatan','jabatan_nama_id')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->select('jabatan_nama_id', [])->placeholder('Pilih Nama Jabatan')->class('form-control select2')->id('jabatan_nama_id')->required() !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('Jenis Kelamin','jenis_kelamin')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->select('jenis_kelamin', ['Laki-laki' => 'Laki-laki', 'Perempuan' => 'Perempuan'])->placeholder('Pilih Jenis Kelamin')->class('form-select')->id('jenis_kelamin')->required() !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('Agama','agama')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('agama')->placeholder('Contoh: Islam')->class('form-control')->id('agama')->required() !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('Pendidikan Terakhir','pendidikan_terakhir')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('pendidikan_terakhir')->placeholder('Contoh: S1 Teknik Informatika')->class('form-control')->id('pendidikan_terakhir')->required() !!}
                </div>
            </div>
        </div>

        <div class="form-group">
            {!! html()->label('Alamat','alamat')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->textarea('alamat')->placeholder('Ketik alamat lengkap pegawai')->class('form-control')->id('alamat')->rows(3)->required() !!}
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('No Telepon','telpon')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('telpon')->placeholder('Ketik nomor telepon')->class('form-control')->id('telpon')->required() !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('Status Aktif','status')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->select('status', ['aktif' => 'Aktif', 'tidak aktif' => 'Tidak Aktif'])->class('form-select')->id('status')->required() !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! html()->label('Periode (Tahun)','periode')->class('control-label') !!}
                    <span class="text-danger">*</span>
                    {!! html()->text('periode', date('Y'))->placeholder('Contoh: 2026')->class('form-control')->id('periode')->required() !!}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Foto Pegawai','foto_pegawai')->class('control-label') !!}
                    {!! html()->file('foto_pegawai')->class('form-control')->id('foto_pegawai')->accept('image/png,image/jpeg') !!}
                    <span class="text-danger">Allowed: JPG, PNG (Max 1MB)</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! html()->label('Spesimen TTE','spesimen_tte')->class('control-label') !!}
                    {!! html()->file('spesimen_tte')->class('form-control')->id('spesimen_tte')->accept('image/png,image/jpeg') !!}
                    <span class="text-danger">Allowed: JPG, PNG (Max 1MB)</span>
                </div>
            </div>
        </div>
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{!! html()->form()->close() !!}
<style>
    .select2-container {
        z-index: 999999 !important;
        width: 100% !important;
    }
    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.select2').each(function () {
        let dropdownParent = $(this).closest('form');
        $(this).select2({
            placeholder: "Silahkan Pilih",
            dropdownParent: dropdownParent
        });
    });

    $('.modal-title').html('<i class="fa fa-plus-circle"></i> Tambah Data {{ $page->title }}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');

    // Dependent dropdown logic
    $('#jabatan_jenis_id').on('change', function() {
        let parentId = $(this).val();
        let dropdown = $('#jabatan_nama_id');
        dropdown.empty().append('<option value="">Pilih Nama Jabatan</option>');
        if (parentId) {
            $.ajax({
                url: "{{ url(config('master.app.url.backend').'/pegawai/get-jabatan-nama') }}/" + parentId,
                type: "GET",
                dataType: "json",
                success: function(data) {
                    $.each(data, function(key, value) {
                        dropdown.append('<option value="'+ key +'">'+ value +'</option>');
                    });
                    dropdown.trigger('change');
                }
            });
        }
    });
</script>
