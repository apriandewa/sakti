@extends('frontend.main')

    @section('container')

  
  <main class="main">

    <!-- Blog Detail Section -->
   <section id="service-details" class="service-details section">

       <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Galeri </h2>
        <p>Berikut adalah Galeri Kegiatan PPID Kabupaten Indragiri Hulu</p>
        
      </div>
      <!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">
  
        <div class="row gy-4">
          <div class="col-lg-8 ps-lg-5" data-aos="fade-up" data-aos-delay="200">
            <div class="row">
              <div class="col-md-6">
                @if($news && $news->getfilebyalias('logo'))
                    @php
                        $file = $news->getfilebyalias('logo');
                    @endphp
                    <div class="form-group text-center">
                        {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid service-img') !!}
                    </div>
                @endif

              </div>
              <div class="col-md-6">
                <h3>{{$news->nama}}</h3>
                <p>
                  {!! html()->p($news->desc) !!}
                </p>
                <div class="d-flex align-items-center mb-3">
                  <i class="bi bi-calendar me-2"></i>
                  <span>Tanggal Terbit : {{ $news->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <i class="bi bi-folder2-open me-2"></i>
                  <span>Kategori : {{ $news->kategori }}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <i class="bi bi-person-circle me-2"></i>
                  <span>Penulis : {{ $news->user->name }}</span>
                </div>
              </div>

            </div>

            <!-- Portfolio Details Section -->
            <section id="portfolio-details" class="portfolio-details section">

              <div class="container" data-aos="fade-up">

                <div class="portfolio-details-slider swiper init-swiper">
                  <script type="application/json" class="swiper-config">
                    {
                      "loop": true,
                      "speed": 600,
                      "autoplay": {
                        "delay": 5000
                      },
                      "slidesPerView": "auto",
                      "navigation": {
                        "nextEl": ".swiper-button-next",
                        "prevEl": ".swiper-button-prev"
                      },
                      "pagination": {
                        "el": ".swiper-pagination",
                        "type": "bullets",
                        "clickable": true
                      }
                    }
                  </script>

               
                  @php
                      $files = $news->getfilesbyalias('galeri_gambar');
                  @endphp

                  @if($files && $files->count())
                  <div class="swiper-wrapper align-items-center">
                      @foreach($files as $key => $file)
                        <div class="swiper-slide">
                          <img src="{{ url($file->public_stream) }}" alt="">
                        </div>
                      @endforeach

                  </div>
                  @endif
                
                  <div class="swiper-button-prev"></div>
                  <div class="swiper-button-next"></div>
                  <div class="swiper-pagination"></div>
                </div>

              </div>

            </section><!-- /Portfolio Details Section -->

          </div>
        
          @include('frontend.partials.sidebar')

          
      </div>



    </section>
    <!-- /Blog Posts Section -->
    
  </main>

@endsection
