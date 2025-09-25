{!! html()->modelForm($data,'PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class='form-group'>
			{!! html()->label()->class('control-label')->for('nama')->text('Nama') !!}
			{!! html()->text('nama',$data->nama)->placeholder('Type Nama here')->class('form-control')->id('nama') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('slug')->text('Slug') !!}
			{!! html()->text('slug',$data->slug)->placeholder('Type Slug here')->class('form-control')->id('slug') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('desc')->text('Desc') !!}
			{!! html()->textarea('desc',$data->desc)->class('form-control')->id('desc') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('keterangan')->text('Keterangan') !!}
			{!! html()->text('keterangan',$data->keterangan)->placeholder('Type Keterangan here')->class('form-control')->id('keterangan') !!}
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
            {!! html()->label('gambar')->text('Unggah File')->class('control-label') !!}
            <span class="text-danger">Jenis File : image (jpg, png)</span><br>
            {!! html()->file('gambar')->class('form-control')->id('gambar')->accept('image/*') !!}
        </div>
		@if($data->files->where('alias', 'gambar')->where('fillable_id', $data->id)->count())
			<div class='form-group'>
				<table class="table table-{!! $data->id !!}">
					@foreach($data->files->where('alias', 'gambar_galeri')->where('fillable_id', $data->id) as $gambar)
						@if($gambar->exists())
							<tr>
								<td>
									File :
									<a href="{{ url($gambar->link_stream) }}" target="_blank"> {{ $gambar->name }} </a>
								</td>
								<td>
									Size : {!! $gambar->size !!}
								</td>
								<td>
									{!! html()->a(url($gambar->link_download),'<i class="fa fa-download"></i> Download')->class('btn btn-xs btn-primary')->target('_blank') !!}
									{!! html()->a('#delete','<i class="fa fa-trash"></i> Delete File')->class('delete btn btn-danger btn-xs')->attribute('data-url',url($gambar->link_delete)) !!}
								</td>
							</tr>
						@endif
					@endforeach
				</table>
			</div>
		@endif

		<div class='form-group'>
            {!! html()->label('File Pendukung','file')->class('control-label') !!}
            <span class="text-danger">*</span>
            <div class="file-loading">
                {!! html()->file('file[]')->id('file')->class('file-drag-drop')->multiple()->data('overwrite-initial',false)->data('min-file-count',1) !!}
            </div>
        </div>
        @if(!$data->file->isEmpty())
            <div class="form-group">
                <label class="control-label">File Pendukung Saat Ini :</label>
                <table class="table">
                    @foreach($data->file as $file)
                        <tr id="{{ $file->id }}">
                            <td>
                                <a href="{{ $file->link_stream }}" target="_blank">{{ $file->file_name }}</a>
                            </td>
                            <td>
                                <a href="#delete" class="btn btn-danger btn-xs delete-file" data-title="Delete" data-id="{{ $file->id }}" data-url="{{ $file->link_delete }}" data-message="Do you want to delete this data ?">
                                    <span class="fa fa-trash"></span> Delete File
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{{--{!! html()->hidden('function','loadMenu,sidebarMenu')->id('function') !!}--}}
{{--{!! html()->hidden('redirect',url('/dashboard'))->id('redirect') !!}--}}
{!! html()->closeModelForm() !!}
<style>
    .kv-file-upload, .fileinput-upload, .file-upload-indicator {
        display: none;
    }

    .select2-container {
        z-index: 9999 !important;
        width: 100% !important;
    }

    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script src="{{ url($template.'/fileupload/js/fileinput.js') }}"></script>
<script>
    $('#menu_id, #parent_id').select2().parent().css('z-index', 9999)
    $('.modal-title').html('<i class="fa fa-edit"></i> Edit Data {{ $page->title }}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');
    $('#content').summernote({
        tabsize: 2,
        height: 250,
        toolbar: [
            "fontsize",
            "fontname",
            "forecolor",
            "paragraph",
            "table",
            "insert",
            "codeview",
            "link",
            "color"
        ],
        fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '36'],
    });
    var noteModal = document.querySelector('.note-modal');
    noteModal.style.zIndex = 9999;
    noteModal.querySelector('.checkbox').style.display = 'none';
    noteModal.querySelector('.note-modal-content').style.padding = '3px';

    $(".file-drag-drop").fileinput({
        theme: 'fa',
        uploadUrl: "/#",
        allowedFileExtensions: ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx', 'xls', 'xlsx'],
        overwriteInitial: false,
        maxFileSize: 2048,
        maxFilesNum: 10,
        slugCallback: function (filename) {
            return filename.replace('(', '_').replace(']', '_');
        },
        initialPreviewAsData: true,
    });

    $('.select2').each(function () {
        let dropdownParent = $(this).closest('form');
        $(this).select2({
            placeholder: "Silahkan Pilih",
            dropdownParent: dropdownParent
        });
    });
</script>