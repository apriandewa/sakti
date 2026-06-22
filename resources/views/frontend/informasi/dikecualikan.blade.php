@extends('frontend.main')

@section('container')

<main class="main">

  <section id="services" class="services section">

    <div class="container section-title text-center" data-aos="fade-up">
      <h2>Daftar Informasi Publik Dikecualikan</h2>
      <p>Berikut adalah Daftar Informasi Publik dan Informasi Kegiatan PPID Kabupaten Indragiri Hulu</p>

      <div class="col-lg-5 col-12 mx-auto">
        {{-- ✅ action pakai route name --}}
        <form action="{{ route('informasi.dikecualikan') }}" method="get"
              class="position-relative rounded-pill m-3" role="search">
          <div class="input-group">
            <input
              name="search"
              type="search"
              class="form-control"
              id="search"
              placeholder="Cari Informasi Dikecualikan Disini ..."
              aria-label="Search"
              value="{{ request('search') }}"
            >
            <button type="submit"
                    class="input-group-text bg-primary text-dark border-0 px-3">
              <i class="bi bi-search"></i> Cari
            </button>
          </div>
        </form>
      </div>
    </div>

    <div class="container">
      <div class="row gy-4">

      {{-- ===== Ringkasan Jumlah Data ===== --}}
      @if(isset($apiTotal) && $apiTotal > 0)
        <div class="row mb-4" data-aos="fade-up">
          <div class="col-12">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-3 rounded-3"
                 style="background: linear-gradient(135deg, #e8f4fd 0%, #d1ecf1 100%); border: 1px solid #bee5eb;">
              {{-- Total keseluruhan --}}
              <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width:42px;height:42px;background:#0d6efd;flex-shrink:0;">
                  <i class="bi bi-files text-white fs-5"></i>
                </div>
                <div>
                  <div class="fw-bold fs-4 lh-1" style="color:#0d6efd;">{{ number_format($apiTotal) }}</div>
                  <div class="text-muted small">Total Dokumen</div>
                </div>
              </div>

              {{-- Info halaman --}}
              @if($apiData->count() > 0)
                <div class="text-muted small">
                  <i class="bi bi-info-circle me-1"></i>
                  Menampilkan
                  <strong>{{ $apiData->firstItem() }}–{{ $apiData->lastItem() }}</strong>
                  dari <strong>{{ $apiData->total() }}</strong> data
                  @if(request('search'))
                    &nbsp;&mdash; pencarian <strong>"{{ request('search') }}"</strong>
                  @endif
                </div>
              @endif

              {{-- Tombol reset jika ada pencarian --}}
              @if(request('search'))
                <a href="{{ route('informasi.dikecualikan') }}" class="btn btn-sm btn-outline-secondary">
                  <i class="bi bi-x-circle me-1"></i> Reset
                </a>
              @endif
            </div>
          </div>
        </div>
      @endif

      {{-- ===== Daftar Item ===== --}}
      <div class="row gy-4">

        @if($apiData->count() > 0)

          @foreach($apiData as $item)
            @php
              $tipe      = $item['tipe_label'] ?? 'DIKECUALIKAN';
              $badgeClass = match(strtoupper($tipe)) {
                  'BERKALA'      => 'bg-success',
                  'TERSEDIA'     => 'bg-primary',
                  'SETIAP SAAT'  => 'bg-info text-dark',
                  'SERTA MERTA'  => 'bg-info text-dark',
                  'DIKECUALIKAN' => 'bg-danger',
                  'TRANSPARANSI' => 'bg-warning text-dark',
                  default        => 'bg-secondary',
              };
              $icon = match(strtoupper($tipe)) {
                  'BERKALA'      => 'bi-arrow-repeat',
                  'TERSEDIA'     => 'bi-check-circle',
                  'SETIAP SAAT'  => 'bi-clock-history',
                  'SERTA MERTA'  => 'bi-clock-history',
                  'DIKECUALIKAN' => 'bi-shield-lock',
                  'TRANSPARANSI' => 'bi-currency-dollar',
                  default        => 'bi-globe',
              };
              $isDb = $item['is_db'] ?? false;
            @endphp

            <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
              <div class="service-item d-flex position-relative h-100">
                <i class="bi {{ $icon }} icon flex-shrink-0"></i>
                <div class="w-100">

                  {{-- Badge tipe --}}
                  <span class="badge {{ $badgeClass }} mb-1" style="font-size:.7rem;">
                    {{ $tipe }}
                  </span>

                  {{-- Judul --}}
                  <h4 class="title mt-1 mb-2">
                    @if(isset($item['urlDownload']) && $item['urlDownload'] !== '#')
                      <a href="{{ $item['urlDownload'] }}" target="_blank" rel="noopener">
                        {{ $item['judul'] ?? $item['name'] ?? '-' }}
                      </a>
                    @else
                      {{ $item['judul'] ?? $item['name'] ?? '-' }}
                    @endif
                  </h4>

                  {{-- Tahun --}}
                  <p class="description mb-1">
                    <i class="bi bi-calendar-event me-2"></i>
                    Tahun: <strong>{{ $item['tahun_pengundangan'] ?? $item['tahun'] ?? '-' }}</strong>
                  </p>

                  {{-- File / Dokumen --}}
                  @if($isDb && !empty($item['db_files']))
                    {{-- Data dari DB: tampilkan semua berkas --}}
                    @foreach($item['db_files'] as $file)
                      <p class="description mb-1">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                        <a href="{{ $file['url'] }}" target="_blank" rel="noopener">
                          {{ $file['name'] }}
                        </a>
                      </p>
                    @endforeach
                  @elseif(!empty($item['berkas_files']) && is_array($item['berkas_files']))
                    {{-- Data dari API Transparansi: tampilkan semua berkas --}}
                    @foreach($item['berkas_files'] as $idx => $file)
                      <p class="description mb-1">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                        <a href="{{ $file['url'] }}" target="_blank" rel="noopener">
                          Lihat Dokumen {{ $idx + 1 }}: {{ $file['name'] }}
                        </a>
                      </p>
                    @endforeach
                  @elseif(!empty($item['berkas']) && is_array($item['berkas']))
                    {{-- Format lama --}}
                    @foreach($item['berkas'] as $idx => $file)
                      <p class="description mb-1">
                        <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                        <a href="{{ isset($item['is_db']) && $item['is_db'] ? $file : $item['source_url'] . 'storage/' . $file }}" target="_blank" rel="noopener">
                          Dokumen {{ $idx + 1 }}: {{ basename($file) }}
                        </a>
                      </p>
                    @endforeach
                  @elseif(isset($item['urlDownload']) && $item['urlDownload'] !== '#')
                    {{-- Fallback: tampilkan 1 file --}}
                    <p class="description mb-1">
                      <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                      <a href="{{ $item['urlDownload'] }}" target="_blank" rel="noopener">
                        {{ $item['fileDownload'] ?? 'Lihat Dokumen' }}
                      </a>
                    </p>
                  @else
                    <p class="description mb-1">
                      <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                      <span class="text-muted">Tidak ada file</span>
                    </p>
                  @endif

                  {{-- Sumber --}}
                  <p class="description mb-0">
                    <i class="bi bi-link-45deg me-2"></i>
                    Sumber:
                    <a href="{{ $item['source_url'] ?? '#' }}" target="_blank" rel="noopener">
                      {{ $item['source_name'] ?? 'Tidak diketahui' }}
                    </a>
                  </p>

                </div>
              </div>
            </div>
          @endforeach

        @else
          <div class="col-12">
            <div class="alert alert-warning text-center py-4">
              <i class="bi bi-exclamation-triangle fs-3 d-block mb-2"></i>
              @if(request('search'))
                Tidak ada data yang cocok dengan pencarian
                <strong>"{{ request('search') }}"</strong>.
                <br><a href="{{ route('informasi.dikecualikan') }}" class="btn btn-sm btn-outline-secondary mt-2">
                  <i class="bi bi-arrow-left me-1"></i> Tampilkan Semua
                </a>
              @else
                Data informasi publik belum tersedia.
              @endif
            </div>
          </div>
        @endif

      </div>

      {{-- Pagination --}}
      @if(method_exists($apiData, 'links'))
        <div class="d-flex justify-content-center mt-4">
          {!! $apiData->links() !!}
        </div>
      @endif
    </div>

  </section>

</main>

@endsection