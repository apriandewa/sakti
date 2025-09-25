<div class="panel shadow-sm">
    <div class="panel-body">
        <div class="row">
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Tahun")->class("control-label") !!}
					{!! html()->p($data->tahun)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Pemohon")->class("control-label") !!}
					{!! html()->p($data->pemohon)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Diminta")->class("control-label") !!}
					{!! html()->p($data->diminta)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Diberikan")->class("control-label") !!}
					{!! html()->p($data->diberikan)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Ditolak")->class("control-label") !!}
					{!! html()->p($data->ditolak)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Keterangan")->class("control-label") !!}
					{!! html()->p($data->keterangan)->class("form-control") !!}
				</div>
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
