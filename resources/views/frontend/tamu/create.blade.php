@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Buku Tamu</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Buku Tamu Elektronik (e-Tamu)' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Silakan isi formulir kunjungan resmi atau konsultasi Anda pada Dinas Komunikasi dan Informatika.' }}</p>
    </div>
  </div>

  <section id="buku-tamu" class="section-dark">
    <div class="container">
      <div class="row gy-4">

        <!-- Form Column -->
        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="150">

          <!-- Alert success -->
          @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <!-- Alert error -->
          @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <strong>Mohon periksa kembali data Anda:</strong>
              <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $err)
                  <li>{{ $err }}</li>
                @endforeach
              </ul>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          <!-- Verifikasi Lokasi -->
          <div class="glass-card p-0 overflow-hidden mb-4" id="locationGate" @if($lokasiVerified) style="display:none;" @endif>
            <div class="d-flex align-items-center gap-3 py-3 px-4 border-bottom border-secondary" style="background: rgba(0, 242, 254, 0.1);">
              <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white" style="width: 44px; height: 44px; box-shadow: 0 0 10px var(--brand-blue-glow);">
                <i class="bi bi-geo-alt-fill fs-5"></i>
              </div>
              <div>
                <h5 class="mb-0 text-white fw-bold">Verifikasi Lokasi</h5>
                <small class="text-secondary">Pastikan Anda berada di area kantor</small>
              </div>
            </div>

            <div class="p-4 text-center">
              <div class="mb-4">
                <i class="bi bi-building text-info" style="font-size: 3.5rem; opacity: 0.85;"></i>
              </div>
              <p class="text-white mb-2">Untuk mengisi buku tamu, Anda harus berada di area kantor.</p>
              <p class="text-secondary small mb-4">
                <i class="bi bi-pin-map me-1"></i>{!! nl2br(e($officeName)) !!}<br>
                <span class="text-muted">Radius diizinkan: {{ $officeRadius }} meter</span>
              </p>
              @if($lokasiTersedia)
                <button type="button" class="btn-cyber px-4 py-2" id="btnIzinkanLokasi">
                  <i class="bi bi-crosshair me-2"></i>Izinkan Lokasi
                </button>
                <p class="text-muted small mt-3 mb-0">
                  <i class="bi bi-info-circle me-1"></i>Browser akan meminta izin akses lokasi perangkat Anda.
                </p>
              @else
                <div class="alert alert-warning rounded-3 mb-0 text-start">
                  <i class="bi bi-exclamation-triangle-fill me-2"></i>
                  Koordinat kantor belum dikonfigurasi. Admin dapat mengatur kolom <strong>Alamat</strong> dan <strong>Peta</strong> di menu Pengaturan.
                </div>
              @endif
            </div>
          </div>

          <!-- Glass Form Card -->
          <div class="glass-card p-0 overflow-hidden" id="formSection" @if(!$lokasiVerified) style="display:none;" @endif>
            
            <!-- Card Header -->
            <div class="d-flex align-items-center gap-3 py-3 px-4 border-bottom border-secondary" style="background: rgba(0, 242, 254, 0.1);">
              <div class="rounded-circle d-flex align-items-center justify-content-center bg-info text-white" style="width: 44px; height: 44px; box-shadow: 0 0 10px var(--brand-blue-glow);">
                <i class="bi bi-journal-text fs-5"></i>
              </div>
              <div>
                <h5 class="mb-0 text-white fw-bold">Isi Buku Tamu</h5>
                <small class="text-secondary">Silakan lengkapi data kunjungan Anda</small>
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-4">
              <form action="{{ route('kunjungan.store') }}" method="POST" enctype="multipart/form-data" id="formTamu" novalidate autocomplete="off" class="form-cyber">
                @csrf
                <input type="hidden" name="user_latitude" id="userLatitude" value="{{ session('buku_tamu_user_lat') }}">
                <input type="hidden" name="user_longitude" id="userLongitude" value="{{ session('buku_tamu_user_lng') }}">

                <!-- Section: Data Diri -->
                <div class="d-flex align-items-center gap-2 mb-3 mt-1">
                  <i class="bi bi-person-fill text-success"></i>
                  <span class="small fw-semibold text-uppercase text-secondary ls-1">Data Diri</span>
                  <hr class="flex-grow-1 my-0 border-secondary">
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-7">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror" placeholder="Masukkan nama lengkap Anda" value="{{ old('nama') }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>

                  <div class="col-sm-5">
                    <label class="form-label d-block">Jenis Kelamin <span class="text-danger">*</span></label>
                    <div class="d-flex gap-3 pt-2">
                      @foreach(['Laki-laki' => '👨 Laki-laki', 'Perempuan' => '👩 Perempuan'] as $jk => $label)
                        <div class="form-check">
                          <input class="form-check-input" type="radio" name="jenis_kelamin" id="jk_{{ Str::slug($jk) }}" value="{{ $jk }}" {{ old('jenis_kelamin') === $jk ? 'checked' : '' }}>
                          <label class="form-check-label text-white small" for="jk_{{ Str::slug($jk) }}">
                            {{ $label }}
                          </label>
                        </div>
                      @endforeach
                    </div>
                    @error('jenis_kelamin')
                      <div class="text-danger small mt-2 d-block">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="email@contoh.com" value="{{ old('email') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label">No. HP / Telepon</label>
                    <input type="tel" name="no_hp" class="form-control @error('no_hp') is-invalid @enderror" placeholder="08xx-xxxx-xxxx" value="{{ old('no_hp') }}">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>

                <!-- Section: Asal & Pekerjaan -->
                <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                  <i class="bi bi-building text-success"></i>
                  <span class="small fw-semibold text-uppercase text-secondary">Asal &amp; Pekerjaan</span>
                  <hr class="flex-grow-1 my-0 border-secondary">
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <label class="form-label">Pekerjaan / Instansi</label>
                    <select name="pekerjaan" class="form-select @error('pekerjaan') is-invalid @enderror">
                      <option value="">-- Pilih Pekerjaan / Instansi --</option>
                      @foreach($pekerjaan as $item)
                        <option value="{{ $item->nama }}" {{ old('pekerjaan') === $item->nama ? 'selected' : '' }}>
                          {{ $item->nama }}
                        </option>
                      @endforeach
                    </select>
                    @error('pekerjaan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label">Asal Instansi / Sekolah / Daerah</label>
                    <input type="text" name="asal" class="form-control @error('asal') is-invalid @enderror" placeholder="Nama instansi, sekolah, atau universitas" value="{{ old('asal') }}">
                    @error('asal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label">Alamat Lengkap</label>
                  <textarea name="alamat" rows="2" class="form-control @error('alamat') is-invalid @enderror" placeholder="Alamat lengkap (opsional)">{{ old('alamat') }}</textarea>
                  @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Section: Keperluan -->
                <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                  <i class="bi bi-clipboard-check text-success"></i>
                  <span class="small fw-semibold text-uppercase text-secondary">Keperluan Kunjungan</span>
                  <hr class="flex-grow-1 my-0 border-secondary">
                </div>

                <div class="mb-3">
                  <label class="form-label">Keperluan <span class="text-danger">*</span></label>
                  <select name="keperluan" class="form-select @error('keperluan') is-invalid @enderror" required>
                    <option value="">-- Pilih Keperluan --</option>
                    @foreach($keperluan as $item)
                      <option value="{{ $item->nama }}" {{ old('keperluan') === $item->nama ? 'selected' : '' }}>
                        {{ $item->nama }}
                      </option>
                    @endforeach
                  </select>
                  @error('keperluan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                  <label class="form-label">Pesan / Kesan &amp; Saran</label>
                  <textarea name="pesan" id="pesanInput" rows="4" maxlength="1000" class="form-control @error('pesan') is-invalid @enderror" placeholder="Tuliskan pesan, kesan, atau saran Anda...">{{ old('pesan') }}</textarea>
                  <div class="text-end mt-1">
                    <small class="text-muted" id="pesanCount">0 / 1000</small>
                  </div>
                  @error('pesan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Section: Lampiran -->
                <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                  <i class="bi bi-paperclip text-success"></i>
                  <span class="small fw-semibold text-uppercase text-secondary">Lampiran</span>
                  <hr class="flex-grow-1 my-0 border-secondary">
                </div>

                <div class="row g-3 mb-3">
                  <!-- Foto Diri -->
                  <div class="col-sm-6">
                    <label class="form-label">Foto Wajah / Diri <span class="fw-normal text-muted">(opsional)</span></label>

                    @if (session('foto_tmp'))
                      <input type="hidden" name="foto_tmp" value="{{ session('foto_tmp') }}">
                      <div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center gap-2 rounded-3" id="fotoPreviewBox" style="background: rgba(0, 242, 254, 0.1); border-color: rgba(0, 242, 254, 0.2);">
                        @php
                          $fotoMime = session('foto_tmp_mime', '');
                          $fotoIsImg = str_starts_with($fotoMime, 'image/');
                        @endphp
                        @if ($fotoIsImg)
                          <img src="{{ Storage::disk('public')->url(session('foto_tmp')) }}" alt="preview foto" class="rounded" style="width:48px;height:48px;object-fit:cover;border:2px solid var(--accent-color);">
                        @else
                          <div class="rounded d-flex align-items-center justify-content-center bg-white border" style="width:48px;height:48px;min-width:48px;">
                            <i class="bi bi-image fs-4 text-info"></i>
                          </div>
                        @endif
                        <div class="flex-grow-1 overflow-hidden">
                          <div class="small fw-semibold text-white text-truncate">
                            {{ session('foto_tmp_name', 'foto-sebelumnya') }}
                          </div>
                          <div class="text-muted" style="font-size:11px">
                            <i class="bi bi-info-circle me-1"></i>File tersimpan sementara
                          </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 flex-shrink-0" onclick="hapusTmp('foto')" title="Hapus dan upload ulang">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </div>
                    @endif

                    <div class="border rounded-3 p-3 text-center bg-dark" id="fotoUploadBox" style="{{ session('foto_tmp') ? 'display:none;' : '' }} border-style:dashed!important; border-color: var(--glass-border) !important;">
                      <i class="bi bi-camera fs-3 text-muted d-block mb-2"></i>
                      <div class="d-flex justify-content-center gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-cyber" onclick="document.getElementById('fotoInput').setAttribute('capture', 'environment'); document.getElementById('fotoInput').click();">
                          <i class="bi bi-camera me-1"></i> Kamera
                        </button>
                        <button type="button" class="btn btn-sm btn-cyber-outline" onclick="document.getElementById('fotoInput').removeAttribute('capture'); document.getElementById('fotoInput').click();">
                          <i class="bi bi-images me-1"></i> Galeri
                        </button>
                      </div>
                      <input type="file" name="foto" id="fotoInput" class="form-control d-none @error('foto') is-invalid @enderror" accept="image/*">
                      <div id="fotoFileName" class="small fw-semibold mt-2 text-success"></div>
                      <div class="form-text text-muted" id="fotoHelpText">Maks. 2MB</div>
                    </div>
                    @error('foto')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>

                  <!-- Dokumen Pendukung -->
                  <div class="col-sm-6">
                    <label class="form-label">Dokumen Pendukung <span class="fw-normal text-muted">(opsional)</span></label>

                    @if (session('dokumen_tmp'))
                      <input type="hidden" name="dokumen_tmp" value="{{ session('dokumen_tmp') }}">
                      <div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center gap-2 rounded-3" id="dokumenPreviewBox" style="background: rgba(0, 242, 254, 0.1); border-color: rgba(0, 242, 254, 0.2);">
                        @php
                          $dokExt = strtolower(pathinfo(session('dokumen_tmp_name', ''), PATHINFO_EXTENSION));
                          $dokIcon = $dokExt === 'pdf' ? 'bi-file-earmark-pdf text-danger' : 'bi-file-earmark-image text-primary';
                        @endphp
                        <div class="rounded d-flex align-items-center justify-content-center bg-white border" style="width:48px;height:48px;min-width:48px;">
                          <i class="bi {{ $dokIcon }} fs-4"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                          <div class="small fw-semibold text-white text-truncate">
                            {{ session('dokumen_tmp_name', 'dokumen-sebelumnya') }}
                          </div>
                          <div class="text-muted" style="font-size:11px">
                            <i class="bi bi-info-circle me-1"></i>File tersimpan sementara
                          </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1 flex-shrink-0" onclick="hapusTmp('dokumen')" title="Hapus dan upload ulang">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </div>
                    @endif

                    <div class="border rounded-3 p-3 text-center bg-dark" id="dokumenUploadBox" style="{{ session('dokumen_tmp') ? 'display:none;' : '' }} border-style:dashed!important; border-color: var(--glass-border) !important;">
                      <i class="bi bi-file-earmark-arrow-up fs-3 text-muted d-block mb-2"></i>
                      <input type="file" name="dokumen" id="dokumenInput" class="form-control @error('dokumen') is-invalid @enderror" accept=".pdf,.jpg,.jpeg,.png">
                      <div class="form-text text-muted mt-2">SPT, Surat Jalan, dll · PDF/JPG/PNG · Maks. 5MB</div>
                    </div>
                    @error('dokumen')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <!-- Section: Verifikasi -->
                <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                  <i class="bi bi-shield-check text-success"></i>
                  <span class="small fw-semibold text-uppercase text-secondary">Verifikasi</span>
                  <hr class="flex-grow-1 my-0 border-secondary">
                </div>

                <div class="mb-2">
                  <x-captcha />
                </div>

              </form>
            </div>

            <!-- Card Footer -->
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-top border-secondary" style="background: rgba(15, 23, 42, 0.4);">
              <small class="text-secondary"><span class="text-danger">*</span> Wajib diisi</small>
              <div class="d-flex gap-2">
                <button type="reset" form="formTamu" class="btn-cyber-outline" style="font-size: 0.85rem; padding: 6px 16px;">
                  <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
                <button type="submit" form="formTamu" class="btn-cyber" id="submitBtn" style="font-size: 0.85rem; padding: 6px 20px;">
                  <i class="bi bi-send me-1"></i>
                  <span id="submitLabel">Kirim Buku Tamu</span>
                </button>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const locationGate = document.getElementById('locationGate');
  const formSection  = document.getElementById('formSection');
  const btnLokasi    = document.getElementById('btnIzinkanLokasi');
  const latInput     = document.getElementById('userLatitude');
  const lngInput     = document.getElementById('userLongitude');
  const swalTheme    = {
    confirmButtonColor: '#059669',
    cancelButtonColor: '#475569',
    background: '#0b1528',
    color: '#fff'
  };

  function showForm() {
    if (locationGate) locationGate.style.display = 'none';
    if (formSection)  formSection.style.display = '';
  }

  function geolocationErrorMessage(error) {
    switch (error.code) {
      case error.PERMISSION_DENIED:
        return 'Akses lokasi ditolak. Aktifkan izin lokasi di browser Anda lalu coba lagi.';
      case error.POSITION_UNAVAILABLE:
        return 'Informasi lokasi tidak tersedia. Pastikan GPS perangkat Anda aktif.';
      case error.TIMEOUT:
        return 'Waktu permintaan lokasi habis. Silakan coba lagi.';
      default:
        return 'Gagal mendapatkan lokasi. Silakan coba lagi.';
    }
  }

  if (btnLokasi) {
    btnLokasi.addEventListener('click', function () {
      if (!navigator.geolocation) {
        Swal.fire({
          title: 'Pemberitahuan',
          text: 'Browser Anda tidak mendukung fitur geolokasi.',
          icon: 'warning',
          ...swalTheme
        });
        return;
      }

      const originalHtml = btnLokasi.innerHTML;
      btnLokasi.disabled = true;
      btnLokasi.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memeriksa lokasi...';

      navigator.geolocation.getCurrentPosition(
        function (position) {
          const latitude  = position.coords.latitude;
          const longitude = position.coords.longitude;

          fetch('{{ route('kunjungan.verify-location') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ latitude, longitude })
          })
          .then(function (response) {
            return response.json().then(function (data) {
              if (!response.ok) {
                throw new Error(data.message || 'Verifikasi lokasi gagal.');
              }
              return data;
            });
          })
          .then(function (data) {
            if (data.allowed) {
              if (latInput) latInput.value = latitude;
              if (lngInput) lngInput.value = longitude;
              showForm();
              return;
            }

            Swal.fire({
              title: 'Pemberitahuan',
              text: 'Anda berada di luar area kantor (' + data.distance + 'm). Silakan datang ke kantor untuk mengisi buku tamu.',
              icon: 'warning',
              ...swalTheme
            });
          })
          .catch(function (error) {
            Swal.fire({
              title: 'Pemberitahuan',
              text: error.message || 'Terjadi kesalahan saat memverifikasi lokasi. Silakan coba lagi.',
              icon: 'error',
              ...swalTheme
            });
          })
          .finally(function () {
            btnLokasi.disabled = false;
            btnLokasi.innerHTML = originalHtml;
          });
        },
        function (error) {
          btnLokasi.disabled = false;
          btnLokasi.innerHTML = originalHtml;
          Swal.fire({
            title: 'Pemberitahuan',
            text: geolocationErrorMessage(error),
            icon: 'warning',
            ...swalTheme
          });
        },
        {
          enableHighAccuracy: true,
          timeout: 15000,
          maximumAge: 0
        }
      );
    });
  }

  const pesan = document.getElementById('pesanInput');
  const count = document.getElementById('pesanCount');
  if (pesan && count) {
    const update = () => {
      const len = pesan.value.length;
      count.textContent = len + ' / 1000';
      count.style.color = len > 900 ? 'var(--accent-color)' : '';
    };
    pesan.addEventListener('input', update);
    update();
  }

  const form  = document.getElementById('formTamu');
  const btn   = document.getElementById('submitBtn');
  const label = document.getElementById('submitLabel');

  if (form && btn) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: 'Konfirmasi Pengiriman',
        text: 'Apakah Anda yakin akan mengirim data buku tamu ini?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Kirim',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#059669',
        cancelButtonColor: '#475569',
        background: '#0b1528',
        color: '#fff'
      }).then((result) => {
        if (result.isConfirmed) {
          btn.disabled = true;
          label.innerHTML = 'Mengirim...';
          form.submit();
        }
      });
    });
  }
});
</script>

<script>
// Hapus preview tmp dan tampilkan kembali input file
function hapusTmp(jenis) {
  const previewBox  = document.getElementById(jenis + 'PreviewBox');
  const uploadBox   = document.getElementById(jenis + 'UploadBox');
  const hiddenInput = document.querySelector('input[name="' + jenis + '_tmp"]');

  if (previewBox)  previewBox.remove();
  if (hiddenInput) hiddenInput.remove();
  if (uploadBox)   uploadBox.removeAttribute('style');
}

// Client-side image compression
document.addEventListener('DOMContentLoaded', function() {
  const fotoInput = document.getElementById('fotoInput');
  const fotoHelpText = document.getElementById('fotoHelpText');

  if (fotoInput) {
    fotoInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (!file) return;

      const fileNameDisplay = document.getElementById('fotoFileName');
      if (fileNameDisplay) fileNameDisplay.innerText = 'Terpilih: ' + file.name;

      // Jika ukuran file > 2MB (2 * 1024 * 1024 bytes)
      if (file.size > 2 * 1024 * 1024) {
        if (fotoHelpText) fotoHelpText.innerText = 'Mengkompresi gambar...';

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = function(event) {
          const img = new Image();
          img.src = event.target.result;
          img.onload = function() {
            const canvas = document.createElement('canvas');
            const MAX_WIDTH = 1280;
            const MAX_HEIGHT = 1280;
            let width = img.width;
            let height = img.height;

            if (width > height) {
              if (width > MAX_WIDTH) {
                height *= MAX_WIDTH / width;
                width = MAX_WIDTH;
              }
            } else {
              if (height > MAX_HEIGHT) {
                width *= MAX_HEIGHT / height;
                height = MAX_HEIGHT;
              }
            }

            canvas.width = width;
            canvas.height = height;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(img, 0, 0, width, height);

            // Kompres menjadi JPEG dengan kualitas 0.7
            canvas.toBlob(function(blob) {
              const compressedFile = new File([blob], file.name, {
                type: 'image/jpeg',
                lastModified: Date.now()
              });

              const dataTransfer = new DataTransfer();
              dataTransfer.items.add(compressedFile);
              fotoInput.files = dataTransfer.files;

              if (fotoHelpText) fotoHelpText.innerText = 'Galeri / kamera · Maks. 2MB (Berhasil dikompresi otomatis)';
            }, 'image/jpeg', 0.7);
          };
        };
      }
    });
  }
});
</script>
@endpush