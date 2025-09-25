<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Nama")->class("control-label") !!}
					{!! html()->p($data->nama)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Desc")->class("control-label") !!}
					{!! html()->p($data->desc)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Link")->class("control-label") !!}
					{!! html()->p($data->link)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Status")->class("control-label") !!}
					{!! html()->p($data->status)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				{!! html()->span()->text("Gambar")->class("control-label") !!}
				@if(!is_null($data->getfilebyalias('gambar_tautan')))
					@php
						$file = $data->getfilebyalias('gambar_tautan');
					@endphp
					@if($file)
						<div class="form-group text-center">
							{!! html()->img(url($file->link_stream), $file->name)->class('img-fluid img-thumbnail') !!}
						</div>
					@endif
				@endif
			</div>
		</div>
    </div>
</div>
<style>
    .modal-lg {
        max-width: 1000px !important;
    }
</style>
<script>
    $('.submit-data').hide();
    $('.modal-title').html('<i class="fa fa-search"></i> Detail Data {!! $page->title !!}');
</script>
