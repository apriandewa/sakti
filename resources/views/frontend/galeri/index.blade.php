@extends('frontend.main')

    @section('container')

  
  <main class="main">

     <!-- Portfolio Section -->
    <section id="portfolio" class="portfolio section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Galeri </h2>
        <p>Berikut adalah Galeri Kegiatan PPID Kabupaten Indragiri Hulu</p>
        <div class="col-lg-5 col-12 mx-auto">      
            <form action="/galeri" method="get"     class="position-relative rounded-pill m-3" role="search">
              @if (request('kategori'))
                  <input type="hidden" name="kategori" value="{{ request('kategori') }}">
              @endif
              @if (request('author'))
                  <input type="hidden" name="author" value="{{ request('author') }}">
              @endif
                <div class="input-group input-group">
                    <input name="search" type="search" class="form-control" id="search" placeholder="Cari Galeri Disini ..."
                        aria-label="Search" value= {{request('search')}}>
      
                    <button type="submit" class="input-group-text bg-primary text-dark border-0 px-3" id="submit">
                      <i class="bi bi-search"></i>  Cari
                    </button>
                </div>
            </form>
          </div>
      </div>
      <!-- End Section Title -->

      <div class="container">
        <div class="row gy-4">
          {{-- ================== LIST GALERI ================== --}}
            @foreach($galeri as $foto)
              @php
                  $imgSrc = $foto->getfilebyalias('logo') 
                      ? url($foto->getfilebyalias('logo')->public_stream) 
                      : '';
              @endphp

              <div class="col-lg-4 col-md-6 portfolio-item isotope-item">
                  @if($imgSrc)
                      <img src="{{ $imgSrc }}" class="img-fluid" alt="{{ $foto->nama }}">
                  @else
                      <div class="img-fluid d-flex align-items-center justify-content-center" 
                          style="height:200px;background-color:#f0f0f0;color:#888;">
                          Tidak ada gambar
                      </div>
                  @endif

                  <div class="portfolio-info">
                    <h4>

                      <a href="{{ route('galeri.detail', $foto->slug) }}">{{ $foto->nama }}</a>
                    </h4>    
                    <p>{!! $foto->desc !!}</p>
                    <a href="{{ route('galeri.detail', $foto->slug) }}" 
                      title="More Details" 
                      class="details-link">
                      <i class="bi bi-link-45deg"></i>
                    </a>
                  </div>
              </div><!-- End Portfolio Item -->
            @endforeach
          {{-- ================== END LIST GALERI ================== --}}
        </div>
      </div><!-- End Portfolio Container -->

      <div class="d-flex justify-content-center mt-4" data-aos="fade-up" data-aos-delay="200">
        {{ $galeri->links() }}
      </div>

    </section>
    <!-- /Portfolio Section -->

  </main>

@endsection
