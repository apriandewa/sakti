<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>@stack('title',config('master.app.profile.name')) | {!! config('master.app.profile.short_name') !!}</title>
  <meta name="description" content="">
  <meta name="keywords" content="">

  <!-- Favicons -->
   <link rel="icon" href="{{ url($template.config('master.app.web.favicon'))}}">
  <link href="{{ url('reveal/assets/img/apple-touch-icon.png') }}" rel="apple-touch-icon">
  

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Montserrat:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Icon FontAwesome -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

  <!-- Vendor CSS Files -->
  <link href="{{ url('reveal/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ url('reveal/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ url('reveal/assets/vendor/aos/aos.css') }}" rel="stylesheet">
  <link href="{{ url('reveal/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">
  <link href="{{ url('reveal/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
  <link href="{{ url('reveal/assets/vendor/owlcarousel/assets/owl.carousel.min.css')}}" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="{{ url('reveal/assets/css/main.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: Reveal
  * Template URL: https://bootstrapmade.com/reveal-bootstrap-corporate-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->

  
</head>

<body class="index-page">

@include('frontend.partials.navbar')

@yield('container')

@include('frontend.partials.footer')


  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Preloader -->
  <div id="preloader"></div>


  <!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- OwlCarousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>



  <!-- Vendor JS Files -->
  <script src="{{ url('reveal/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <script src="{{ url('reveal/assets/vendor/php-email-form/validate.js') }}"></script>
  <script src="{{ url('reveal/assets/vendor/aos/aos.js') }}"></script>
  <script src="{{ url('reveal/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>
  <script src="{{ url('reveal/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
  <script src="{{ url('reveal/assets/vendor/imagesloaded/imagesloaded.pkgd.min.js') }}"></script>
  <script src="{{ url('reveal/assets/vendor/isotope-layout/isotope.pkgd.min.js') }}"></script>
  <script src="{{ url('reveal/assets/vendor/purecounter/purecounter_vanilla.js') }}"></script>
  <script src="{{ url('reveal/assets/vendor/owlcarousel/owl.carousel.min.js')}}"></script>
  
  <!-- Main JS File -->
  <script src="{{ url('reveal/assets/js/main.js') }}"></script>

  <!-- Script ganti gambar saat klik -->
<script>
  document.addEventListener("DOMContentLoaded", function () {
    const faqItems = document.querySelectorAll(".faq-item");
    const faqImage = document.getElementById("faq-image");

    faqItems.forEach(item => {
      item.addEventListener("click", function () {
        const newImg = this.getAttribute("data-img-src");
        if (newImg && faqImage) {
          faqImage.src = newImg;
        }
      });
    });
  });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const faqItems = document.querySelectorAll(".faq .faq-container .faq-item");

  faqItems.forEach(item => {
    item.addEventListener("click", function() {
      // tutup semua dulu
      faqItems.forEach(i => i.classList.remove("faq-active"));

      // buka item yang diklik
      this.classList.add("faq-active");
    });
  });
});
</script>

<script>
    (function(d){
        var s = d.createElement("script");
        s.setAttribute("data-account", "{{ config('services.userway.widget_id') }}");
        s.setAttribute("src", "https://cdn.userway.org/widget.js");
        (d.body || d.head).appendChild(s);
    })(document);
</script>



</body>

</html>