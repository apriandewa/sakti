{{--
  ==========================================================================
  HALAMAN: Penilaian e-Kinerja Pegawai (Frontend Publik)
  ROUTE   : GET  /sakti/kinerja        -> ekinerja.index
            GET  /sakti/kinerja/periode -> ekinerja.periode (AJAX Select2, JSON {results:[{id,text}]})
            POST /sakti/kinerja/cari    -> ekinerja.cari    (AJAX, JSON {success, data, message, nama_cocok})
  LOKASI  : resources/views/frontend/ekinerja/index.blade.php
  CATATAN : Bagian captcha di bawah masih PLACEHOLDER (route 'captcha.image').
            Sesuaikan dengan komponen/helper package "meaws captcha" yang
            sudah terpasang di project (mis. ganti <img> src & validasi
            dengan directive/helper resminya) pada tahap integrasi backend.
  ==========================================================================
--}}
@extends('frontend.main')

@php
    $title = 'Penilaian e-Kinerja';
@endphp

@section('container')
<main class="main ekinerja-page">

  {{-- ==========================================
       PAGE TITLE / BREADCRUMB
       ========================================== --}}
  <div class="page-title-section">
    <div class="container">
      <h1 data-aos="fade-up">Penilaian e-Kinerja Pegawai</h1>
      <p class="text-white-50 mb-3" data-aos="fade-up" data-aos-delay="80" style="max-width:640px;">
        Layanan pengecekan mandiri hasil penilaian e-Kinerja (SKP) Aparatur Sipil Negara
        di lingkungan Pemerintah Kabupaten Indragiri Hulu.
      </p>
      <nav data-aos="fade-up" data-aos-delay="150">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active" aria-current="page">Penilaian e-Kinerja</li>
        </ol>
      </nav>
    </div>
  </div>

  {{-- ==========================================
       FORM PENCARIAN
       ========================================== --}}
  <section class="section-light-dark">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">

          <div class="glass-card ekinerja-search-card" data-aos="fade-up">
            <div class="d-flex align-items-center gap-3 mb-4">
              <div class="service-icon-box mb-0"><i class="fa fa-search"></i></div>
              <div>
                <h4 class="mb-1">Cari Hasil Penilaian</h4>
                <p class="text-secondary small mb-0">Masukkan periode, NIP, dan nama Anda sesuai data kepegawaian.</p>
              </div>
            </div>

            <form id="formCariEkinerja" class="form-cyber" autocomplete="off">
              @csrf

              <div class="mb-3">
                <label for="periodeSelect">Periode Penilaian</label>
                <select id="periodeSelect" name="periode_id" class="form-control" style="width:100%" required>
                  <option value=""></option>
                </select>
              </div>

              <div class="row g-3">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="nipInput">NIP</label>
                    <input type="text" id="nipInput" name="nip" class="form-control"
                           inputmode="numeric" maxlength="18" placeholder="18 digit NIP" required>
                    <div class="form-text text-muted small mt-1">Nomor Induk Pegawai, 18 digit angka.</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="namaInput">Nama Pegawai</label>
                    <input type="text" id="namaInput" name="nama" class="form-control"
                           placeholder="Nama sesuai SK Kepegawaian" required>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label>Kode Keamanan</label>
                <x-captcha />
              </div>

              <button type="submit" class="btn-cyber w-100 justify-content-center" id="btnCari">
                <i class="bi bi-search"></i> Cari Penilaian
              </button>
            </form>
          </div>

          {{-- Loading state --}}
          <div id="ekinerjaLoading" class="text-center py-5" style="display:none;">
            <div class="preloader-spinner mx-auto" style="position:static;border-top-color:var(--gold-500);border-bottom-color:var(--navy-500);"></div>
            <p class="text-secondary mt-3 mb-0">Mencari data penilaian e-Kinerja...</p>
          </div>

          {{-- Hasil pencarian (di-render via JS) --}}
          <div id="ekinerjaResult" class="mt-4" style="display:none;"></div>

          {{-- Catatan bantuan --}}
          <div class="text-center mt-4" data-aos="fade-up">
            <p class="text-muted small mb-0">
              <i class="bi bi-info-circle me-1"></i>
              Data bersumber dari Sistem e-Kinerja BKN. Jika hasil tidak sesuai, silakan hubungi
              admin kepegawaian OPD masing-masing.
            </p>
          </div>

        </div>
      </div>
    </div>
  </section>

</main>
@endsection

@push('css')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
  /* ===== Scoped styles: Halaman Penilaian e-Kinerja ===== */
  .ekinerja-page .ekinerja-search-card { max-width: 100%; }

  .ekinerja-page .select2-container--default .select2-selection--single {
    height: calc(1.5em + 1.5rem + 2px);
    background: var(--bg-secondary);
    border: 1px solid var(--glass-border);
    border-radius: 10px;
    display: flex;
    align-items: center;
    padding: 0 12px;
  }
  .ekinerja-page .select2-container--default .select2-selection--single .select2-selection__rendered {
    color: var(--text-primary);
    line-height: normal;
    padding-left: 0;
  }
  .ekinerja-page .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: calc(1.5em + 1.5rem);
  }
  .ekinerja-page .select2-dropdown {
    background: var(--bg-secondary);
    border: 1px solid var(--glass-border);
    border-radius: 10px;
    overflow: hidden;
  }
  .ekinerja-page .select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: var(--navy-800);
  }
  .ekinerja-page .select2-search__field {
    background: var(--bg-secondary) !important;
    color: var(--text-primary) !important;
  }
  .ekinerja-page .select2-container--default .select2-selection--single:focus,
  .ekinerja-page .select2-container--open .select2-selection--single {
    border-color: var(--gold-500);
    box-shadow: 0 0 0 4px rgba(245, 166, 35, 0.15);
  }

  /* Result card */
  .ekinerja-page .ekinerja-result-card { padding: 32px; }
  @media (max-width: 576px) { .ekinerja-page .ekinerja-result-card { padding: 22px; } }

  .ekinerja-page .ek-info-item { display: flex; flex-direction: column; gap: 4px; }
  .ekinerja-page .ek-info-label {
    font-family: var(--font-mono); font-size: 0.72rem; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--text-muted);
  }
  .ekinerja-page .ek-info-value { font-weight: 600; color: var(--text-primary); font-size: 0.98rem; }

  .ekinerja-page .ek-score-box {
    background: var(--bg-tertiary); border: 1px solid var(--glass-border);
    border-radius: 12px; padding: 16px; text-align: center;
    display: flex; flex-direction: column; align-items: center; gap: 10px; height: 100%;
  }
  .ekinerja-page .ek-score-label {
    font-family: var(--font-mono); font-size: 0.72rem; text-transform: uppercase;
    letter-spacing: 0.06em; color: var(--text-secondary);
  }

  .ekinerja-page .ek-penilai-box {
    background: var(--bg-tertiary); border: 1px solid var(--glass-border);
    border-radius: 12px; padding: 18px 20px;
  }

  .ekinerja-page .ek-badge {
    display: inline-flex; align-items: center; padding: 6px 14px;
    border-radius: 20px; font-size: 0.78rem; font-weight: 700;
    letter-spacing: 0.03em; font-family: var(--font-subtitle);
  }
  .ekinerja-page .ek-badge-lg { padding: 8px 18px; font-size: 0.85rem; }

  .ekinerja-page .ek-badge-success { background: rgba(46, 160, 96, 0.14); color: #1f8c53; }
  .ekinerja-page .ek-badge-info     { background: rgba(27, 36, 80, 0.10); color: var(--navy-700); }
  .ekinerja-page .ek-badge-warning  { background: rgba(245, 166, 35, 0.16); color: var(--gold-600); }
  .ekinerja-page .ek-badge-danger   { background: rgba(214, 69, 69, 0.14); color: #c53838; }
  .ekinerja-page .ek-badge-secondary{ background: rgba(19, 26, 58, 0.08); color: var(--text-secondary); }

  [data-theme="dark"] .ekinerja-page .ek-badge-success { background: rgba(46, 160, 96, 0.2); color: #4ad189; }
  [data-theme="dark"] .ekinerja-page .ek-badge-info     { background: rgba(255,255,255,0.08); color: #c7cbe6; }
  [data-theme="dark"] .ekinerja-page .ek-badge-danger   { background: rgba(214, 69, 69, 0.22); color: #ff8484; }
  [data-theme="dark"] .ekinerja-page .ek-badge-secondary{ background: rgba(255,255,255,0.08); color: var(--text-secondary); }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Select2 & AJAX submit membutuhkan jQuery yang di-defer pada layout utama.
    // Tunggu jQuery & Select2 siap sebelum inisialisasi.
    var waitReady = setInterval(function () {
      if (window.jQuery && window.jQuery.fn.select2 && window.Swal) {
        clearInterval(waitReady);
        initEkinerjaPage(window.jQuery);
      }
    }, 50);
  });

  function initEkinerjaPage($) {
    'use strict';

    /* ---------- Select2: Periode (AJAX) ---------- */
    $('#periodeSelect').select2({
      placeholder: 'Pilih Periode',
      allowClear: true,
      width: '100%',
      minimumInputLength: 0,
      ajax: {
        url: '{{ route('ekinerja.periode') }}',
        dataType: 'json',
        delay: 250,
        data: function (params) {
          return { q: params.term || '' };
        },
        processResults: function (data) {
          return { results: data.results || [] };
        },
        cache: true
      }
    });

    /* ---------- NIP: hanya angka, maksimal 18 digit ---------- */
    $('#nipInput').on('input', function () {
      this.value = this.value.replace(/\D/g, '').slice(0, 18);
    });

    /* ---------- Helpers ---------- */
    function escapeHtml(str) {
      return $('<div>').text(str === null || str === undefined ? '' : str).html();
    }

    function badgeClass(value) {
      if (!value) return 'ek-badge-secondary';
      var v = value.toString().toLowerCase();
      if (v.indexOf('diatas') > -1 || v.indexOf('di atas') > -1 || v.indexOf('sangat baik') > -1) return 'ek-badge-success';
      if (v.indexOf('sesuai') > -1 || v.indexOf('baik') > -1) return 'ek-badge-info';
      if (v.indexOf('cukup') > -1) return 'ek-badge-warning';
      if (v.indexOf('bawah') > -1 || v.indexOf('kurang') > -1) return 'ek-badge-danger';
      return 'ek-badge-secondary';
    }

    function formatTanggal(str) {
      if (!str) return '-';
      var bulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
      var dt = new Date(str);
      if (isNaN(dt.getTime())) return str;
      return dt.getDate() + ' ' + bulan[dt.getMonth()] + ' ' + dt.getFullYear();
    }

    function formatPeriode(d) {
      if (d.periode_awal_skp && d.periode_akhir_skp) {
        return formatTanggal(d.periode_awal_skp) + ' s/d ' + formatTanggal(d.periode_akhir_skp);
      }
      return d.tahun_skp ? ('Tahun ' + d.tahun_skp) : '-';
    }

    /* ---------- Render hasil ---------- */
    function renderHasil(d, namaCocok) {
      var warn = (namaCocok === false)
        ? '<div class="alert alert-warning small mb-4"><i class="bi bi-exclamation-triangle me-1"></i> ' +
          'Nama yang Anda masukkan tidak sepenuhnya cocok dengan data sistem. Periksa kembali kesesuaian data.</div>'
        : '';

      var html = ''
        + '<div class="detail-content ekinerja-result-card" data-aos="fade-up">'
        +   warn
        +   '<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4 pb-4" style="border-bottom:1px solid var(--glass-border);">'
        +     '<div>'
        +       '<h4 class="mb-1">' + escapeHtml(d.nama || '-') + '</h4>'
        +       '<div class="text-secondary small font-monospace">NIP. ' + escapeHtml(d.nip || '-') + '</div>'
        +     '</div>'
        +     '<span class="ek-badge ek-badge-lg ' + badgeClass(d.hasil_akhir) + '">' + escapeHtml((d.hasil_akhir || '-').toString().toUpperCase()) + '</span>'
        +   '</div>'

        +   '<div class="row g-4 mb-4">'
        +     '<div class="col-md-6"><div class="ek-info-item"><span class="ek-info-label">Jabatan</span><span class="ek-info-value">' + escapeHtml(d.skp_jabatan || '-') + '</span></div></div>'
        +     '<div class="col-md-6"><div class="ek-info-item"><span class="ek-info-label">Unit Kerja</span><span class="ek-info-value">' + escapeHtml(d.skp_unor || '-') + '</span></div></div>'
        +     '<div class="col-md-6"><div class="ek-info-item"><span class="ek-info-label">Golongan Ruang</span><span class="ek-info-value">' + escapeHtml(d.golru || '-') + '</span></div></div>'
        +     '<div class="col-md-6"><div class="ek-info-item"><span class="ek-info-label">Periode SKP</span><span class="ek-info-value">' + escapeHtml(formatPeriode(d)) + '</span></div></div>'
        +   '</div>'

        +   '<div class="row g-3 mb-4">'
        +     '<div class="col-md-4"><div class="ek-score-box"><span class="ek-score-label">Hasil Kerja</span><span class="ek-badge ek-badge-lg ' + badgeClass(d.hasil_kerja) + '">' + escapeHtml((d.hasil_kerja || '-').toString().toUpperCase()) + '</span></div></div>'
        +     '<div class="col-md-4"><div class="ek-score-box"><span class="ek-score-label">Perilaku Kerja</span><span class="ek-badge ek-badge-lg ' + badgeClass(d.perilaku_kerja) + '">' + escapeHtml((d.perilaku_kerja || '-').toString().toUpperCase()) + '</span></div></div>'
        +     '<div class="col-md-4"><div class="ek-score-box"><span class="ek-score-label">Hasil Akhir</span><span class="ek-badge ek-badge-lg ' + badgeClass(d.hasil_akhir) + '">' + escapeHtml((d.hasil_akhir || '-').toString().toUpperCase()) + '</span></div></div>'
        +   '</div>'

        +   '<div class="ek-penilai-box">'
        +     '<span class="ek-info-label mb-2 d-block"><i class="bi bi-person-check me-1"></i> Pejabat Penilai</span>'
        +     '<div class="ek-info-value">' + escapeHtml(d.pegawai_atasan_nama || '-') + '</div>'
        +     '<div class="text-secondary small">' + escapeHtml(d.pegawai_atasan_jabatan || '-') + '</div>'
        +     (d.waktu_dinilai ? '<div class="text-muted small mt-1"><i class="bi bi-clock-history me-1"></i> Dinilai pada ' + escapeHtml(d.waktu_dinilai) + '</div>' : '')
        +   '</div>'
        + '</div>';

      var $result = $('#ekinerjaResult');
      $result.html(html).fadeIn(300);
      if (typeof AOS !== 'undefined') AOS.refresh();
      $('html, body').animate({ scrollTop: $result.offset().top - 130 }, 500);
    }

    /* ---------- Submit form pencarian ---------- */
    $('#formCariEkinerja').on('submit', function (e) {
      e.preventDefault();

      var periodeId = $('#periodeSelect').val();
      var nip = $('#nipInput').val().trim();
      var nama = $('#namaInput').val().trim();

      if (!periodeId) {
        Swal.fire({ icon: 'warning', title: 'Periode belum dipilih', text: 'Silakan pilih periode penilaian terlebih dahulu.' });
        return;
      }
      if (nip.length !== 18) {
        Swal.fire({ icon: 'warning', title: 'NIP tidak valid', text: 'NIP harus terdiri dari 18 digit angka.' });
        return;
      }
      if (!nama) {
        Swal.fire({ icon: 'warning', title: 'Nama belum diisi', text: 'Silakan masukkan nama pegawai.' });
        return;
      }

      var $btn = $('#btnCari');
      var originalHtml = $btn.html();
      $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Mencari...');
      $('#ekinerjaResult').hide().empty();
      $('#ekinerjaLoading').show();

      // serialize() otomatis ikut sertakan seluruh field form, termasuk
      // 'captcha' & 'captcha_key' yang di-render oleh komponen <x-captcha />.
      $.ajax({
        url: '{{ route('ekinerja.cari') }}',
        method: 'POST',
        dataType: 'json',
        data: $('#formCariEkinerja').serialize(),
        success: function (res) {
          $('#ekinerjaLoading').hide();
          if (res && res.success && res.data) {
            renderHasil(res.data, res.nama_cocok);
          } else {
            Swal.fire({
              icon: 'info',
              title: 'Data Tidak Ditemukan',
              text: (res && res.message) || 'Data penilaian e-Kinerja untuk NIP dan periode tersebut tidak ditemukan.'
            });
          }
          refreshCaptcha();
        },
        error: function (xhr) {
          $('#ekinerjaLoading').hide();
          refreshCaptcha();
          var msg = 'Terjadi kesalahan pada server. Silakan coba beberapa saat lagi.';
          if (xhr.status === 422 && xhr.responseJSON) {
            if (xhr.responseJSON.errors) {
              var firstError = Object.values(xhr.responseJSON.errors)[0];
              if (firstError && firstError[0]) msg = firstError[0];
            } else if (xhr.responseJSON.message) {
              msg = xhr.responseJSON.message;
            }
          } else if (xhr.status === 429) {
            msg = 'Terlalu banyak percobaan pencarian. Silakan tunggu beberapa saat sebelum mencoba lagi.';
          }
          Swal.fire({ icon: 'error', title: 'Pencarian Gagal', text: msg });
        },
        complete: function () {
          $btn.prop('disabled', false).html(originalHtml);
        }
      });
    });

    /* Reload captcha setelah submit (berhasil/gagal), memakai mekanisme
       bawaan mews/captcha jika komponen <x-captcha /> menyediakan tombol
       refresh sendiri (biasanya class .captcha-refresh / event klik pada
       gambar). Fallback: reload gambar captcha via endpoint bawaan package. */
    function refreshCaptcha() {
      var $img = $('#formCariEkinerja img[src*="captcha"]');
      if ($img.length) {
        $img.attr('src', $img.attr('src').split('?')[0] + '?_=' + Date.now());
      }
    }
  }
</script>
@endpush