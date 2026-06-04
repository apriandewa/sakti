{!! html()->modelForm($data,'PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}
<div class="panel shadow-sm">
    <div class="panel-body">
		<div class="row">
			<div class="col-md-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('nama')->text('Nama') !!}
					{!! html()->text('nama',$data->nama)->placeholder('Type Nama here')->class('form-control')->id('nama') !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('slug')->text('Slug') !!}
					{!! html()->text('slug',$data->slug)->placeholder('Type Slug here')->class('form-control')->id('slug')->attribute('readonly', true) !!}
				</div>
			</div>
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('desc')->text('Desc') !!}
			{!! html()->textarea('desc',$data->desc)->class('form-control')->id('desc') !!}
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->label('Ikon','ikon')->class('control-label') !!}
					<span class="text-danger">*</span>
					<div class="input-group mb-3">
						<span class="input-group-prepend">
							<i class="input-group-text selected-icon"></i>
						</span>
						{!! html()->text('ikon',$data->ikon)->placeholder('Ikon',$data->ikon)->class('form-control iconpicker')->id('icon')->attributes(['autocomplete' => 'off']) !!}
					</div>
				</div>
			</div>
			<div class="col-md-6">
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
			</div>
		</div>
		{{-- UPLOAD GAMBAR (SHOW OLD IF EXISTS) --}}
		<div class="row">
			<div class="col-md-6">
				<div class="form-group">

					{!! html()->label('Gambar / Logo (Optional)','gambar')->class('control-label') !!}

					{{-- TAMPILKAN GAMBAR LAMA DARI FILE MANAGER --}}
					@php
						$file = $data->getfilebyalias('gambar_kategori');
					@endphp

					@if($file)
						<div class="mb-2 text-center">
							<img src="{{ url($file->link_stream) }}"
								alt="{{ $file->name }}"
								class="img-fluid img-thumbnail"
								style="max-height: 150px;">
						</div>
					@endif
				</div>
			</div>

			<div class="col-md-6">
			{{-- INPUT UPLOAD GAMBAR --}}
				{!! html()->file('gambar')
					->class('form-control')
					->id('gambar')
					->accept('image/jpeg,image/png') !!}
				<small class="text-danger">Jenis File : jpg, jpeg, png (Maksimal 2MB)</small>
				{{-- PREVIEW GAMBAR BARU --}}
				<div class="mt-2 text-center">
					<img id="preview"
						src="#"
						class="img-thumbnail d-none"
						style="max-height: 150px;">
				</div>
			</div>
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
<script src="{{ url($template.'/fileupload/js/fileinput.js') }}"></script>
<script src="{{ url($template.'/js/forminput.js') }}"></script>
<script src="{{ url($template.'/js/slug.js') }}"></script>
<script src="{{ url($template.'/assets/vendor_components/bootstrap-iconpicker/dist/iconpicker.js') }}"></script>
<script>
	$('.select2').select2();

    (async () => {
        const response = await fetch("{{ url($template.'/assets/vendor_components/bootstrap-iconpicker/dist/iconsets/fontawesome4.json') }}")
        const result = await response.json()
        const iconpicker = new Iconpicker(document.querySelector(".iconpicker"), {
            icons: result,
            showSelectedIn: document.querySelector(".selected-icon"),
            defaultValue: "{!! $data->ikon !!}",
            valueFormat: val => `fa ${val.replace('fas-', 'fa-')}`,
        });
        iconpicker.set()
        iconpicker.set("{!! str_replace('fa ','',$data->ikon) !!}")
    })()

</script>

<script>
	// PREVIEW GAMBAR BARU
	document.getElementById('gambar').addEventListener('change', function(e) {
		let reader = new FileReader();
		reader.onload = function(e) {
			let img = document.getElementById('preview');
			img.src = e.target.result;
			img.classList.remove('d-none');
		}
		reader.readAsDataURL(this.files[0]);
	});

</script>