@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item"><a href="{{ url('unduhan') }}">Unduhan</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Detail Berkas</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Detail Unduhan Berkas' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Detail metadata berkas publik resmi Dinas Komunikasi dan Informatika.' }}</p>
    </div>
  </div>

  <!-- Detail Content Section -->
  <section class="section-dark">
    <div class="container">
      <div class="row gy-4">
        
        <!-- Main Column -->
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
          <div class="detail-content">
            
            <div class="row g-4 mb-4">
              <!-- Cover Image/Icon -->
              <div class="col-md-5">
                @if(!is_null($news->getfilebyalias('gambar_unduhan')))
                  @php
                    $file = $news->getfilebyalias('gambar_unduhan');
                  @endphp
                  @if($file)
                    <img src="{{ url($file->public_stream) }}" alt="{{ $news->nama }}" class="img-fluid rounded border border-secondary shadow-lg">
                  @endif
                @else
                  <div class="d-flex align-items-center justify-content-center bg-secondary rounded" style="aspect-ratio: 4/3; min-height: 180px;">
                    <i class="bi bi-file-earmark-arrow-down fs-1 text-muted"></i>
                  </div>
                @endif
              </div>

              <!-- File Information Details -->
              <div class="col-md-7 d-flex flex-column justify-content-between">
                <div>
                  <h3 class="text-white fw-bold mb-3" style="font-family: var(--font-title);">{{ $news->nama }}</h3>
                  <p class="text-secondary small mb-4">
                    {{ $news->desc }}
                  </p>
                </div>

                <div class="detail-meta border-0 p-0 m-0">
                  <div class="d-flex flex-column gap-2 text-secondary font-subtitle" style="font-size: 0.85rem;">
                    <span><i class="bi bi-calendar3 text-success me-2"></i> Rilis : {{ $news->created_at->format('d M Y') }}</span>
                    <span><i class="bi bi-folder2-open text-success me-2"></i> Kategori : {{ $news->kategori }}</span>
                    <span><i class="bi bi-person-circle text-success me-2"></i> Penulis : {{ $news->user->name ?? 'Admin' }}</span>
                    @if(!empty($news->verifikator))
                      <span><i class="bi bi-shield-check text-success me-2"></i> Verifikator : {{ $news->verifikator->name }}</span>
                    @endif
                    <span><i class="bi bi-cloud-arrow-down text-warning me-2"></i> Diunduh : {{ number_format($news->download ?? 0) }} kali</span>
                    <span><i class="bi bi-eye text-cyan me-2"></i> Dilihat : {{ number_format($news->view ?? 0) }} kali</span>
                  </div>
                </div>
              </div>
            </div>

            <!-- Downloadable Attachments List -->
            @php
              $berkasFiles = $news->files->where('alias', 'berkas_unduhan');
            @endphp

            @if($berkasFiles->count())
              <div class="border-top border-secondary pt-4 mt-5">
                <h4 class="text-white mb-4" style="font-family: var(--font-title);"><i class="bi bi-paperclip text-success me-2"></i> Berkas Lampiran</h4>
                
                <div class="d-flex flex-column gap-3">
                  @foreach($berkasFiles as $berkas)
                    @php
                      $fileName = $berkas->data['name'] ?? $berkas->name ?? 'Dokumen';
                      $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
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

                    <div class="glass-card py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                      <div class="d-flex align-items-center gap-3">
                        <i class="bi {{ $iconClass }} fs-2"></i>
                        <div>
                          <h6 class="text-white mb-0" style="font-size: 0.95rem;">{{ $fileName }}</h6>
                        </div>
                      </div>

                      <div class="d-flex gap-2">
                        <a href="{{ $downloadUrl }}" class="btn-cyber py-2 px-3 text-center" style="font-size: 0.85rem;">
                          <i class="bi bi-download"></i> Unduh
                        </a>
                        
                        @if($previewable)
                          <button 
                            type="button" 
                            class="btn-cyber-outline py-2 px-3 btn-lihat" 
                            data-modal-id="{{ $modalId }}" 
                            data-view-url="{{ $viewUrl }}"
                            style="font-size: 0.85rem;"
                          >
                            <i class="bi bi-eye"></i> Baca Online
                          </button>
                        @endif
                      </div>
                    </div>

                    <!-- Modal Preview (hanya PDF) -->
                    @if($previewable)
                      <div class="modal fade" id="{{ $modalId }}" tabindex="-1" aria-hidden="true" style="background: rgba(7, 13, 25, 0.85);">
                        <div class="modal-dialog modal-xl modal-dialog-centered">
                          <div class="modal-content bg-secondary border border-secondary shadow-lg">
                            <div class="modal-header border-bottom border-secondary">
                              <h5 class="modal-title text-white">
                                <i class="bi bi-filetype-pdf text-danger me-2"></i>{{ $fileName }}
                              </h5>
                              <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-0" style="height: 80vh;">
                              <iframe src="" class="pdf-iframe" width="100%" height="100%" style="border: none;"></iframe>
                            </div>
                          </div>
                        </div>
                      </div>
                    @endif

                  @endforeach
                </div>
              </div>
            @endif

            <!-- Share Buttons -->
            <div class="border-top border-secondary pt-4 mt-5">
              <h5 class="text-white mb-3" style="font-family: var(--font-subtitle);">Bagikan Halaman Ini :</h5>
              <div class="d-flex gap-2">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="btn-cyber-outline py-2 px-3">
                  <i class="bi bi-facebook me-1"></i> Facebook
                </a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->nama) }}" target="_blank" class="btn-cyber-outline py-2 px-3">
                  <i class="bi bi-twitter-x me-1"></i> Twitter
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($news->nama . ' - ' . url()->current()) }}" target="_blank" class="btn-cyber-outline py-2 px-3">
                  <i class="bi bi-whatsapp me-1"></i> WhatsApp
                </a>
              </div>
            </div>

          </div>
        </div>

        <!-- Sidebar Column -->
        @include('frontend.partials.sidebar')

      </div>
    </div>
  </section>

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
