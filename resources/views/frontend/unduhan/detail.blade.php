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
                
                {{-- Baris Download --}}
                <div class="d-flex align-items-center mb-2 gap-2">
                  <i class="bi bi-filetype-pdf me-2"></i>
                    <span class="text-muted small">
                        (diunduh : {{ $news->download ?? 0 }} kali)
                    </span>
                </div>

                {{-- Baris Lihat File --}}
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-filetype-pdf me-2"></i>
                    <span class="text-muted small">
                        (dilihat : {{ $news->view ?? 0 }} kali)
                    </span>
                </div>
                
              </div>

              {{-- Daftar Berkas Unduhan --}}
              @php
                  $berkasFiles = $news->file()->where('alias', 'berkas_unduhan')->get();
              @endphp

              @if($berkasFiles->isNotEmpty())
                  <div class="mt-3">
                      <h6 class="fw-semibold mb-2"><i class="bi bi-paperclip me-1"></i>Berkas Unduhan</h6>
                      <ul class="list-group list-group-flush">
                          @foreach($berkasFiles as $berkas)
                              @php
                                  $ext = strtolower(pathinfo($berkas->name, PATHINFO_EXTENSION));
                                  $iconClass = match($ext) {
                                      'pdf'           => 'bi-filetype-pdf text-danger',
                                      'doc', 'docx'   => 'bi-filetype-docx text-primary',
                                      'xls', 'xlsx'   => 'bi-filetype-xlsx text-success',
                                      'ppt', 'pptx'   => 'bi-filetype-pptx text-warning',
                                      default         => 'bi-file-earmark text-secondary',
                                  };
                                  $previewable = in_array($ext, ['pdf']);
                                  $modalId = 'previewModal_' . $berkas->id;
                                  $downloadUrl = route('unduhan.download', [$news->slug, $berkas->id]);
                                  $viewUrl     = route('unduhan.view', [$news->slug, $berkas->id]);
                              @endphp

                              <li class="list-group-item px-0">
                                  <div class="d-flex align-items-start gap-2 flex-wrap">
                                      {{-- Icon & Nama --}}
                                      <div class="d-flex align-items-center gap-2 flex-grow-1">
                                          <i class="bi {{ $iconClass }} fs-4"></i>
                                          <div>
                                              <div class="fw-medium small">{{ $berkas->name }}</div>
                                          </div>
                                      </div>

                                      {{-- Tombol Aksi --}}
                                      <div class="d-flex gap-2 align-items-center">
                                          {{-- Download --}}
                                          <a href="{{ $downloadUrl }}"
                                            class="btn btn-success btn-sm"
                                            title="Download">
                                              <i class="bi bi-download me-1"></i>Download
                                          </a>

                                          {{-- Lihat (hanya PDF) --}}
                                          @if($previewable)
                                              <button type="button"
                                                      class="btn btn-warning btn-sm btn-lihat"
                                                      data-modal-id="{{ $modalId }}"
                                                      data-view-url="{{ $viewUrl }}"
                                                      title="Lihat File">
                                                  <i class="bi bi-eye me-1"></i>Lihat
                                              </button>
                                          @endif
                                      </div>
                                  </div>
                              </li>

                              {{-- Modal Preview (hanya PDF) --}}
                              @if($previewable)
                                  <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true">
                                      <div class="modal-dialog modal-xl modal-dialog-centered">
                                          <div class="modal-content">
                                              <div class="modal-header">
                                                  <h5 class="modal-title">
                                                      <i class="bi bi-filetype-pdf text-danger me-2"></i>{{ $berkas->name }}
                                                  </h5>
                                                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                              </div>
                                              <div class="modal-body p-0" style="height:80vh;">
                                                  <iframe src="" 
                                                          class="pdf-iframe" 
                                                          width="100%" 
                                                          height="100%" 
                                                          style="border:none;">
                                                  </iframe>
                                              </div>
                                          </div>
                                      </div>
                                  </div>
                              @endif

                          @endforeach
                      </ul>
                  </div>
              @endif
            </div>

          </div>
        
          @include('frontend.partials.sidebar')

          
      </div>



    </section>
    <!-- /Blog Posts Section -->
    
  </main>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.btn-lihat').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const modalId  = this.dataset.modalId;
            const viewUrl  = this.dataset.viewUrl;
            const modal    = document.getElementById(modalId);
            const iframe   = modal.querySelector('.pdf-iframe');

            // Catat view ke server (POST dengan CSRF)
            fetch(viewUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            })
            .then(res => res.json())
            .then(data => {
                // Set src iframe setelah dapat URL dari server
                iframe.src = data.url;
            });

            // Buka modal Bootstrap
            new bootstrap.Modal(modal).show();

            // Kosongkan iframe saat modal ditutup (hemat memori)
            modal.addEventListener('hidden.bs.modal', function () {
                iframe.src = '';
            }, { once: true });
        });
    });
});
</script>
@endpush
