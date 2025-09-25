<div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">

              <div class="service-box">
                <div class="services-list"> 
                  <h4>Pencarian</h4>
                    <div class="col-12 mx-auto">      
                        <form action="/berita" method="get"     class="position-relative rounded-pill m-3" role="search">
                          @if (request('kategori'))
                              <input type="hidden" name="kategori" value="{{ request('kategori') }}">
                          @endif
                          @if (request('author'))
                              <input type="hidden" name="author" value="{{ request('author') }}">
                          @endif
                            <div class="input-group input-group">
                                <input name="search" type="search" class="form-control" id="search" placeholder="Cari Berita Disini ..."
                                    aria-label="Search" value= {{request('search')}}>
                  
                                <button type="submit" class="input-group-text bg-primary text-dark border-0 px-3" id="submit">
                                  <i class="bi bi-search"></i>  Cari
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
              </div>
              <!-- End Services List -->

              <div class="service-box">
                  <div class="services-list">
                      <h4>Kategori Berita</h4>
                      @foreach($beritaList as $berita)
                          @php
                              $slugBerita = str_replace(' ', '-', strtolower($berita));
                          @endphp
                          <a href="{{ url('berita') }}?kategori={{ $slugBerita }}"
                            class="{{ request('kategori') == $slugBerita ? 'active' : '' }}">
                              <i class="bi bi-arrow-right-circle"></i>
                              <span>{{ $berita }}</span>
                          </a>

                      @endforeach
                  </div>
              </div>    
              <!-- End Services List -->

              <!-- End Services List -->
              <div class="service-box">
                  <div class="services-list">
                      <h4>Kategori galeri</h4>
                      @foreach($galeriList as $galeri)
                          @php
                              $slugGaleri = str_replace(' ', '-', strtolower($galeri));
                          @endphp
                          <a href="{{ url('galeri') }}?kategori={{ $slugGaleri }}"
                            class="{{ request('kategori') == $slugGaleri ? 'active' : '' }}">
                              <i class="bi bi-arrow-right-circle"></i>
                              <span>{{ $galeri }}</span>
                          </a>

                      @endforeach
                  </div>
              </div>    
              <!-- End Services List -->

              <div class="service-box">
                  <div class="services-list">
                      <h4>Kategori Unduhan</h4>
                      @foreach($unduhanList as $unduhan)
                          @php
                              $slugUnduhan = str_replace(' ', '-', strtolower($unduhan));
                          @endphp
                          <a href="{{ url('unduhan') }}?kategori={{ $slugUnduhan }}"
                            class="{{ request('kategori') == $slugUnduhan ? 'active' : '' }}">
                              <i class="bi bi-arrow-right-circle"></i>
                              <span>{{ $unduhan }}</span>
                          </a>

                      @endforeach
                  </div>
              </div>    
              <!-- End Services List -->

              <div class="service-box">
                <div class="services-list">
                  <h4>Berita Terbaru</h4>
                  @foreach($latestNews as $news)
                    <a href="{{ url('berita/'.$news->slug) }}" class="d-flex align-items-center mb-3">
                        
                        {{-- Thumbnail pakai getfilebyalias --}}
                        @if(!is_null($news->getfilebyalias('gambar_berita')))
                            @php
                                $file = $news->getfilebyalias('gambar_berita');
                            @endphp
                            @if($file)
                                <img src="{{ url($file->public_stream) }}" 
                                    alt="{{ $file->name }}" 
                                    class="me-2 rounded"
                                    style="width:60px; height:50px; object-fit:cover;">
                            @endif
                        @endif

                        {{-- Judul & tanggal --}}
                      <div>
                      <div class="fw-bold">{{ $news->nama }}</div>
                        <small class="text-muted d-flex align-items-center gap-2">
                          <span><i class="bi bi-calendar"></i> {{ $news->created_at->format('d M Y') }}</span>
                          <span><i class="bi bi-eye"></i> {{ $news->views ?? 0 }} kali</span>
                        </small>
                      </div>
                    </a>
                  @endforeach
                </div>
              </div>


              <div class="service-box">
                <h4>Download Catalog</h4>
                <div class="services-list">
                  <a href="#"><i class="bi bi-filetype-pdf"></i><span>Catalog PDF</span></a>
                  <a href="#"><i class="bi bi-file-earmark-word"></i><span>Catalog DOC</span></a>
                </div>
              </div><!-- End Services List -->

              <div class="help-box d-flex flex-column justify-content-center align-items-center">
                <i class="bi bi-headset help-icon"></i>
                <h4>Have a Question?</h4>
                <p class="d-flex align-items-center mt-2 mb-0"><i class="bi bi-telephone me-2"></i> <span>+1 5589 55488 55</span></p>
                <p class="d-flex align-items-center mt-1 mb-0"><i class="bi bi-envelope me-2"></i> <a href="mailto:contact@example.com">contact@example.com</a></p>
              </div>

          </div>