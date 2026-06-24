@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Halaman Informasi</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Arsip Halaman Publik' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Daftar halaman informasi publik, profil, dan layanan digital di Kabupaten Indragiri Hulu.' }}</p>
    </div>
  </div>

  <!-- Page List Section -->
  <section class="section-dark">
    <div class="container">

      <!-- Search & Filters Panel -->
      <div class="glass-card mb-5" data-aos="fade-up">
        <form action="{{ url('page') }}" method="get" class="search-form form-cyber">
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
                  placeholder="Cari halaman..." 
                  value="{{ request('search') }}"
                  aria-label="Cari Halaman"
                >
              </div>
            </div>
            
            <div class="col-lg-4 col-md-5 d-flex gap-2">
              <button type="submit" class="btn-cyber w-100 justify-content-center">
                <i class="bi bi-search"></i> Cari Halaman
              </button>
              @if(request('search') || request('kategori') || request('author'))
                <a href="{{ url('page') }}" class="btn-cyber-outline justify-content-center" title="Reset Pencarian">
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

      <!-- Page Grid -->
      <div class="row g-4">
        @forelse($page as $pageIndex => $p)
          @php
            $file = $p->getfilebyalias('gambar_page');
            $pageImg = $file ? url($file->public_stream) : 'https://via.placeholder.com/600x400?text=Profil+Diskominfotik';
            $slugKategori = str_replace(' ', '-', strtolower($p->kategori));
          @endphp
          <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($pageIndex % 3) * 100 }}">
            <div class="berita-card">
              <div class="berita-img-wrapper">
                <span class="berita-badge">
                  <a href="{{ url('page') }}?kategori={{ $slugKategori }}" class="text-white text-decoration-none">{{ $p->kategori }}</a>
                </span>
                <img src="{{ $pageImg }}" alt="{{ $p->nama }}">
              </div>
              <div class="berita-body">
                <div class="berita-meta">
                  <span>
                    <i class="bi bi-person"></i> 
                    @if($p->user)
                      <a href="{{ url('page') }}?author={{ $p->user_id }}">{{ $p->user->name }}</a>
                    @else
                      <span>Admin</span>
                    @endif
                  </span>
                  <span><i class="bi bi-calendar3"></i> {{ \Carbon\Carbon::parse($p->created_at)->translatedFormat('d M Y') }}</span>
                  <span><i class="bi bi-eye"></i> {{ number_format($p->view ?? 0) }}</span>
                </div>
                <h4 class="berita-title">
                  <a href="{{ route('page.detail', $p->slug) }}">{{ $p->nama }}</a>
                </h4>
                <div class="text-secondary small flex-grow-1 mb-4" style="height: 60px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical;">
                  {!! strip_tags($p->desc) !!}
                </div>
                <a href="{{ route('page.detail', $p->slug) }}" class="btn-cyber py-2 px-3 text-center">Selengkapnya</a>
              </div>
            </div>
          </div>
        @empty
          <div class="col-12 text-center py-5">
            <div class="glass-card py-5">
              <i class="bi bi-file-earmark-x fs-1 text-muted mb-3 d-block"></i>
              <h4 class="text-white">Halaman Tidak Ditemukan</h4>
              <p class="text-secondary">Maaf, kami tidak menemukan halaman yang cocok dengan kata kunci atau filter pencarian Anda.</p>
              <a href="{{ url('page') }}" class="btn-cyber mt-3">Tampilkan Semua Halaman</a>
            </div>
          </div>
        @endforelse
      </div>

      <!-- Pagination -->
      <div class="d-flex justify-content-center mt-5" data-aos="fade-up">
        {{ $page->appends(request()->input())->links() }}
      </div>

    </div>
  </section>

</main>
@endsection
