{{ html()->form('POST', route($page->url.'.store'))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class='form-group'>
			{!! html()->label()->class('control-label')->for('nama')->text('Nama') !!}
			{!! html()->text('nama',NULL)->placeholder('Type Nama here')->class('form-control')->id('nama') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('desc')->text('Desc') !!}
			{!! html()->textarea('desc',NULL)->class('form-control')->id('desc') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('penyelenggara')->text('Penyelenggara') !!}
			{!! html()->text('penyelenggara',NULL)->placeholder('Type Penyelenggara here')->class('form-control')->id('penyelenggara') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('keterangan')->text('Keterangan') !!}
			{!! html()->text('keterangan',NULL)->placeholder('Type Keterangan here')->class('form-control')->id('keterangan') !!}
		</div>
		<div class="form-group">
            {!! html()->label('Status')->class('control-label')->for('status') !!}
            
            {!! html()->select('status', [
                'aktif' => 'Aktif',
                'nonaktif' => 'Tidak Aktif'
            ])->placeholder('Pilih status di sini')->class('form-control select2')->id('status') !!}
        </div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('gambar')->text('Upload Logo') !!}
            {!! html()->file('gambar')->class('form-control')->id('gambar')->accept('image/jpeg,image/png') !!}
		</div>
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{{--{!! html()->hidden('function','loadMenu,sidebarMenu')->id('function') !!}--}}
{{--{!! html()->hidden('redirect',url('/dashboard'))->id('redirect') !!}--}}
{!! html()->form()->close() !!}
<style>
    .select2-container {
        z-index: 9999 !important;
        width: 100% !important;
    }

    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.select2').select2();
    $('.modal-title').html('<i class="fa fa-plus-circle"></i> Tambah Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');
</script>
