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
		<div class="row">
            <div class="col-md-6">
		        <div class="form-group">
					{!! html()->label('Kategori')->class('control-label')->for('kategori') !!}
					
					{!! html()->select('kategori', [
						'welcome' => 'Welcome',
						'profil' => 'Profil',
						'saluran' => 'Saluran Informasi',
						'banner' => 'Banner',
						'single_page' => 'Single Page',
						'lainnya' => 'Lainnya'
					])->placeholder('Pilih kategori di sini')->class('form-control select2')->id('kategori') !!}
				</div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
					{!! html()->label('Icon','icon')->class('control-label') !!}
					<span class="text-danger">*</span>
					<div class="input-group mb-3">
						<span class="input-group-prepend">
							<i class="input-group-text selected-icon"></i>
						</span>
						{!! html()->text('icon',$data->icon)->placeholder('Icon')->class('form-control iconpicker')->id('icon')->attributes(['autocomplete' => 'off']) !!}
					</div>
				</div>
            </div>
        </div>
		<div class='form-group'>
			{{-- Hidden agar default = 0 jika tidak dicentang --}}
			{!! html()->hidden('menu', 0) !!}
			{!! html()->checkbox('menu', $data->menu == 1, 1)->id('menu_checkbox')->class('filled-in chk-col-primary') !!}
			{!! html()->label('Tampilkan di Menu','menu_checkbox')->class('control-label') !!}
		</div>

		<div class='form-group'>
			{!! html()->hidden('beranda', 0) !!}
			{!! html()->checkbox('beranda', $data->beranda == 1, 1)->id('beranda_checkbox')->class('filled-in chk-col-primary') !!}
			{!! html()->label('Tampilkan di Beranda','beranda_checkbox')->class('control-label') !!}
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
            {!! html()->label('gambar')->text('Unggah Gambar Page')->class('control-label') !!}
            <span class="text-danger">Jenis File : image (jpg, png)</span><br>
            {!! html()->file('gambar')->class('form-control')->id('gambar')->accept('image/*') !!}
        </div>
		@if($data->files->where('alias', 'gambar')->where('fillable_id', $data->id)->count())
			<div class='form-group'>
				<table class="table table-{!! $data->id !!}">
					@foreach($data->files->where('alias', 'gambar_page')->where('fillable_id', $data->id) as $gambar)
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

<script src="{{ url($template.'/js/slug.js') }}"></script>
<script src="{{ url($template.'/fileupload/js/fileinput.js') }}"></script>
<script src="{{ url($template.'/js/forminput.js') }}"></script>
<script src="{{ url($template.'/assets/vendor_components/bootstrap-iconpicker/dist/iconpicker.js') }}"></script>
<script src="{{ url($template.'/assets/vendor_components/nestable/jquery.nestable.js') }}"></script>
<script>
	$('.select2').select2();

    (async () => {
        const response = await fetch("{{ url($template.'/assets/vendor_components/bootstrap-iconpicker/dist/iconsets/fontawesome4.json') }}")
        const result = await response.json()
        const iconpicker = new Iconpicker(document.querySelector(".iconpicker"), {
            icons: result,
            showSelectedIn: document.querySelector(".selected-icon"),
            defaultValue: "{!! $data->icon !!}",
            valueFormat: val => `fa ${val.replace('fas-', 'fa-')}`,
        });
        iconpicker.set()
        iconpicker.set("{!! str_replace('fa ','',$data->icon) !!}")
    })()

</script>