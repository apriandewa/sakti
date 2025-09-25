{!! html()->modelForm($data,'PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class='form-group'>
			{!! html()->label()->class('control-label')->for('tahun')->text('Tahun') !!}
			{!! html()->date('tahun',$data->tahun)->class('form-control')->id('tahun') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('pemohon')->text('Pemohon') !!}
			{!! html()->number('pemohon',$data->pemohon)->placeholder('Type Pemohon here')->class('form-control')->id('pemohon') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('diminta')->text('Diminta') !!}
			{!! html()->number('diminta',$data->diminta)->placeholder('Type Diminta here')->class('form-control')->id('diminta') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('diberikan')->text('Diberikan') !!}
			{!! html()->number('diberikan',$data->diberikan)->placeholder('Type Diberikan here')->class('form-control')->id('diberikan') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('ditolak')->text('Ditolak') !!}
			{!! html()->number('ditolak',$data->ditolak)->placeholder('Type Ditolak here')->class('form-control')->id('ditolak') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('keterangan')->text('Keterangan') !!}
			{!! html()->text('keterangan',$data->keterangan)->placeholder('Type Keterangan here')->class('form-control')->id('keterangan') !!}
		</div>
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{{--{!! html()->hidden('function','loadMenu,sidebarMenu')->id('function') !!}--}}
{{--{!! html()->hidden('redirect',url('/dashboard'))->id('redirect') !!}--}}
{!! html()->closeModelForm() !!}
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
    $('.modal-title').html('<i class="fa fa-edit"></i> Edit Data {!! $page->title !!}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');
</script>