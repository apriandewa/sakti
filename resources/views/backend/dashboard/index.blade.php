@extends('backend.main.index')
@push('title', 'Dashboard')
@section('content')
    <div class="content-wrapper">
        <div class="container-full">
            <section class="content">
                <div class="row align-items-end">
                    <div class="col-md-7 col-12">
                        <div class="box bg-primary-light">
                            <div class="box-body">
                                <div class="row align-items-center">
                                    <div class="col-12 col-lg-8">
                                        <h3 class="fs-26 text-dark">Selamat Datang <br> {{ $user->name }}</h3>
                                        <p class="text-dark mb-2 fs-20">
                                            Website {!! config('master.app.profile.name') !!}
                                        </p>
                                    </div>
                                    <div class="col-12 col-lg-4">
                                        <img src="{{ url($template."/images/svg-icon/color-svg/custom-14.svg") }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-md-4 order-1">
                        <div class="row">
                            <div class="col-lg-4 col-md-6 col-4 mb-4">
                                <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="{{ url($template."/images/svg-icon/color-svg/berita.svg") }}"
                                        alt="Credit Card"
                                        class="rounded"
                                        />
                                    </div>
                                    
                                    </div>
                                    <span>Berita Saya</span>
                                    <h3 class="card-title text-nowrap mb-1 text-warning">{{ $berita_saya }}</h3>
                                    
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-4 mb-4">
                                <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="{{ url($template."/images/svg-icon/color-svg/galeri.svg") }}"
                                        alt="Credit Card"
                                        class="rounded"
                                        />
                                    </div>
                                    
                                    </div>
                                    <span>Galeri Saya</span>
                                    <h3 class="card-title text-nowrap mb-1 text-warning">{{ $galeri_saya }}</h3>
                                    
                                </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-6 col-4 mb-4">
                                <div class="card">
                                <div class="card-body">
                                    <div class="card-title d-flex align-items-start justify-content-between">
                                    <div class="avatar flex-shrink-0">
                                        <img
                                        src="{{ url($template."/images/svg-icon/color-svg/unduhan.svg") }}"
                                        alt="Credit Card"
                                        class="rounded"
                                        />
                                    </div>
                                    
                                    </div>
                                    <span>Unduhan Saya</span>
                                    <h3 class="card-title text-nowrap mb-1 text-warning">{{ $unduhan_saya }}</h3>
                                    
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
                            src="{{ url($template."/images/svg-icon/color-svg/berita.svg") }}"
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
                            src="{{ url($template."/images/svg-icon/color-svg/galeri.svg") }}"
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
                            src="{{ url($template."/images/svg-icon/color-svg/unduhan.svg") }}"
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
