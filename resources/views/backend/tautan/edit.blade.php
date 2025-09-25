{!! html()->modelForm($data,'PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}
<div class="panel shadow-sm">
    <div class="panel-body">
        <div class='form-group'>
			{!! html()->label()->class('control-label')->for('nama')->text('Nama') !!}
			{!! html()->text('nama',$data->nama)->placeholder('Type Nama here')->class('form-control')->id('nama') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('desc')->text('Desc') !!}
			{!! html()->textarea('desc',$data->desc)->class('form-control')->id('desc') !!}
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('link')->text('Link') !!}
			{!! html()->text('link',$data->link)->placeholder('Type Link here')->class('form-control')->id('link') !!}
		</div>
		<div class="form-group">
            {!! html()->label('Status')->class('control-label')->for('status') !!}
            {!! html()->select('status', [
                'aktif' => 'Aktif',
                'nonaktif' => 'Tidak Aktif'
            ], $data->status) // <-- tambahkan value default di sini
            ->placeholder('Pilih status di sini')
            ->class('form-control select2')
            ->id('status') !!}
        </div>
		<div class='form-group'>
            {!! html()->label('gambar')->text('Unggah File')->class('control-label') !!}
            <span class="text-danger">Jenis File : image (jpg, png)</span><br>
            {!! html()->file('gambar')->class('form-control')->id('gambar')->accept('image/*') !!}
        </div>
		@if($data->files->where('alias', 'gambar_tautan')->where('fillable_id', $data->id)->count())
			<div class='form-group'>
				<table class="table table-{!! $data->id !!}">
					@foreach($data->files->where('alias', 'gambar_tautan')->where('fillable_id', $data->id) as $gambar)
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