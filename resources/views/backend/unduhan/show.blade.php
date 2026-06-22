<div class="panel shadow-sm">
    <div class="panel-body">
		<div class="row">
			<div class="col-md-10">
				<div class="form-group">
					{!! html()->span()->text("Judul Unduhan")->class("control-label") !!}
					{!! html()->p($data->nama)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-2">
				<div class="form-group">
					{!! html()->span()->text("Status")->class("control-label") !!}
					{!! html()->p($data->status)->class("form-control") !!}
				</div>
			</div>
		</div>
        <div class="row">
			<div class="col-md-6">
				<div class="form-group">
					{!! html()->span()->text("Kategori Unduhan")->class("control-label") !!}
					{!! html()->p($data->kategori)->class("form-control") !!}
				</div>
				<div class="form-group">
					{!! html()->span()->text("Dilihat")->class("control-label") !!}
					{!! html()->p($data->view)->class("form-control") !!}
				</div>
				<div class="form-group">
					{!! html()->span()->text("Penulis")->class("control-label") !!}
					{!! html()->p($data->user->name)->class("form-control") !!}
				</div>
				<div class="form-group">
					{!! html()->span()->text("Verifikator")->class("control-label") !!}
					{!! html()->p($data->verifikator->name ?? '-')->class("form-control") !!}
				</div>
				<div class="form-group">
					{!! html()->span()->text("Desc")->class("control-label") !!}
					{!! html()->p($data->desc)->class("form-control") !!}
				</div>
			</div>
			<div class="col-md-6">
				{!! html()->span()->text("Gambar")->class("control-label") !!}
				@if(!is_null($data->getfilebyalias('gambar_unduhan')))
					@php
						$file = $data->getfilebyalias('gambar_unduhan');
					@endphp
					@if($file)
						<div class="form-group text-center">
							{!! html()->img(url($file->link_stream), $file->name)->class('img-fluid img-thumbnail') !!}
						</div>
					@endif
				@endif
			</div>

			<div class="col-md-12">
                <div class="form-group">
                    <label>Berkas Lampiran :</label>
					@php $berkasFiles = $data->files->where('alias', 'berkas_unduhan'); @endphp
					@if($berkasFiles->count())
						<ul>
							@foreach($berkasFiles as $file)
								<li class="row">
									<div class="col-md-6">
										<i class="fa fa-file-pdf-o text-danger"></i> {{ $file->data['name'] ?? $file->id }}
									</div>
									<div class="col-md-5 mb-2"> 
										<a href="{{ url($file->link_download) }}" class="btn btn-success btn-sm" title="Klik untuk Mengunduh">
											<i class="fa fa-download"></i> Unduh
										</a>
										|
										<a href="{{ url($file->link_stream) }}" target="_blank" class="btn btn-info btn-sm" title="Klik untuk Melihat">
											<i class="fa fa-eye"></i> Lihat
										</a>
									</div>
								</li>
							@endforeach
						</ul>
					@else
						<span class="badge badge-danger">Tidak ada file</span>
					@endif
                </div>
            </div>
		</div>
		<div class="col-md-12 mt-4">
			<x-histori-verifikasi 
				:verifiable_id="$data->id" 
				:verifiable_type="get_class($data)" 
			/>
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
