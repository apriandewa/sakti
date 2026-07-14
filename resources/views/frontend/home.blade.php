@extends('frontend.main')


@section('container')
<main class="main">

  <!-- ==========================================
       HERO SECTION — SAKTI branded, sailing ship animation
       ========================================== -->
  @php
    $totalBidang = isset($bidang) ? count($bidang) : 0;
    $totalProgram = isset($program) ? count($program) : 0;
    $totalBerita = isset($berita) ? count($berita) : 0;
    $totalUnduhan = isset($unduhan) ? count($unduhan) : 0;
  @endphp
  <section id="hero" class="hero hero-sakti">

    <!-- Night sky layer: stars (particles.js), shooting stars, constellations -->
    <div class="hero-sky-night" aria-hidden="true">
      <div id="hero-particles-night" class="hero-particles"></div>
      <span class="shooting-star ss1"></span>
      <span class="shooting-star ss2"></span>
      <span class="shooting-star ss3"></span>
      <svg class="constellation const-1" viewBox="0 0 200 120" xmlns="http://www.w3.org/2000/svg">
        <g stroke="rgba(255,255,255,0.35)" stroke-width="1">
          <line x1="10" y1="20" x2="50" y2="10"></line>
          <line x1="50" y1="10" x2="92" y2="36"></line>
          <line x1="92" y1="36" x2="140" y2="16"></line>
          <line x1="140" y1="16" x2="180" y2="46"></line>
        </g>
        <g fill="#fff">
          <circle cx="10" cy="20" r="1.6"></circle>
          <circle cx="50" cy="10" r="2"></circle>
          <circle cx="92" cy="36" r="1.4"></circle>
          <circle cx="140" cy="16" r="1.8"></circle>
          <circle cx="180" cy="46" r="1.4"></circle>
        </g>
      </svg>
      <svg class="constellation const-2" viewBox="0 0 200 120" xmlns="http://www.w3.org/2000/svg">
        <g stroke="rgba(255,255,255,0.3)" stroke-width="1">
          <line x1="20" y1="60" x2="60" y2="82"></line>
          <line x1="60" y1="82" x2="112" y2="56"></line>
          <line x1="112" y1="56" x2="152" y2="72"></line>
        </g>
        <g fill="#fff">
          <circle cx="20" cy="60" r="1.5"></circle>
          <circle cx="60" cy="82" r="1.8"></circle>
          <circle cx="112" cy="56" r="1.3"></circle>
          <circle cx="152" cy="72" r="1.6"></circle>
        </g>
      </svg>
      <svg class="constellation const-3" viewBox="0 0 200 120" xmlns="http://www.w3.org/2000/svg">
        <g stroke="rgba(255,255,255,0.28)" stroke-width="1">
          <line x1="30" y1="30" x2="70" y2="18"></line>
          <line x1="70" y1="18" x2="100" y2="40"></line>
        </g>
        <g fill="#fff">
          <circle cx="30" cy="30" r="1.4"></circle>
          <circle cx="70" cy="18" r="1.7"></circle>
          <circle cx="100" cy="40" r="1.3"></circle>
        </g>
      </svg>
    </div>

    <!-- Day sky layer: sun + drifting clouds -->
    <div class="hero-sky-day" aria-hidden="true">
      <div class="hero-sun">
        <span class="sun-rays"></span>
        <span class="sun-core"></span>
        <span class="sun-shine"></span>
      </div>
      <span class="hero-cloud cloud-1"></span>
      <span class="hero-cloud cloud-2"></span>
      <span class="hero-cloud cloud-3"></span>
      <span class="hero-cloud cloud-4"></span>
    </div>

    <div class="hero-glossy-sheen"></div>

    <!-- Ocean layer: waves + sailboat -->
    <div class="hero-ocean" aria-hidden="true">

      <svg class="wave-layer wave-far" viewBox="0 0 1600 220" preserveAspectRatio="none">
        <path d="M0,150 C200,190 400,120 600,150 C800,180 1000,120 1200,145 C1400,170 1500,140 1600,150 L1600,220 L0,220 Z"></path>
        <path d="M1600,150 C1800,190 2000,120 2200,150 C2400,180 2600,120 2800,145 C3000,170 3100,140 3200,150 L3200,220 L1600,220 Z"></path>
      </svg>

      <svg class="wave-layer wave-back" viewBox="0 0 1600 220" preserveAspectRatio="none">
        <path d="M0,120 C200,180 400,60 600,110 C800,160 1000,60 1200,100 C1400,140 1500,90 1600,110 L1600,220 L0,220 Z"></path>
        <path d="M1600,120 C1800,180 2000,60 2200,110 C2400,160 2600,60 2800,100 C3000,140 3100,90 3200,110 L3200,220 L1600,220 Z"></path>
      </svg>

      <div class="hero-sailboat" id="heroSailboat">
        <svg viewBox="0 0 120 140" xmlns="http://www.w3.org/2000/svg">
          <path d="M60 8 L60 90 L20 90 Z" fill="#f5a623"/>
          <path d="M64 2 L64 90 L104 90 Z" fill="#1b2450"/>
          <rect x="58" y="8" width="4" height="90" fill="#0f1533"/>
          <path d="M10 96 C35 108 85 108 110 96 L118 108 C90 122 30 122 2 108 Z" fill="#1b2450"/>
          <path d="M2 108 C30 120 90 120 118 108" fill="none" stroke="#f5a623" stroke-width="3" stroke-linecap="round"/>
        </svg>
      </div>

      <svg class="wave-layer wave-mid" viewBox="0 0 1600 220" preserveAspectRatio="none">
        <path d="M0,130 C220,80 420,170 620,130 C820,90 1020,180 1220,135 C1380,105 1500,145 1600,130 L1600,220 L0,220 Z"></path>
        <path d="M1600,130 C1820,80 2020,170 2220,130 C2420,90 2620,180 2820,135 C2980,105 3100,145 3200,130 L3200,220 L1600,220 Z"></path>
      </svg>

      <svg class="wave-layer wave-front" viewBox="0 0 1600 220" preserveAspectRatio="none">
        <path d="M0,140 C220,90 420,190 620,140 C820,90 1020,190 1220,140 C1380,105 1500,150 1600,140 L1600,220 L0,220 Z"></path>
        <path d="M1600,140 C1820,90 2020,190 2220,140 C2420,90 2620,190 2820,140 C2980,105 3100,150 3200,140 L3200,220 L1600,220 Z"></path>
      </svg>

    </div>

    <div class="container hero-sakti-content text-center">
      <button type="button" id="saktiLogoBtn" class="sakti-logo-btn" data-aos="zoom-in" aria-label="Jalankan animasi kapal layar SAKTI">
        <span class="sakti-logo-ring"></span>
        <span class="sakti-logo-ring-glow"></span>
        <img src="{{ asset('eduadmin/images/sakti.png') }}" alt="Logo SAKTI" class="sakti-logo-img">
        <span class="sakti-logo-shine"></span>
      </button>

      <h1 class="sakti-title" data-aos="fade-up" data-aos-delay="150">SAKTI</h1>
      <p class="sakti-subtitle" data-aos="fade-up" data-aos-delay="250">Sistem Aplikasi Kepegawaian Terintegrasi</p>
      <span class="sakti-location" data-aos="fade-up" data-aos-delay="350">
        <i class="bi bi-geo-alt-fill"></i> Kabupaten Indragiri Hulu
      </span>
    </div>
  </section>

  <!-- ==========================================
       SAMBUTAN SECTION
       ========================================== -->
  <section id="about" class="section-dark">
    <div class="container">

      <!-- Sub-section 1: Deskripsi Portal & Foto Pimpinan -->
      <div class="row align-items-center mb-5 pb-5 border-bottom" style="border-color: var(--glass-border) !important;">
        <div class="col-lg-4 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
          <div class="leader-image-card mx-auto">
            <img src="{{ asset('eduadmin/images/pimpinaninhu.png') }}" alt="Pimpinan Indragiri Hulu" class="img-fluid">
            <div class="leader-badge">
              <h5>Bupati & Wakil Bupati</h5>
              <span>Kabupaten Indragiri Hulu</span>
            </div>
          </div>
        </div>
        <div class="col-lg-8" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
          <div class="sambutan-content">
            <span class="eyebrow">Sistem Aplikasi Kepegawaian Terintegrasi</span>
            <h3>S A K T I  &nbsp; I N H U</h3>
            <p class="text-secondary mb-4">
              SAKTI (Sistem Aplikasi Kepegawaian Terintegrasi) merupakan platform digital yang dikembangkan untuk mengintegrasikan berbagai layanan dan proses administrasi kepegawaian dalam satu sistem yang terpadu, modern, dan mudah digunakan.
            </p>
            <p class="text-secondary mb-4">
              Melalui SAKTI, seluruh pengelolaan data Aparatur Sipil Negara (ASN) dapat dilakukan secara lebih efektif, efisien, transparan, dan akuntabel. SAKTI hadir sebagai wujud transformasi digital dalam tata kelola kepegawaian Pemerintah Kabupaten Indragiri Hulu menuju pelayanan yang profesional, responsif, dan berorientasi pada peningkatan kualitas pelayanan publik.
            </p>
            
          </div>
        </div>
      </div>

      <!-- Sub-section 2: Sambutan Kepala Dinas -->
      @if($welcome)
        @php
          $fileWelcome = $welcome->getfilebyalias('gambar_page');
          $welcomeImg = $fileWelcome ? url($fileWelcome->public_stream) : asset('eduadmin/images/pimpinaninhu.png');
        @endphp
        <div class="row align-items-center">
          <div class="col-lg-7 order-2 order-lg-1" data-aos="fade-right" data-aos-duration="1000" data-aos-delay="100">
            <div class="sambutan-content">
              <span class="eyebrow">Sambutan Kepala Dinas</span>
              <h3>{{ $welcome->nama }}</h3>
              <div class="text-secondary mb-4">
                {!! $welcome->desc !!}
              </div>
            </div>
          </div>
          <div class="col-lg-5 order-1 order-lg-2 mb-4 mb-lg-0" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="300">
            <div class="leader-image-card mx-auto">
              <img src="{{ $welcomeImg }}" alt="Kepala Dinas Komunikasi dan Informatika" class="img-fluid">
              <div class="leader-badge">
                <h5>Kepala Dinas</h5>
                <span>Diskominfotik Indragiri Hulu</span>
              </div>
            </div>
          </div>
        </div>
      @endif

    </div>
  </section>

  <!-- ==========================================
       STATISTIK SECTION (inspired by JDIH "Statistik" block)
       ========================================== -->
  <section id="statistik" class="section-light-dark">
    <div class="container">
      <div class="row g-4 align-items-stretch">
        <div class="col-lg-4" data-aos="fade-up">
          <div class="stat-counter-box h-100 d-flex flex-column justify-content-center">
            <i class="bi bi-bar-chart-line stat-icon"></i>
            <div class="stat-number" data-target="{{ $totalBidang + $totalProgram + $totalBerita + $totalUnduhan }}">0</div>
            <div class="stat-label">Total Konten Terpublikasi</div>
            <div class="d-flex flex-wrap gap-2 mt-3">
              <span class="unduhan-badge"><i class="bi bi-diagram-3 me-1"></i>{{ $totalBidang }} Bidang</span>
              <span class="unduhan-badge"><i class="bi bi-rocket-takeoff me-1"></i>{{ $totalProgram }} Program</span>
            </div>
          </div>
        </div>
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="150">
          <div class="stat-counter-box h-100 d-flex flex-column justify-content-center">
            <i class="bi bi-newspaper stat-icon"></i>
            <div class="stat-number" data-target="{{ $totalBerita }}">0</div>
            <div class="stat-label">Berita & Artikel</div>
            <p class="text-secondary small mt-3 mb-0">Rilis terbaru seputar kegiatan dan kebijakan Pemkab Indragiri Hulu.</p>
          </div>
        </div>
        <div class="col-lg-4" data-aos="fade-up" data-aos-delay="300">
          <div class="stat-counter-box h-100 d-flex flex-column justify-content-center">
            <i class="bi bi-cloud-arrow-down stat-icon"></i>
            <div class="stat-number" data-target="{{ $totalUnduhan }}">0</div>
            <div class="stat-label">Dokumen Unduhan</div>
            <p class="text-secondary small mt-3 mb-0">Formulir, regulasi, dan laporan resmi yang siap diakses publik.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ==========================================
       SERVICE SECTION (BIDANG DI DINAS) — 3D Coverflow Carousel
       ========================================== -->
  <section id="services" class="section-dark">
    <div class="container">

      <div class="container section-title text-center" data-aos="fade-up">
        <span class="eyebrow">Layanan Kepegawaian</span> <br>
        <h2>Layanan Kepegawaian</h2>
        <p class="mx-auto">Beberapa Layanan Kepegawaian pada Pemerintah Kabupaten Indragiri Hulu</p>
      </div>

      <div class="swiper bidangSwiper" data-aos="fade-up" data-aos-delay="200">
        <div class="swiper-wrapper">
          @forelse($bidang as $index => $prof)
            <div class="swiper-slide text-center">
              <div class="glass-card service-card h-100 d-flex flex-column align-items-center">
                <div class="service-icon-box">
                  <i class="fa {{ $prof->icon ?? 'fa-laptop' }}"></i>
                </div>
                <h4>{{ $prof->nama }}</h4>
                <p class="text-secondary small mb-4 flex-grow-1">
                  {{ $prof->keterangan }}
                </p>
                <a href="{{ route('page.detail', $prof->slug) }}" class="btn-cyber-outline py-2 px-3 text-center">Selengkapnya</a>
              </div>
            </div>
          @empty
            <div class="swiper-slide">
              <div class="text-center text-secondary">
                <p>Data profil bidang belum tersedia.</p>
              </div>
            </div>
          @endforelse
        </div>
        <div class="swiper-pagination bidang-swiper-pagination mt-4"></div>
      </div>

    </div>
  </section>

  <!-- ==========================================
       FEATURE SECTION (PROGRAM & KEGIATAN)
       ========================================== -->
  <section id="features" class="section-light-dark">
    <div class="container">

      <div class="container section-title text-center" data-aos="fade-up">
        <span class="eyebrow">Digitalisasi Daerah</span>
        <h2>Program & Kegiatan</h2>
        <p class="mx-auto">Pemenuhan hak atas informasi publik dan program digitalisasi unggulan daerah</p>
      </div>

      <div class="row g-4">
        @forelse($program as $index => $sal)
          <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $index * 150 }}">
            <div class="feature-card">
                @php $filePage = $sal->getfilebyalias('gambar_page'); @endphp
                @if($filePage)
                  <div style="position: absolute; top: 15px; right: 15px; width: 64px;">
                    <img src="{{ url($filePage->public_stream) }}" alt="{{ $sal->nama }}" style="width: 100%; height: 100%; object-fit: contain;">
                  </div>
                @else
                  <div class="feature-icon-circle">
                    <i class="fa {{ $sal->icon ?? 'fa-info-circle' }}"></i>
                  </div>
                @endif
              <h4 class="mt-3">{{ $sal->nama }}</h4>
              <p class="text-secondary small mb-3">
                {{ $sal->keterangan }}
              </p>
              <a href="{{ route('page.detail', $sal->slug) }}" class="fw-bold" style="color: var(--navy-700);">Detail Program <i class="bi bi-chevron-right small"></i></a>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-secondary">
            <p>Data program kegiatan belum tersedia.</p>
          </div>
        @endforelse
      </div>

    </div>
  </section>

  <!-- ==========================================
       SECTION BERITA — 3D Coverflow Carousel (Swiper)
       ========================================== -->
  <section id="blog-posts" class="section-dark">
    <div class="container">

      <div class="container section-title text-center" data-aos="fade-up">
        <span class="eyebrow">Informasi Terkini</span> <br>
        <h2>Berita & Artikel</h2>
        <p class="mx-auto">Berita, artikel, dan rilis pers seputar kegiatan Pemkab Indragiri Hulu</p>
      </div>

      <div class="swiper beritaSwiper" data-aos="fade-up" data-aos-delay="200">
        <div class="swiper-wrapper">
          @forelse($berita as $news)
            @php
              $fileNews = $news->getfilebyalias('gambar_berita');
              $newsImg = $fileNews ? url($fileNews->public_stream) : 'https://via.placeholder.com/600x400?text=Berita+Diskominfotik';
              $slugKategori = str_replace(' ', '-', strtolower($news->kategori));
            @endphp
            <div class="swiper-slide">
              <div class="berita-card">
                <div class="berita-img-wrapper">
                  <span class="berita-badge">{{ $news->kategori }}</span>
                  <img src="{{ $newsImg }}" alt="{{ $news->nama }}">
                </div>
                <div class="berita-body">
                  <div class="berita-meta">
                    <span><i class="bi bi-person"></i> {{ $news->user ? $news->user->name : 'Admin' }}</span>
                    <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($news->created_at)->translatedFormat('d M Y') }}</span>
                    <span><i class="bi bi-eye"></i> {{ number_format($news->view) }}</span>
                  </div>
                  <h4 class="berita-title">
                    <a href="{{ route('berita.detail', $news->slug) }}">{{ $news->nama }}</a>
                  </h4>
                  <div class="text-secondary small flex-grow-1 mb-4" style="height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                    {!! strip_tags($news->desc) !!}
                  </div>
                  <a href="{{ route('berita.detail', $news->slug) }}" class="btn-cyber py-2 px-3 text-center">Selengkapnya</a>
                </div>
              </div>
            </div>
          @empty
            <div class="swiper-slide">
              <div class="text-center text-secondary py-5">
                <p>Belum ada rilis berita saat ini.</p>
              </div>
            </div>
          @endforelse
        </div>
        <div class="swiper-pagination berita-swiper-pagination mt-4"></div>
      </div>

      <div class="text-center mt-5" data-aos="fade-up">
        <a href="{{ url('berita') }}" class="btn-cyber-outline"><i class="bi bi-newspaper"></i> Lihat Semua Berita</a>
      </div>

    </div>
  </section>

  <!-- ==========================================
       SECTION UNDUHAN (SWIPER CAROUSEL)
       ========================================== -->
  <section id="unduhan" class="section-light-dark">
    <div class="container">

      <div class="container section-title text-center" data-aos="fade-up">
        <span class="eyebrow">Pusat Dokumen</span> <br>
        <h2>Dokumen Unduhan</h2>
        <p class="mx-auto">Pusat pengelolaan Berkas, Formulir, Regulasi, dan Surat Edaran</p>
      </div>

      <div class="swiper unduhanSwiper" data-aos="fade-up" data-aos-delay="200">
        <div class="swiper-wrapper">
          @forelse($unduhan as $download)
            @php
              $fileDownload = $download->getfilebyalias('gambar_unduhan');
              $downloadImg = $fileDownload ? url($fileDownload->public_stream) : 'https://via.placeholder.com/600x400?text=Download+Document';
              $slugUnduh = str_replace(' ', '-', strtolower($download->kategori));
            @endphp
            <div class="swiper-slide">
              <div class="unduhan-card">
                <div class="unduhan-header">
                  <div class="unduhan-icon">
                    <i class="bi bi-file-earmark-arrow-down"></i>
                  </div>
                  <span class="unduhan-badge">{{ $download->kategori }}</span>
                </div>
                <h4 class="unduhan-title">{{ $download->nama }}</h4>
                <p class="text-secondary small mb-3" style="height: 48px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                  {{ $download->desc }}
                </p>
                <div class="unduhan-meta">
                  <span><i class="bi bi-calendar"></i> {{ \Carbon\Carbon::parse($download->created_at)->translatedFormat('d M Y') }}</span>
                  <span><i class="bi bi-eye"></i> {{ number_format($download->view) }} kali</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3">
                  <a href="{{ route('unduhan.detail', $download->slug) }}" class="btn-cyber py-2 px-3 text-center w-100">Lihat Berkas</a>
                </div>
              </div>
            </div>
          @empty
            <div class="swiper-slide">
              <div class="text-center text-secondary py-5">
                <p>Dokumen tidak tersedia.</p>
              </div>
            </div>
          @endforelse
        </div>
        <div class="swiper-pagination unduhan-swiper-pagination mt-4"></div>
      </div>

      <div class="text-center mt-5" data-aos="fade-up">
        <a href="{{ url('unduhan') }}" class="btn-cyber-outline"><i class="bi bi-cloud-arrow-down"></i> Buka Semua Unduhan</a>
      </div>

    </div>
  </section>

  <!-- ==========================================
       TAUTAN / LINKS SECTION — "Aplikasi & Instansi Terkait"
       ========================================== -->
  <section id="clients" class="section-dark">
    <div class="container">

      <div class="container section-title text-center" data-aos="fade-up">
        <span class="eyebrow">Ekosistem Digital</span> <br>
        <h2>Tautan Terkait</h2>
        <p class="mx-auto">Konektivitas portal dengan kementerian, lembaga, dan instansi daerah</p>
      </div>

      <div class="swiper tautanSwiper" data-aos="fade-up" data-aos-delay="200">
        <div class="swiper-wrapper">
          @forelse($client as $tautan)
            @php
              $fileTautan = $tautan->getfilebyalias('gambar_tautan');
              $tautanImg = $fileTautan ? url($fileTautan->public_stream) : '';
            @endphp
            @if($tautanImg)
              <div class="swiper-slide">
                <a href="{{ $tautan->link }}" target="_blank" class="client-logo-wrapper">
                  <img src="{{ $tautanImg }}" alt="Tautan Partner" class="img-fluid">
                </a>
              </div>
            @endif
          @empty
            <div class="swiper-slide text-center text-secondary">
              <p>Tautan belum diunggah.</p>
            </div>
          @endforelse
        </div>
        <div class="swiper-pagination tautan-swiper-pagination mt-4"></div>
      </div>

    </div>
  </section>

  <!-- ==========================================
       SECTION ULASAN (VERIFIED TESTIMONIALS)
       ========================================== -->
  <section id="testimonials" class="section-light-dark">
    <div class="container">

      <div class="container section-title text-center" data-aos="fade-up">
        <span class="eyebrow">Suara Masyarakat</span> <br>
        <h2>Ulasan Pengguna</h2>
        <p class="mx-auto">Apresiasi dan saran yang dikirimkan oleh pengguna layanan informasi kami</p>
      </div>

      <div class="swiper testimonialsSwiper" data-aos="fade-up" data-aos-delay="200">
        <div class="swiper-wrapper">
          @forelse($testimoni as $testi)
            @php
              $fileTesti = $testi->getfilebyalias('gambar_testimoni');
              $testiImg = $fileTesti ? url($fileTesti->public_stream) : 'https://via.placeholder.com/150?text=User';

              $ket = strtolower(trim($testi->keterangan));
              $ratingVal = 5;
              if ($ket == 'buruk') $ratingVal = 1;
              elseif ($ket == 'kurang baik') $ratingVal = 2;
              elseif ($ket == 'cukup') $ratingVal = 3;
              elseif ($ket == 'baik') $ratingVal = 4;
              elseif ($ket == 'sangat baik') $ratingVal = 5;
            @endphp
            <div class="swiper-slide">
              <div class="testi-card h-100 d-flex flex-column align-items-center">
                <img src="{{ $testiImg }}" alt="{{ $testi->nama }}" class="testi-img">
                <h5 class="mb-1">{{ $testi->nama }}</h5>
                <span class="text-muted small mb-2 text-capitalize">{{ $testi->keterangan }}</span>

                <div class="testi-rating mb-3">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $ratingVal ? 'bi-star-fill' : 'bi-star' }} fs-6"></i>
                  @endfor
                </div>

                <p class="text-secondary small mb-0 flex-grow-1">
                  <i class="bi bi-quote quote-icon-left me-1" style="color: var(--gold-500);"></i>
                  {{ $testi->desc }}
                  <i class="bi bi-quote quote-icon-right ms-1" style="color: var(--gold-500);"></i>
                </p>
              </div>
            </div>
          @empty
            <div class="swiper-slide">
              <div class="text-center text-secondary py-5">
                <p>Belum ada ulasan yang ditampilkan.</p>
              </div>
            </div>
          @endforelse
        </div>
        <div class="swiper-pagination testimonials-swiper-pagination mt-4"></div>
      </div>

      <div class="text-center mt-5" data-aos="fade-up">
        <a href="{{ url('ulasan') }}" class="btn-cyber"><i class="bi bi-star"></i> Kirim Ulasan Layanan</a>
      </div>

    </div>
  </section>

</main>
@endsection