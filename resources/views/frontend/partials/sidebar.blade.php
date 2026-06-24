<div class="col-lg-4 sidebar d-flex flex-column gap-4" data-aos="fade-up" data-aos-delay="300">

  <!-- Widget: Search -->
  <div class="glass-card">
    <h4 class="sidebar-title">Pencarian Berita</h4>
    <form action="{{ url('berita') }}" method="get" class="search-form form-cyber mt-3">
      @if (request('kategori'))
        <input type="hidden" name="kategori" value="{{ request('kategori') }}">
      @endif
      @if (request('author'))
        <input type="hidden" name="author" value="{{ request('author') }}">
      @endif
      <div class="input-group">
        <input 
          name="search" 
          type="search" 
          class="form-control" 
          placeholder="Cari kata kunci..." 
          aria-label="Cari Berita" 
          value="{{ request('search') }}"
        >
        <button type="submit" class="btn-search btn-cyber py-2 px-3">
          <i class="bi bi-search"></i>
        </button>
      </div>
    </form>
  </div>

  <!-- Widget: Latest News -->
  <div class="glass-card">
    <h4 class="sidebar-title">Berita Terbaru</h4>
    <div class="d-flex flex-column gap-3 mt-3">
      @forelse($latestNews as $latest)
        @php
          $file = $latest->getfilebyalias('gambar_berita');
          $imgUrl = $file ? url($file->public_stream) : 'https://via.placeholder.com/80?text=News';
        @endphp
        <div class="sidebar-news-item">
          <a href="{{ url('berita/' . $latest->slug) }}" class="flex-shrink-0">
            <img src="{{ $imgUrl }}" alt="{{ $latest->nama }}" style="width: 70px; height: 60px; object-fit: cover;">
          </a>
          <div class="sidebar-news-info">
            <h5 class="m-0 text-white" style="font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              <a href="{{ url('berita/' . $latest->slug) }}" class="text-white text-decoration-none">{{ $latest->nama }}</a>
            </h5>
            <span class="small text-muted"><i class="bi bi-calendar3 me-1"></i> {{ $latest->created_at->format('d M Y') }}</span>
          </div>
        </div>
      @empty
        <p class="text-secondary small mb-0">Belum ada berita terbaru.</p>
      @endforelse
    </div>
  </div>

  <!-- Widget: Popular News -->
  <div class="glass-card">
    <h4 class="sidebar-title">Berita Terpopuler</h4>
    <div class="d-flex flex-column gap-3 mt-3">
      @forelse($popularNews as $popular)
        @php
          $file = $popular->getfilebyalias('gambar_berita');
          $imgUrl = $file ? url($file->public_stream) : 'https://via.placeholder.com/80?text=News';
        @endphp
        <div class="sidebar-news-item">
          <a href="{{ url('berita/' . $popular->slug) }}" class="flex-shrink-0">
            <img src="{{ $imgUrl }}" alt="{{ $popular->nama }}" style="width: 70px; height: 60px; object-fit: cover;">
          </a>
          <div class="sidebar-news-info">
            <h5 class="m-0 text-white" style="font-size: 0.85rem; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
              <a href="{{ url('berita/' . $popular->slug) }}" class="text-white text-decoration-none">{{ $popular->nama }}</a>
            </h5>
            <span class="small text-muted"><i class="bi bi-eye me-1"></i> {{ number_format($popular->view ?? 0) }} dilihat</span>
          </div>
        </div>
      @empty
        <p class="text-secondary small mb-0">Belum ada berita populer.</p>
      @endforelse
    </div>
  </div>

  <!-- Widget: News Categories -->
  <div class="glass-card">
    <h4 class="sidebar-title">Kategori Berita</h4>
    <ul class="sidebar-list mt-3">
      @foreach($beritaList as $cat)
        @php
          $slugBerita = str_replace(' ', '-', strtolower($cat));
        @endphp
        <li>
          <a href="{{ url('berita') }}?kategori={{ $slugBerita }}" class="text-decoration-none {{ request('kategori') == $slugBerita ? 'text-success fw-bold' : '' }}">
            <span><i class="bi bi-chevron-right me-1 small"></i> {{ $cat }}</span>
          </a>
        </li>
      @endforeach
    </ul>
  </div>

  <!-- Widget: Gallery Categories -->
  <div class="glass-card">
    <h4 class="sidebar-title">Kategori Galeri</h4>
    <ul class="sidebar-list mt-3">
      @foreach($galeriList as $cat)
        @php
          $slugGaleri = str_replace(' ', '-', strtolower($cat));
        @endphp
        <li>
          <a href="{{ url('galeri') }}?kategori={{ $slugGaleri }}" class="text-decoration-none {{ request('kategori') == $slugGaleri ? 'text-success fw-bold' : '' }}">
            <span><i class="bi bi-chevron-right me-1 small"></i> {{ $cat }}</span>
          </a>
        </li>
      @endforeach
    </ul>
  </div>

  <!-- Widget: Download Categories -->
  <div class="glass-card">
    <h4 class="sidebar-title">Kategori Unduhan</h4>
    <ul class="sidebar-list mt-3">
      @foreach($unduhanList as $cat)
        @php
          $slugUnduhan = str_replace(' ', '-', strtolower($cat));
        @endphp
        <li>
          <a href="{{ url('unduhan') }}?kategori={{ $slugUnduhan }}" class="text-decoration-none {{ request('kategori') == $slugUnduhan ? 'text-success fw-bold' : '' }}">
            <span><i class="bi bi-chevron-right me-1 small"></i> {{ $cat }}</span>
          </a>
        </li>
      @endforeach
    </ul>
  </div>

</div>