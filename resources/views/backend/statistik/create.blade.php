{{ html()->form('POST', route($page->url.'.store'))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
		<div class='form-group'>
				{!! html()->label()->class('control-label')->for('tahun')->text('Tahun') !!}
				{!! html()->number('tahun',NULL)->placeholder('Masukkan Tahun')->class('form-control')->id('tahun')->attribute('min', 1900)->attribute('max', date('Y')) !!}
			</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('pemohon')->text('Pemohon') !!}
			{!! html()->number('pemohon',NULL)->placeholder('Masukkan Jumlah Pemohon')->class('form-control')->id('pemohon') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('diminta')->text('Diminta') !!}
			{!! html()->number('diminta',NULL)->placeholder('Masukkan Jumlah Diminta disini')->class('form-control')->id('diminta') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('diberikan')->text('Diberikan') !!}
			{!! html()->number('diberikan',NULL)->placeholder('Masukkan Jumlah Diberikan disini')->class('form-control')->id('diberikan') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('ditolak')->text('Ditolak') !!}
			{!! html()->number('ditolak',NULL)->placeholder('Masukkan Jumlah Ditolak disini')->class('form-control')->id('ditolak') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('keterangan')->text('Keterangan') !!}
			{!! html()->text('keterangan',NULL)->placeholder('Type Keterangan disini')->class('form-control')->id('keterangan') !!}
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
