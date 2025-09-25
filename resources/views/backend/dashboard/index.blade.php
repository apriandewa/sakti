@extends('backend.main.index')
@push('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row align-items-end">
                    <div class="col-md-6 col-12">
                        <div class="box bg-primary-light overflow-hidden pull-up">
                            <div class="box-body pe-0 ps-lg-50 ps-15 py-0">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-8">
                                        <h1 class="fs-40 text-dark">Halo, {{ $user->name }}!</h1>
                                        <p class="text-dark mb-0 fs-20">
                                            Selamat datang di {!! config('master.app.profile.name') !!}, 
                                        </p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <img src="{{ url($template."/images/svg-icon/color-svg/custom-15.svg") }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-4 order-1">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-4 mb-4">
                                <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="{{ url($template."/images/svg-icon/color-svg/010-refresh.svg") }}"
                                        alt="Credit Card"
                                        class="rounded"
                                        />
                                    </div>
                                    
                                    </div>
                                    <span>Berita Saya</span>
                                    <h3 class="card-title text-nowrap mb-1 text-info">{{ $berita_saya }}</h3>
                                    
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-4 mb-4">
                                <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="{{ url($template."/images/svg-icon/color-svg/010-refresh.svg") }}"
                                        alt="Credit Card"
                                        class="rounded"
                                        />
                                    </div>
                                    
                                    </div>
                                    <span>Galeri Saya</span>
                                    <h3 class="card-title text-nowrap mb-1 text-info">{{ $galeri_saya }}</h3>
                                    
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-4 mb-4">
                                <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="{{ url($template."/images/svg-icon/color-svg/010-refresh.svg") }}"
                                        alt="Credit Card"
                                        class="rounded"
                                        />
                                    </div>
                                    
                                    </div>
                                    <span>Unduhan Saya</span>
                                    <h3 class="card-title text-nowrap mb-1 text-info">{{ $unduhan_saya }}</h3>
                                    
                                </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-6 order-1">
              <div class="row">
               
                <div class="col-lg-4 col-4 col-md-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <img
                            src="{{ url($template."/images/svg-icon/color-svg/010-refresh.svg") }}"
                            alt="Credit Card"
                            class="rounded"
                          />
                        </div>
                        
                      </div>
                      <span>Semua Berita</span>
                      <h3 class="card-title text-nowrap mb-1 text-primary">{{ $jml_berita }}</h3>
                      
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-4 col-md-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <img
                            src="{{ url($template."/images/svg-icon/color-svg/010-refresh.svg") }}"
                            alt="Credit Card"
                            class="rounded"
                          />
                        </div>
                        
                      </div>
                      <span>Semua Galeri</span>
                      <h3 class="card-title text-nowrap mb-1 text-primary">{{ $jml_galeri }}</h3>
                      
                    </div>
                  </div>
                </div>
                <div class="col-lg-4 col-4 col-md-6 mb-4">
                  <div class="card">
                    <div class="card-body">
                      <div class="card-title d-flex align-items-start justify-content-between">
                        <div class="avatar flex-shrink-0">
                          <img
                            src="{{ url($template."/images/svg-icon/color-svg/010-refresh.svg") }}"
                            alt="Credit Card"
                            class="rounded"
                          />
                        </div>
                        
                      </div>
                      <span>Semua Unduhan</span>
                      <h3 class="card-title text-nowrap mb-1 text-primary">{{ $jml_unduhan }}</h3>
                      
                    </div>
                  </div>
                </div>
              </div>
            
            </div>


                    @include('backend.main.menu.announcement')
                </div>
            </section>
        </div>
    </div>
@endsection
@push('js')
    <script src="{{ url($template.'/assets/vendor_components/jquery-validation-1.17.0/lib/jquery.form.js') }}"></script>
    <script src="{{ url('js/jquery-crud.js?id='.time()) }}"></script>
@endpush
