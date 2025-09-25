<div class="panel shadow-sm">
    <div class="panel-body">
		<div class="row">
			<div class="col-md-10">
				<div class="form-group">
					{!! html()->span()->text("Judul Galerin")->class("control-label") !!}
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
					{!! html()->span()->text("Kategori Galeri")->class("control-label") !!}
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
				{!! html()->span()->text("Gambar Sampul")->class("control-label") !!}
				@if(!is_null($data->getfilebyalias('cover_galeri')))
					@php
						$file = $data->getfilebyalias('cover_galeri');
					@endphp
					@if($file)
						<div class="form-group text-center">
							{!! html()->img(url($file->link_stream), $file->name)->class('img-fluid img-thumbnail') !!}
						</div>
					@endif
				@endif
			</div>

			<div class="col-md-12">
    {!! html()->span()->text("Gambar")->class("control-label mb-2 d-block") !!}

    @php
        $files = $data->getfilesbyalias('gambar_galeri');
    @endphp

    @if($files && $files->count())
        {{-- Carousel Utama --}}
        <div id="carouselGaleri" class="carousel slide mb-3" data-bs-ride="carousel">
            <div class="carousel-inner">
                @foreach($files as $key => $file)
                    <div class="carousel-item {{ $key == 0 ? 'active' : '' }}">
                        <img src="{{ url($file->link_stream) }}"
                             class="d-block img-fluid img-thumbnail rounded shadow"
                             alt="{{ $file->name }}"
                             style="max-height:500px; object-fit:contain;">
                    </div>
                @endforeach
            </div>

            {{-- Tombol Prev/Next --}}
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselGaleri" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselGaleri" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>

        {{-- Thumbnail Preview --}}
        <div class="d-flex justify-content-center gap-2 flex-wrap">
            @foreach($files as $key => $file)
                <img src="{{ url($file->link_stream) }}"
                     class="thumbnail-preview rounded border {{ $key == 0 ? 'active' : '' }}"
                     data-bs-target="#carouselGaleri" 
                     data-bs-slide-to="{{ $key }}"
                     style="width:100px; height:70px; object-fit:cover; cursor:pointer; opacity: {{ $key == 0 ? '0.6' : '1' }};">
            @endforeach
        </div>
    @endif
</div>


		</div>
		<div class="col-md-12">
			<div class="form-group">
				{!! html()->span()->text("Histori Verifikasi")->class("control-label") !!}
				<div class="table-responsive">
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Tanggal</th>
								<th>Status</th>
								<th>Catatan</th>
								<th>Verifikator</th>
							</tr>
						</thead>
						<tbody>
							@forelse($histori_verifikasi as $histori)
								<tr>
									<td>{{ \Carbon\Carbon::parse($histori->updated_at)->format('d-m-Y H:i') }}</td>
									<td>{{ $histori->status }}</td>
									<td>{{ $histori->catatan }}</td>
									<td>{{ $histori->user->name ?? '-' }}</td>
								</tr>
							@empty
								<tr>
									<td colspan="4" class="text-center">Belum ada histori verifikasi.</td>
								</tr>
							@endforelse
						</tbody>
					</table>
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

{{-- Script untuk handle thumbnail active --}}
@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const thumbnails = document.querySelectorAll('.thumbnail-preview');
        const carousel = document.querySelector('#carouselGaleri');

        // Saat thumbnail diklik
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function () {
                thumbnails.forEach(t => t.style.opacity = "1");
                this.style.opacity = "0.6"; // efek abu/transparan
            });
        });

        // Update thumbnail saat carousel digeser otomatis
        carousel.addEventListener('slid.bs.carousel', function (e) {
            thumbnails.forEach(t => t.style.opacity = "1");
            thumbnails[e.to].style.opacity = "0.6";
        });
    });
</script>
@endpush

