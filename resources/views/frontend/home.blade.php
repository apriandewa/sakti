@extends('frontend.main')

    @section('container')

  
  <main class="main">

    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">

      <div class="info d-flex align-items-center">
        <div class="container">
          <div class="row" data-aos="fade-up" data-aos-delay="100">
            <div class="col-lg-12">
              <h2><span>PPID </span> Indragiri Hulu</h2>
              <p>Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indargiri Hulu</p>
              <a href="#featured-services" class="btn-get-started">Minta Data</a>
              <a href="#featured-services" class="btn-get-started">Transparansi</a>
            </div>
          </div>
        </div>
      </div>

      <div id="hero-carousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">

      @foreach($slider as $index => $slide)
        <div class="carousel-item{{ $index === 0 ? ' active' : '' }}">
          @if(!is_null($slide->getfilebyalias('gambar_slider')))
            @php
              $file = $slide->getfilebyalias('gambar_slider');
            @endphp
            @if($file)
              <div class="form-group text-center">
                {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid') !!}
              </div>
            @endif
          @endif
        </div>
      @endforeach

        <a class="carousel-control-prev" href="#hero-carousel" role="button" data-bs-slide="prev">
          <span class="carousel-control-prev-icon bi bi-chevron-left" aria-hidden="true"></span>
        </a>

        <a class="carousel-control-next" href="#hero-carousel" role="button" data-bs-slide="next">
          <span class="carousel-control-next-icon bi bi-chevron-right" aria-hidden="true"></span>
        </a>

      </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">
          <div class="col-lg-5 2">
            @if(!is_null($welcome->getfilebyalias('gambar_page')))
                    @php
                      $file = $welcome->getfilebyalias('gambar_page');
                    @endphp
                    @if($file)
                      <div class="form-group text-center">
                        {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid') !!}
                      </div>
                    @endif
                @endif
          </div>
          <div class="col-lg-7 content">
            <h3>{{$welcome->nama}}</h3>
            <p>
              {!! $welcome->desc !!}
            </p>
          </div>
        </div>

      </div>

    </section><!-- /About Section -->

    <!-- Stats Section -->
    <section id="stats" class="stats section">

       <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Statistik</h2>
        <p>Berikut adalah Statistik Layanan Informasi pada Pejabat Pengelola Informasi dan Dokumentasi (PPID) Kabupaten Indragiri Hulu</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="stats-grid">
          <div class="row g-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-6" data-aos="fade-up" data-aos-delay="100">
              <div class="stat-item featured">
                <div class="stat-icon">
                  <i class="bi bi-people-fill"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">
                    <span data-purecounter-start="0" data-purecounter-end="{{$pemohon}}" data-purecounter-duration="2" class="purecounter"></span>
                  </div>
                  <div class="stat-label">Pemohon Informasi</div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-6" data-aos="fade-up" data-aos-delay="200">
              <div class="stat-item">
                <div class="stat-icon">
                  <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">
                    <span data-purecounter-start="0" data-purecounter-end="{{$diminta}}" data-purecounter-duration="2" class="purecounter"></span>
                  </div>
                  <div class="stat-label">Permintaan Informasi</div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-6" data-aos="fade-up" data-aos-delay="300">
              <div class="stat-item">
                <div class="stat-icon">
                  <i class="bi bi-award-fill"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">
                    <span data-purecounter-start="0" data-purecounter-end="{{$diberikan}}" data-purecounter-duration="2" class="purecounter"></span>
                  </div>
                  <div class="stat-label">Informasi Diberikan</div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-6" data-aos="fade-up" data-aos-delay="400">
              <div class="stat-item">
                <div class="stat-icon">
                  <i class="bi bi-globe"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">
                    <span data-purecounter-start="0" data-purecounter-end="{{$ditolak}}" data-purecounter-duration="2" class="purecounter"></span>
                  </div>
                  <div class="stat-label">Informasi Tidak Dikuasai</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="cta-button text-center mt-3" data-aos="fade-up" data-aos-delay="200">
        <a href="/statistik" class="btn">
          <span>Statistik Pelayanan Informasi</span>
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>
    </section>
    <!-- /Stats Section -->

    <!-- Services Section -->
    <section id="services" class="services section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Profil</h2>
        <p>Berikut adalah Profil Pejabat Pengelola Informasi dan Dokumentasi (PPID) Kabupaten Indragiri Hulu</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4" data-aos="fade-up" data-aos-delay="100">

        @foreach($profil as $prof)
          <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
            <div class="service-item d-flex position-relative h-100">
              <i class="fa {{ $prof->icon }} fa-2x text-success me-4"></i>
              <div>
                <h3 class="title"><a href="{{ route('page.detail', $prof->slug) }}" class="stretched-link">{{$prof->nama}}</a></h3>
                <p class="description">{{$prof->keterangan}}</p>
              </div>
            </div>
          </div><!-- End Service Item -->
        @endforeach

        </div>

      </div>

    </section><!-- /Services Section -->

    <!-- Team Section -->
    <section id="team" class="team section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Struktur</h2>
        <p>Berikut ini adalah Susunan Organisasi Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu</p>
      </div><!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

        @foreach($struktur as $member)
          <div class="col-lg-3 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
            <div class="team-member">
              <div class="member-img">
                @if(!is_null($member->getfilebyalias('gambar_struktur')))
                    @php
                      $file = $member->getfilebyalias('gambar_struktur');
                    @endphp
                    @if($file)
                      <div class="form-group text-center">
                        {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid') !!}
                      </div>
                    @endif
                @endif
                <div class="social">
                  <p class="text-primary mt-2">{{$member->tugas}}</p>
                </div>
              </div>
              <div class="member-info">
                <h5>{{$member->nama}}</h5>
                <span>{{$member->jabatan}}</span>
              </div>
            </div>
          </div><!-- End Team Member -->
        @endforeach
        </div>

      </div>

    </section><!-- /Team Section -->

    <!-- Services Section -->
    <section id="stats" class="stats section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>DIP</h2>
        <p>Berikut adalah Daftar Informasi Publik Kabupaten Indragiri Hulu</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">
        <div class="stats-grid">
          <div class="row g-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-6" data-aos="fade-up" data-aos-delay="100">
              <a href="{{ url('/berkala') }}" class="text-decoration-none">
                <div class="stat-item featured hover-shadow">
                  <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                  </div>
                  <div class="stat-content">
                    <div class="stat-number">
                      <span data-purecounter-start="0"
                            data-purecounter-end="{{ $infoberkala }}"
                            data-purecounter-duration="2"
                            class="purecounter">
                      </span>
                    </div>
                    <div class="stat-label">Informasi Berkala</div>
                  </div>
                </div>
              </a>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-6" data-aos="fade-up" data-aos-delay="100">
              <a href="{{ url('/tersedia') }}" class="text-decoration-none">
                <div class="stat-item featured hover-shadow">
                  <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                  </div>
                  <div class="stat-content">
                    <div class="stat-number">
                      <span data-purecounter-start="0"
                            data-purecounter-end="{{ $infotersedia }}"
                            data-purecounter-duration="2"
                            class="purecounter">
                      </span>
                    </div>
                    <div class="stat-label">Informasi Tersedia Setiap Saat</div>
                  </div>
                </div>
              </a>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-6" data-aos="fade-up" data-aos-delay="300">
              <div class="stat-item featured">
                <div class="stat-icon">
                  <i class="bi bi-award-fill"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">
                    <span data-purecounter-start="0" data-purecounter-end="{{$diberikan}}" data-purecounter-duration="2" class="purecounter"></span>
                  </div>
                  <div class="stat-label">Informasi Serta Merta</div>
                </div>
              </div>
            </div>

            <div class="col-xl-3 col-lg-6 col-md-6 col-6" data-aos="fade-up" data-aos-delay="400">
              <div class="stat-item featured">
                <div class="stat-icon">
                  <i class="bi bi-globe"></i>
                </div>
                <div class="stat-content">
                  <div class="stat-number">
                    <span data-purecounter-start="0" data-purecounter-end="{{$ditolak}}" data-purecounter-duration="2" class="purecounter"></span>
                  </div>
                  <div class="stat-label">Informasi Dikecualikan</div>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>
      <div class="cta-button text-center mt-3" data-aos="fade-up" data-aos-delay="200">
        <a href="#" class="btn">
          <span>Statistik Pelayanan Informasi</span>
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>

    </section><!-- /Services Section -->

     <!-- Faq Section -->
    <section id="faq" class="faq section light-background">
      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Penghargaan</h2>
        <p>Berikut ini adalah Prestasi dan Penghargaan yang diperoleh Pejabat Pengelola Informasi dan Dokumentasi Kabupaten Indragiri Hulu</p>
      </div>
      <!-- End Section Title -->

      <div class="container">
        <div class="row gy-4">
          <div class="col-lg-7 d-flex flex-column justify-content-center order-2 order-lg-1">
            <div class="faq-container px-xl-3" data-aos="fade-up" data-aos-delay="200">
              @foreach($penghargaan as $faq)
                @php
                  $imgSrc = $faq->getfilebyalias('gambar_penghargaan') ? url($faq->getfilebyalias('gambar_penghargaan')->public_stream) : '';
                @endphp
                <div class="faq-item" data-img-src="{{ $imgSrc }}">
                  <h3>
                    <i class="faq-icon bi bi-x-diamond" style="color: bg-primary"></i>
                    {{ $faq->nama }}
                  </h3>
                  <div class="faq-content">
                    <p>{{ $faq->desc }}</p>
                  </div>
                  <i class="faq-toggle bi bi-chevron-right"></i>
                </div><!-- End Faq item-->
              @endforeach
            </div>
          </div>

          <div class="col-lg-5 order-1 order-lg-2 d-flex justify-content-center align-items-center">
            @php
              // Ambil penghargaan pertama untuk gambar default
              $first = $penghargaan->first();
              $file = $first ? $first->getfilebyalias('gambar_penghargaan') : null;
              $imgSrc = $file ? url($file->public_stream) : '';
            @endphp
            @if($file)
              <img id="faq-image" src="{{ $imgSrc }}" class="img-fluid img-thumbnail" style="max-width:100%;height:auto;" alt="{{ $file->name }}">
            @else
              <span class="text-muted">Tidak ada gambar</span>
            @endif
          </div>
        </div>
      </div>

    </section><!-- /Faq Section -->

    <!-- Features Section -->
    <section id="features" class="features section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Informasi</h2>
        <p>Berikut ini beberapa cara yang bisa digunakan untuk mendapatkan informasi resmi dari PPID Kabupaten Indragiri Hulu</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="tabs-wrapper">
          {{-- ============== TAB NAVIGATION ============== --}}
          <ul class="nav nav-tabs" data-aos="fade-up" data-aos-delay="100">
            @foreach($saluran as $index => $sal)
              <li class="nav-item">
                <a class="nav-link {{ $index === 0 ? 'active show' : '' }}" 
                  data-bs-toggle="tab" 
                  data-bs-target="#features-tab-{{ $index + 1 }}">
                  <div class="tab-icon">
                    <i class="fa {{ $sal->icon }}"></i>
                  </div>
                  <div class="tab-content">
                    <h5>{{ $sal->nama }}</h5>
                    <span>{{ $sal->keterangan }}</span>
                  </div>
                </a>
              </li>
            @endforeach
          </ul>

          {{-- ============== TAB CONTENT ============== --}}
          <div class="tab-content" data-aos="fade-up" data-aos-delay="200">
            @foreach($saluran as $index => $sal)
              @php
                $imgSrc = $sal->getfilebyalias('gambar_page') 
                  ? url($sal->getfilebyalias('gambar_page')->public_stream) 
                  : 'https://via.placeholder.com/500x350?text=No+Image';
              @endphp

              <div class="tab-pane fade {{ $index === 0 ? 'active show' : '' }}" id="features-tab-{{ $index + 1 }}">
                <div class="row align-items-center">

                  <div class="content-wrapper">
                    <div class="d-flex align-items-center">
                      <div class="icon-badge">
                        <i class="fa {{ $sal->icon }}"></i>
                      </div>
                      <h4 class="mb-0 ms-3 flex-grow-1">
                        Permintaan Informasi dengan cara {{ $sal->nama }}
                      </h4>
                    </div>
                  </div>

                  <div class="col-lg-7">
                    <p>{!! $sal->desc !!}</p>
                  </div>

                  <div class="col-lg-5">
                    <div class="visual-content">
                      <div class="main-image">
                        <img src="{{ $imgSrc }}" alt="{{ $sal->nama }}" class="img-fluid rounded shadow">
                      </div>
                    </div>
                  </div>

                </div>
              </div><!-- End tab content item -->
            @endforeach
          </div>
        </div>

      </div>


    </section>
    <!-- /Features Section -->

    <!-- Blog Posts Section -->
    <section id="blog-posts" class="blog-posts section">

       <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Berita </h2>
        <p>Berikut adalah Berita dan Informasi Kegiatan PPID Kabupaten Indragiri Hulu</p>
      </div>
      <!-- End Section Title -->

      <div class="container owl-carousel berita-carousel">
  
        @foreach($berita as $news)
                <div class="blog-item">
                    <article>
                        <div class="post-img">
                          
                          <span class="post-date">{{ \Carbon\Carbon::parse($news->created_at)->translatedFormat('d F Y') }}</span>

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

                          @php
                              $slugKategori = str_replace(' ', '-', strtolower($news->kategori));
                          @endphp

                          <span class="post-category">
                              <a href="{{ url('berita') }}?kategori={{ $slugKategori }}">
                                 
                                {{ $news->kategori }}
                              </a>
                          </span>
                        </div>

                        <h2 class="title">
                            <a href="{{ route('berita.detail', $news->slug) }}">
                                {{ $news->nama }}
                            </a>
                        </h2>


                        <div class="meta-top">
                          <ul>
                            <li class="d-flex align-items-center"><i class="bi bi-person"></i> 
                              @if($news->user)
                                <a href="{{ url('berita') }}?penulis={{ $news->user_id }}">
                                    {{ $news->user->name }}
                                </a>
                              @else
                                <span class="text-muted">Penulis tidak diketahui</span>
                              @endif
                            </li>
                            <li class="d-flex align-items-center"><i class="bi bi-eye"></i> 
                              <a href="blog-details.html">Dilihat : {{ $news->view }} Kali</a>
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
        <!-- End blog item -->
      </div>

      <div class="cta-button text-center mt-3" data-aos="fade-up" data-aos-delay="200">
        <a href="/berita" class="btn">
          <span>Semua Berita</span>
          <i class="bi bi-arrow-right"></i>
        </a>
      </div>

    </section>
    <!-- /Blog Posts Section -->
    
      
    <!-- Call To Action Section -->
    <section id="call-to-action" class="call-to-action section dark-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-lg-center">
          <div class="col-lg-6 order-lg-2" data-aos="fade-left" data-aos-delay="200">
            <div class="image-wrapper position-relative">
              <div class="floating-card">
                <i class="bi bi-shield-lock"></i>
                <h4>Transparansi Anggaran</h4>
                <p>Untuk Pemerintahan yang bersih dan terbuka</p>
              </div>
              <img src="reveal/assets/img/misc/misc-6.webp" alt="Security Solutions" class="img-fluid main-image">
            </div>
          </div>

          <div class="col-lg-6 order-lg-1" data-aos="fade-right" data-aos-delay="100">
            <div class="content-area">
              <h2>Transparansi Anggaran</h2>
              <p>Kanal Transparansi Anggaran Pemerintah Kabupaten Indragiri Hulu</p>

              <ul class="feature-list">
                <li>
                  <i class="bi bi-check"></i>
                  <span>Dokumen Perencanaan Anggaran</span>
                </li>
                <li>
                  <i class="bi bi-check"></i>
                  <span>Dokumen Pelaksanaan Anggaran</span>
                </li>
                <li>
                  <i class="bi bi-check"></i>
                  <span>Dokumen Realisasi Anggaran</span>
                </li>
              </ul>

              <div class="cta-wrapper">
                <a href="#" class="btn btn-cta">Lihat Dokumen Transparansi</a>
              </div>
            </div>
          </div>

        </div>

      </div>

    </section><!-- /Call To Action Section -->

    <!-- Portfolio Section -->
    <section id="portfolio" class="portfolio section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Galeri </h2>
        <p>Berikut adalah Galeri Foto Kegiatan PPID Kabupaten Indragiri Hulu</p>
      </div>
      <!-- End Section Title -->

      <div class="container">

      <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">

          {{-- ================== FILTER ================== --}}
          @php
              use Illuminate\Support\Str;

              // Definisikan fungsi hanya jika belum ada
              if (!function_exists('kategoriToFilter')) {
                  function kategoriToFilter($kategori) {
                      return 'filter-' . strtolower(str_replace(' ', '-', trim($kategori)));
                  }
              }

              // Ambil semua kategori unik dari field kategori
              $allCategories = collect();
              foreach($galeri as $item) {
                  $categories = explode(',', $item->kategori); // konsisten pakai kategori
                  foreach ($categories as $cat) {
                      if(trim($cat) !== '') {
                          $allCategories->push(trim($cat));
                      }
                  }
              }
              $uniqueCategories = $allCategories->unique()->sort()->values();
          @endphp

          <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
              <li data-filter="*" class="filter-active">Semua</li>
              @foreach($uniqueCategories as $category)
                  <li data-filter=".{{ kategoriToFilter($category) }}">
                      {{ $category }}
                  </li>
              @endforeach
          </ul>
          {{-- ================== END FILTER ================== --}}

          <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">

              {{-- ================== LIST GALERI ================== --}}
              @foreach($galeri as $foto)
                  @php
                      $imgSrc = $foto->getfilebyalias('cover_galeri') 
                          ? url($foto->getfilebyalias('cover_galeri')->public_stream) 
                          : '';

                      $categories = explode(',', $foto->kategori);
                      $filterClasses = collect($categories)
                          ->filter(fn($cat) => trim($cat) !== '')
                          ->map(fn($cat) => kategoriToFilter($cat))
                          ->implode(' ');
                  @endphp

                  <div class="col-lg-4 col-md-6 portfolio-item isotope-item {{ $filterClasses }}">
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
                          <p>{{ $foto->desc }}</p>

                        @if($imgSrc)
                            @php
                                // Ambil kategori pertama
                                $firstCategory = $categories[0] ?? 'default';
                                $slug = strtolower(str_replace(' ', '-', trim($firstCategory)));

                                // ambil kata terakhir setelah strip (contoh: info-grafis -> grafis)
                                $lastPart = Str::of($slug)->afterLast('-');
                                $galleryGroup = 'portfolio-gallery-' . $lastPart;
                            @endphp

                            <a href="{{ $imgSrc }}" alt="{{ $foto->nama }}"
                              data-gallery="{{ $galleryGroup }}"
                              class="glightbox preview-link ">
                              <i class="bi bi-zoom-in"></i>
                            </a>
                        @endif

                          <a href="{{ route('galeri.detail', $foto->slug) }}" 
                            title="More Details" 
                            class="details-link">
                            <i class="bi bi-link-45deg"></i>
                          </a>
                      </div>
                  </div><!-- End Portfolio Item -->
              @endforeach
              {{-- ================== END LIST GALERI ================== --}}

          </div><!-- End Portfolio Container -->

      </div>

    </div>


    </section>
    <!-- /Portfolio Section -->

    <!-- unduhan Section -->
        <section id="unduhan" class="unduhan section light-background">

          <!-- Section Title -->
          <div class="container section-title text-center" data-aos="fade-up">
            <h2>Unduhan</h2>
            <div><span>Berikut adalah</span> <span class="description-title">Berkas ataupun dokumen unduhan</span></div>
          </div><!-- End Section Title -->

          <div class="container owl-carousel unduhan-carousel" data-aos="fade-up" data-aos-delay="100">
          @foreach($unduhan as $download)
            <div class="unduhan-member d-flex">

            @php
                $slugUnduh = str_replace(' ', '-', strtolower($download->kategori));
            @endphp
            
              <div class="member-img">
                <span class="unduhan-category">
                  <a href="{{ url('unduhan') }}?kategori={{ $slugUnduh }}" class="text-white">             
                    {{ $download->kategori }}
                  </a>
                </span>
                
                @if(!is_null($download->getfilebyalias('gambar_unduhan')))
                    @php
                      $file = $download->getfilebyalias('gambar_unduhan');
                    @endphp
                    @if($file)
                      <div class="form-group text-center">
                        {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid') !!}
                      </div>
                    @endif
                @endif
                <span class="unduhan-date">{{ \Carbon\Carbon::parse($download->created_at)->translatedFormat('d F Y') }}</span>
              </div>

              <div class="member-info flex-grow-1">
                
                <h4><a href="{{ route('unduhan.detail', $download->slug) }}">
                                {{ $download->nama }}
                            </a>
                          </h4>
                
                <p class="mt-2">{{$download->desc}}</p>
                <div class="content">
                  <div class="read-more">
                    <a href="{{ route('unduhan.detail', $download->slug) }}">Selengkapnya <i class="bi bi-arrow-right"></i></a> 
                  </div>
                </div>

                <div class="meta-top">
                  <ul>
                    <li class="d-flex align-items-center"><i class="bi bi-person"></i>
                      @if($download->user)
                        <a href="blog-details.html"> Penulis : {{$download->user->name}}</a>
                      @else
                        <span class="text-muted">Penulis tidak diketahui</span>
                      @endif
                    </li>
                    <li class="d-flex align-items-center"><i class="bi bi-eye"></i> <a href="blog-details.html">Dilihat : {{ $download->view }} Kali</a></li>
                  </ul>
                </div>
              </div>
            </div>
            <!-- End unduhan Member -->
          @endforeach
            
          </div>

          <div class="cta-button text-center mt-3" data-aos="fade-up" data-aos-delay="200">
            <a href="/unduhan" class="btn">
              <span>Semua Unduhan</span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>

        </section>
      <!-- /unduhan Section -->


    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Testimonials</h2>
        <p>Necessitatibus eius consequatur ex aliquid fuga eum quidem sint consectetur velit</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 1,
                  "spaceBetween": 40
                },
                "1200": {
                  "slidesPerView": 3,
                  "spaceBetween": 10
                }
              }
            }
          </script>
          <div class="swiper-wrapper">
          @foreach($testimoni as $testi)
            <div class="swiper-slide">
              <div class="testimonial-item text-center">
                @if(!is_null($testi->getfilebyalias('gambar_testimoni')))
                    @php
                      $file = $testi->getfilebyalias('gambar_testimoni');
                    @endphp
                    @if($file)
                      <div class="form-group text-center">
                        {!! html()->img(url($file->public_stream), $file->name)->class('testimonial-img') !!}
                      </div>
                    @endif
                @endif
                <h3>{{$testi->nama}}</h3>
                <h4>{{$testi->keterangan}}</h4>
                <p>
                  <i class="bi bi-quote quote-icon-left"></i>
                  <span>{{$testi->desc}}</span>
                  <i class="bi bi-quote quote-icon-right"></i>
                </p>
              </div>
            </div><!-- End testimonial item -->
          @endforeach
          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Testimonials Section -->

    <!-- Call To Action Section -->
    <section id="call-to-action" class="call-to-action section dark-background">

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="row align-items-lg-center">
          <div class="col-lg-6 order-lg-2" data-aos="fade-left" data-aos-delay="200">
            <div class="image-wrapper position-relative">
              <div class="floating-card">
                <i class="bi bi-shield-lock"></i>
                <h5>PPIM Kabupaten Indragiri Hulu</h5>
                <p>Pusat Pelayanan Informasi Masyarakat Kabupaten Indragiri Hulu</p>
              </div>
              <img src="reveal/assets/img/misc/misc-6.webp" alt="Security Solutions" class="img-fluid main-image">
            </div>
          </div>

          <div class="col-lg-6 order-lg-1" data-aos="fade-right" data-aos-delay="100">
            <div class="content-area">
              <h3>PPIM Kabupaten Indragiri Hulu</h3>
              <p>Pusat Pelayanan Informasi Masyarakat Kabupaten Indragiri Hulu</p>

              <ul class="feature-list">
                <span>Jadwal Pelayanan Informasi</span>
                <li>
                  <i class="bi bi-check"></i>
                  <span>Senin-Kamis</span>
                  &nbsp;
                  <span>Pukul : 08.00 WIB s/d 16.00 WIB</span>
                </li>
                <li>
                  <i class="bi bi-check"></i>
                  <span>Jum'at</span>
                  &nbsp;
                  <span>Pukul : 08.00 WIB s/d 15.00 WIB</span>
                </li>
              </ul>

              <div class="cta-wrapper">
                <a href="https://sipatin.inhukab.go.id" target="_blank" class="btn btn-cta">Permintaan Data</a>
              </div>
            </div>
          </div>

        </div>

      </div>

    </section><!-- /Call To Action Section -->

     <!-- Clients Section -->
    <section id="clients" class="clients section light-background">
      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Tautan </h2>
        <p>Berikut adalah Tautan Terkait dengan PPID Kabupaten Indragiri Hulu</p>
      </div>
      <!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
          <script type="application/json" class="swiper-config">
            {
              "loop": true,
              "speed": 600,
              "autoplay": {
                "delay": 5000
              },
              "slidesPerView": "auto",
              "pagination": {
                "el": ".swiper-pagination",
                "type": "bullets",
                "clickable": true
              },
              "breakpoints": {
                "320": {
                  "slidesPerView": 2,
                  "spaceBetween": 40
                },
                "480": {
                  "slidesPerView": 3,
                  "spaceBetween": 60
                },
                "640": {
                  "slidesPerView": 4,
                  "spaceBetween": 80
                },
                "992": {
                  "slidesPerView": 5,
                  "spaceBetween": 120
                }
              }
            }
          </script>
          <div class="swiper-wrapper align-items-center">
            @foreach($client as $tautan)
            <div class="swiper-slide">
                <a href="{{$tautan->link}}" target="_blank">
                {!! html()->span()->class("control-label") !!}
                  @if(!is_null($tautan->getfilebyalias('gambar_tautan')))
                    @php
                      $file = $tautan->getfilebyalias('gambar_tautan');
                    @endphp
                    @if($file)
                      <div class="form-group text-center">
                        {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid') !!}
                      </div>
                    @endif
                  @endif
                </a>
            </div>
            @endforeach
          </div>
          <div class="swiper-pagination"></div>
        </div>

      </div>

    </section><!-- /Clients Section -->

   
  </main>

@endsection
