@extends('frontend.main')

@section('container')

<main class="main">

  <section id="ulasan" class="service-details section">

    <div class="container section-title text-center" data-aos="fade-up">
      <h2>Ulasan</h2>
      <p>Berikut adalah Ulasan dan Umpan Balik dari masyarakat untuk PPID Kabupaten Indragiri Hulu</p>
    </div>

    <div class="container" data-aos="fade-up" data-aos-delay="100">
      <div class="row gy-4">

        <div class="col-lg-8" data-aos="fade-up" data-aos-delay="150">

          {{-- Alert sukses --}}
          @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
              <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          @endif

          {{-- Alert error --}}
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

          {{-- ╔══════════════════════ CARD FORM ══════════════════════╗ --}}
          <div class="card border-0 shadow-sm rounded-4 overflow-hidden">

            {{-- Header card --}}
            <div class="card-header text-white d-flex align-items-center gap-2 py-3 px-4"
                 style="background-color: var(--heading-color);">
              <div class="rounded-circle d-flex align-items-center justify-content-center"
                   style="width:40px;height:40px;background:rgba(255,255,255,0.2);">
                <i class="bi bi-journal-text fs-5"></i>
              </div>
              <div>
                <h5 class="mb-0 fs-5 fw-semibold opacity-75 text-white">Isi Ulasan</h5>
                <small class="opacity-75">Silakan lengkapi data ulasan Anda</small>
              </div>
            </div>

            {{-- Body card --}}
            <div class="card-body p-4">

              <form action="{{ route('ulasan.store') }}" method="POST"
                    enctype="multipart/form-data" id="formUlasan" novalidate autocomplete="off">
                @csrf

                {{-- ── Seksi: Data Diri ──────────────────────────── --}}
                <div class="d-flex align-items-center gap-2 mb-3 mt-1">
                  <i class="bi bi-person-fill text-secondary"></i>
                  <span class="small fw-semibold text-uppercase text-secondary ls-1">Data Diri</span>
                  <hr class="flex-grow-1 my-0">
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-7">
                    <label class="form-label fw-semibold small">
                      Nama Lengkap <span class="text-danger">*</span>
                    </label>
                    <input type="text" name="nama"
                           class="form-control form-control-sm @error('nama') is-invalid @enderror"
                           placeholder="Masukkan nama lengkap Anda"
                           value="{{ old('nama') }}" required>
                    @error('nama') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold small">desc / Kesan &amp; Saran</label>
                  <textarea name="desc" id="descInput" rows="4" maxlength="1000"
                            class="form-control form-control-sm @error('desc') is-invalid @enderror"
                            placeholder="Tuliskan desc, kesan, atau saran Anda...">{{ old('desc') }}</textarea>
                  <div class="text-end mt-1">
                    <small class="text-muted" id="descCount">0 / 1000</small>
                  </div>
                  @error('desc') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- ── Seksi: Keterangan (Rating) ────────────────────── --}}
                <div class="mb-4 mt-2">
                  <label class="form-label fw-semibold small">
                    Keterangan (Penilaian) <span class="text-danger">*</span>
                  </label>
                  <div class="d-flex align-items-center gap-3">
                    <div class="star-rating d-flex gap-2" id="starRating">
                      <i class="bi bi-star star-icon" data-value="1" title="Buruk"></i>
                      <i class="bi bi-star star-icon" data-value="2" title="Kurang Baik"></i>
                      <i class="bi bi-star star-icon" data-value="3" title="Cukup"></i>
                      <i class="bi bi-star star-icon" data-value="4" title="Baik"></i>
                      <i class="bi bi-star star-icon" data-value="5" title="Sangat Baik"></i>
                    </div>
                    <span id="ratingText" class="badge rounded-pill bg-light text-secondary border px-3 py-2" style="transition: all 0.3s; min-width: 100px; text-align: center;">Belum Dinilai</span>
                  </div>
                  <input type="hidden" name="keterangan" id="keteranganInput" value="{{ old('keterangan') }}">
                  @error('keterangan') <div class="invalid-feedback d-block mt-2">{{ $message }}</div> @enderror
                </div>

                <style>
                  .star-rating .star-icon {
                    font-size: 2.2rem;
                    color: #e4e5e9; /* redup / dim */
                    cursor: pointer;
                    transition: transform 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275), color 0.2s ease-in-out, text-shadow 0.2s ease-in-out;
                  }
                  .star-rating .star-icon:hover {
                    transform: scale(1.15);
                  }
                  .star-rating .star-icon.active {
                    color: #ffc107; /* kuning bercahaya */
                    text-shadow: 0 0 15px rgba(255, 193, 7, 0.6);
                  }
                </style>

              {{-- ── Seksi: Lampiran ───────────────────────────── --}}
                <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                  <i class="bi bi-paperclip text-secondary"></i>
                  <span class="small fw-semibold text-uppercase text-secondary">Lampiran</span>
                  <hr class="flex-grow-1 my-0">
                </div>

                <div class="row g-3 mb-3">

                  {{-- ── Foto Wajah ── --}}
                  <div class="col-sm-6">
                    <label class="form-label fw-semibold small">
                      Foto Wajah / Diri
                      <span class="fw-normal text-muted">(opsional)</span>
                    </label>

                    @if (session('foto_tmp'))
                      {{-- Preview file sebelumnya --}}
                      <input type="hidden" name="foto_tmp" value="{{ session('foto_tmp') }}">
                      <div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center gap-2 rounded-3"
                          id="fotoPreviewBox">
                        @php
                          $fotoMime = session('foto_tmp_mime', '');
                          $fotoIsImg = str_starts_with($fotoMime, 'image/');
                        @endphp
                        @if ($fotoIsImg)
                          <img src="{{ Storage::disk('public')->url(session('foto_tmp')) }}"
                              alt="preview foto"
                              class="rounded"
                              style="width:48px;height:48px;object-fit:cover;border:2px solid #0dcaf0;">
                        @else
                          <div class="rounded d-flex align-items-center justify-content-center bg-white border"
                              style="width:48px;height:48px;min-width:48px;">
                            <i class="bi bi-image fs-4 text-info"></i>
                          </div>
                        @endif
                        <div class="flex-grow-1 overflow-hidden">
                          <div class="small fw-semibold text-truncate">
                            {{ session('foto_tmp_name', 'foto-sebelumnya') }}
                          </div>
                          <div class="text-muted" style="font-size:11px">
                            <i class="bi bi-info-circle me-1"></i>File tersimpan sementara
                          </div>
                        </div>
                        <button type="button"
                                class="btn btn-sm btn-outline-danger py-0 px-1 flex-shrink-0"
                                onclick="hapusTmp('foto')"
                                title="Hapus dan upload ulang">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </div>
                    @endif

                    <div class="border rounded-3 p-3 text-center bg-light"
                        id="fotoUploadBox"
                        style="{{ session('foto_tmp') ? 'display:none;' : '' }}border-style:dashed!important">
                      <i class="bi bi-camera fs-4 text-muted d-block mb-1"></i>
                      <input type="file" name="foto" id="fotoInput"
                            class="form-control form-control-sm @error('foto') is-invalid @enderror"
                            accept="image/*" capture="environment">
                      <div class="form-text">Galeri / kamera · Maks. 2MB</div>
                    </div>
                    @error('foto')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>

                </div>

                {{-- ── Seksi: Verifikasi (Captcha) ───────────────── --}}
                <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                  <i class="bi bi-shield-check text-secondary"></i>
                  <span class="small fw-semibold text-uppercase text-secondary">Verifikasi</span>
                  <hr class="flex-grow-1 my-0">
                </div>

                <div class="mb-2">
                  <x-captcha />
                </div>

              </form>
            </div>

            {{-- Footer card --}}
            <div class="card-footer bg-light d-flex justify-content-between align-items-center px-4 py-3">
              <small class="text-muted"><span class="text-danger">*</span> Wajib diisi</small>
              <div class="d-flex gap-2">
                <button type="reset" form="formUlasan" class="btn btn-sm btn-outline-secondary">
                  <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
                <button type="submit" form="formUlasan" class="btn btn-sm px-4" id="submitBtn"
                        style="background-color: var(--accent-color); border-color: var(--accent-color); color:#fff;">
                  <i class="bi bi-send me-1"></i>
                  <span id="submitLabel">Kirim Ulasan</span>
                </button>
              </div>
            </div>

          </div>
          {{-- ╚══════════════════════ /CARD FORM ═════════════════════╝ --}}

        </div>{{-- /col form --}}

        @include('frontend.partials.sidebar')

      </div>
    </div>

  </section>

</main>

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
      ratingText.className = 'badge rounded-pill bg-light text-secondary border px-3 py-2';
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
        confirmButtonColor: '#0d6efd',
        cancelButtonColor: '#6c757d',
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
</script>

@endsection