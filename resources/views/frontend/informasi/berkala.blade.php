@extends('frontend.main')

    @section('container')

  
  <main class="main">

    <!-- Blog Posts Section -->
    <section  id="services" class="services section">

       <!-- Section Title -->
      <div class="container section-title text-center" data-aos="fade-up">
        <h2>Daftar Informasi Publik Berkala</h2>
        <p>Berikut adalah Daftar Informasi Publik dan Informasi Kegiatan PPID Kabupaten Indragiri Hulu</p>
       <div class="col-lg-5 col-12 mx-auto">
          {{-- ✅ action pakai route name, bukan URL dengan spasi --}}
          <form action="{{ route('informasi.berkala') }}" method="get"
                class="position-relative rounded-pill m-3" role="search">

              <div class="input-group">
                  <input
                      name="search"
                      type="search"
                      class="form-control"
                      id="search"
                      placeholder="Cari Informasi Berkala Disini ..."
                      aria-label="Search"
                      value="{{ request('search') }}"  {{-- ✅ pakai quotes --}}
                  >
                  <button type="submit"
                          class="input-group-text bg-primary text-dark border-0 px-3"
                          id="submit">
                      <i class="bi bi-search"></i> Cari
                  </button>
              </div>
          </form>
      </div>
      </div>
      <!-- End Section Title -->

      <div class="container">

        <div class="row gy-4">

        {{-- ✅ Tambahkan info jumlah data di sini --}}
          @if($apiData->count() > 0)
            <div class="col-12 mb-2">
              <p class="text-muted small mb-0">
                <i class="bi bi-info-circle me-1"></i>
                Menampilkan
                <strong>{{ $apiData->firstItem() }}–{{ $apiData->lastItem() }}</strong>
                dari <strong>{{ $apiData->total() }}</strong> data
                @if(request('search'))
                  untuk pencarian <strong>"{{ request('search') }}"</strong>
                @endif
              </p>
            </div>
          @endif


          @if($apiData->count() > 0)
              @foreach($apiData as $item)
              <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="service-item d-flex position-relative h-100">
                  <i class="bi bi-files icon flex-shrink-0"></i>
                  <div>
                    <h4 class="title">
                      <a href="{{ $item['urlDownload'] ?? '#' }}" target="_blank">
                        {{ $item['name'] ?? '-' }}
                      </a>
                    </h4>
                    <p class="description mb-1">
                      <i class="bi bi-calendar-event me-2"></i>
                      Tahun: {{ $item['tahun'] ?? '-' }}
                    </p>
                    <p class="description mb-1">
                      
                      @if(!empty($item['berkas']) && is_array($item['berkas']))
                          @foreach($item['berkas'] as $file)
                              <div>
                                <i class="bi bi-file-earmark-pdf text-danger me-2"></i>
                                  <a href="{{ $item['source_url'] . 'storage/' . $file }}" target="_blank">
                                      Lihat Dokumen : {{ $loop->iteration }}
                                  </a>
                              </div>
                          @endforeach
                      @else
                          Tidak ada file
                      @endif
                    </p>

                    <p class="description mb-1">
                      <i class="bi bi-link-45deg me-2"></i>
                      Sumber:
                      <a href="{{ $item['source_url'] ?? '#' }}" target="_blank">
                        {{ $item['source_name'] ?? 'Tidak diketahui' }}
                      </a>
                    </p>

                  </div>
                </div>
              </div>
            @endforeach
          @else
              <div class="col-12">
                  <div class="alert alert-warning text-center">
                      @if(request('search'))
                          Tidak ada data yang cocok dengan pencarian
                          <strong>"{{ request('search') }}"</strong>.
                      @else
                          Data dari sumber belum tersedia.
                      @endif
                  </div>
              </div>
          @endif
          
        </div>



        @if(method_exists($apiData, 'links'))
          <div class="d-flex justify-content-center mt-4">
            {!! $apiData->links() !!}
          </div>
        @endif
      </div>


    </section>
    <!-- /Blog Posts Section -->
    
  </main>

@endsection
