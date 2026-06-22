<div class="panel shadow-sm">
    <div class="panel-body">

        {{-- ===== HEADER: JUDUL & STATUS ===== --}}
        <div class="row">
            <div class="col-md-10">
                <div class="form-group">
                    {!! html()->span()->text(
                        $type == 'galeri' ? 'Judul Galeri' : 
                        ($type == 'unduhan' ? 'Judul Unduhan' : 'Judul Berita')
                    )->class("control-label") !!}
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
                    {!! html()->span()->text(
                        $type == 'galeri' ? 'Kategori Galeri' : 
                        ($type == 'unduhan' ? 'Kategori Unduhan' : 'Kategori Berita')
                    )->class("control-label") !!}
                    {!! html()->p($data->kategori)->class("form-control") !!}
                </div>

                @if($type == 'berita')
                    <div class="form-group">
                        {!! html()->span()->text("Keterangan")->class("control-label") !!}
                        {!! html()->p($data->keterangan)->class("form-control") !!}
                    </div>
                @endif

                @if(in_array($type, ['galeri', 'unduhan']))
                    <div class="form-group">
                        {!! html()->span()->text("Dilihat")->class("control-label") !!}
                        {!! html()->p($data->view)->class("form-control") !!}
                    </div>
                @endif

                <div class="form-group">
                    {!! html()->span()->text("Penulis")->class("control-label") !!}
                    {!! html()->p($data->user->name)->class("form-control") !!}
                </div>

                <div class="form-group">
                    {!! html()->span()->text("Verifikator")->class("control-label") !!}
                    {!! html()->p($data->verifikator->name ?? '-')->class("form-control") !!}
                </div>

                @if(in_array($type, ['galeri', 'unduhan']))
                    <div class="form-group">
                        {!! html()->span()->text("Desc")->class("control-label") !!}
                        {!! html()->p($data->desc)->class("form-control") !!}
                    </div>
                @endif

            </div>

            {{-- ===== GAMBAR SAMPUL / THUMBNAIL ===== --}}
            <div class="col-md-6">
                @if($type == 'galeri')
                    {!! html()->span()->text("Gambar Sampul")->class("control-label") !!}
                    @php $logoFile = $data->getfilebyalias('logo'); @endphp
                    @if($logoFile)
                        <div class="form-group text-center">
                            {!! html()->img(url($logoFile->link_stream), $logoFile->name)->class('img-fluid img-thumbnail') !!}
                        </div>
                    @else
                        <p class="text-muted"><em>Belum ada gambar sampul.</em></p>
                    @endif

                @elseif($type == 'unduhan')
                    {!! html()->span()->text("Gambar")->class("control-label") !!}
                    @php $fileGambar = $data->getfilebyalias('gambar_unduhan'); @endphp
                    @if($fileGambar)
                        <div class="form-group text-center">
                            {!! html()->img(url($fileGambar->link_stream), $fileGambar->name)->class('img-fluid img-thumbnail') !!}
                        </div>
                    @endif

                @elseif($type == 'berita')
                    {!! html()->span()->text("Gambar")->class("control-label") !!}
                    @php $fileGambar = $data->getfilebyalias('gambar_berita'); @endphp
                    @if($fileGambar)
                        <div class="form-group text-center">
                            {!! html()->img(url($fileGambar->link_stream), $fileGambar->name)->class('img-fluid img-thumbnail') !!}
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- ===== CAROUSEL — khusus Galeri ===== --}}
        @if($type == 'galeri')
            <div class="row">
                <div class="col-md-12">
                    {!! html()->span()->text("Galeri Gambar")->class("control-label mb-2 d-block") !!}
                    @php $files = $data->getfilesbyalias('galeri_gambar'); @endphp
                    @if($files && $files->count())
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
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselGaleri" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselGaleri" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                        <div class="d-flex justify-content-center gap-2 flex-wrap mb-3">
                            @foreach($files as $key => $file)
                                <img src="{{ url($file->link_stream) }}"
                                     class="thumbnail-preview rounded border"
                                     data-bs-target="#carouselGaleri"
                                     data-bs-slide-to="{{ $key }}"
                                     style="width:100px; height:70px; object-fit:cover; cursor:pointer; opacity: {{ $key == 0 ? '0.6' : '1' }};">
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted"><em>Belum ada gambar galeri.</em></p>
                    @endif
                </div>
            </div>
        @endif

        {{-- ===== KONTEN DESC — khusus Berita ===== --}}
        @if($type == 'berita')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        {!! html()->span()->text("Konten Berita")->class("control-label") !!}
                        {!! html()->p($data->desc)->class("form-control") !!}
                    </div>
                </div>
            </div>
        @endif

        {{-- ===== BERKAS LAMPIRAN — khusus Unduhan ===== --}}
        @if($type == 'unduhan')
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label>Berkas Lampiran :</label>
                        @php $berkasFiles = $data->files->where('alias', 'berkas_unduhan'); @endphp
                        @if($berkasFiles->count())
                            <ul>
                                @foreach($berkasFiles as $file)
                                    <li class="row mb-2">
                                        <div class="col-md-6">
                                            <i class="fa fa-file-pdf-o text-danger"></i> {{ $file->data['name'] ?? $file->id }}
                                        </div>
                                        <div class="col-md-5">
                                            <a href="{{ url($file->link_download) }}" class="btn btn-success btn-sm">
                                                <i class="fa fa-download"></i> Unduh
                                            </a>
                                            |
                                            <a href="{{ url($file->link_stream) }}" target="_blank" class="btn btn-info btn-sm">
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
        @endif

        {{-- ===== HISTORI VERIFIKASI ===== --}}
        <div class="row mt-4">
            <div class="col-md-12">
                <x-histori-verifikasi 
                    :verifiable_id="$data->id" 
                    :verifiable_type="get_class($data)" 
                />
            </div>
        </div>

    </div>
</div>

<style>
    .modal-lg { max-width: 1000px !important; }
</style>

<script>
    $('.submit-data').hide();
    $('.modal-title').html('<i class="fa fa-search"></i> Detail Data {!! $page->title !!}');
</script>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const carousel = document.querySelector('#carouselGaleri');
        if (!carousel) return;

        const thumbnails = document.querySelectorAll('.thumbnail-preview');

        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function () {
                thumbnails.forEach(t => t.style.opacity = "1");
                this.style.opacity = "0.6";
            });
        });

        carousel.addEventListener('slid.bs.carousel', function (e) {
            thumbnails.forEach(t => t.style.opacity = "1");
            if (thumbnails[e.to]) thumbnails[e.to].style.opacity = "0.6";
        });
    });
</script>
@endpush