@extends('frontend.main')

@push('css')
<style>
/* Hero static layer setup */
.hero-static-layer {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  z-index: 3;
  pointer-events: none;
}

/* Center the hero content text */
.hero-content {
  left: 50% !important;
  top: 26% !important;
  transform: translateX(-50%) !important;
  width: 90% !important;
  max-width: 900px !important;
  text-align: center !important;
}

.hero-desc {
  margin-left: auto !important;
  margin-right: auto !important;
  max-width: 650px !important;
  font-size: 1.05rem !important;
  margin-bottom: 24px !important;
}

.hero-actions {
  justify-content: center !important;
}

/* Center the laptop in the static layer */
.hero-static-layer .row {
  height: 100% !important;
}

.hero-laptop-col {
  position: absolute !important;
  top: 65% !important;
  left: 50% !important;
  transform: translate(-50%, -50%) !important;
  width: 100% !important;
  display: flex !important;
  justify-content: center !important;
  align-items: center !important;
  pointer-events: auto;
  z-index: 10;
}

/* Laptop Styles - Scaled Up */
.laptop-wrapper {
  position: relative;
  width: 560px;
  height: 360px;
  display: flex;
  justify-content: center;
  align-items: center;
  perspective: 1500px;
  transform: scale(1.1); /* Enlarged scale for desktop */
  transform-origin: center center;
}

.laptop-container {
  position: relative;
  width: 480px;
  height: 320px;
  transform-style: preserve-3d;
  transform: rotateX(12deg) rotateY(0deg) rotateZ(0deg);
  transition: transform 0.5s ease;
}

.laptop-screen-container {
  position: absolute;
  bottom: 32px; /* Axis of screen hinge */
  left: 25px;
  width: 430px;
  height: 265px;
  transform-origin: bottom center;
  transform-style: preserve-3d;
  transform: rotateX(-90deg); /* Closed */
  z-index: 2;
  transition: transform 1.8s cubic-bezier(0.16, 1, 0.3, 1);
}

.laptop-container.open .laptop-screen-container {
  transform: rotateX(-12deg);
}

.laptop-screen {
  position: absolute;
  width: 100%;
  height: 100%;
  background: #090d16;
  border: 10px solid #1e293b;
  border-radius: 16px 16px 0 0;
  box-shadow: 
    inset 0 0 15px rgba(0, 0, 0, 0.8),
    0 0 0px rgba(0, 242, 254, 0);
  display: flex;
  flex-direction: column;
  overflow: hidden;
  transition: box-shadow 1s ease 1s;
}

.laptop-container.open .laptop-screen {
  box-shadow: 
    inset 0 0 15px rgba(0, 0, 0, 0.8),
    0 0 45px rgba(0, 242, 254, 0.25);
}

.laptop-screen::before {
  content: '';
  position: absolute;
  top: 4px;
  left: 50%;
  transform: translateX(-50%);
  width: 5px;
  height: 5px;
  background: #334155;
  border-radius: 50%;
  z-index: 10;
}

.screen-content {
  flex-grow: 1;
  background: #020617;
  padding: 14px;
  font-family: 'Space Grotesk', monospace;
  color: #10b981;
  font-size: 0.78rem;
  line-height: 1.5;
  opacity: 0;
  transition: opacity 0.8s ease 1.2s;
  display: flex;
  flex-direction: column;
}

.laptop-container.open .screen-content {
  opacity: 1;
}

.screen-glow {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: radial-gradient(circle at center, rgba(0, 242, 254, 0.15) 0%, transparent 70%);
  pointer-events: none;
  opacity: 0;
  transition: opacity 1s ease 1.5s;
}

.laptop-container.open .screen-glow {
  opacity: 1;
}

/* Terminal header & body */
.terminal-header {
  display: flex;
  align-items: center;
  gap: 5px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.08);
  padding-bottom: 4px;
  margin-bottom: 8px;
}

.terminal-dot {
  width: 6px;
  height: 6px;
  border-radius: 50%;
}
.terminal-dot.red { background: #ef4444; }
.terminal-dot.yellow { background: #f59e0b; }
.terminal-dot.green { background: #10b981; }

.terminal-title {
  color: rgba(255, 255, 255, 0.35);
  font-size: 0.55rem;
  margin-left: 4px;
}

.terminal-body {
  flex-grow: 1;
  text-align: left;
  white-space: normal;
  word-break: break-word;
}

.terminal-prompt {
  color: #00f2fe;
  margin-right: 4px;
  font-weight: bold;
}

.typing-text {
  color: #fff;
  font-weight: 500;
  font-size: 1.15rem;
  line-height: 1.6;
  text-shadow: 0 0 5px rgba(255, 255, 255, 0.2);
}

.terminal-cursor {
  display: inline-block;
  width: 6px;
  height: 18px;
  background: #10b981;
  margin-left: 3px;
  vertical-align: text-bottom;
  animation: cursorBlink 0.8s steps(2, start) infinite;
}

@keyframes cursorBlink {
  to { opacity: 0; }
}

/* Base styling */
.laptop-base-container {
  position: absolute;
  bottom: 10px;
  left: 0;
  width: 480px;
  height: 30px;
  transform-style: preserve-3d;
  z-index: 1;
}

.laptop-keyboard-deck {
  position: absolute;
  top: -16px;
  left: 8px;
  width: 464px;
  height: 30px;
  background: #334155;
  transform: rotateX(60deg);
  border-radius: 4px;
  box-shadow: 
    inset 0 1px 2px rgba(255, 255, 255, 0.2),
    inset 0 -15px 30px rgba(0, 0, 0, 0.6);
  border: 1px solid #1e293b;
}

.keyboard-keys {
  position: absolute;
  top: 2px;
  left: 13px;
  width: 438px;
  height: 15px;
  background: repeating-linear-gradient(
    90deg,
    #0f172a,
    #0f172a 16px,
    transparent 16px,
    transparent 19px
  );
  opacity: 0.8;
  border-radius: 1px;
}

.keyboard-trackpad {
  position: absolute;
  bottom: 1px;
  left: 50%;
  transform: translateX(-50%);
  width: 68px;
  height: 8px;
  background: #1e293b;
  border-radius: 2px;
  border: 1px solid rgba(255, 255, 255, 0.05);
}

.laptop-base-lip {
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  height: 16px;
  background: #1e293b;
  border-radius: 0 0 16px 16px;
  box-shadow: 
    0 15px 30px rgba(0, 0, 0, 0.6),
    0 2px 4px rgba(0, 242, 254, 0.1);
  border-bottom: 2px solid #0f172a;
}

/* Floating folders styling */
.folders-outer-container {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  z-index: 4;
  overflow: hidden;
}

.floating-folder {
  position: absolute;
  width: 82px;
  height: 62px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  cursor: pointer;
  z-index: 10;
  opacity: 0;
  pointer-events: auto;
  transition: transform 0.2s ease, box-shadow 0.2s ease !important;
}

.folder-tab {
  width: 26px;
  height: 8px;
  background: inherit;
  border-radius: 3px 3px 0 0;
  position: absolute;
  top: -8px;
  left: 6px;
  filter: brightness(1.18);
}

.folder-body {
  width: 100%;
  height: 100%;
  background: inherit;
  border-radius: 0 4px 4px 4px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 4px;
  border: 1px solid rgba(255, 255, 255, 0.2);
}

.folder-icon {
  font-size: 1.15rem;
  color: rgba(255, 255, 255, 0.95);
  margin-bottom: 1px;
}

.folder-text {
  font-family: 'Space Grotesk', sans-serif;
  font-size: 0.64rem;
  font-weight: 700;
  color: #fff;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  text-align: center;
  white-space: nowrap;
  max-width: 90%;
  overflow: hidden;
  text-overflow: ellipsis;
  text-shadow: 0 1px 2px rgba(0, 0, 0, 0.55);
}

/* Gradients & Glow colors */
.floating-folder.color-0 { background: linear-gradient(135deg, #00f2fe 0%, #0072ff 100%); box-shadow: 0 4px 15px rgba(0, 242, 254, 0.4); }
.floating-folder.color-1 { background: linear-gradient(135deg, #10b981 0%, #059669 100%); box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4); }
.floating-folder.color-2 { background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); box-shadow: 0 4px 15px rgba(245, 158, 11, 0.4); }
.floating-folder.color-3 { background: linear-gradient(135deg, #ec4899 0%, #a855f7 100%); box-shadow: 0 4px 15px rgba(236, 72, 153, 0.4); }
.floating-folder.color-4 { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); box-shadow: 0 4px 15px rgba(99, 102, 241, 0.4); }
.floating-folder.color-5 { background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); box-shadow: 0 4px 15px rgba(244, 63, 94, 0.4); }
.floating-folder.color-6 { background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%); box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4); }
.floating-folder.color-7 { background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%); box-shadow: 0 4px 15px rgba(20, 184, 166, 0.4); }
.floating-folder.color-8 { background: linear-gradient(135deg, #84cc16 0%, #65a30d 100%); box-shadow: 0 4px 15px rgba(132, 204, 22, 0.4); }
.floating-folder.color-9 { background: linear-gradient(135deg, #a855f7 0%, #7e22ce 100%); box-shadow: 0 4px 15px rgba(168, 85, 247, 0.4); }

.floating-folder:hover {
  transform: scale(1.22) translateY(-6px) !important;
  box-shadow: 0 12px 28px rgba(255, 255, 255, 0.6) !important;
  z-index: 100 !important;
}

/* Animations */
@keyframes flyOutFolder {
  0% {
    left: var(--start-x);
    top: var(--start-y);
    transform: scale(0) rotate(0deg);
    opacity: 0;
  }
  70% {
    opacity: 1;
  }
  100% {
    left: var(--target-x);
    top: var(--target-y);
    transform: scale(1) rotate(var(--random-rotate));
    opacity: 1;
  }
}

.floating-folder.flying {
  animation: flyOutFolder 2.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

@keyframes bobbingFolder {
  0%, 100% {
    transform: translateY(0) rotate(var(--random-rotate));
  }
  50% {
    transform: translateY(var(--bob-amplitude)) rotate(calc(var(--random-rotate) + var(--bob-rotate)));
  }
}

.floating-folder.bobbing {
  animation: bobbingFolder var(--bob-duration) ease-in-out infinite;
}

/* Responsive Scaling & Layout adjustments */
@media (max-width: 1400px) {
  .laptop-wrapper {
    transform: scale(1);
  }
}

@media (max-width: 1199px) {
  .laptop-wrapper {
    transform: scale(0.9);
  }
}

@media (max-width: 991px) {
  .hero-content {
    top: 22% !important;
  }
  .hero-laptop-col {
    top: 62% !important;
  }
  .laptop-wrapper {
    transform: scale(0.72);
  }
}

@media (max-width: 767px) {
  .hero-content {
    top: 18% !important;
  }
  .hero-laptop-col {
    top: 60% !important;
  }
  .laptop-wrapper {
    transform: scale(0.55);
  }
  .floating-folder {
    width: 65px;
    height: 50px;
  }
  .folder-tab {
    width: 20px;
    height: 6px;
    top: -6px;
  }
  .folder-icon {
    font-size: 0.95rem;
  }
  .folder-text {
    font-size: 0.52rem;
  }
}

@media (max-width: 575px) {
  .hero-content {
    top: 16% !important;
  }
  .hero-title {
    font-size: 1.8rem !important;
  }
  .hero-desc {
    font-size: 0.85rem !important;
    max-width: 100% !important;
  }
  .hero-laptop-col {
    top: 56% !important;
  }
  .laptop-wrapper {
    transform: scale(0.44);
  }
}
</style>
@endpush

@section('container')
<main class="main">

  <!-- ==========================================
       HERO SECTION WITH SWIPER
       ========================================== -->
  <section id="hero" class="hero">
    <div class="swiper hero-swiper">
      <div class="swiper-wrapper">
        @forelse($slider as $slide)
          @php
            $file = $slide->getfilebyalias('gambar_slider');
            $imgUrl = $file ? url($file->public_stream) : 'https://via.placeholder.com/1920x1080?text=Slider+Image';
          @endphp
          <div class="swiper-slide" style="background-image: url('{{ $imgUrl }}');">
            <div class="hero-overlay"></div>
            <div class="hero-content container">
              <span class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">Teknologi & Digitalisasi</span>
              <h2 class="hero-title" data-aos="fade-up" data-aos-delay="400">
                <span>{{ $slide->nama ?? 'Portal Resmi' }}</span>
              </h2>
              <p class="hero-desc" data-aos="fade-up" data-aos-delay="600">
                {{ $slide->keterangan ?? 'Menuju tata kelola pemerintahan yang transparan dan akuntabel di Kabupaten Indragiri Hulu.' }}
              </p>
              <div class="hero-actions" data-aos="fade-up" data-aos-delay="800">
                <a href="#services" class="btn-cyber"><i class="bi bi-cpu"></i> Layanan Kami</a>
                <a href="#about" class="btn-cyber-outline"><i class="bi bi-info-circle"></i> Tentang Kami</a>
              </div>
            </div>
          </div>
        @empty
          <!-- Default Fallback Slide -->
          <div class="swiper-slide" style="background-image: url('https://via.placeholder.com/1920x1080?text=Portal+Diskominfotik');">
            <div class="hero-overlay"></div>
            <div class="hero-content container">
              <span class="hero-subtitle">Teknologi & Digitalisasi</span>
              <h2 class="hero-title"><span>{{ $title ?? 'Diskominfotik Kabupaten Indragiri Hulu' }}</span></h2>
              <p class="hero-desc">Layanan keterbukaan informasi publik secara digital, cepat, mudah, dan transparan.</p>
              <div class="hero-actions">
                <a href="#services" class="btn-cyber"><i class="bi bi-cpu"></i> Layanan Kami</a>
                <a href="#about" class="btn-cyber-outline"><i class="bi bi-info-circle"></i> Tentang Kami</a>
              </div>
            </div>
          </div>
        @endforelse
      </div>

      <!-- Navigation Arrows -->
      <div class="swiper-button-prev hero-swiper-button-prev d-none d-md-flex"></div>
      <div class="swiper-button-next hero-swiper-button-next d-none d-md-flex"></div>
      <!-- Pagination Dots -->
      <div class="swiper-pagination hero-swiper-pagination"></div>
    </div>

    <!-- Persistent Static Layer for Laptop & Floating Folders -->
    <div class="hero-static-layer">
      <div class="container h-100">
        <div class="row h-100 g-0">
          <div class="col-12 hero-laptop-col">
            <div class="laptop-wrapper">
              <div class="laptop-container">
                <!-- Screen Lid -->
                <div class="laptop-screen-container">
                  <div class="laptop-screen">
                    <div class="screen-content">
                      <div class="terminal-header">
                        <span class="terminal-dot red"></span>
                        <span class="terminal-dot yellow"></span>
                        <span class="terminal-dot green"></span>
                        <span class="terminal-title">diskominfotik@inhukab:~</span>
                      </div>
                      <div class="terminal-body">
                        <span class="terminal-prompt">&gt;</span>
                        <span class="typing-text"></span>
                        <span class="terminal-cursor"></span>
                      </div>
                    </div>
                    <div class="screen-glow"></div>
                  </div>
                </div>
                <!-- Base -->
                <div class="laptop-base-container">
                  <div class="laptop-keyboard-deck">
                    <div class="keyboard-keys"></div>
                    <div class="keyboard-trackpad"></div>
                  </div>
                  <div class="laptop-base-lip"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Floating Folders Outer Container -->
      <div class="folders-outer-container"></div>
    </div>
  </section>

  <!-- ==========================================
       SAMBUTAN SECTION
       ========================================== -->
  <section id="about" class="section-dark">
    <div class="container">
      
      <!-- Sub-section 1: Deskripsi Portal & Foto Pimpinan -->
      <div class="row align-items-center mb-5 pb-5 border-bottom border-secondary">
        <div class="col-lg-5 mb-4 mb-lg-0" data-aos="fade-right" data-aos-duration="1000">
          <div class="leader-image-card mx-auto">
            <img src="{{ asset('eduadmin/images/pimpinaninhu.png') }}" alt="Pimpinan Indragiri Hulu" class="img-fluid">
            <div class="leader-badge">
              <h5>Bupati & Wakil Bupati</h5>
              <span>Kabupaten Indragiri Hulu</span>
            </div>
          </div>
        </div>
        <div class="col-lg-7" data-aos="fade-left" data-aos-duration="1000" data-aos-delay="200">
          <div class="sambutan-content">
            <span class="text-success font-subtitle fw-bold uppercase letter-spacing-1 text-cyan d-block mb-2">Portal Diskominfotik Inhu</span>
            <h3>Pelayanan Informasi Terpadu</h3>
            <p class="lead text-white font-subtitle mb-4">
              Selamat datang di {{ $title ?? 'Dinas Komunikasi, Informatika, Statistik dan Persandian Kabupaten Indragiri Hulu' }}.
            </p>
            <p class="text-secondary mb-4">
              Berdasarkan amanah Undang-Undang Nomor 14 Tahun 2008 tentang Keterbukaan Informasi Publik, kami berkomitmen untuk menyediakan layanan data dan dokumentasi yang handal, cepat, serta mudah diakses bagi masyarakat luas guna mewujudkan transparansi dan integrasi data yang andal.
            </p>
            <a href="#services" class="btn-cyber-outline"><i class="bi bi-arrow-down-short fs-5"></i> Eksplor Layanan</a>
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
              <span class="text-success font-subtitle fw-bold uppercase text-cyan d-block mb-2">Sambutan Kepala Dinas</span>
              <h3>{{ $welcome->nama }}</h3>
              <div class="quote-text text-secondary mb-4">
                "{!! strip_tags(Str::limit($welcome->desc, 300)) !!}"
              </div>
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
       SERVICE SECTION (BIDANG DI DINAS)
       ========================================== -->
  <section id="services" class="section-light-dark">
    <div class="container">
      
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Bidang Diskominfotik</h2>
        <p>Bidang-bidang pelayanan dan tugas pokok pada Dinas Komunikasi, Informatika, Statistik, dan Persandian</p>
      </div>

      <div class="row g-4 justify-content-center">
        @forelse($bidang as $index => $prof)
          <div class="col-md-6 col-lg-3" data-aos="flip-left" data-aos-delay="{{ $index * 150 }}">
            <div class="glass-card service-card h-100 d-flex flex-column align-items-start">
              <div class="service-icon-box">
                <i class="fa {{ $prof->icon ?? 'fa-laptop' }}"></i>
              </div>
              <h4>{{ $prof->nama }}</h4>
              <p class="text-secondary small mb-4 flex-grow-1">
                {{ $prof->keterangan }}
              </p>
              <a href="{{ route('page.detail', $prof->slug) }}" class="btn-cyber-outline py-2 px-3 text-center w-100">Detail Bidang</a>
            </div>
          </div>
        @empty
          <div class="col-12 text-center text-secondary">
            <p>Data profil bidang belum tersedia.</p>
          </div>
        @endforelse
      </div>

    </div>
  </section>

  <!-- ==========================================
       FEATURE SECTION (PROGRAM & KEGIATAN)
       ========================================== -->
  <section id="features" class="section-dark">
    <div class="container">
      
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Program & Kegiatan</h2>
        <p>pemenuhan hak atas informasi publik dan program digitalisasi unggulan daerah</p>
      </div>

      <div class="row g-4">
        @forelse($program as $index => $sal)
          <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="{{ $index * 150 }}">
            <div class="feature-card">
              <div class="feature-icon-circle">
                <i class="fa {{ $sal->icon ?? 'fa-info-circle' }}"></i>
              </div>
              <h4 class="text-white mt-3">{{ $sal->nama }}</h4>
              <p class="text-secondary small mb-3">
                {{ $sal->keterangan }}
              </p>
              <a href="{{ route('page.detail', $sal->slug) }}" class="text-cyan font-subtitle fw-bold">Detail Program <i class="bi bi-chevron-right small"></i></a>
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
       SECTION BERITA (CAROUSEL WITH SWIPER)
       ========================================== -->
  <section id="blog-posts" class="section-light-dark">
    <div class="container">

      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Berita & Artikel</h2>
        <p>Berita, artikel, dan rilis pers seputar kegiatan Pemkab Indragiri Hulu</p>
      </div>

      <!-- Swiper Container -->
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
        <!-- Pagination -->
        <div class="swiper-pagination berita-swiper-pagination mt-4"></div>
      </div>

      <div class="text-center mt-5" data-aos="fade-up">
        <a href="{{ url('berita') }}" class="btn-cyber-outline"><i class="bi bi-newspaper"></i> Lihat Semua Berita</a>
      </div>

    </div>
  </section>

  <!-- ==========================================
       SECTION ORGANISASI / TEAM
       ========================================== -->
  <section id="team" class="section-dark">
    <div class="container">
      
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Struktur Organisasi</h2>
        <p>Susunan pimpinan dan aparatur dinas di lingkungan Pemerintah Kabupaten Indragiri Hulu</p>
      </div>

      <div class="swiper teamSwiper" data-aos="fade-up" data-aos-delay="200">
        <div class="swiper-wrapper">
          @forelse($struktur as $member)
            @php
              $memberImg = $member->foto_url;
              $dep = $member->gelar_depan ? $member->gelar_depan . ' ' : '';
              $bel = $member->gelar_belakang ? ', ' . $member->gelar_belakang : '';
              $fullName = $dep . $member->nama . $bel;
              $jabatanStr = $member->jabatanNama ? $member->jabatanNama->nama : '-';
              $pangkatStr = $member->pangkat ? $member->pangkat->nama : '';
              $statusStr = $member->statusPegawai ? $member->statusPegawai->nama : '';
              $subBadge = $pangkatStr ? $pangkatStr . ' (' . $statusStr . ')' : $statusStr;
            @endphp
            <div class="swiper-slide">
              <div class="leader-image-card mx-auto" style="border: 2px solid {{ $member->jabatan_styling['border'] }}; box-shadow: 0 5px 20px {{ $member->jabatan_styling['glow'] }}; background: {{ $member->jabatan_styling['gradient'] }};">
                <img src="{{ $memberImg }}" alt="{{ $fullName }}" class="img-fluid w-100" style="aspect-ratio: 3/4; object-fit: cover; background: rgba(255, 255, 255, 0.05);">
                <div class="leader-badge" style="border-top: 1px solid rgba(255, 255, 255, 0.1);">
                  <h5>{{ $fullName }}</h5>
                  <span style="color: {{ $member->jabatan_styling['theme_color'] }};">{{ $jabatanStr }}</span>
                  <div class="small text-muted mt-1">{{ $subBadge }}</div>
                </div>
              </div>
            </div>
          @empty
            <div class="swiper-slide">
              <div class="text-center text-secondary py-5">
                <p>Data pegawai belum diunggah.</p>
              </div>
            </div>
          @endforelse
        </div>
        <!-- Add arrows and pagination -->
        <div class="swiper-pagination team-swiper-pagination mt-4"></div>
      </div>

    </div>
  </section>

  <!-- ==========================================
       SECTION PENGHARGAAN / FAQ
       ========================================== -->
  <section id="faq" class="section-light-dark">
    <div class="container">
      
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Penghargaan & Prestasi</h2>
        <p>Apresiasi atas komitmen digitalisasi, pelayanan publik terpadu, dan transparansi data</p>
      </div>

      <div class="row align-items-center">
        <!-- FAQ/Penghargaan Accordion -->
        <div class="col-lg-7 order-2 order-lg-1" data-aos="fade-right">
          <div class="faq-container">
            @forelse($penghargaan as $faqIndex => $faq)
              @php
                $faqImg = $faq->getfilebyalias('gambar_penghargaan') ? url($faq->getfilebyalias('gambar_penghargaan')->public_stream) : '';
              @endphp
              <div class="faq-item glass-card mb-3 {{ $faqIndex === 0 ? 'faq-active' : '' }}" data-img-src="{{ $faqImg }}">
                <h3>
                  <i class="bi bi-trophy text-gold me-2"></i>
                  {{ $faq->nama }}
                </h3>
                <div class="faq-content">
                  <p class="text-secondary mb-0">{{ $faq->desc }}</p>
                </div>
                <i class="faq-toggle bi bi-chevron-right"></i>
              </div>
            @empty
              <div class="text-secondary">
                <p>Belum ada penghargaan terdaftar.</p>
              </div>
            @endforelse
          </div>
        </div>

        <!-- Awards Visual Display -->
        <div class="col-lg-5 order-1 order-lg-2 mb-4 mb-lg-0 text-center" data-aos="fade-left" data-aos-delay="200">
          @php
            $firstAward = $penghargaan->first();
            $defaultAwardImg = $firstAward && $firstAward->getfilebyalias('gambar_penghargaan') 
              ? url($firstAward->getfilebyalias('gambar_penghargaan')->public_stream) 
              : 'https://via.placeholder.com/500x350?text=Penghargaan+Diskominfotik';
          @endphp
          <div class="leader-image-card mx-auto shadow-lg">
            <img id="faq-image" src="{{ $defaultAwardImg }}" class="img-fluid" alt="Award Display">
            <div class="leader-badge">
              <h5>Apresiasi & Komitmen</h5>
              <span>Kabupaten Indragiri Hulu</span>
            </div>
          </div>
        </div>
      </div>

    </div>
  </section>

  <!-- ==========================================
       SECTION GALERI (PORTFOLIO WITH FILTER TABS)
       ========================================== -->
  <section id="portfolio" class="section-dark">
    <div class="container">
      
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Galeri Foto Kegiatan</h2>
        <p>Dokumentasi visual kegiatan dan pembangunan di Kabupaten Indragiri Hulu</p>
      </div>

      <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
        
        <!-- Filter Tabs -->
        @php
          $allCategories = collect();
          foreach($galeri as $item) {
            $categories = explode(',', $item->kategori);
            foreach ($categories as $cat) {
              if(trim($cat) !== '') {
                $allCategories->push(trim($cat));
              }
            }
          }
          $uniqueCategories = $allCategories->unique()->sort()->values();
        @endphp

        <ul class="portfolio-filters" data-aos="fade-up" data-aos-delay="100">
          <li data-filter="*" class="filter-active">Semua</li>
          @foreach($uniqueCategories as $category)
            @php
              $filterClass = 'filter-' . strtolower(str_replace(' ', '-', trim($category)));
            @endphp
            <li data-filter=".{{ $filterClass }}">{{ $category }}</li>
          @endforeach
        </ul>

        <!-- Portfolio Items -->
        <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">
          @forelse($galeri as $foto)
            @php
              $imgSrc = $foto->getfilebyalias('logo') ? url($foto->getfilebyalias('logo')->public_stream) : '';
              $categories = explode(',', $foto->kategori);
              $filterClasses = collect($categories)
                ->filter(fn($cat) => trim($cat) !== '')
                ->map(fn($cat) => 'filter-' . strtolower(str_replace(' ', '-', trim($cat))))
                ->implode(' ');
              
              // GLightbox group name based on first category
              $firstCat = $categories[0] ?? 'default';
              $galleryGroup = 'gallery-' . strtolower(str_replace(' ', '-', trim($firstCat)));
            @endphp

            <div class="col-lg-4 col-md-6 portfolio-item isotope-item {{ $filterClasses }}">
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
            <div class="col-12 text-center text-secondary">
              <p>Belum ada foto galeri terdaftar.</p>
            </div>
          @forelse($galeri as $foto) @empty @endforelse
          @endforelse
        </div>

      </div>

      <div class="text-center mt-5" data-aos="fade-up">
        <a href="{{ url('galeri') }}" class="btn-cyber-outline"><i class="bi bi-images"></i> Galeri Selengkapnya</a>
      </div>

    </div>
  </section>

  <!-- ==========================================
       SECTION UNDUHAN (SWIPER CAROUSEL)
       ========================================== -->
  <section id="unduhan" class="section-light-dark">
    <div class="container">
      
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Unduhan Dokumen</h2>
        <p>Pusat berkas, formulir, regulasi, dan laporan resmi Diskominfotik Kabupaten Indragiri Hulu</p>
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
       TAUTAN / LINKS SECTION (GRAY SCALE HOVER)
       ========================================== -->
  <section id="clients" class="section-dark">
    <div class="container">
      
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Tautan Terkait</h2>
        <p>Konektivitas portal dengan kementerian, lembaga, dan instansi daerah</p>
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
        <h2>Ulasan Pengguna</h2>
        <p>Apresiasi dan saran yang dikirimkan oleh pengguna layanan informasi kami</p>
      </div>

      <div class="swiper testimonialsSwiper" data-aos="fade-up" data-aos-delay="200">
        <div class="swiper-wrapper">
          @forelse($testimoni as $testi)
            @php
              $fileTesti = $testi->getfilebyalias('gambar_testimoni');
              $testiImg = $fileTesti ? url($fileTesti->public_stream) : 'https://via.placeholder.com/150?text=User';
              
              // Map testionial label to rating
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
                <h5 class="text-white mb-1">{{ $testi->nama }}</h5>
                <span class="text-muted small mb-2 text-capitalize">{{ $testi->keterangan }}</span>
                
                <div class="testi-rating mb-3">
                  @for($i = 1; $i <= 5; $i++)
                    <i class="bi {{ $i <= $ratingVal ? 'bi-star-fill' : 'bi-star' }} fs-6"></i>
                  @endfor
                </div>
                
                <p class="text-secondary small mb-0 flex-grow-1">
                  <i class="bi bi-quote quote-icon-left text-success me-1"></i>
                  {{ $testi->desc }}
                  <i class="bi bi-quote quote-icon-right text-success ms-1"></i>
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

<!-- FAQ Interactive Image Changing Script -->
@push('scripts')
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const faqItems = document.querySelectorAll(".faq-item");
    const faqImage = document.getElementById("faq-image");

    faqItems.forEach(item => {
      item.addEventListener("click", function () {
        const newImg = this.getAttribute("data-img-src");
        if (newImg && faqImage) {
          // Add transition effect
          faqImage.style.opacity = 0;
          setTimeout(() => {
            faqImage.src = newImg;
            faqImage.style.opacity = 1;
          }, 300);
        }
        
        // Remove active class from others and add to current
        faqItems.forEach(i => i.classList.remove("faq-active"));
        this.classList.add("faq-active");
      });
    });
  });
</script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    const laptop = document.querySelector(".laptop-container");
    const typingEl = document.querySelector(".typing-text");
    
    // Dynamic data from database
    const folderData = [
      @foreach($program as $prog)
      { 
        name: '{!! addslashes($prog->nama) !!}', 
        icon: '{!! addslashes($prog->icon ?? "bi bi-folder-fill") !!}' 
      },
      @endforeach
    ];

    if (!laptop || !typingEl) return;

    // Start the animation loop
    runAnimationLoop();

    function runAnimationLoop() {
      // Clear folders and screen content from previous runs
      const container = document.querySelector(".folders-outer-container");
      if (container) container.innerHTML = '';
      typingEl.innerHTML = '';
      laptop.classList.remove("open");

      // 1. Open laptop lid after 1 second
      setTimeout(() => {
        laptop.classList.add("open");
        
        // 2. Start typing effect after lid opens (1.2s delay)
        setTimeout(() => {
          const fullText = "Selamat Datang di Website Resmi<br>Dinas Komunikasi Informatika dan Statistik<br>Kabupaten Indragiri Hulu";
          typeWriter(typingEl, fullText, 30, () => {
            
            // 3. Trigger folder flight when typing finishes
            triggerFolderFlight((activeFolders) => {
              
              // 4. Hold folders floating for 40 seconds
              setTimeout(() => {
                
                // 5. Start fading out folders AND close laptop simultaneously
                fadeFoldersOutTogether(activeFolders);
                
                // Fade out screen text faster and trigger lid close
                typingEl.style.transition = "opacity 0.6s ease";
                typingEl.style.opacity = 0;
                laptop.classList.remove("open");
                
                // 6. Wait for closing animation to finish (1.5s), then clean up and schedule restart
                setTimeout(() => {
                  typingEl.style.opacity = 1;
                  typingEl.innerHTML = '';
                  
                  // 7. Wait 4 seconds and restart loop
                  setTimeout(runAnimationLoop, 4000);
                }, 1500);
                
              }, 40000); // Float duration
              
            });
          });
        }, 1200);
      }, 1000);
    }

    function typeWriter(element, text, speed, callback) {
      let i = 0;
      element.innerHTML = '';
      
      // Ensure cursor is visible
      const cursor = document.querySelector(".terminal-cursor");
      if (cursor) cursor.style.display = "inline-block";

      function type() {
        if (i < text.length) {
          // If we encounter an HTML tag, append it entirely at once
          if (text.charAt(i) === '<') {
            let tag = '';
            while (i < text.length && text.charAt(i) !== '>') {
              tag += text.charAt(i);
              i++;
            }
            tag += '>';
            i++; // skip the '>'
            element.innerHTML += tag;
          } else {
            element.innerHTML += text.charAt(i);
            i++;
          }
          setTimeout(type, speed);
        } else {
          if (callback) callback();
        }
      }
      type();
    }

    function triggerFolderFlight(callback) {
      const container = document.querySelector(".folders-outer-container");
      const laptopScreen = document.querySelector(".laptop-screen");
      if (!container || !laptopScreen) return;

      const isMobile = window.innerWidth < 992;
      const containerRect = container.getBoundingClientRect();
      const screenRect = laptopScreen.getBoundingClientRect();

      // Calculate center of laptop screen relative to the outer folder container
      const startX = ((screenRect.left + screenRect.width / 2) - containerRect.left) / containerRect.width * 100;
      const startY = ((screenRect.top + screenRect.height / 2) - containerRect.top) / containerRect.height * 100;

      const activeFolders = [];
      let spawnedCount = 0;

      folderData.forEach((data, index) => {
        setTimeout(() => {
          // If the loop was reset mid-run, check if laptop is still open
          if (!laptop.classList.contains("open")) return;

          const folder = document.createElement("div");
          const colorIndex = index % 10;
          folder.className = `floating-folder color-${colorIndex}`;
          
          // Icon handling
          const iconClass = data.icon.includes('fa-') ? `fa ${data.icon}` : data.icon;
          
          folder.innerHTML = `
            <div class="folder-tab"></div>
            <div class="folder-body">
              <i class="${iconClass} folder-icon"></i>
              <span class="folder-text">${data.name}</span>
            </div>
          `;

          // Position coordinates
          const targetPos = getFolderCoordinates(index, isMobile);
          
          // Randomizations for bobbing effect
          const randomRot = (Math.random() * 20 - 10).toFixed(1); // -10deg to 10deg
          const bobDuration = (Math.random() * 2.5 + 3.5).toFixed(1) + 's'; // 3.5s to 6s
          const bobAmp = (Math.random() * 8 + 6).toFixed(0) + 'px'; // 6px to 14px
          const bobRot = (Math.random() * 6 - 3).toFixed(1) + 'deg'; // -3deg to 3deg

          // Set inline variables
          folder.style.setProperty("--start-x", `${startX}%`);
          folder.style.setProperty("--start-y", `${startY}%`);
          folder.style.setProperty("--target-x", `${targetPos.x}%`);
          folder.style.setProperty("--target-y", `${targetPos.y}%`);
          folder.style.setProperty("--random-rotate", `${randomRot}deg`);
          folder.style.setProperty("--bob-duration", bobDuration);
          folder.style.setProperty("--bob-amplitude", bobAmp);
          folder.style.setProperty("--bob-rotate", bobRot);

          container.appendChild(folder);
          activeFolders.push(folder);
          
          // Add flying class to trigger flight
          folder.classList.add("flying");

          // Switch to bobbing/floating once flight completes
          folder.addEventListener("animationend", function handler(e) {
            if (e.animationName === "flyOutFolder") {
              folder.classList.remove("flying");
              folder.classList.add("bobbing");
              // Set static coordinates
              folder.style.left = `${targetPos.x}%`;
              folder.style.top = `${targetPos.y}%`;
              folder.removeEventListener("animationend", handler);
            }
          });

          spawnedCount++;
          if (spawnedCount === folderData.length && callback) {
            // Callback when all folders have spawned and finished flying (2.5s flight time)
            setTimeout(() => {
              callback(activeFolders);
            }, 2500);
          }
        }, index * 400); // staggered entrance
      });
    }

    function fadeFoldersOutTogether(folders, callback) {
      let count = folders.length;
      if (count === 0) {
        if (callback) callback();
        return;
      }

      folders.forEach((folder) => {
        // Verify folder is still in DOM
        if (folder.parentNode) {
          folder.style.transition = "all 1.5s cubic-bezier(0.25, 1, 0.5, 1)";
          folder.style.opacity = "0";
          folder.style.transform = "scale(0) translateY(-30px)";
          
          setTimeout(() => {
            folder.remove();
            count--;
            if (count === 0 && callback) {
              callback();
            }
          }, 1500);
        } else {
          count--;
          if (count === 0 && callback) {
            callback();
          }
        }
      });
    }

    function getFolderCoordinates(index, isMobile) {
      const desktopCoords = [
        { x: 4, y: 15 },   // ppid
        { x: 14, y: 20 },  // bakohumas
        { x: 5, y: 36 },   // swaifm
        { x: 15, y: 48 },  // livestreaming
        { x: 24, y: 30 },  // aplikasi
        { x: 6, y: 65 },   // sandiman
        { x: 16, y: 60 },  // csirt
        { x: 25, y: 45 },  // website
        { x: 75, y: 45 },  // pemdi
        { x: 84, y: 60 },  // statisti
        { x: 94, y: 65 },  // pasti
        { x: 76, y: 30 },  // media
        { x: 85, y: 48 },  // pers
        { x: 95, y: 36 },  // blankspot
        { x: 86, y: 20 },  // infra
        { x: 96, y: 15 },  // server
        { x: 32, y: 80 },  // lapor
        { x: 68, y: 80 },  // sosmed
        { x: 50, y: 86 }   // inhukab
      ];

      if (!isMobile) {
        return desktopCoords[index] || { x: Math.floor(Math.random() * 90) + 5, y: Math.floor(Math.random() * 80) + 10 };
      }

      const mobileCoords = [
        { x: 6, y: 15 },   // ppid
        { x: 84, y: 15 },  // bakohumas
        { x: 8, y: 28 },   // swaifm
        { x: 82, y: 28 },  // livestreaming
        { x: 10, y: 42 },  // aplikasi
        { x: 80, y: 42 },  // sandiman
        { x: 8, y: 56 },   // csirt
        { x: 82, y: 56 },  // website
        { x: 12, y: 70 },  // pemdi
        { x: 78, y: 70 },  // statisti
        { x: 45, y: 12 },  // pasti
        { x: 28, y: 16 },  // media
        { x: 62, y: 16 },  // pers
        { x: 25, y: 90 },  // blankspot
        { x: 65, y: 90 },  // infra
        { x: 45, y: 92 },  // server
        { x: 4, y: 82 },   // lapor
        { x: 86, y: 82 },  // sosmed
        { x: 45, y: 50 }   // inhukab
      ];
      
      return mobileCoords[index] || { x: Math.floor(Math.random() * 80) + 10, y: Math.floor(Math.random() * 80) + 10 };
    }
  });
</script>
@endpush
@endsection
