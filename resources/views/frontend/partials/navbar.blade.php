<header id="header" class="header sticky-top">
  <!-- Top Bar -->
  <div class="topbar d-none d-md-flex align-items-center">
    <div class="container d-flex align-items-center justify-content-between">

      <!-- Left: Date & Ticker -->
      <div class="contact-info d-flex align-items-center flex-grow-1 me-3" style="overflow: hidden;">
        <div id="tanggal-box" class="tanggal-box d-inline-flex align-items-center px-3 py-1 me-3">
          <i class="bi bi-calendar-date me-2"></i>
          <span id="tanggal-text"></span>
        </div>

        <div class="ticker-container flex-grow-1 position-relative overflow-hidden" style="height: 30px;">
          <ul id="info-list" class="list-unstyled m-0 p-0 position-relative h-100">
            @forelse($navTicker as $ticker)
              <li class="position-absolute w-100 d-flex align-items-center h-100" style="transition: all 0.5s ease;">
                <a href="{{ $ticker['url'] }}" class="ticker-item text-decoration-none">{{ $ticker['label'] }}</a>
              </li>
            @empty
              <li class="position-absolute w-100 d-flex align-items-center h-100">
                <span class="ticker-item">Selamat Datang di Portal SAKTI Kabupaten Indragiri Hulu</span>
              </li>
            @endforelse
          </ul>
        </div>
      </div>

      <!-- Right: Shortcut Links -->
      <div class="topbar-shortcuts d-flex align-items-center gap-3">
        <a href="https://skm.inhukab.go.id/home/ques/27" target="_blank" class="text-decoration-none">
          <i class="bi bi-bar-chart-fill me-1"></i> Survey
        </a>
        <a href="{{ url('ulasan') }}" class="text-decoration-none">
          <i class="bi bi-star-fill me-1"></i> Ulasan
        </a>
        <a href="{{ url('tamu') }}" class="text-decoration-none">
          <i class="bi bi-person-badge-fill me-1"></i> e-Tamu
        </a>
        <a href="{{ url('login') }}" class="text-decoration-none">
          <i class="bi bi-box-arrow-in-right me-1"></i> Login
        </a>
      </div>

    </div>
  </div><!-- End Top Bar -->

  <!-- Main Navbar -->
  <div class="branding">
    <div class="container d-flex align-items-center justify-content-between">
      
      <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none">
        <img src="{{ url($template . config('master.app.web.logo_dark')) }}" alt="Logo" width="140" class="d-inline-block align-top" style="object-fit: contain;">
      </a>

      <nav id="navmenu" class="navmenu">
        <ul>
          <li><a href="{{ url('/') }}" class="active">Beranda</a></li>
          
          <li class="dropdown"><a href="#"><span>Profil</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              @foreach($pagemenu as $p)
                <li><a href="{{ url('page/' . $p->slug) }}">{{ $p->nama }}</a></li>
              @endforeach
            </ul>
          </li>
          
          <li><a href="{{ url('/cekkehadiran') }}">e-Presensi</a></li>
          <li><a href="{{ url('/kinerja') }}">e-Kinerja</a></li>
          <li><a href="{{ url('/prformance') }}">e-Performance</a></li>
          
          <li><a href="{{ url('/#blog-posts') }}">Berita</a></li>
          
          <li class="dropdown"><a href="#"><span>Unduhan</span> <i class="bi bi-chevron-down"></i></a>
            <ul>
              @foreach($unduhanmenu as $u)
                @php
                  $slugUnduhan = str_replace(' ', '-', strtolower($u));
                @endphp
                <li><a href="{{ url('unduhan') }}?kategori={{ $slugUnduhan }}">{{ $u }}</a></li>
              @endforeach
            </ul>
          </li>
          
          <li><a href="{{ url('/#contact') }}">Hubungi Kami</a></li>
        </ul>
      </nav>

      <div class="header-actions d-flex align-items-center gap-3">
        <button type="button" id="theme-toggle" class="btn-theme-toggle" aria-label="Toggle theme">
          <i class="bi bi-moon-stars-fill theme-icon-dark"></i>
          <i class="bi bi-sun-fill theme-icon-light"></i>
        </button>
        <i class="mobile-nav-toggle bi bi-list"></i>
      </div>

    </div>
  </div>
</header>