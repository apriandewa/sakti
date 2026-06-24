@extends('frontend.main')

@section('container')
<main class="main">

  <!-- Page Title Section -->
  <div class="page-title-section">
    <div class="container">
      <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-2">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active text-white" aria-current="page">Ulasan</li>
        </ol>
      </nav>
      <h1 class="text-white">{{ $judul ?? 'Ulasan & Umpan Balik' }}</h1>
      <p class="text-secondary">{{ $subjudul ?? 'Kirimkan apresiasi, saran, dan masukan Anda demi meningkatkan kualitas pelayanan informasi kami.' }}</p>
    </div>
  </div>

  <section id="ulasan" class="section-dark">
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

          <!-- Glass Form Card -->
          <div class="glass-card p-0 overflow-hidden">
            
            <!-- Card Header -->
            <div class="d-flex align-items-center gap-3 py-3 px-4 border-bottom border-secondary" style="background: rgba(5, 150, 105, 0.15);">
              <div class="rounded-circle d-flex align-items-center justify-content-center bg-success text-white" style="width: 44px; height: 44px; box-shadow: 0 0 10px var(--brand-green-glow);">
                <i class="bi bi-journal-text fs-5"></i>
              </div>
              <div>
                <h5 class="mb-0 text-white fw-bold">Isi Ulasan</h5>
                <small class="text-secondary">Silakan lengkapi data ulasan Anda</small>
              </div>
            </div>

            <!-- Card Body -->
            <div class="p-4">
              <form action="{{ route('ulasan.store') }}" method="POST" enctype="multipart/form-data" id="formUlasan" novalidate autocomplete="off" class="form-cyber">
                @csrf

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
                </div>

                <div class="mb-3">
                  <label class="form-label">Kesan & Saran</label>
                  <textarea name="desc" id="descInput" rows="4" maxlength="1000" class="form-control @error('desc') is-invalid @enderror" placeholder="Tuliskan kesan, pesan, atau saran Anda...">{{ old('desc') }}</textarea>
                  <div class="text-end mt-1">
                    <small class="text-muted" id="descCount">0 / 1000</small>
                  </div>
                  @error('desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <!-- Section: Penilaian -->
                <div class="mb-4 mt-2">
                  <label class="form-label d-block">Penilaian Anda <span class="text-danger">*</span></label>
                  <div class="d-flex align-items-center gap-3 flex-wrap">
                    <div class="star-rating d-flex gap-2" id="starRating">
                      <i class="bi bi-star star-icon" data-value="1" title="Buruk"></i>
                      <i class="bi bi-star star-icon" data-value="2" title="Kurang Baik"></i>
                      <i class="bi bi-star star-icon" data-value="3" title="Cukup"></i>
                      <i class="bi bi-star star-icon" data-value="4" title="Baik"></i>
                      <i class="bi bi-star star-icon" data-value="5" title="Sangat Baik"></i>
                    </div>
                    <span id="ratingText" class="badge rounded-pill bg-dark text-secondary border border-secondary px-3 py-2" style="transition: all 0.3s; min-width: 100px; text-align: center;">Belum Dinilai</span>
                  </div>
                  <input type="hidden" name="keterangan" id="keteranganInput" value="{{ old('keterangan') }}">
                  @error('keterangan') <div class="text-danger small mt-2 d-block">{{ $message }}</div> @enderror
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
                          <div class="small fw-semibold text-truncate text-white">
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
                <button type="reset" form="formUlasan" class="btn-cyber-outline" style="font-size: 0.85rem; padding: 6px 16px;">
                  <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
                <button type="submit" form="formUlasan" class="btn-cyber" id="submitBtn" style="font-size: 0.85rem; padding: 6px 20px;">
                  <i class="bi bi-send me-1"></i>
                  <span id="submitLabel">Kirim Ulasan</span>
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

<style>
  .star-rating .star-icon {
    font-size: 2.2rem;
    color: #334155; /* dark grey star fill */
    cursor: pointer;
    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), color 0.2s ease-in-out, text-shadow 0.2s ease-in-out;
  }
  .star-rating .star-icon:hover {
    transform: scale(1.15);
  }
  .star-rating .star-icon.active {
    color: #ffc107; /* yellow active */
    text-shadow: 0 0 15px rgba(255, 193, 7, 0.6);
  }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
  // --- Script untuk Rating Bintang (Keterangan) ---
  const stars = document.querySelectorAll('.star-icon');
  const keteranganInput = document.getElementById('keteranganInput');
  const ratingText = document.getElementById('ratingText');

  const ratingLabels = {
    1: 'buruk',
    2: 'kurang baik',
    3: 'cukup',
    4: 'baik',
    5: 'sangat baik'
  };
  
  const ratingDisplay = {
    1: 'Buruk',
    2: 'Kurang Baik',
    3: 'Cukup',
    4: 'Baik',
    5: 'Sangat Baik'
  };

  const badgeColors = {
    1: 'bg-danger text-white border-danger',
    2: 'bg-warning text-dark border-warning',
    3: 'bg-info text-dark border-info',
    4: 'bg-primary text-white border-primary',
    5: 'bg-success text-white border-success'
  };

  let currentVal = 0;

  // Set nilai awal jika ada old()
  if (keteranganInput.value) {
    const valKey = Object.keys(ratingLabels).find(key => ratingLabels[key] === keteranganInput.value.toLowerCase());
    if (valKey) currentVal = parseInt(valKey);
  }

  function highlightStars(val) {
    stars.forEach(s => {
      const sVal = parseInt(s.getAttribute('data-value'));
      if (sVal <= val && val > 0) {
        s.classList.remove('bi-star');
        s.classList.add('bi-star-fill', 'active');
      } else {
        s.classList.remove('bi-star-fill', 'active');
        s.classList.add('bi-star');
      }
    });
  }

  function updateStars(val) {
    highlightStars(val);
    if (val > 0) {
      ratingText.textContent = ratingDisplay[val];
      ratingText.className = 'badge rounded-pill px-3 py-2 border ' + badgeColors[val];
      ratingText.style.transform = 'scale(1.1)';
      setTimeout(() => ratingText.style.transform = 'scale(1)', 200);
    } else {
      ratingText.textContent = 'Belum Dinilai';
      ratingText.className = 'badge rounded-pill bg-dark text-secondary border border-secondary px-3 py-2';
    }
  }

  if (currentVal > 0) updateStars(currentVal);

  stars.forEach(star => {
    star.addEventListener('mouseover', function() {
      const val = parseInt(this.getAttribute('data-value'));
      highlightStars(val);
    });

    star.addEventListener('mouseout', function() {
      highlightStars(currentVal);
    });

    star.addEventListener('click', function() {
      currentVal = parseInt(this.getAttribute('data-value'));
      keteranganInput.value = ratingLabels[currentVal]; // value disimpan sebagai teks
      updateStars(currentVal);
    });
  });
  // --- End Script Rating ---

  const desc = document.getElementById('descInput');
  const count = document.getElementById('descCount');
  if (desc && count) {
    const update = () => {
      const len = desc.value.length;
      count.textContent = len + ' / 1000';
      count.style.color = len > 900 ? 'var(--accent-color)' : '';
    };
    desc.addEventListener('input', update);
    update();
  }

  const form  = document.getElementById('formUlasan');
  const btn   = document.getElementById('submitBtn');
  const label = document.getElementById('submitLabel');

  if (form && btn) {
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      Swal.fire({
        title: 'Konfirmasi Pengiriman',
        text: 'Apakah Anda yakin akan mengirim data Ulasan ini?',
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
@endsection