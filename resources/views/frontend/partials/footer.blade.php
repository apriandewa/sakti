<!-- Contact Section -->
<section id="contact" class="contact section-light-dark">

  <!-- Section Title -->
  <div class="container section-title text-center" data-aos="fade-up">
    <h2>Kontak & Statistik</h2>
    <p>Informasi Hubungi Kami dan Statistik Kunjungan Portal Resmi Diskominfotik</p>
  </div>

  <div class="container" data-aos="fade-up" data-aos-delay="100">
    <div class="row gy-4">

      <!-- Column Left: Visitor Stats -->
      <div class="col-lg-4 d-flex flex-column gap-4" data-aos="fade-right" data-aos-delay="150">
        
        <!-- Glass Card for Stats -->
        <div class="glass-card flex-grow-1">
          <h3 class="sidebar-title mb-4">
            <i class="bi bi-bar-chart-line-fill text-success me-2"></i>
            Statistik Kunjungan
          </h3>
          
          <div class="vstats-grid">
            <div class="vstat-card">
              <div class="vstat-icon">
                <i class="bi bi-calendar-day-fill"></i>
              </div>
              <div class="vstat-body">
                <span class="vstat-number">{{ number_format($visitorStats['hari_ini'] ?? 0) }}</span>
                <span class="vstat-label">Hari Ini</span>
              </div>
            </div>

            <div class="vstat-card">
              <div class="vstat-icon">
                <i class="bi bi-calendar-month-fill"></i>
              </div>
              <div class="vstat-body">
                <span class="vstat-number">{{ number_format($visitorStats['bulan_ini'] ?? 0) }}</span>
                <span class="vstat-label">Bulan Ini</span>
              </div>
            </div>

            <div class="vstat-card">
              <div class="vstat-icon">
                <i class="bi bi-calendar-check-fill"></i>
              </div>
              <div class="vstat-body">
                <span class="vstat-number">{{ number_format($visitorStats['tahun_ini'] ?? 0) }}</span>
                <span class="vstat-label">Tahun Ini</span>
              </div>
            </div>

            <div class="vstat-card">
              <div class="vstat-icon">
                <i class="bi bi-calendar2-x-fill"></i>
              </div>
              <div class="vstat-body">
                <span class="vstat-number">{{ number_format($visitorStats['tahun_lalu'] ?? 0) }}</span>
                <span class="vstat-label">Tahun Lalu</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Visitor Info Panel -->
        <div class="vvisitor-info">
          <div class="vvisitor-info-title">
            <i class="bi bi-person-fill-check text-cyan"></i> Info Pengunjung
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
              <i class="bi bi-laptop"></i>
              <span class="vi-label">Sistem Operasi</span>
              <strong class="vi-value">{{ $visitorInfo['os'] ?? '-' }}</strong>
            </li>
          </ul>
        </div>

      </div>

      <!-- Column Right: Contact Details & Map -->
      <div class="col-lg-8" data-aos="fade-left" data-aos-delay="200">
        <div class="glass-card h-100 d-flex flex-column justify-content-between gap-4">
          
          <div>
            <h3 class="sidebar-title mb-4">
              <i class="bi bi-info-circle-fill text-cyan me-2"></i>
              Informasi Hubungi Kami
            </h3>
            
            <div class="row g-4">
              <div class="col-md-6">
                <div class="contact-info-item">
                  <div class="contact-info-icon">
                    <i class="bi bi-geo-alt"></i>
                  </div>
                  <div>
                    <h5 class="text-white mb-1">Alamat :</h5>
                    <p class="mb-0 text-secondary" style="font-size: 0.9rem;">{{ $pengaturan->alamat ?? 'Alamat Diskominfotik' }}</p>
                  </div>
                </div>
              </div>
              
              <div class="col-md-3">
                <div class="contact-info-item">
                  <div class="contact-info-icon">
                    <i class="bi bi-telephone"></i>
                  </div>
                  <div>
                    <h5 class="text-white mb-1">Telepon :</h5>
                    <p class="mb-0 text-secondary" style="font-size: 0.9rem;">{{ $pengaturan->telepon ?? '-' }}</p>
                  </div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="contact-info-item">
                  <div class="contact-info-icon">
                    <i class="bi bi-envelope"></i>
                  </div>
                  <div>
                    <h5 class="text-white mb-1">Email :</h5>
                    <p class="mb-0 text-secondary" style="font-size: 0.9rem;">{{ $pengaturan->email ?? '-' }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Maps iframe -->
          @if(!empty($pengaturan->peta))
            <div class="contact-map-card flex-grow-1">
              <iframe 
                src="{{ $pengaturan->peta }}" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
              </iframe>
            </div>
          @endif

        </div>
      </div>

    </div>
  </div>
</section>

<!-- Footer -->
<footer id="footer" class="footer-top">
  <div class="container">
    <div class="row gy-4">
      
      <!-- Footer Brand Column -->
      <div class="col-lg-5 col-md-12 footer-brand">
        <h3 class="text-white">{{ $pengaturan->judul ?? config('master.app.profile.name') }}</h3>
        <p class="text-secondary">{{ $pengaturan->subjudul ?? config('master.app.profile.description') }}</p>
        
        <div class="footer-social-links">
          @if(!empty($pengaturan->facebook))
            <a href="{{ $pengaturan->facebook }}" target="_blank"><i class="bi bi-facebook"></i></a>
          @endif
          @if(!empty($pengaturan->instagram))
            <a href="{{ $pengaturan->instagram }}" target="_blank"><i class="bi bi-instagram"></i></a>
          @endif
          @if(!empty($pengaturan->twiter))
            <a href="{{ $pengaturan->twiter }}" target="_blank"><i class="bi bi-twitter-x"></i></a>
          @endif
          @if(!empty($pengaturan->tiktok))
            <a href="{{ $pengaturan->tiktok }}" target="_blank"><i class="bi bi-tiktok"></i></a>
          @endif
          @if(!empty($pengaturan->youtube))
            <a href="{{ $pengaturan->youtube }}" target="_blank"><i class="bi bi-youtube"></i></a>
          @endif
        </div>
      </div>

      <!-- Quick Links -->
      <div class="col-lg-3 col-6 footer-links">
        <h4 class="text-white">Tautan Cepat</h4>
        <ul>
          <li><a href="{{ url('/') }}"><i class="bi bi-chevron-right me-1 text-success"></i> Beranda</a></li>
          <li><a href="{{ url('/#services') }}"><i class="bi bi-chevron-right me-1 text-success"></i> Profil</a></li>
          <li><a href="{{ url('/#team') }}"><i class="bi bi-chevron-right me-1 text-success"></i> Struktur Organisasi</a></li>
          <li><a href="{{ url('/#blog-posts') }}"><i class="bi bi-chevron-right me-1 text-success"></i> Berita Terbaru</a></li>
        </ul>
      </div>

      <!-- Portal Services -->
      <div class="col-lg-4 col-6 footer-links">
        <h4 class="text-white">Layanan Informasi</h4>
        <ul>
          <li><a href="{{ url('/#portfolio') }}"><i class="bi bi-chevron-right me-1 text-success"></i> Galeri Kegiatan</a></li>
          <li><a href="{{ url('/unduhan') }}"><i class="bi bi-chevron-right me-1 text-success"></i> Pusat Unduhan Dokumen</a></li>
          <li><a href="{{ url('tamu') }}"><i class="bi bi-chevron-right me-1 text-success"></i> e-Tamu (Buku Tamu Online)</a></li>
          <li><a href="{{ url('ulasan') }}"><i class="bi bi-chevron-right me-1 text-success"></i> Kirim Ulasan Layanan</a></li>
        </ul>
      </div>

    </div>
  </div>
</footer>

<div class="footer-bottom">
  <div class="container text-center">
    <p class="mb-0">&copy; {{ date('Y') }} <strong>{{ config('master.app.profile.short_name') }} Indragiri Hulu</strong>. All Rights Reserved.</p>
  </div>
</div>

<!-- Mobile Bottom Navigation (Visible on Mobile only) -->
<nav class="mobile-bottom-nav d-lg-none" aria-label="Mobile Navigation">
  <a href="https://skm.inhukab.go.id/home/ques/27" target="_blank" class="mbn-item">
    <span class="mbn-icon-wrap"><i class="fa fa-user"></i></span>
    <span class="mbn-label">Survey</span>
  </a>

  <a href="{{ url('ulasan') }}" class="mbn-item">
    <span class="mbn-icon-wrap"><i class="fa fa-star"></i></span>
    <span class="mbn-label">Ulasan</span>
  </a>

  <a href="{{ url('/') }}" class="mbn-item mbn-center">
    <span class="mbn-center-circle"><i class="fa fa-home"></i></span>
    <span class="mbn-label">Beranda</span>
  </a>

  <a href="{{ url('tamu') }}" class="mbn-item">
    <span class="mbn-icon-wrap"><i class="fa fa-desktop"></i></span>
    <span class="mbn-label">e-Tamu</span>
  </a>

  <a href="{{ url('login') }}" class="mbn-item">
    <span class="mbn-icon-wrap"><i class="fa fa-sign-in"></i></span>
    <span class="mbn-label">Login</span>
  </a>
</nav>
