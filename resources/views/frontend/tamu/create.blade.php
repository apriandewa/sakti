@extends('frontend.main')

@section('container')

<main class="main">

  <section id="buku-tamu" class="service-details section">

    <div class="container section-title text-center" data-aos="fade-up">
      <h2>Buku Tamu</h2>
      <p>Berikut adalah Tamu dan Kunjungan PPID Kabupaten Indragiri Hulu</p>
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
                <h5 class="mb-0 fs-5 fw-semibold opacity-75 text-white">Isi Buku Tamu</h5>
                <small class="opacity-75">Silakan lengkapi data kunjungan Anda</small>
              </div>
            </div>

            {{-- Body card --}}
            <div class="card-body p-4">

              <form action="{{ route('kunjungan.store') }}" method="POST"
                    enctype="multipart/form-data" id="formTamu" novalidate autocomplete="off">
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

                  <div class="col-sm-5">
                    <label class="form-label fw-semibold small">
                      Jenis Kelamin <span class="text-danger">*</span>
                    </label>
                    <div class="d-flex gap-3 pt-1">
                      @foreach(['Laki-laki' => '👨', 'Perempuan' => '👩'] as $jk => $ikon)
                        <div class="form-check">
                          <input class="form-check-input" type="radio"
                                 name="jenis_kelamin" id="jk_{{ Str::slug($jk) }}"
                                 value="{{ $jk }}"
                                 {{ old('jenis_kelamin') === $jk ? 'checked' : '' }}>
                          <label class="form-check-label small" for="jk_{{ Str::slug($jk) }}">
                            {{ $ikon }} {{ $jk }}
                          </label>
                        </div>
                      @endforeach
                    </div>
                    @error('jenis_kelamin')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <label class="form-label fw-semibold small">Email</label>
                    <input type="email" name="email"
                           class="form-control form-control-sm @error('email') is-invalid @enderror"
                           placeholder="email@contoh.com" value="{{ old('email') }}">
                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                  <div class="col-sm-6">
                    <label class="form-label fw-semibold small">No. HP / Telepon</label>
                    <input type="tel" name="no_hp"
                           class="form-control form-control-sm @error('no_hp') is-invalid @enderror"
                           placeholder="08xx-xxxx-xxxx" value="{{ old('no_hp') }}">
                    @error('no_hp') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>

                {{-- ── Seksi: Asal & Pekerjaan ───────────────────── --}}
                <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                  <i class="bi bi-building text-secondary"></i>
                  <span class="small fw-semibold text-uppercase text-secondary">Asal &amp; Pekerjaan</span>
                  <hr class="flex-grow-1 my-0">
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-sm-6">
                    <label class="form-label fw-semibold small">Pekerjaan / Instansi</label>
                    <select name="pekerjaan"
                            class="form-select form-select-sm @error('pekerjaan') is-invalid @enderror">
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
                    <label class="form-label fw-semibold small">Asal Instansi / Sekolah / Daerah</label>
                    <input type="text" name="asal"
                          class="form-control form-control-sm @error('asal') is-invalid @enderror"
                          placeholder="Nama instansi, sekolah, atau universitas"
                          value="{{ old('asal') }}">
                    @error('asal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                  </div>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-semibold small">Alamat Lengkap</label>
                  <textarea name="alamat" rows="2"
                            class="form-control form-control-sm @error('alamat') is-invalid @enderror"
                            placeholder="Alamat lengkap (opsional)">{{ old('alamat') }}</textarea>
                  @error('alamat') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- ── Seksi: Keperluan Kunjungan ────────────────── --}}
                  <div class="d-flex align-items-center gap-2 mb-3 mt-4">
                    <i class="bi bi-clipboard-check text-secondary"></i>
                    <span class="small fw-semibold text-uppercase text-secondary">Keperluan Kunjungan</span>
                    <hr class="flex-grow-1 my-0">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-semibold small">
                      Keperluan <span class="text-danger">*</span>
                    </label>
                    <select name="keperluan"
                            class="form-select form-select-sm @error('keperluan') is-invalid @enderror" required>
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
                  <label class="form-label fw-semibold small">Pesan / Kesan &amp; Saran</label>
                  <textarea name="pesan" id="pesanInput" rows="4" maxlength="1000"
                            class="form-control form-control-sm @error('pesan') is-invalid @enderror"
                            placeholder="Tuliskan pesan, kesan, atau saran Anda...">{{ old('pesan') }}</textarea>
                  <div class="text-end mt-1">
                    <small class="text-muted" id="pesanCount">0 / 1000</small>
                  </div>
                  @error('pesan') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

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
                            accept="image/*">
                      <div class="form-text" id="fotoHelpText">Galeri / kamera · Maks. 2MB</div>
                    </div>
                    @error('foto')
                      <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                  </div>

                  {{-- ── Dokumen Pendukung ── --}}
                  <div class="col-sm-6">
                    <label class="form-label fw-semibold small">
                      Dokumen Pendukung
                      <span class="fw-normal text-muted">(opsional)</span>
                    </label>

                    @if (session('dokumen_tmp'))
                      {{-- Preview file sebelumnya --}}
                      <input type="hidden" name="dokumen_tmp" value="{{ session('dokumen_tmp') }}">
                      <div class="alert alert-info py-2 px-3 mb-2 d-flex align-items-center gap-2 rounded-3"
                          id="dokumenPreviewBox">
                        @php
                          $dokExt = strtolower(pathinfo(session('dokumen_tmp_name', ''), PATHINFO_EXTENSION));
                          $dokIcon = $dokExt === 'pdf' ? 'bi-file-earmark-pdf text-danger'
                                  : 'bi-file-earmark-image text-primary';
                        @endphp
                        <div class="rounded d-flex align-items-center justify-content-center bg-white border"
                            style="width:48px;height:48px;min-width:48px;">
                          <i class="bi {{ $dokIcon }} fs-4"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                          <div class="small fw-semibold text-truncate">
                            {{ session('dokumen_tmp_name', 'dokumen-sebelumnya') }}
                          </div>
                          <div class="text-muted" style="font-size:11px">
                            <i class="bi bi-info-circle me-1"></i>File tersimpan sementara
                          </div>
                        </div>
                        <button type="button"
                                class="btn btn-sm btn-outline-danger py-0 px-1 flex-shrink-0"
                                onclick="hapusTmp('dokumen')"
                                title="Hapus dan upload ulang">
                          <i class="bi bi-x-lg"></i>
                        </button>
                      </div>
                    @endif

                    <div class="border rounded-3 p-3 text-center bg-light"
                        id="dokumenUploadBox"
                        style="{{ session('dokumen_tmp') ? 'display:none;' : '' }}border-style:dashed!important">
                      <i class="bi bi-file-earmark-arrow-up fs-4 text-muted d-block mb-1"></i>
                      <input type="file" name="dokumen" id="dokumenInput"
                            class="form-control form-control-sm @error('dokumen') is-invalid @enderror"
                            accept=".pdf,.jpg,.jpeg,.png">
                      <div class="form-text">SPT, Surat Jalan, dll · PDF/JPG/PNG · Maks. 5MB</div>
                    </div>
                    @error('dokumen')
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
                <button type="reset" form="formTamu" class="btn btn-sm btn-outline-secondary">
                  <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
                <button type="submit" form="formTamu" class="btn btn-sm px-4" id="submitBtn"
                        style="background-color: var(--accent-color); border-color: var(--accent-color); color:#fff;">
                  <i class="bi bi-send me-1"></i>
                  <span id="submitLabel">Kirim Buku Tamu</span>
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

// Client-side image compression
document.addEventListener('DOMContentLoaded', function() {
  const fotoInput = document.getElementById('fotoInput');
  const fotoHelpText = document.getElementById('fotoHelpText');

  if (fotoInput) {
    fotoInput.addEventListener('change', function(e) {
      const file = e.target.files[0];
      if (!file) return;

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