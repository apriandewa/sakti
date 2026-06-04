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
            {!! html()->select('kategori', $kategoris ?? [])->value($data->kategori ?? '')->placeholder('Pilih kategori di sini')->class('form-control select2')->id('kategori') !!}
        </div>

		{{-- ========== UPLOAD LOGO (SINGLE) ========== --}}
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('logo')->text('Upload Logo / Gambar Sampul Baru') !!}
			<span class="text-muted">(Kosongkan jika tidak ingin mengganti)</span>
			<small class="text-muted d-block mb-1">Format: jpg, jpeg, png. Maks 2MB.</small>
			<div class="logo-upload-area" id="logoUploadArea">
				<label for="logo" class="logo-drop-label" id="logoDropLabel">
					<i class="fa fa-cloud-upload fa-2x text-muted"></i>
					<span class="d-block mt-1 text-muted">Klik untuk pilih logo / gambar sampul baru</span>
				</label>
				<input type="file" name="logo" id="logo" accept="image/jpeg,image/png,image/jpg" class="d-none">
			</div>
			{{-- Preview Logo Baru --}}
			<div id="logoPreviewWrap" class="mt-2 d-none">
				<div class="logo-thumb-card">
					<img id="logoPreviewImg" src="#" alt="Preview Logo" class="logo-thumb-img">
					<div class="logo-thumb-actions">
						<button type="button" class="btn btn-xs btn-info" id="btnZoomLogo" title="Perbesar">
							<i class="fa fa-search-plus"></i>
						</button>
						<button type="button" class="btn btn-xs btn-danger" id="btnRemoveLogo" title="Hapus">
							<i class="fa fa-times"></i>
						</button>
					</div>
					<p class="logo-thumb-name" id="logoPreviewName"></p>
				</div>
			</div>
		</div>

		{{-- Tampilkan logo saat ini --}}
		@php $logoFile = $data->files->where('alias','logo')->first(); @endphp
		@if($logoFile)
			<div class="form-group mb-4">
				<label class="control-label">Logo Saat Ini :</label>
				<div class="logo-thumb-card" style="display:block;">
					<img src="{{ url($logoFile->link_stream ?? '#') }}" alt="logo" class="logo-thumb-img">
					<div class="logo-thumb-actions">
						<a href="{{ url($logoFile->link_stream ?? '#') }}" target="_blank" class="btn btn-xs btn-info" title="Lihat">
							<i class="fa fa-search-plus"></i>
						</a>
					</div>
					<p class="logo-thumb-name">{{ $logoFile->file_name ?? 'logo' }}</p>
				</div>
			</div>
		@endif

		{{-- ========== UPLOAD GALERI (MULTIPLE / DRAG DROP) ========== --}}
		<div class='form-group'>
			{!! html()->label('Upload Galeri Baru (bisa lebih dari 1)','galeri')->class('control-label') !!}
			<small class="text-muted d-block mb-1">Format: jpg, jpeg, png. Maks 2MB per file. Bisa pilih banyak sekaligus.</small>

			<div class="galeri-drop-zone" id="galeriDropZone">
				<i class="fa fa-images fa-2x text-muted"></i>
				<p class="mb-1 mt-2 text-muted">Drag &amp; Drop gambar ke sini, atau</p>
				<label for="galeriTrigger" class="btn btn-sm btn-outline-primary mb-0" style="cursor:pointer;">
					<i class="fa fa-folder-open"></i> Pilih File
				</label>
				<input type="file" id="galeriTrigger" accept="image/jpeg,image/png,image/jpg" multiple class="d-none">
				<input type="file" name="galeri[]" id="galeri" accept="image/jpeg,image/png,image/jpg" multiple class="d-none">
			</div>

			{{-- Grid Thumbnail Galeri --}}
			<div id="galeriPreviewGrid" class="galeri-grid mt-3"></div>
		</div>

		{{-- Daftar Galeri Saat Ini --}}
		@php $galeriFiles = $data->files->where('alias','galeri_gambar'); @endphp
		@if($galeriFiles->count())
			<div class="form-group">
				<label class="control-label">Galeri Saat Ini :</label>
				<div class="galeri-grid">
					@foreach($galeriFiles as $file)
						<div class="galeri-thumb-card">
							<img src="{{ url($file->link_stream ?? '#') }}" alt="galeri">
							<div class="galeri-thumb-actions">
								<a href="{{ url($file->link_stream ?? '#') }}" target="_blank" class="btn btn-xs btn-info" title="Lihat">
									<i class="fa fa-search-plus"></i>
								</a>
							</div>
							<div class="mt-1 text-center">
								<label style="font-size:11px;color:#e55;cursor:pointer;margin-bottom:0;">
									<input type="checkbox" name="delete_galeri[]" value="{{ $file->id }}"> Hapus
								</label>
							</div>
							<p class="galeri-thumb-name" title="{{ $file->file_name ?? 'image' }}">{{ $file->file_name ?? 'image' }}</p>
						</div>
					@endforeach
				</div>
			</div>
		@endif
    </div>
</div>
{!! html()->hidden('table-id','datatable')->id('table-id') !!}
{!! html()->closeModelForm() !!}

{{-- MODAL ZOOM PREVIEW --}}
<div class="modal fade" id="modalZoomPreview" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
		<div class="modal-content bg-dark">
			<div class="modal-header border-0 pb-0">
				<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
			</div>
			<div class="modal-body text-center pt-0">
				<img id="zoomPreviewImg" src="#" alt="Preview" class="img-fluid rounded" style="max-height:70vh;">
				<p id="zoomPreviewName" class="text-white mt-2 mb-0 small"></p>
			</div>
		</div>
	</div>
</div>

<style>
    .select2-container { z-index: 999999 !important; width: 100% !important; }
    .modal-lg { max-width: 1000px !important; }
</style>


<script>
    $('.select2').each(function () {
        let dropdownParent = $(this).closest('form');
        $(this).select2({ placeholder: "Silahkan Pilih", dropdownParent: dropdownParent });
    });
    $('.modal-title').html('<i class="fa fa-edit"></i> Edit Data {{ $page->title }}');
    $('.submit-data').html('<i class="fa fa-save"></i> Simpan Data');

    // Inisialisasi upload menggunakan fungsi global dari template.js
    initLogoUpload('logo', 'logoPreviewWrap', 'logoPreviewImg', 'logoPreviewName', 'btnRemoveLogo', 'btnZoomLogo', 'modalZoomPreview');
    initGaleriUpload('galeriTrigger', 'galeri', 'galeriDropZone', 'galeriPreviewGrid', 'modalZoomPreview');
</script>

<script src="{{ url($template.'/js/slug.js') }}"></script>
<script src="{{ url($template.'/js/forminput.js') }}"></script>