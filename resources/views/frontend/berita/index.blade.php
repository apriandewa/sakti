@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Berita & Informasi</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Arsip Berita & Informasi' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Temukan berita terbaru, pengumuman, dan publikasi resmi dari Diskominfotik Kabupaten Indragiri Hulu.' }}</p>
    </div>
  </div>

  <!-- News List Section -->
  <section class="section-dark">
    <div class="container">

      <!-- Search & Filters Panel -->
      <div class="glass-card mb-5" data-aos="fade-up">
        <form action="{{ url('berita') }}" method="get" class="search-form form-cyber">
          @if(request('kategori'))
            <input type="hidden" name="kategori" value="{{ request('kategori') }}">
          @endif
          @if(request('author'))
            <input type="hidden" name="author" value="{{ request('author') }}">
          @endif
          
          <div class="row g-3">
            <div class="col-lg-8 col-md-7">
              <div class="position-relative">
                <input 
                  name="search" 
                  type="search" 
                  class="form-control ps-4" 
                  placeholder="Cari berita berdasarkan judul..." 
                  value="{{ request('search') }}"
                  aria-label="Cari Berita"
                >
              </div>
            </div>
            
            <div class="col-lg-4 col-md-5 d-flex gap-2">
              <button type="submit" class="btn-cyber w-100 justify-content-center">
                <i class="bi bi-search"></i> Cari Berita
              </button>
              @if(request('search') || request('kategori') || request('author'))
                <a href="{{ url('berita') }}" class="btn-cyber-outline justify-content-center" title="Reset Pencarian">
                  <i class="bi bi-x-lg"></i>
                </a>
              @endif
            </div>
          </div>
        </form>

        <!-- Active Filter Badges -->
        @if(request('kategori') || request('author') || request('search'))
          <div class="d-flex flex-wrap gap-2 mt-3 align-items-center">
            <span class="text-muted small">Filter Aktif:</span>
            @if(request('search'))
              <span class="badge bg-secondary px-3 py-2">Pencarian: "{{ request('search') }}"</span>
            @endif
            @if(request('kategori'))
              <span class="badge bg-success px-3 py-2">Kategori: {{ request('kategori') }}</span>
            @endif
            @if(request('author'))
              <span class="badge bg-info px-3 py-2">Penulis ID: {{ request('author') }}</span>
            @endif
          </div>
        @endif
      </div>

      <!-- News Grid -->
      <div class="row g-4">
        @forelse($berita as $newsIndex => $news)
          @php
            $file = $news->getfilebyalias('gambar_berita');
            $newsImg = $file ? url($file->public_stream) : 'https://via.placeholder.com/600x400?text=Berita+Diskominfotik';
            $slugKategori = str_replace(' ', '-', strtolower($news->kategori));
          @endphp
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($newsIndex % 3) * 100 }}">
            <div class="berita-card">
              <div class="berita-img-wrapper">
                <span class="berita-badge">
                  <a href="{{ url('berita') }}?kategori={{ $slugKategori }}" class="text-white text-decoration-none">{{ $news->kategori }}</a>
                </span>
                <img src="{{ $newsImg }}" alt="{{ $news->nama }}">
              </div>
              <div class="berita-body">
                <div class="berita-meta">
                  <span>
                    <i class="bi bi-person"></i> 
                    @if($news->user)
                      <a href="{{ url('berita') }}?author={{ $news->user_id }}">{{ $news->user->name }}</a>
                    @else
                      <span>Admin</span>
                    @endif
                  </span>
                  <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($news->created_at)->translatedFormat('d M Y') }}</span>
                  <span><i class="bi bi-eye"></i> {{ number_format($news->view ?? 0) }}</span>
                </div>
                <h4 class="berita-title">
                  <a href="{{ route('berita.detail', $news->slug) }}">{{ $news->nama }}</a>
                </h4>
                <div class="text-secondary small flex-grow-1 mb-4" style="height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                  {!! strip_tags($news->desc) !!}
                </div>
                <a href="{{ route('berita.detail', $news->slug) }}" class="btn-cyber py-2 px-3 text-center">Selengkapnya</a>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <div class="glass-card py-5">
              <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
              <h4 class="text-white">Berita Tidak Ditemukan</h4>
              <p class="text-secondary">Maaf, kami tidak menemukan berita yang cocok dengan kata kunci atau filter pencarian Anda.</p>
              <a href="{{ url('berita') }}" class="btn-cyber mt-3">Tampilkan Semua Berita</a>
            </div>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
        {{ $berita->appends(request()->input())->links() }}
      </div>

    </div>
  </section>

</main>
@endsection
