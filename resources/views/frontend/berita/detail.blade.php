@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item"><a href="{{ url('berita') }}">Berita</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Detail Berita</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Detail Berita' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Informasi terbaru seputar kegiatan dan perkembangan di Kabupaten Indragiri Hulu.' }}</p>
    </div>
  </div>

  <!-- Detail Content Section -->
  <section class="section-dark">
    <div class="container">
      <div class="row gy-4">
        
        <!-- Main Article Column -->
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
          <div class="detail-content">
            
            <!-- Article Image -->
            @if(!is_null($news->getfilebyalias('gambar_berita')))
              @php
                $file = $news->getfilebyalias('gambar_berita');
              @endphp
              @if($file)
                <img src="{{ url($file->public_stream) }}" alt="{{ $file->name }}" class="detail-img img-fluid">
              @endif
            @endif

            <!-- Article Title -->
            <h2 class="text-white fw-bold mb-3" style="font-family: var(--font-title);">{{ $news->nama }}</h2>
            
            <!-- Article Metadata -->
            <div class="detail-meta">
              <span class="d-flex align-items-center"><i class="bi bi-person text-success me-2"></i> Penulis : {{ $news->user ? $news->user->name : 'Admin' }}</span>
              <span class="d-flex align-items-center"><i class="bi bi-calendar3 text-success me-2"></i> {{ \Carbon\Carbon::parse($news->created_at)->translatedFormat('d F Y H:i') }} WIB</span>
              <span class="d-flex align-items-center"><i class="bi bi-eye text-success me-2"></i> Dilihat : {{ number_format($news->view ?? 0) }} Kali</span>
              <span class="d-flex align-items-center"><i class="bi bi-tags text-success me-2"></i> Kategori : {{ $news->kategori }}</span>
            </div>

            <!-- Article Body -->
            <div class="detail-body">
              {!! html_entity_decode($news->desc) !!}
            </div>

            <!-- Share Buttons -->
            <div class="border-top border-secondary pt-4 mt-5">
              <h5 class="text-white mb-3" style="font-family: var(--font-subtitle);">Bagikan Berita Ini :</h5>
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
