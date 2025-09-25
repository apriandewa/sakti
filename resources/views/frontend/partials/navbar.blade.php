<header id="header" class="header sticky-top">

    <div class="topbar d-flex align-items-center">
      <div class="container d-flex justify-content-center justify-content-md-between">
        <div class="contact-info d-flex align-items-center">
          <i class="bi bi-envelope d-flex align-items-center"><a href="mailto:contact@example.com">contact@example.com</a></i>
          <i class="bi bi-phone d-flex align-items-center ms-4"><span>+1 5589 55488 55</span></i>
        </div>
        <div class="social-links d-none d-md-flex align-items-center">
          <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
          <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
          <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
          <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
        </div>
      </div>
    </div><!-- End Top Bar -->

    <div class="branding d-flex align-items-cente">

      <div class="container position-relative d-flex align-items-center justify-content-between">
        <a href="/" class="d-flex align-items-center">
          <!-- Uncomment the line below if you also wish to use an image logo -->
          <i class="dark-logo"><img src="{{ url($template).config('master.app.web.logo_dark')}}" alt="logo" width="140"></i>
          <!-- <img src="reveal/assets/img/logo.png" alt=""> -->
          <!-- <h1 class="sitename">PPID INHU</h1> -->
        </a>

        <nav id="navmenu" class="navmenu">
          <ul>
            <li><a href="/" class="active">Beranda</a></li>
            <li class="dropdown"><a href="{{ url('/#services') }}"><span>Profil</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                @foreach($pagemenu as $p)
                <li><a href="{{ url('page/'.$p->slug) }}">{{ $p->nama }}</a></li>
                @endforeach
              </ul>
            </li>
            <li><a href="{{ url('/#team') }}">Struktur</a></li>
            <li class="dropdown"><a href="{{ url('/informasi') }}"><span>DIP</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                
                <li><a href="{{ url('berkala') }}">Informasi Berkala</a></li>
                <li><a href="{{ url('serta-merta') }}">Informasi Serta Merta</a></li>
                <li><a href="{{ url('setiap-saat') }}">Informasi Tersedia Setiap Saat</a></li>
                <li><a href="{{ url('dikecualikan') }}">Informasi dikecualikan</a></li>
                
              </ul>
            </li>
            <li class="dropdown"><a href="{{ url('/#features') }}"><span>Akses Informasi</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                @foreach($saluranmenu as $s)
                <li><a href="{{ url('page/'.$s->slug) }}">{{ $s->nama }}</a></li>
                @endforeach
              </ul>
            </li>
            <li><a href="{{ url('/#blog-posts') }}">Berita</a></li>
            <li><a href="{{ url('/#portfolio') }}">Galeri</a></li>
            <li class="dropdown"><a href="{{ url('/#unduhan') }}"><span>Unduhan</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                @foreach($unduhanmenu as $u)
                @php
                    $slugUnduhan = str_replace(' ', '-', strtolower($u));
                @endphp
                <li><a href="{{ url('unduhan') }}?kategori={{ $slugUnduhan }}">{{ $u }}</a></li>
                @endforeach
              </ul>
            </li>
            <li><a href="#contact">Contact</a></li>
            <li>
              <a href="{{ route('login') }}" class="btn btn-primary btn-sm d-flex text-white align-items-center px-2 py-1" style="gap: 3px;">
              <i class="bi bi-box-arrow-in-right"></i>
              <span>Masuk</span>
              </a>
            </li>
          </ul>
          <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>

      </div>

    </div>

  </header>
