    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Kontak</h2>
        <p>Informasi kontak dan statistik kunjungan Website PPID Kabupaten Indragiri Hulu</p>
      </div><!-- End Section Title -->

      <div class="container mb-5" data-aos="fade-up" data-aos-delay="100">

        <div class="row gy-4">

          {{-- ================================================================
               KOLOM KIRI: STATISTIK KUNJUNGAN (col-md-4)
               ================================================================ --}}
          <div class="col-lg-4" data-aos="fade-right" data-aos-delay="150">

            {{-- Header Statistik --}}
            <div class="vstats-header mb-3">
              <h3 class="vstats-title">
                <i class="bi bi-bar-chart-line-fill"></i>
                Statistik Kunjungan
              </h3>
              <p class="vstats-subtitle">Data kunjungan website secara real-time</p>
            </div>

            {{-- Grid 2x2: 4 kartu statistik --}}
            <div class="vstats-grid">

              <div class="vstat-card" data-aos="zoom-in" data-aos-delay="200">
                <div class="vstat-icon">
                  <i class="bi bi-calendar-day-fill"></i>
                </div>
                <div class="vstat-body">
                  <span class="vstat-number">{{ number_format($visitorStats['hari_ini'] ?? 0) }}</span>
                  <span class="vstat-label">Hari Ini</span>
                </div>
              </div>

              <div class="vstat-card" data-aos="zoom-in" data-aos-delay="250">
                <div class="vstat-icon">
                  <i class="bi bi-calendar-month-fill"></i>
                </div>
                <div class="vstat-body">
                  <span class="vstat-number">{{ number_format($visitorStats['bulan_ini'] ?? 0) }}</span>
                  <span class="vstat-label">Bulan Ini</span>
                </div>
              </div>

              <div class="vstat-card" data-aos="zoom-in" data-aos-delay="300">
                <div class="vstat-icon">
                  <i class="bi bi-calendar-check-fill"></i>
                </div>
                <div class="vstat-body">
                  <span class="vstat-number">{{ number_format($visitorStats['tahun_ini'] ?? 0) }}</span>
                  <span class="vstat-label">Tahun Ini</span>
                </div>
              </div>

              <div class="vstat-card" data-aos="zoom-in" data-aos-delay="350">
                <div class="vstat-icon">
                  <i class="bi bi-calendar2-x-fill"></i>
                </div>
                <div class="vstat-body">
                  <span class="vstat-number">{{ number_format($visitorStats['tahun_lalu'] ?? 0) }}</span>
                  <span class="vstat-label">Tahun Lalu</span>
                </div>
              </div>

            </div>{{-- /vstats-grid --}}

            {{-- Info Pengunjung --}}
            <div class="vvisitor-info mt-3" data-aos="fade-up" data-aos-delay="400">
              <div class="vvisitor-info-title">
                <i class="bi bi-person-fill-check"></i> Info Pengunjung Saat Ini
              </div>
              <ul class="vvisitor-info-list">
                <li>
                  <i class="bi bi-globe2"></i>
                  <span class="vi-label">IP Publik</span>
                  <strong class="vi-value">{{ $visitorInfo['ip'] ?? '-' }}</strong>
                </li>
                <li>
                  <i class="bi bi-browser-chrome"></i>
                  <span class="vi-label">Browser</span>
                  <strong class="vi-value">{{ $visitorInfo['browser'] ?? '-' }}</strong>
                </li>
                <li>
                  <i class="bi bi-laptop-fill"></i>
                  <span class="vi-label">Sistem Operasi</span>
                  <strong class="vi-value">{{ $visitorInfo['os'] ?? '-' }}</strong>
                </li>
              </ul>
            </div>

          </div>{{-- /col-lg-4 --}}

          {{-- ================================================================
               KOLOM KANAN: KONTAK (col-md-8)
               ================================================================ --}}
          <div class="col-lg-8" data-aos="fade-left">

          {{-- Header Kontak --}}
            <div class="vstats-header mb-3">
              <h3 class="vstats-title">
                <i class="bi bi-info-circle-fill"></i>
                Informasi Kontak
              </h3>
              <p class="vstats-subtitle">Informasi kontak dan statistik kunjungan Website PPID Kabupaten Indragiri Hulu</p>
            </div>

          {{-- Info Kontak dalam baris --}}
            <div class="row g-3">
              <div class="col-md-6">
                <div class="info-item d-flex" data-aos="fade-up">
                  <i class="bi bi-geo-alt flex-shrink-0"></i>
                  <div>
                    <h3>Alamat :</h3>
                    <p>{{ $pengaturan->alamat }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-item d-flex" data-aos="fade-up">
                  <i class="bi bi-telephone flex-shrink-0"></i>
                  <div>
                    <h3>Telepon :</h3>
                    <p>{{ $pengaturan->telepon }}</p>
                  </div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
                  <i class="bi bi-envelope flex-shrink-0"></i>
                  <div>
                    <h3>Email :</h3>
                    <p>{{ $pengaturan->email }}</p>
                  </div>
                </div>
              </div>
            </div>{{-- /row kontak --}}


            {{-- Peta --}}
            <div class="mb-4" data-aos="fade-up">
              <iframe style="border:0; width: 100%; height: 280px; border-radius: 8px;"
                src="{{ $pengaturan->peta }}"
                frameborder="0"
                allowfullscreen=""
                loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div><!-- End Google Maps -->
            
          </div>{{-- /col-lg-8 --}}

        </div>{{-- /row --}}

      </div>

    </section><!-- /Contact Section -->

  <footer id="footer" class="footer dark-background">
    <div class="container">
      <h3 class="sitename">{{ $pengaturan->judul }}</h3>
      <p>{{ $pengaturan->subjudul }}</p>
      <div class="social-links d-flex justify-content-center">
        <a href="{{ $pengaturan->facebook }}" target="_blank"><i class="bi bi-facebook"></i></a>
        <a href="{{ $pengaturan->instagram }}" target="_blank"><i class="bi bi-instagram"></i></a>
        <a href="{{ $pengaturan->twiter }}" target="_blank"><i class="bi bi-twitter-x"></i></a>
        <a href="{{ $pengaturan->tiktok }}" target="_blank"><i class="bi bi-tiktok"></i></a>
        <a href="{{ $pengaturan->youtube }}" target="_blank"><i class="bi bi-youtube"></i></a>
      </div>
      <div class="container">
        <div class="copyright">
          <span>{{ date('Y') }} &copy; Copyright</span>
          <strong class="px-1 sitename">PPID Indragiri Hulu</strong>
          <span>All Rights Reserved</span>
        </div>
        <div class="credits">
          Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Mobile Bottom Menu Start -->
  <nav class="mobile-bottom-nav d-lg-none" aria-label="Mobile Navigation">

    <!-- Item: Survey -->
    <a href="https://skm.inhukab.go.id/home/ques/27" class="mbn-item">
      <span class="mbn-icon-wrap">
        <i class="fa fa-user"></i>
      </span>
      <span class="mbn-label">Survey</span>
    </a>

    <!-- Item: Ulasan -->
    <a href="{{ url('ulasan') }}" class="mbn-item">
      <span class="mbn-icon-wrap">
        <i class="fa fa-star"></i>
      </span>
      <span class="mbn-label">Ulasan</span>
    </a>

    <!-- Item: Beranda (CENTER - elevated circle) -->
    <a href="{{ url('/') }}" class="mbn-item mbn-center">
      <span class="mbn-center-circle">
        <i class="fa fa-home"></i>
      </span>
      <span class="mbn-label">Beranda</span>
    </a>

    <!-- Item: e-Tamu -->
    <a href="{{ url('tamu') }}" class="mbn-item">
      <span class="mbn-icon-wrap">
        <i class="fa fa-desktop"></i>
      </span>
      <span class="mbn-label">e-Tamu</span>
    </a>

    <!-- Item: Login -->
    <a href="{{ url('login') }}" class="mbn-item">
      <span class="mbn-icon-wrap">
        <i class="fa fa-sign-in"></i>
      </span>
      <span class="mbn-label">Login</span>
    </a>

  </nav>
  <!-- Mobile Bottom Menu End -->



