@extends('frontend.main')

    @section('container')

  
  <main class="main">

    <!-- Blog Detail Section -->
   <section id="service-details" class="service-details section">

       <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Page </h2>
        <p>Berikut adalah Page dan Informasi Kegiatan PPID Kabupaten Indragiri Hulu</p>
        
      </div>
      <!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">
  
        <div class="row gy-4">
          <div class="col-lg-8 ps-lg-5" data-aos="fade-up" data-aos-delay="200">
            @if(!is_null($news->getfilebyalias('gambar_page')))
                @php
                  $file = $news->getfilebyalias('gambar_page');
                @endphp
                @if($file)
                  <div class="form-group text-center">
                    {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid service-img') !!}
                  </div>
                @endif
            @endif
            <h3>{{$news->nama}}</h3>
            <p>
              {!! html()->p($news->desc) !!}
            </p>
            
          </div>

          @include('frontend.partials.sidebar')
    
      </div>



    </section>
    <!-- /Blog Posts Section -->
    
  </main>

@endsection
