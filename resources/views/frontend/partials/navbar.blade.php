<header id="header" class="header sticky-top">
        <!--Top Bar -->
        <div class="topbar d-none d-md-flex align-items-center">
          <div class="container d-flex align-items-center justify-content-between">

            <!-- KIRI (running text) -->
            <div class="contact-info d-flex align-items-center flex-grow-1 me-3">
              <div class="d-flex align-items-center w-100 px-3" style="overflow:hidden; height:40px;">

                <div id="tanggal-box"
                    class="tanggal-box d-inline-flex align-items-center px-3 me-3">
                  <i class="bi bi-calendar-date-fill me-2 text-white"></i>
                  <span id="tanggal-text"></span>
                </div>

                <div class="flex-grow-1 position-relative overflow-hidden" style="height:100%;">
                  <ul id="info-list" class="list-unstyled m-0 p-0 position-relative h-100">
                    @forelse($navTicker as $ticker)
                    <li class="position-absolute w-100">
                      <a href="{{ $ticker['url'] }}" class="text-dark text-decoration-none text-white">{{ $ticker['label'] }}</a>
                    </li>
                    @empty
                    <li class="position-absolute w-100">Selamat Datang di Website Resmi</li>
                    @endforelse
                  </ul>
                </div>

              </div>
            </div>

            <!-- KANAN (social links) -->
            <div class="social-links d-none d-md-flex align-items-center">
              <div class="d-inline-flex align-items-center h-100">
                <a href="https://skm.inhukab.go.id/home/ques/27"><small class="me-3 text-dark"><i class="fa fa-user text-primary me-2"></i>Survey</small></a>
                <a href="{{ url('ulasan') }}"><small class="me-3 text-dark"><i class="fa fa-star text-primary me-2"></i>Ulasan</small></a>
                <a href="{{ url('tamu') }}"><small class="me-3 text-dark"><i class="fa fa-desktop text-primary me-2"></i>e-Tamu</small></a>
                <a href="{{ url('login') }}"><small class="text-dark"><i class="fa fa-sign-in text-primary me-2"></i>Login</small></a>
              </div>
            </div>

          </div>
        </div><!-- End Top Bar -->

        <div class="branding d-flex align-items-cente">

          <div class="container position-relative d-flex align-items-center justify-content-between">
            <a href="{{ url('/') }}" class="d-flex align-items-center">
              <!-- Uncomment the line below if you also wish to use an image logo -->
              <i class="dark-logo"><img src="{{ url($template).config('master.app.web.logo_dark')}}" alt="logo" width="140"></i>
              <!-- <img src="reveal/assets/img/logo.png" alt=""> -->
              <!-- <h1 class="sitename">PPID INHU</h1> -->
            </a>

            <nav id="navmenu" class="navmenu">
              <ul>
                <li><a href="{{ url('/') }}" class="active">Beranda</a></li>
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
                    <li><a href="{{ url('tersedia') }}">Informasi Serta Merta</a></li>
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
                <li><a href="{{ url('/#contact') }}">Hubungi Kami</a></li>
              </ul>
              <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
            </nav>

          </div>

        </div>

      </header>