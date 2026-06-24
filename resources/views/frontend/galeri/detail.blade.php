@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item"><a href="{{ url('galeri') }}">Galeri</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Detail Album</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Detail Album Galeri' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Dokumentasi visual kegiatan dan pembangunan di Kabupaten Indragiri Hulu.' }}</p>
    </div>
  </div>

  <!-- Detail Content Section -->
  <section class="section-dark">
    <div class="container">
      <div class="row gy-4">
        
        <!-- Main Column -->
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
          <div class="detail-content">
            
            <!-- Album Cover & Meta Grid -->
            <div class="row g-4 mb-5">
              <div class="col-md-5">
                @if($news && $news->getfilebyalias('logo'))
                  @php
                    $file = $news->getfilebyalias('logo');
                  @endphp
                  <img src="{{ url($file->public_stream) }}" alt="{{ $news->nama }}" class="img-fluid rounded border border-secondary shadow-lg">
                @else
                  <div class="d-flex align-items-center justify-content-center bg-secondary rounded" style="aspect-ratio: 1; min-height: 200px;">
                    <span class="text-muted">No Cover</span>
                  </div>
                @endif
              </div>
              
              <div class="col-md-7 d-flex flex-column justify-content-between">
                <div>
                  <h3 class="text-white fw-bold mb-3" style="font-family: var(--font-title);">{{ $news->nama }}</h3>
                  <div class="text-secondary mb-4">
                    {!! html_entity_decode($news->desc) !!}
                  </div>
                </div>

                <div class="detail-meta border-0 p-0 m-0">
                  <div class="d-flex flex-column gap-2 text-secondary font-subtitle">
                    <span><i class="bi bi-calendar3 text-success me-2"></i> Rilis : {{ $news->created_at->format('d M Y') }}</span>
                    <span><i class="bi bi-folder2-open text-success me-2"></i> Kategori : {{ $news->kategori }}</span>
                    <span><i class="bi bi-person-circle text-success me-2"></i> Penulis : {{ $news->user->name ?? 'Admin' }}</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Slideshow Slider Section -->
            @php
              $files = $news->getfilesbyalias('galeri_gambar');
            @endphp

            @if($files && $files->count() > 0)
              <div class="border-top border-secondary pt-4 mt-4">
                <h4 class="text-white mb-4" style="font-family: var(--font-title);"><i class="bi bi-images text-cyan me-2"></i> Slide Gambar Dokumentasi</h4>
                
                <div class="swiper albumDetailSwiper position-relative overflow-hidden rounded border border-secondary shadow-lg">
                  <div class="swiper-wrapper align-items-center">
                    @foreach($files as $file)
                      <div class="swiper-slide text-center bg-black">
                        <a href="{{ url($file->public_stream) }}" class="glightbox" data-gallery="album-slideshow" title="{{ $file->name }}">
                          <img src="{{ url($file->public_stream) }}" alt="{{ $file->name }}" class="img-fluid" style="max-height: 500px; object-fit: contain; width: 100%;">
                        </a>
                      </div>
                    @endforeach
                  </div>
                  
                  <!-- Navigation & Pagination -->
                  <div class="swiper-button-prev text-white"></div>
                  <div class="swiper-button-next text-white"></div>
                  <div class="swiper-pagination"></div>
                </div>
              </div>
            @endif

            <!-- Share Area -->
            <div class="border-top border-secondary pt-4 mt-5">
              <h5 class="text-white mb-3" style="font-family: var(--font-subtitle);">Bagikan Album Ini :</h5>
              <div class="d-flex gap-2">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn-cyber-outline py-2 px-3">
                  <i class="bi bi-facebook me-1"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->nama) }}" target="_blank" class="btn-cyber-outline py-2 px-3">
                  <i class="bi bi-twitter-x me-1"></i> Twitter
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($news->nama . ' - ' . url()->current()) }}" target="_blank" class="btn-cyber-outline py-2 px-3">
                  <i class="bi bi-whatsapp me-1"></i> WhatsApp
                </a>
              </div>
            </div>

          </div>
        </div>

        <!-- Sidebar Column -->
        @include('frontend.partials.sidebar')

      </div>
    </div>
  </section>

</main>

@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    if (document.querySelector('.albumDetailSwiper')) {
      new Swiper('.albumDetailSwiper', {
        loop: true,
        speed: 800,
        autoplay: {
          delay: 5000,
          disableOnInteraction: false
        },
        navigation: {
          nextEl: '.swiper-button-next',
          prevEl: '.swiper-button-prev'
        },
        pagination: {
          el: '.swiper-pagination',
          clickable: true
        }
      });
    }
  });
</script>
@endpush
@endsection
