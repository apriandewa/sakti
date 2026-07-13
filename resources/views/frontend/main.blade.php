<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <!-- Theme Initialization (prevents flash of dark/light theme) -->
  <script>
    (function() {
      const savedTheme = localStorage.getItem('theme_frontend') || 'light';
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

  <!-- Google Fonts (Sora, Inter, JetBrains Mono) — Non-blocking async load -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preload" as="style" onload="this.onload=null;this.rel='stylesheet'" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap">
  <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"></noscript>

  <!-- Icon FontAwesome & Bootstrap Icons (kept — used across navbar/footer partials) -->
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

  <!-- Vendor JS Files — defer agar tidak blocking render awal -->
  <script src="{{ url('portal/vendor/bootstrap/js/bootstrap.bundle.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/aos/aos.js') }}" defer></script>
  <script src="{{ url('portal/vendor/swiper/swiper-bundle.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/glightbox/js/glightbox.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/isotope-layout/isotope.pkgd.min.js') }}" defer></script>
  <script src="{{ url('portal/vendor/purecounter/purecounter_vanilla.js') }}" defer></script>

  <!-- Particles.js — powers the night-sky starfield/shooting-star effect in the hero section -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js" defer></script>

  <!-- Main JS File -->
  <script src="{{ url('portal/js/portal.js') }}" defer></script>

  @stack('scripts')

</body>
</html>