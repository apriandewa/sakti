     <!-- Contact Section -->
    <section id="contact" class="contact section">

      <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Kontak</h2>
        <p>Untuk informasi lebih lanjut dapat menghubungi kami pada saluran komunikasi berikut ini</p>
      </div><!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">

        
        <div class="row gy-4">

          <div class="col-lg-4">
            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
              <i class="bi bi-geo-alt flex-shrink-0"></i>
              <div>
                <h3>Alamat :</h3>
                <p>{{$pengaturan->alamat}}</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
              <i class="bi bi-telephone flex-shrink-0"></i>
              <div>
                <h3>Telepon :</h3>
                <p>{{$pengaturan->telepon}}</p>
              </div>
            </div><!-- End Info Item -->

            <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="500">
              <i class="bi bi-envelope flex-shrink-0"></i>
              <div>
                <h3>Email :</h3>
                <p>{{$pengaturan->email}}</p>
              </div>
            </div><!-- End Info Item -->

            
          </div>

          <div class="col-lg-8">
            <div class="mb-4" data-aos="fade-up" data-aos-delay="200">
          <iframe style="border:0; width: 100%; height: 270px;" src="{{$pengaturan->peta}}" frameborder="0" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div><!-- End Google Maps -->


        </div>

      </div>

    </section><!-- /Contact Section -->

  <footer id="footer" class="footer dark-background">
    <div class="container">
      <h3 class="sitename">{{$pengaturan->judul}}</h3>
      <p>{{$pengaturan->subjudul}}</p>
      <div class="social-links d-flex justify-content-center">
        <a href="{{$pengaturan->facebook}}" target="_blank"><i class="bi bi-facebook"></i></a>
        <a href="{{$pengaturan->instagram}}"><i class="bi bi-instagram" target="_blank"></i></a>
        <a href="{{$pengaturan->twiter}}"><i class="bi bi-twitter-x" target="_blank"></i></a>
        <a href="{{$pengaturan->tiktok}}"><i class="bi bi-tiktok" target="_blank"></i></a>
        <a href="{{$pengaturan->youtube}}"><i class="bi bi-youtube" target="_blank"></i></a>
      </div>
      <div class="container">
        <div class="copyright">
        <span>{{ date('Y') }} &copy; Copyright</span> <strong class="px-1 sitename">PPID Indragiri Hulu</strong> <span>All Rights Reserved</span>     </div>
        <div class="credits">
          <!-- All the links in the footer should remain intact. -->
          <!-- You can delete the links only if you've purchased the pro version. -->
          <!-- Licensing information: https://bootstrapmade.com/license/ -->
          <!-- Purchase the pro version with working PHP/AJAX contact form: [buy-url] -->
          Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
        </div>
      </div>
    </div>
  </footer>
