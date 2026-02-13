@extends('frontend.main')

    @section('container')

  
  <main class="main">

    <!-- Blog Posts Section -->
    <section id="blog-posts" class="blog-posts section">

       <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Berita </h2>
        <p>Berikut adalah Berita dan Informasi Kegiatan PPID Kabupaten Indragiri Hulu</p>
        <div class="col-lg-5 col-12 mx-auto">      
            <form action="/berita" method="get"     class="position-relative rounded-pill m-3" role="search">
              @if (request('kategori'))
                  <input type="hidden" name="kategori" value="{{ request('kategori') }}">
              @endif
              @if (request('author'))
                  <input type="hidden" name="author" value="{{ request('author') }}">
              @endif
                <div class="input-group input-group">
                    <input name="search" type="search" class="form-control" id="search" placeholder="Cari Berita Disini ..."
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
        @foreach($berita as $news)
          <!-- Blog item -->
            <div class="blog-item col-md-4">
                <article>
                    <div class="post-img">
                      @php
                          $slugKategori = str_replace(' ', '-', strtolower($news->kategori));
                      @endphp
                      <span class="post-category">

                          <a href="{{ url('berita') }}?kategori={{ $slugKategori }}">
                              
                            {{ $news->kategori }}
                          </a>
                      </span>
                      <span class="post-category">{{$news->kategori}}</span>
                      @if(!is_null($news->getfilebyalias('gambar_berita')))
                              @php
                                $file = $news->getfilebyalias('gambar_berita');
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
                      <a href="{{ route('berita.detail', $news->slug) }}">{{$news->nama}}</a>
                    </h2>

                    <div class="meta-top">
                      <ul>
                        <li class="d-flex align-items-center"><i class="bi bi-person"></i> 
                          <a href="blog-details.html">Penulis : {{$news->user->name}}</a>
                        </li>
                        <li class="d-flex align-items-center"><i class="bi bi-eye"></i> 
                          <a href="blog-details.html">Dilihat : {{ $news->view ?? 0 }} Kali</a>
                        </li>
                      </ul>
                    </div>

                    <div class="content">
                      <div class="read-more">
                        <a href="{{ route('berita.detail', $news->slug) }}">Selengkapnya <i class="bi bi-arrow-right"></i></a> 
                      </div>
                    </div>
                </article>
            </div>
          @endforeach
          </div>
        
      </div>

      <div class="d-flex justify-content-center mt-4" data-aos="fade-up" data-aos-delay="200">
        {{ $berita->links() }}
      </div>


    </section>
    <!-- /Blog Posts Section -->
    
    <div class="notable-alumni">
          <div class="section-header text-center" data-aos="fade-up" data-aos-delay="200">
            <h3>Notable Alumni Spotlights</h3>
            <p>Extraordinary graduates making an impact in their fields</p>
          </div>

          <div class="row">
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="300">
              <div class="alumni-profile">
                <div class="profile-header">
                  <div class="profile-img">
                    <img src="assets/img/person/person-f-3.webp" alt="Alumni" class="img-fluid">
                  </div>
                  <div class="profile-year">2009</div>
                </div>
                <div class="profile-body">
                  <h4>Emma Richardson</h4>
                  <span class="profile-title">Climate Science Researcher</span>
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas convallis velit a enim tincidunt, sed tincidunt nulla feugiat. Cras efficitur magna in metus lacinia.</p>
                  <a href="#" class="btn-view-profile">View Profile <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="achievement-badge">
                  <i class="bi bi-award"></i>
                  <span>Environmental Leadership Award</span>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="400">
              <div class="alumni-profile">
                <div class="profile-header">
                  <div class="profile-img">
                    <img src="assets/img/person/person-m-7.webp" alt="Alumni" class="img-fluid">
                  </div>
                  <div class="profile-year">2013</div>
                </div>
                <div class="profile-body">
                  <h4>Dr. Marcus Johnson</h4>
                  <span class="profile-title">Neurosurgeon &amp; Medical Innovator</span>
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas convallis velit a enim tincidunt, sed tincidunt nulla feugiat. Cras efficitur magna in metus.</p>
                  <a href="#" class="btn-view-profile">View Profile <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="achievement-badge">
                  <i class="bi bi-stars"></i>
                  <span>Medical Innovation Excellence</span>
                </div>
              </div>
            </div>

            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="500">
              <div class="alumni-profile">
                <div class="profile-header">
                  <div class="profile-img">
                    <img src="assets/img/person/person-f-9.webp" alt="Alumni" class="img-fluid">
                  </div>
                  <div class="profile-year">2015</div>
                </div>
                <div class="profile-body">
                  <h4>Sophia Lin</h4>
                  <span class="profile-title">Tech Entrepreneur &amp; VC Partner</span>
                  <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas convallis velit a enim tincidunt, sed tincidunt nulla feugiat. Cras efficitur magna in metus.</p>
                  <a href="#" class="btn-view-profile">View Profile <i class="bi bi-arrow-right"></i></a>
                </div>
                <div class="achievement-badge">
                  <i class="bi bi-lightning"></i>
                  <span>Tech Visionary of the Year</span>
                </div>
              </div>
            </div>
          </div>

          <div class="text-center mt-5" data-aos="fade-up" data-aos-delay="600">
            <a href="#" class="btn-explore">Explore More Alumni Stories</a>
          </div>
        </div>

  </main>

@endsection
