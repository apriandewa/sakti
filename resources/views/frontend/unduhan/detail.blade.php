@extends('frontend.main')

    @section('container')

  
  <main class="main">

    <!-- Blog Detail Section -->
   <section id="service-details" class="service-details section">

       <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Unduhan </h2>
        <p>Berikut adalah Unduhan dan Informasi Kegiatan PPID Kabupaten Indragiri Hulu</p>
        
      </div>
      <!-- End Section Title -->

      <div class="container" data-aos="fade-up" data-aos-delay="100">
  
        <div class="row gy-4">
          <div class="col-lg-8 ps-lg-5" data-aos="fade-up" data-aos-delay="200">
            <div class="row">
              <div class="col-md-6">
                @if(!is_null($news->getfilebyalias('gambar_unduhan')))
                    @php
                      $file = $news->getfilebyalias('gambar_unduhan');
                    @endphp
                    @if($file)
                      <div class="form-group text-center">
                        {!! html()->img(url($file->public_stream), $file->name)->class('img-fluid service-img') !!}
                      </div>
                    @endif
                @endif
              </div>
              <div class="col-md-6">
                <h3>{{$news->nama}}</h3>
                <p>
                  {!! html()->p($news->desc) !!}
                </p>
                <div class="d-flex align-items-center mb-3">
                  <i class="bi bi-calendar me-2"></i>
                  <span>Tanggal Terbit : {{ $news->created_at->format('d M Y') }}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <i class="bi bi-folder2-open me-2"></i>
                  <span>Kategori : {{ $news->kategori }}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <i class="bi bi-person-circle me-2"></i>
                  <span>Penulis : {{ $news->user->name }}</span>
                </div>
                <div class="d-flex align-items-center mb-3">
                  <i class="bi bi-person-circle me-2"></i>
                  <span>Verifikator : {{ $news->verifikator->name }}</span>
                </div>
                
                @if(!is_null($news->getfilebyalias('berkas_unduhan')))
                  @php
                      $file = $news->getfilebyalias('berkas_unduhan');
                  @endphp

                  @if($file)
                      {{-- Baris Download --}}
                      <div class="d-flex align-items-center mb-2 gap-2">
                        <i class="bi bi-filetype-pdf me-2"></i>
                          <span class="text-muted small">
                              (diunduh : {{ $file->download ?? 0 }} kali)
                          </span>
                          <a href="{{ url($file->public_stream) }}" class="btn btn-success btn-sm" download>
                              <i class="bi bi-download me-2"></i>Download
                          </a>
                      </div>

                      {{-- Baris Lihat File --}}
                      <div class="d-flex align-items-center gap-2">
                          <i class="bi bi-filetype-pdf me-2"></i>
                          <span class="text-muted small">
                              (dilihat : {{ $file->view ?? 0 }} kali)
                          </span>
                          <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#pdfPreviewModal">
                              <i class="bi bi-eye me-2"></i> Lihat File
                          </button>
                      </div>

                      {{-- Modal Preview PDF --}}
                      <div class="modal fade" id="pdfPreviewModal" tabindex="-1" aria-labelledby="pdfPreviewModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-xl modal-dialog-centered">
                              <div class="modal-content">
                                  <div class="modal-header">
                                      <h5 class="modal-title" id="pdfPreviewModalLabel">Preview File PDF</h5>
                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                  </div>
                                  <div class="modal-body" style="height: 80vh;">
                                      <iframe src="{{ url($file->public_stream) }}" width="100%" height="100%" style="border:none;"></iframe>
                                  </div>
                              </div>
                          </div>
                      </div>
                  @endif
              @endif

                
              </div>

            </div>

          </div>
        
          @include('frontend.partials.sidebar')

          
      </div>



    </section>
    <!-- /Blog Posts Section -->
    
  </main>

@endsection
