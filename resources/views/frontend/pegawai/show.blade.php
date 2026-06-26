@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item"><a href="{{ route('pegawai') }}">Pegawai</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">{{ $fullName }}</li>
        </ol>
      </nav>
      <h1 class="text-white">Detail Profil</h1>
      <p class="text-secondary">Informasi lengkap aparatur sipil negara & pengelola portal</p>
    </div>
  </div>

  <section id="pegawai-detail" class="section-dark">
    <div class="container">
      <div class="row gy-4">
        
        <!-- Main Content Column -->
        <div class="col-lg-8">
          
          <!-- Profile Card -->
          <div class="glass-card p-4 mb-4 employee-detail-card" data-aos="fade-up"
               style="--card-theme-color: {{ $pegawai->jabatan_styling['theme_color'] }}; --card-glow-color: {{ $pegawai->jabatan_styling['glow'] }}; border-color: rgba(255,255,255,0.08);">
            
            <div class="row align-items-center g-4 pb-4 mb-4 position-relative overflow-hidden"
                 style="background: {{ $pegawai->jabatan_styling['gradient'] }}; margin: -24px -24px 24px -24px; padding: 24px; border-top-left-radius: 12px; border-top-right-radius: 12px; border-bottom: 1px solid rgba(255,255,255,0.08) !important;">
              
              <!-- Badge Jabatan Jenis on top right -->
              <span class="position-absolute top-0 end-0 m-3 badge" style="background-color: {{ $pegawai->jabatan_styling['badge_bg'] }}; color: {{ $pegawai->jabatan_styling['badge_text'] }}; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; z-index: 10;">
                {{ $pegawai->jabatanJenis ? $pegawai->jabatanJenis->nama : '-' }}
              </span>

              <div class="col-md-3 text-center">
                <div class="position-relative d-inline-block">
                  <div class="avatar-glow-ring" style="position: absolute; top: -5px; left: -5px; right: -5px; bottom: -5px; border-radius: 50%; border: 3px solid {{ $pegawai->jabatan_styling['border'] }}; opacity: 0.7; box-shadow: 0 0 20px {{ $pegawai->jabatan_styling['glow'] }};"></div>
                  <img src="{{ $pegawai->foto_url }}" alt="{{ $fullName }}" class="rounded-circle img-thumbnail shadow-lg position-relative z-1" style="width: 130px; height: 130px; object-fit: cover; border: 2px solid {{ $pegawai->jabatan_styling['border'] }}; background: rgba(255,255,255,0.07); backdrop-filter: blur(4px);">
                </div>
              </div>
              <div class="col-md-9 text-center text-md-start position-relative z-1">
                <div class="d-flex flex-wrap gap-2 justify-content-center justify-content-md-start mb-2">
                  <span class="badge rounded-pill px-3 py-1" style="background-color: {{ $pegawai->jabatan_styling['badge_bg'] }}; color: {{ $pegawai->jabatan_styling['badge_text'] }}; font-weight: 600; font-size: 0.75rem;">
                    {{ $pegawai->statusPegawai ? $pegawai->statusPegawai->nama : 'Pegawai' }}
                  </span>
                  <span class="badge bg-success rounded-pill px-3 py-1" style="font-size: 0.75rem; background: rgba(25, 135, 84, 0.2) !important; color: #198754 !important; border: 1px solid #198754;">
                    {{ strtoupper($pegawai->status) }}
                  </span>
                </div>
                <h3 class="text-white mb-1 fw-bold">{{ $fullName }}</h3>
                <h5 class="mb-2 fw-semibold {{ $pegawai->jabatan_styling['text_class'] }}">{{ $pegawai->jabatanNama ? $pegawai->jabatanNama->nama : 'Staff' }}</h5>
                <p class="text-muted small mb-0"><i class="bi bi-shield-check text-success me-1"></i> Terverifikasi oleh Sistem Kepegawaian Daerah</p>
              </div>
            </div>

            <!-- Profile Info Table -->
            <div class="table-responsive">
              <table class="table table-cyber text-white">
                <tbody>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary" width="35%"><i class="bi bi-credit-card-2-front me-2" style="color: var(--card-theme-color);"></i> NIP</td>
                    <td class="border-secondary">{{ $pegawai->nip ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-credit-card me-2" style="color: var(--card-theme-color);"></i> NIK</td>
                    <td class="border-secondary">{{ $pegawai->nik ?? '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-diagram-3 me-2" style="color: var(--card-theme-color);"></i> Bidang Kerja</td>
                    <td class="border-secondary">{{ $pegawai->bidang ? $pegawai->bidang->nama : '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-award me-2" style="color: var(--card-theme-color);"></i> Pangkat / Golongan</td>
                    <td class="border-secondary">{{ $pegawai->pangkat ? $pegawai->pangkat->nama : '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-journal-check me-2" style="color: var(--card-theme-color);"></i> Jenis Jabatan</td>
                    <td class="border-secondary">{{ $pegawai->jabatanJenis ? $pegawai->jabatanJenis->nama : '-' }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-gender-ambiguous me-2" style="color: var(--card-theme-color);"></i> Jenis Kelamin</td>
                    <td class="border-secondary">{{ $pegawai->jenis_kelamin }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-bookmark-star me-2" style="color: var(--card-theme-color);"></i> Agama</td>
                    <td class="border-secondary">{{ $pegawai->agama }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-mortarboard me-2" style="color: var(--card-theme-color);"></i> Pendidikan Terakhir</td>
                    <td class="border-secondary">{{ $pegawai->pendidikan_terakhir }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-telephone me-2" style="color: var(--card-theme-color);"></i> No. Telepon</td>
                    <td class="border-secondary">{{ $pegawai->telpon }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-geo-alt me-2" style="color: var(--card-theme-color);"></i> Alamat</td>
                    <td class="border-secondary">{{ $pegawai->alamat }}</td>
                  </tr>
                  <tr>
                    <td class="text-secondary fw-semibold border-secondary"><i class="bi bi-calendar-event me-2" style="color: var(--card-theme-color);"></i> Periode Kerja</td>
                    <td class="border-secondary">Tahun {{ $pegawai->periode }}</td>
                  </tr>
                </tbody>
              </table>
            </div>

            <!-- TTE Specimen Section -->
            @php
              $tteFile = $pegawai->getfilebyalias('spesimen_tte');
              $tteImg = ($tteFile && $tteFile->exists) ? url($tteFile->public_stream) : '';
            @endphp
            <div class="mt-4 p-3 rounded bg-dark border border-secondary" style="border-color: rgba(255,255,255,0.08) !important;">
              <h6 class="text-white mb-2"><i class="bi bi-patch-check-fill text-success me-2"></i> Verifikasi TTE Elektronik</h6>
              <div class="row align-items-center g-3">
                <div class="col-md-3 text-center">
                  @if($tteImg)
                    <img src="{{ $tteImg }}" alt="Spesimen TTE" class="img-fluid rounded border bg-white p-1" style="max-height: 80px; object-fit: contain;">
                  @else
                    <div class="d-flex align-items-center justify-content-center bg-secondary rounded" style="height: 80px; width: 100%; background-color: rgba(255,255,255,0.05) !important;">
                      <span class="text-muted small">Belum terdaftar</span>
                    </div>
                  @endif
                </div>
                <div class="col-md-9">
                  <p class="text-secondary small mb-0">
                    Aparatur bersangkutan memiliki spesimen tanda tangan elektronik (TTE) resmi yang terdaftar di BSrE (Balai Sertifikasi Elektronik) BSSN untuk keperluan penandatanganan dokumen dinas secara elektronik dan legal.
                  </p>
                </div>
              </div>
            </div>

            <div class="mt-4 d-flex justify-content-start gap-2">
              <a href="{{ route('pegawai') }}" class="btn-cyber-outline"><i class="bi bi-arrow-left"></i> Kembali ke Daftar</a>
            </div>

          </div>

        </div>

        <!-- Sidebar Column -->
        @include('frontend.partials.sidebar')
      </div>
    </div>
  </section>

</main>

<style>
  .table-cyber {
    background: transparent;
  }
  .table-cyber td {
    padding: 12px;
  }
  .text-purple {
    color: #c084fc !important;
  }
  .employee-detail-card {
    box-shadow: 0 10px 30px var(--card-glow-color, rgba(0, 242, 254, 0.05));
    transition: box-shadow 0.3s ease;
  }
</style>
@endsection

