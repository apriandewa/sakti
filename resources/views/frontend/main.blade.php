<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <!-- Theme Initialization (prevents flash of dark/light theme) -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('theme') || 'dark';
      document.documentElement.setAttribute('data-theme', savedTheme);
    })();
  </script>
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@stack('title', $title ?? config('master.app.profile.name')) | {!! config('master.app.profile.short_name') !!}</title>
  <meta name="description" content="{{ $subjudul ?? config('master.app.profile.description') }}">
  <meta name="keywords" content="{{ config('master.app.profile.keywords') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Favicons -->
  <link rel="icon" href="{{ url($template . config('master.app.web.favicon')) }}">
  <link href="{{ url('portal/vendor/bootstrap-icons/apple-touch-icon.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts (Poppins, Space Grotesk, Outfit) — Non-blocking async load -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Poppins:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@400;500;600;700&display=swap"></noscript>
  
  <!-- Icon FontAwesome — CDN lebih cepat -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" crossorigin="anonymous">

  <!-- Vendor CSS Files -->
  <link href="{{ url('portal/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ url('portal/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ url('portal/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ url('portal/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ url('portal/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ url('portal/css/portal.css') }}" rel="stylesheet">
  
  @stack('css')
</head>

<body class="index-page">

  <!-- Particles Background -->
  <div id="particles-js"></div>

  @include('frontend.partials.navbar')

  @yield('container')

  @include('frontend.partials.footer')

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader">
    <div class="preloader-spinner"></div>
  </div>

  <!-- jQuery & Sweet Alert — defer agar tidak blocking render -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>

  <!-- Particles.js -->
  <script src="{{ url('portal/js/particles.min.js') }}" defer></script>

  <!-- Particles Initialization (Theme-Aware & Interactive) -->
  <script>
    (function() {
      function getParticlesConfig() {
        var isDark = (document.documentElement.getAttribute('data-theme') || 'dark') === 'dark';

        return {
          "particles": {
            "number": {
              "value": isDark ? 80 : 50,
              "density": {
                "enable": true,
                "value_area": 900
              }
            },
            "color": {
              "value": isDark
                ? ["#00f2fe", "#059669", "#f59e0b", "#06b6d4", "#10b981"]
                : ["#0284c7", "#059669", "#0ea5e9", "#0d9488", "#6366f1"]
            },
            "shape": {
              "type": ["circle", "triangle", "edge"],
              "stroke": {
                "width": 0,
                "color": "#000000"
              },
              "polygon": {
                "nb_sides": 6
              }
            },
            "opacity": {
              "value": isDark ? 0.35 : 0.2,
              "random": true,
              "anim": {
                "enable": true,
                "speed": 0.8,
                "opacity_min": isDark ? 0.08 : 0.05,
                "sync": false
              }
            },
            "size": {
              "value": isDark ? 3.5 : 3,
              "random": true,
              "anim": {
                "enable": true,
                "speed": 2,
                "size_min": 0.5,
                "sync": false
              }
            },
            "line_linked": {
              "enable": true,
              "distance": 150,
              "color": isDark ? "#00f2fe" : "#0284c7",
              "opacity": isDark ? 0.12 : 0.08,
              "width": 1
            },
            "move": {
              "enable": true,
              "speed": 1.2,
              "direction": "none",
              "random": true,
              "straight": false,
              "out_mode": "out",
              "bounce": false,
              "attract": {
                "enable": true,
                "rotateX": 800,
                "rotateY": 1400
              }
            }
          },
          "interactivity": {
            "detect_on": "window",
            "events": {
              "onhover": {
                "enable": true,
                "mode": ["grab", "bubble"]
              },
              "onclick": {
                "enable": true,
                "mode": "push"
              },
              "resize": true
            },
            "modes": {
              "grab": {
                "distance": 180,
                "line_linked": {
                  "opacity": isDark ? 0.55 : 0.35
                }
              },
              "bubble": {
                "distance": 200,
                "size": 6,
                "duration": 2,
                "opacity": isDark ? 0.7 : 0.5,
                "speed": 3
              },
              "repulse": {
                "distance": 120,
                "duration": 0.4
              },
              "push": {
                "particles_nb": 4
              },
              "remove": {
                "particles_nb": 2
              }
            }
          },
          "retina_detect": true
        };
      }

      function initParticles() {
        if (typeof particlesJS === 'undefined') return;
        // Destroy existing instance if present
        if (window.pJSDom && window.pJSDom.length > 0) {
          try {
            window.pJSDom[0].pJS.fn.vendors.destroypJS();
          } catch(e) {}
          window.pJSDom = [];
        }
        particlesJS('particles-js', getParticlesConfig());
      }

      // Initial load — tunda sampai browser idle agar tidak block render konten utama
      function launchParticles() {
        if (typeof particlesJS !== 'undefined') {
          initParticles();
        } else {
          // Coba lagi jika script belum ready
          setTimeout(launchParticles, 200);
        }
      }

      if ('requestIdleCallback' in window) {
        requestIdleCallback(launchParticles, { timeout: 2000 });
      } else {
        setTimeout(launchParticles, 500);
      }

      // Re-initialize particles on theme change
      var observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
          if (mutation.type === 'attributes' && mutation.attributeName === 'data-theme') {
            setTimeout(initParticles, 100);
          }
        });
      });
      observer.observe(document.documentElement, { attributes: true });
    })();
  </script>

  <!-- Vendor JS Files — defer agar tidak blocking render awal -->
  <script src="{{ url('portal/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/aos/aos.js') }}" defer></script>
  <script src="{{ url('portal/vendor/swiper/swiper-bundle.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/glightbox/js/glightbox.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/isotope-layout/isotope.pkgd.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/purecounter/purecounter_vanilla.js') }}" defer></script>
  
  <!-- Main JS File -->
  <script src="{{ url('portal/js/portal.js') }}" defer></script>




  @stack('scripts')

</body>
</html>