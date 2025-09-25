{{ html()->form('POST', route($page->url.'.store'))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() }}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class='form-group'>
			{!! html()->label()->class('control-label')->for('nama')->text('Nama') !!}
			{!! html()->text('nama',NULL)->placeholder('Type Nama here')->class('form-control')->id('nama') !!}
		</div>
		 <div class='form-group'>
            {!! html()->label()->class('control-label')->for('slug')->text('Slug') !!}
            {!! html()->text('slug',NULL)->placeholder('Auto generated')->class('form-control')->id('slug')->attribute('readonly', true) !!}

        </div>
		<div class='form-group'>
            {!! html()->label('Isi Ringkasan','desc')->class('control-label') !!}
            <span class="text-danger">*</span>
            {!! html()->textarea('desc')->class('form-control')->id('desc')->placeholder('Ketik Disini')->required() !!}
        </div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('keterangan')->text('Keterangan') !!}
			{!! html()->text('keterangan',NULL)->placeholder('Type Keterangan here')->class('form-control')->id('keterangan') !!}
		</div>
		<div class="form-group">
            {!! html()->label('Kategori')->class('control-label')->for('kategori') !!}
            
            {!! html()->select('kategori', [
                'Infografis' => 'Infografis',
                'Info Kegiatan' => 'Info Kegiatan',
                'Permintaan Data' => 'Permintaan Data',
                'Sengketa Informasi' => 'Sengketa Informasi'
            ])->placeholder('Pilih kategori di sini')->class('form-control select2')->id('kategori') !!}
        </div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('gambar')->text('Upload Gambar Sampul') !!}
            {!! html()->file('gambar')->class('form-control')->id('gambar')->accept('image/jpeg,image/png') !!}
		</div>

        <div class='form-group'>
            {!! html()->label('File Pendukung','file')->class('control-label') !!}
            <span class="text-danger">*</span>
            <div class="file-loading">
                {!! html()->file('file[]')->id('file')->class('file-drag-drop')->multiple()->data('overwrite-initial',false)->data('min-file-count',1) !!}
            </div>
            <span class="text-danger">Allowed : jpg, jpeg, png</span>
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

<link href="{{ url($template.'/fileupload/css/fileinput.css') }}" rel="stylesheet">
<link href="{{ url($template.'/fileupload/css/font_bootstrap-icons.min.css') }}" rel="stylesheet">
<style>
    .kv-file-upload, .fileinput-upload, .file-upload-indicator {
        display: none;
    }

    .select2-container {
        z-index: 999999 !important;
        width: 100% !important;
    }

    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script src="{{ url($template.'/fileupload/js/fileinput.js') }}"></script>
<script src="{{ url($template.'/js/slug.js') }}"></script>
<script src="{{ url($template.'/js/forminput.js') }}"></script>
