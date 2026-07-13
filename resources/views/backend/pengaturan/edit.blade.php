{!! html()->modelForm($data,'PUT', route($page->url.'.update', $data->id))->id('form-create-'.$page->code)->acceptsFiles()->class('form form form-horizontal')->open() !!}
<div class="panel shadow-sm">
    <div class="panel-body">
		<div class="row">
			<div class="col-5">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('judul')->text('Judul') !!}
					{!! html()->text('judul',$data->judul)->placeholder('Type Judul here')->class('form-control')->id('judul') !!}
				</div>
			</div>
			<div class="col-7">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('subjudul')->text('Subjudul') !!}
					{!! html()->text('subjudul',$data->subjudul)->placeholder('Type Subjudul here')->class('form-control')->id('subjudul') !!}
				</div>
			</div>
		</div>
        
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('deskripsi')->text('Deskripsi') !!}
			{!! html()->textarea('deskripsi',$data->deskripsi)->class('form-control')->id('deskripsi') !!}
		</div>
		<div class="row">
			<div class="col-6">
				{!! html()->span()->text("Logo")->class("control-label") !!}
				@if(!is_null($data->getfilebyalias('logo_app')))
					@php
						$file = $data->getfilebyalias('logo_app');
					@endphp
					@if($file)
						<div class="form-group text-center">
							{!! html()->img(url($file->link_stream), $file->name)->class('img-fluid img-thumbnail') !!}
						</div>
					@endif
				@endif
				<div class='form-group'>
					{!! html()->label('Logo (JPG,PNG Max 1MB)', 'logo')->class('control-label') !!}
					{!! html()->file('logo')
						->class('form-control')
						->id('logo')
						->accept('image/jpg,image/jpeg,image/png') !!}
				</div>
			</div>
			<div class="col-6">
				{!! html()->span()->text("Ikon")->class("control-label") !!}
				@if(!is_null($data->getfilebyalias('ikon_app')))
					@php
						$file = $data->getfilebyalias('ikon_app');
					@endphp
					@if($file)
						<div class="form-group text-center">
							{!! html()->img(url($file->link_stream), $file->name)->class('img-fluid img-thumbnail') !!}
						</div>
					@endif
				@endif
				<div class='form-group'>
					{!! html()->label('Ikon (JPG,PNG Max 1MB)', 'ikon')->class('control-label') !!}
					{!! html()->file('ikon')
						->class('form-control')
						->id('ikon')
						->accept('image/jpg,image/jpeg,image/png') !!}
					
				</div>
			</div>
		</div>

		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('alamat')->text('Alamat') !!}
			{!! html()->textarea('alamat',$data->alamat)->class('form-control')->id('alamat') !!}
		</div>
		<div class="row">
			<div class="col-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('telepon')->text('Telepon') !!}
					{!! html()->text('telepon',$data->telepon)->placeholder('Type Telepon here')->class('form-control')->id('telepon') !!}
				</div>
			</div>
			<div class="col-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('email')->text('Email') !!}
					{!! html()->text('email',$data->email)->placeholder('Type Email here')->class('form-control')->id('email') !!}
				</div>
			</div>
		</div>
		<div class='form-group'>
			{!! html()->label()->class('control-label')->for('peta')->text('Peta') !!}
			{!! html()->textarea('peta',$data->peta)->class('form-control')->id('peta') !!}
			<small class="text-muted d-block mt-1">
				<i class="fa fa-info-circle"></i> URL embed Google Maps ini juga digunakan untuk verifikasi lokasi buku tamu (radius {{ config('kunjungan.radius_meters', 200) }} meter).
			</small>
		</div>
		<div class="row">
			<div class="col-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('facebook')->text('Facebook') !!}
					{!! html()->text('facebook',$data->facebook)->placeholder('Type Facebook here')->class('form-control')->id('facebook') !!}
				</div>
			</div>
			<div class="col-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('instagram')->text('Instagram') !!}
					{!! html()->text('instagram',$data->instagram)->placeholder('Type Instagram here')->class('form-control')->id('instagram') !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('twiter')->text('Twiter') !!}
					{!! html()->text('twiter',$data->twiter)->placeholder('Type Twiter here')->class('form-control')->id('twiter') !!}
				</div>
			</div>
			<div class="col-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('tiktok')->text('Tiktok') !!}
					{!! html()->text('tiktok',$data->tiktok)->placeholder('Type Tiktok here')->class('form-control')->id('tiktok') !!}
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('youtube')->text('Youtube') !!}
					{!! html()->text('youtube',$data->youtube)->placeholder('Type Youtube here')->class('form-control')->id('youtube') !!}
				</div>
			</div>
			<div class="col-6">
				<div class='form-group'>
					{!! html()->label()->class('control-label')->for('call_center')->text('Call Center') !!}
					{!! html()->text('call_center',$data->call_center)->placeholder('Type Call Center here')->class('form-control')->id('call_center') !!}
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