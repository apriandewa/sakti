@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Pegawai</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Daftar Pegawai' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Aparatur Sipil Negara & Tenaga Kerja di lingkungan Diskominfotik' }}</p>
    </div>
  </div>

  <section id="pegawai-list" class="section-dark">
    <div class="container">
      
      <!-- Search and Filter Bar -->
      <div class="glass-card p-4 mb-5" data-aos="fade-up">
        <form action="{{ route('pegawai') }}" method="GET" class="row g-3 align-items-end">
          <div class="col-md-5">
            <label for="search" class="form-label text-white small fw-bold">Cari Pegawai</label>
            <div class="input-group">
              <span class="input-group-text bg-dark border-secondary text-secondary"><i class="bi bi-search"></i></span>
              <input type="text" name="search" id="search" class="form-control" placeholder="Ketik nama, NIP, atau jabatan..." value="{{ request('search') }}">
            </div>
          </div>
          <div class="col-md-4">
            <label for="bidang_id" class="form-label text-white small fw-bold">Filter Bidang</label>
            <select name="bidang_id" id="bidang_id" class="form-select">
              <option value="">Semua Bidang</option>
              @foreach($bidangs as $b)
                <option value="{{ $b->id }}" {{ request('bidang_id') == $b->id ? 'selected' : '' }}>{{ $b->nama }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn-cyber w-100 py-2"><i class="bi bi-filter"></i> Terapkan</button>
            <a href="{{ route('pegawai') }}" class="btn-cyber-outline w-100 py-2 text-center"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
          </div>
        </form>
      </div>

      <div class="row gy-4">
        <!-- Main Content Column -->
        <div class="col-lg-8">
          <div class="row g-4">
            @forelse($pegawai as $member)
              @php
                $dep = $member->gelar_depan ? $member->gelar_depan . ' ' : '';
                $bel = $member->gelar_belakang ? ', ' . $member->gelar_belakang : '';
                $fullName = $dep . $member->nama . $bel;
              @endphp
              <div class="col-md-6" data-aos="fade-up" data-aos-delay="50">
                <div class="glass-card employee-card h-100 d-flex flex-column p-0 overflow-hidden"
                     style="--card-theme-color: {{ $member->jabatan_styling['theme_color'] }}; --card-glow-color: {{ $member->jabatan_styling['glow'] }}; border-color: rgba(255,255,255,0.08);">
                  
                  <!-- Profile Header -->
                  <div class="position-relative text-center pt-4 pb-3 px-3 border-bottom" style="background: {{ $member->jabatan_styling['gradient'] }}; border-color: rgba(255,255,255,0.08) !important;">
                    <!-- Badge Jabatan Jenis on top right -->
                    <span class="position-absolute top-0 end-0 m-2 badge" style="background-color: {{ $member->jabatan_styling['badge_bg'] }}; color: {{ $member->jabatan_styling['badge_text'] }}; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;">
                      {{ $member->jabatanJenis ? $member->jabatanJenis->nama : '-' }}
                    </span>
                    
                    <div class="employee-avatar-wrapper mx-auto mb-3">
                      <div class="avatar-glow-ring" style="position: absolute; top: -5px; left: -5px; right: -5px; bottom: -5px; border-radius: 50%; border: 2px solid {{ $member->jabatan_styling['border'] }}; opacity: 0.6; box-shadow: 0 0 15px {{ $member->jabatan_styling['glow'] }}; transition: all 0.3s ease;"></div>
                      <img src="{{ $member->foto_url }}" alt="{{ $fullName }}" class="rounded-circle img-thumbnail position-relative z-1" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid {{ $member->jabatan_styling['border'] }}; background: rgba(255, 255, 255, 0.07); backdrop-filter: blur(4px);">
                      
                      <!-- Status Badge -->
                      <span class="position-absolute bottom-0 end-50 translate-middle-x badge rounded-pill bg-success border border-dark z-2" style="font-size: 0.65rem;">
                        {{ $member->statusPegawai ? $member->statusPegawai->nama : 'Pegawai' }}
                      </span>
                    </div>
                    
                    <h5 class="text-white mb-1 fw-bold text-truncate" title="{{ $fullName }}" style="font-size: 1.1rem; letter-spacing: 0.2px;">{{ $fullName }}</h5>
                    <p class="small mb-1 fw-semibold text-truncate {{ $member->jabatan_styling['text_class'] }}" style="opacity: 0.9;">{{ $member->jabatanNama ? $member->jabatanNama->nama : 'Staff' }}</p>
                    <div class="d-flex justify-content-center align-items-center gap-2">
                      <small class="text-muted text-xs">NIP. {{ $member->nip ?? '-' }}</small>
                      <span class="badge bg-success border border-success rounded-pill py-0 px-2" style="font-size: 0.6rem; font-weight: 600; background: rgba(25, 135, 84, 0.2) !important; color: #198754 !important;">{{ strtoupper($member->status) }}</span>
                    </div>
                  </div>

                  <!-- Profile Details -->
                  <div class="p-3 flex-grow-1">
                    <ul class="list-unstyled mb-0 small">
                      <li class="d-flex justify-content-between mb-2">
                        <span class="text-secondary"><i class="bi bi-diagram-3 me-2"></i>Bidang</span>
                        <span class="text-white fw-medium text-end text-truncate ms-2" style="max-width: 60%;">{{ $member->bidang ? $member->bidang->nama : '-' }}</span>
                      </li>
                      <li class="d-flex justify-content-between mb-2">
                        <span class="text-secondary"><i class="bi bi-award me-2"></i>Pangkat</span>
                        <span class="text-white fw-medium text-end text-truncate ms-2" style="max-width: 60%;">{{ $member->pangkat ? $member->pangkat->nama : '-' }}</span>
                      </li>
                      <li class="d-flex justify-content-between mb-0">
                        <span class="text-secondary"><i class="bi bi-telephone me-2"></i>Telepon</span>
                        <span class="text-white fw-medium">{{ $member->telpon ?? '-' }}</span>
                      </li>
                    </ul>
                  </div>

                  <!-- Card Footer / Button -->
                  <div class="px-3 py-3 border-top border-secondary text-center" style="background: rgba(15, 23, 42, 0.2);">
                    <a href="{{ route('pegawai.detail', $member->id) }}" class="btn-cyber-outline w-100 py-2 btn-sm"><i class="bi bi-eye"></i> Lihat Profil Lengkap</a>
                  </div>

                </div>
              </div>
            @empty
              <div class="col-12 text-center text-secondary py-5">
                <i class="bi bi-people fs-1 d-block mb-3 text-muted"></i>
                <p class="h5 text-white">Pegawai Tidak Ditemukan</p>
                <p class="small">Silakan gunakan kata kunci pencarian atau filter yang lain.</p>
              </div>
            @endforelse
          </div>

          <!-- Pagination -->
          <div class="mt-5 d-flex justify-content-center">
            {!! $pegawai->appends(request()->input())->links('pagination::bootstrap-5') !!}
          </div>
        </div>

        <!-- Sidebar Column -->
        @include('frontend.partials.sidebar')
      </div>

    </div>
  </section>

</main>

<style>
  .employee-avatar-wrapper {
    position: relative;
    width: 100px;
    height: 100px;
  }
  .employee-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
  }
  .employee-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px var(--card-glow-color, rgba(0, 242, 254, 0.15));
    border-color: var(--card-theme-color, var(--accent-color)) !important;
  }
  .employee-card:hover .avatar-glow-ring {
    transform: scale(1.05);
    opacity: 0.9 !important;
  }
  .text-xs {
    font-size: 0.75rem;
  }
  .text-purple {
    color: #c084fc !important;
  }
</style>
@endsection

