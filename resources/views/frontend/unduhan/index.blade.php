@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Unduhan</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Pusat Unduhan Dokumen' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Akses mudah berkas regulasi, laporan keuangan, formulir permohonan informasi, dan data publik lainnya.' }}</p>
    </div>
  </div>

  <!-- Downloads List Section -->
  <section class="section-dark">
    <div class="container">

      <!-- Search & Filters Panel -->
      <div class="glass-card mb-5" data-aos="fade-up">
        <form action="{{ url('unduhan') }}" method="get" class="search-form form-cyber">
          @if(request('kategori'))
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
          @endif
          
          <div class="row g-3">
            <div class="col-lg-8 col-md-7">
              <div class="position-relative">
                <input 
                  name="search" 
                  type="search" 
                  class="form-control ps-4" 
                  placeholder="Cari berkas atau dokumen..." 
                  value="{{ request('search') }}"
                  aria-label="Cari Dokumen"
                >
              </div>
            </div>
            
            <div class="col-lg-4 col-md-5 d-flex gap-2">
              <button type="submit" class="btn-cyber w-100 justify-content-center">
                <i class="bi bi-search"></i> Cari Berkas
              </button>
              @if(request('search') || request('kategori'))
                <a href="{{ url('unduhan') }}" class="btn-cyber-outline justify-content-center" title="Reset Pencarian">
                  <i class="bi bi-x-lg"></i>
                </a>
              @endif
            </div>
          </div>
        </form>

        <!-- Active Filter Badges -->
        @if(request('kategori') || request('search'))
          <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
            <span class="text-muted small">Filter Aktif:</span>
            @if(request('search'))
              <span class="badge bg-secondary px-3 py-2">Pencarian: "{{ request('search') }}"</span>
            @endif
            @if(request('kategori'))
              <span class="badge bg-success px-3 py-2">Kategori: {{ request('kategori') }}</span>
            @endif
          </div>
        @endif
      </div>

      <!-- Downloads Grid -->
      <div class="row g-4">
        @forelse($unduhan as $downloadIndex => $download)
          @php
            $file = $download->getfilebyalias('gambar_unduhan');
            $downloadImg = $file ? url($file->public_stream) : 'https://via.placeholder.com/600x400?text=Download+Document';
            $slugKategori = str_replace(' ', '-', strtolower($download->kategori));
          @endphp
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($downloadIndex % 3) * 100 }}">
            <div class="unduhan-card d-flex flex-column justify-content-between h-100">
              <div>
                <div class="unduhan-header">
                  <div class="unduhan-icon">
                    <i class="bi bi-file-earmark-arrow-down"></i>
                  </div>
                  <span class="unduhan-badge">
                    <a href="{{ url('unduhan') }}?kategori={{ $slugKategori }}" class="text-decoration-none" style="color: var(--brand-gold);">{{ $download->kategori }}</a>
                  </span>
                </div>
                <h4 class="unduhan-title mt-2 text-white">{{ $download->nama }}</h4>
                <p class="text-secondary small mb-3" style="height: 48px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                  {{ $download->desc }}
                </p>
              </div>

              <div>
                <div class="unduhan-meta border-top border-secondary pt-3 mt-3">
                  <span><i class="bi bi-person me-1"></i> {{ $download->user ? $download->user->name : 'Admin' }}</span>
                  <span><i class="bi bi-calendar me-1"></i> {{ \Carbon\Carbon::parse($download->created_at)->translatedFormat('d M Y') }}</span>
                  <span><i class="bi bi-eye me-1"></i> {{ number_format($download->view ?? 0) }} kali</span>
                </div>
                <div class="mt-3">
                  <a href="{{ route('unduhan.detail', $download->slug) }}" class="btn-cyber py-2 px-3 text-center w-100 justify-content-center">
                    <i class="bi bi-cloud-arrow-down-fill"></i> Detail & Unduh
                  </a>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <div class="glass-card py-5">
              <i class="bi bi-file-earmark-x fs-1 text-muted mb-3 d-block"></i>
              <h4 class="text-white">Dokumen Tidak Ditemukan</h4>
              <p class="text-secondary">Maaf, kami tidak menemukan berkas dokumen yang cocok dengan kata kunci pencarian Anda.</p>
              <a href="{{ url('unduhan') }}" class="btn-cyber mt-3">Tampilkan Semua Dokumen</a>
            </div>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
        {{ $unduhan->appends(request()->input())->links() }}
      </div>

    </div>
  </section>

</main>
@endsection
