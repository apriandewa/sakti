@extends('frontend.main')

    @section('container')

  
  <main class="main">

    <!-- Blog Posts Section -->
    <section id="blog-posts" class="blog-posts section">

       <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Page </h2>
        <p>Berikut adalah Page dan Informasi Kegiatan PPID Kabupaten Indragiri Hulu</p>
        <div class="col-lg-5 col-12 mx-auto">      
            <form action="/page" method="get"     class="position-relative rounded-pill m-3" role="search">
              @if (request('kategori'))
                  <input type="hidden" name="kategori" value="{{ request('kategori') }}">
              @endif
              @if (request('author'))
                  <input type="hidden" name="author" value="{{ request('author') }}">
              @endif
                <div class="input-group input-group">
                    <input name="search" type="search" class="form-control" id="search" placeholder="Cari Page Disini ..."
                        aria-label="Search" value= {{request('search')}}>
      
                    <button type="submit" class="input-group-text bg-primary text-dark border-0 px-3" id="submit">
                      <i class="bi bi-search"></i>  Cari
                    </button>
                </div>
            </form>
          </div>
      </div>
      <!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">
  
        <div class="row gy-4">
        @foreach($page as $news)
          <!-- Blog item -->
            <div class="blog-item col-md-4">
                <article>
                    <div class="post-img">
                      @php
                          $slugKategori = str_replace(' ', '-', strtolower($news->kategori));
                      @endphp
                      <span class="post-category">

                          <a href="{{ url('page') }}?kategori={{ $slugKategori }}">
                              
                            {{ $news->kategori }}
                          </a>
                      </span>
                      <span class="post-category">{{$news->kategori}}</span>
                      @if(!is_null($news->getfilebyalias('gambar_page')))
                              @php
                                $file = $news->getfilebyalias('gambar_page');
                              @endphp
                              @if($file)
                                <div class="form-group text-center">
                                  {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid') !!}
                                </div>
                              @endif
                          @endif
                      <span class="post-date">{{ \Carbon\Carbon::parse($news->created_at)->translatedFormat('d F Y') }}</span>
                    </div>

                    <h2 class="title">
                      <a href="{{ route('page.detail', $news->slug) }}">{{$news->nama}}</a>
                    </h2>

                    <div class="meta-top">
                      <ul>
                        <li class="d-flex align-items-center"><i class="bi bi-person"></i> 
                          <a href="blog-details.html">Penulis : {{$news->user->name}}</a>
                        </li>
                        <li class="d-flex align-items-center"><i class="bi bi-eye"></i> 
                          <a href="blog-details.html">Dilihat : 12 Kali</a>
                        </li>
                      </ul>
                    </div>

                    <div class="content">
                      <div class="read-more">
                        <a href="{{ route('page.detail', $news->slug) }}">Selengkapnya <i class="bi bi-arrow-right"></i></a> 
                      </div>
                    </div>
                </article>
            </div>
          @endforeach
          </div>
        
      </div>

      <div class="d-flex justify-content-center mt-4" data-aos="fade-up" data-aos-delay="200">
        {{ $page->links() }}
      </div>


    </section>
    <!-- /Blog Posts Section -->
    
  </main>

@endsection
