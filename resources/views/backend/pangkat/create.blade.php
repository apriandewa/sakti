{{ html()->form('POST', route($page->url.'.store'))->id('form-create-'.$page->code)->class('form form-horizontal')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="form-group">
            {!! html()->label('Nama Pangkat','nama')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->text('nama')->placeholder('Contoh: Pembina Utama Muda / IVc')->class('form-control')->id('nama')->required() !!}
        </div>
        <div class="form-group">
            {!! html()->label('Deskripsi','desc')->class('control-label') !!}
            {!! html()->textarea('desc')->placeholder('Ketik deskripsi pangkat di sini')->class('form-control')->id('desc') !!}
        </div>
        <div class="form-group">
            {!! html()->label('Keterangan','keterangan')->class('control-label') !!}
            {!! html()->text('keterangan')->placeholder('Ketik keterangan tambahan di sini')->class('form-control')->id('keterangan') !!}
        </div>
        <div class="form-group">
            {!! html()->label('Status','status')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->select('status', ['aktif' => 'Aktif', 'tidak aktif' => 'Tidak Aktif'])->class('form-select')->id('status')->required() !!}
        </div>
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{!! html()->form()->close() !!}
<script>
    $('.modal-title').html('<i class="fa fa-plus-circle"></i> Tambah Data {{ $page->title }}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');
</script>
