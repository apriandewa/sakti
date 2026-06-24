@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Galeri</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Galeri Kegiatan' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Dokumentasi visual program kerja, pelayanan publik, dan pembangunan di Kabupaten Indragiri Hulu.' }}</p>
    </div>
  </div>

  <!-- Portfolio Section -->
  <section id="portfolio" class="section-dark">
    <div class="container">

      <!-- Search and Filter Panel -->
      <div class="glass-card mb-5" data-aos="fade-up">
        <form action="{{ url('galeri') }}" method="get" class="search-form form-cyber">
          @if (request('kategori'))
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
          @endif
          <div class="row g-3">
            <div class="col-lg-8 col-md-7">
              <input 
                name="search" 
                type="search" 
                class="form-control ps-4" 
                placeholder="Cari dokumentasi galeri..." 
                value="{{ request('search') }}"
                aria-label="Cari Galeri"
              >
            </div>
            <div class="col-lg-4 col-md-5 d-flex gap-2">
              <button type="submit" class="btn-cyber w-100 justify-content-center">
                <i class="bi bi-search"></i> Cari Album
              </button>
              @if(request('search') || request('kategori'))
                <a href="{{ url('galeri') }}" class="btn-cyber-outline justify-content-center" title="Reset">
                  <i class="bi bi-x-lg"></i>
                </a>
              @endif
            </div>
          </div>
        </form>

        <!-- Active Filter Badge -->
        @if(request('kategori') || request('search'))
          <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
            <span class="text-muted small">Filter Aktif:</span>
            @if(request('search'))
              <span class="badge bg-secondary px-3 py-2">Kata Kunci: "{{ request('search') }}"</span>
            @endif
            @if(request('kategori'))
              <span class="badge bg-success px-3 py-2">Kategori: {{ request('kategori') }}</span>
            @endif
          </div>
        @endif
      </div>

      <!-- Portfolio Grid -->
      <div class="row gy-4" data-aos="fade-up" data-aos-delay="200">
        @forelse($galeri as $fotoIndex => $foto)
          @php
            $imgSrc = $foto->getfilebyalias('logo') 
              ? url($foto->getfilebyalias('logo')->public_stream) 
              : '';
            $categories = explode(',', $foto->kategori);
            $firstCat = $categories[0] ?? 'default';
            $galleryGroup = 'gallery-' . strtolower(str_replace(' ', '-', trim($firstCat)));
          @endphp

          <div class="col-lg-4 col-md-6">
            <div class="portfolio-card">
              @if($imgSrc)
                <img src="{{ $imgSrc }}" alt="{{ $foto->nama }}">
              @else
                <div class="d-flex align-items-center justify-content-center bg-secondary" style="height: 100%; min-height: 250px;">
                  <span class="text-muted">Tidak Ada Gambar</span>
                </div>
              @endif
              <div class="portfolio-overlay">
                <h4>{{ $foto->nama }}</h4>
                <p>{!! strip_tags(Str::limit($foto->desc, 100)) !!}</p>
                <div class="portfolio-links">
                  @if($imgSrc)
                    <a href="{{ $imgSrc }}" data-gallery="{{ $galleryGroup }}" class="glightbox" title="{{ $foto->nama }}"><i class="bi bi-zoom-in"></i></a>
                  @endif
                  <a href="{{ route('galeri.detail', $foto->slug) }}"><i class="bi bi-link-45deg"></i></a>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <div class="glass-card py-5">
              <i class="bi bi-images fs-1 text-muted mb-3 d-block"></i>
              <h4 class="text-white">Dokumentasi Tidak Ditemukan</h4>
              <p class="text-secondary">Maaf, kami tidak menemukan album galeri yang cocok dengan filter pencarian Anda.</p>
              <a href="{{ url('galeri') }}" class="btn-cyber mt-3">Tampilkan Semua Galeri</a>
            </div>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
        {{ $galeri->appends(request()->input())->links() }}
      </div>

    </div>
  </section>

</main>
@endsection
