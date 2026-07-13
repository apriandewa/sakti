{{--
  ==========================================================================
  HALAMAN: Cek Kehadiran Pegawai (Frontend Publik)
  ROUTE   : GET  /cekkehadiran        -> cekkehadiran.index
            POST /cekkehadiran/cari   -> cekkehadiran.cari (AJAX JSON)
  LOKASI  : resources/views/frontend/cekkehadiran/index.blade.php
  ==========================================================================
--}}
@extends('frontend.main')

@php
    $title = 'Cek Kehadiran Pegawai';
@endphp

@section('container')
<main class="main cekkehadiran-page">

  {{-- ==========================================
       PAGE TITLE / BREADCRUMB
       ========================================== --}}
  <div class="page-title-section">
    <div class="container">
      <h1 data-aos="fade-up">
        <i class="bi bi-calendar-check me-2"></i>Cek Kehadiran Pegawai
      </h1>
      <p class="text-white-50 mb-3" data-aos="fade-up" data-aos-delay="80" style="max-width:640px;">
        Layanan pengecekan mandiri data kehadiran dan rekapitulasi absensi ASN
        di lingkungan Pemerintah Kabupaten Indragiri Hulu.
      </p>
      <nav data-aos="fade-up" data-aos-delay="150">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active" aria-current="page">Cek Kehadiran</li>
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

          <div class="glass-card ck-search-card" data-aos="fade-up">
            <div class="d-flex align-items-center gap-3 mb-4">
              <div class="service-icon-box mb-0"><i class="bi bi-search"></i></div>
              <div>
                <h4 class="mb-1">Cari Data Kehadiran</h4>
                <p class="text-secondary small mb-0">
                  Masukkan periode, nama, dan NIP sesuai data kepegawaian Anda.
                </p>
              </div>
            </div>

            <form id="formCekKehadiran" class="form-cyber" autocomplete="off">
              @csrf

              {{-- Baris 1: Bulan & Tahun --}}
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="selectBulan">Bulan</label>
                  <select id="selectBulan" name="bulan" class="form-control" required>
                    @foreach($bulanList as $num => $label)
                      <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>
                        {{ $label }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="selectTahun">Tahun</label>
                  <select id="selectTahun" name="tahun" class="form-control" required>
                    @foreach($tahunList as $y => $_)
                      <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- Baris 2: Nama & NIP --}}
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="namaInput">Nama Pegawai</label>
                  <input type="text" id="namaInput" name="nama" class="form-control"
                         placeholder="Nama sesuai SK Kepegawaian" required>
                  <div class="form-text text-muted small mt-1">Minimal sebagian nama (tidak perlu lengkap).</div>
                </div>
                <div class="col-md-6">
                  <label for="nipInput">NIP</label>
                  <input type="text" id="nipInput" name="nip" class="form-control"
                         inputmode="numeric" placeholder="Nomor Induk Pegawai" required>
                </div>
              </div>

              {{-- Captcha --}}
              <div class="mb-4">
                <label>Kode Keamanan</label>
                <x-captcha />
              </div>

              <button type="submit" class="btn-cyber w-100 justify-content-center" id="btnCari">
                <i class="bi bi-search me-1"></i> Cari Data Kehadiran
              </button>
            </form>
          </div>

          {{-- Loading --}}
          <div id="ckLoading" class="text-center py-5" style="display:none;">
            <div class="preloader-spinner mx-auto" style="position:static;border-top-color:var(--gold-500);border-bottom-color:var(--navy-500);"></div>
            <p class="text-secondary mt-3 mb-0">Mencari data kehadiran...</p>
          </div>

          {{-- Hasil --}}
          <div id="ckResult" class="mt-4" style="display:none;"></div>

          {{-- Info --}}
          <div class="text-center mt-4" data-aos="fade-up">
            <p class="text-muted small mb-0">
              <i class="bi bi-info-circle me-1"></i>
              Data kehadiran bersumber dari Sistem Informasi Absensi ASN (Simpegnas) BKN.
              Jika data belum tersedia, hubungi administrator kepegawaian OPD masing-masing.
            </p>
          </div>

        </div>
      </div>
    </div>
  </section>

</main>
@endsection

@push('css')
<style>
/* ===== Scoped styles: Halaman Cek Kehadiran ===== */

.cekkehadiran-page .ck-search-card { max-width: 100%; }

/* Statistik rekap mini */
.cekkehadiran-page .ck-stat-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
  gap: 10px;
  margin-bottom: 20px;
}
.cekkehadiran-page .ck-stat-item {
  background: var(--bg-tertiary);
  border: 1px solid var(--glass-border);
  border-radius: 10px;
  padding: 10px;
  text-align: center;
}
.cekkehadiran-page .ck-stat-label {
  font-family: var(--font-mono); font-size: 0.68rem;
  text-transform: uppercase; letter-spacing: 0.06em;
  color: var(--text-muted); display: block; margin-bottom: 4px;
}
.cekkehadiran-page .ck-stat-val {
  font-size: 1.4rem; font-weight: 700; color: var(--text-primary);
}
.cekkehadiran-page .ck-stat-item.danger  { border-color: rgba(214,69,69,0.4); }
.cekkehadiran-page .ck-stat-item.success { border-color: rgba(46,160,96,0.4); }
.cekkehadiran-page .ck-stat-item.warning { border-color: rgba(245,166,35,0.4); }
.cekkehadiran-page .ck-stat-item.info    { border-color: rgba(59,130,246,0.4); }

/* Tabel harian */
.cekkehadiran-page .ck-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.875rem;
  margin-top: 16px;
}
.cekkehadiran-page .ck-table th {
  background: var(--bg-tertiary);
  border: 1px solid var(--glass-border);
  padding: 8px 10px;
  text-align: left;
  font-size: 0.75rem;
  text-transform: uppercase;
  letter-spacing: 0.04em;
  color: var(--text-secondary);
}
.cekkehadiran-page .ck-table td {
  border: 1px solid var(--glass-border);
  padding: 7px 10px;
  color: var(--text-primary);
}
.cekkehadiran-page .ck-table tr:hover td { background: var(--bg-tertiary); }

/* Badge status */
.cekkehadiran-page .ck-badge {
  display: inline-block; padding: 3px 10px;
  border-radius: 20px; font-size: 0.72rem; font-weight: 700;
}
.cekkehadiran-page .ck-badge.badge-success  { background: rgba(46,160,96,0.14); color: #1f8c53; }
.cekkehadiran-page .ck-badge.badge-danger   { background: rgba(214,69,69,0.14); color: #c53838; }
.cekkehadiran-page .ck-badge.badge-warning  { background: rgba(245,166,35,0.16); color: var(--gold-600); }
.cekkehadiran-page .ck-badge.badge-info     { background: rgba(27,36,80,0.10); color: var(--navy-700); }
.cekkehadiran-page .ck-badge.badge-primary  { background: rgba(59,130,246,0.14); color: #2563eb; }
.cekkehadiran-page .ck-badge.badge-secondary{ background: rgba(19,26,58,0.08); color: var(--text-secondary); }

[data-theme="dark"] .cekkehadiran-page .ck-badge.badge-success  { background: rgba(46,160,96,0.22); color: #4ad189; }
[data-theme="dark"] .cekkehadiran-page .ck-badge.badge-danger   { background: rgba(214,69,69,0.22); color: #ff8484; }
[data-theme="dark"] .cekkehadiran-page .ck-badge.badge-warning  { background: rgba(245,166,35,0.22); color: var(--gold-400); }
[data-theme="dark"] .cekkehadiran-page .ck-badge.badge-primary  { background: rgba(59,130,246,0.22); color: #60a5fa; }
[data-theme="dark"] .cekkehadiran-page .ck-badge.badge-secondary{ background: rgba(255,255,255,0.08); color: var(--text-secondary); }

/* Potongan highlight */
.cekkehadiran-page .potongan-danger { color: #c53838; font-weight: 700; }
.cekkehadiran-page .potongan-warning { color: var(--gold-600); font-weight: 700; }
.cekkehadiran-page .potongan-ok { color: #1f8c53; }

/* Responsive tabel scroll */
.cekkehadiran-page .ck-table-wrap {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 10px;
}

.cekkehadiran-page .ck-result-card {
  padding: 28px;
}
@media (max-width: 576px) {
  .cekkehadiran-page .ck-result-card { padding: 18px 14px; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var waitReady = setInterval(function () {
    if (window.jQuery && window.Swal) {
      clearInterval(waitReady);
      initCekKehadiran(window.jQuery);
    }
  }, 50);
});

function initCekKehadiran($) {
  'use strict';

  /* ---- Helpers ---- */
  function esc(str) {
    return $('<div>').text(str === null || str === undefined ? '' : String(str)).html();
  }

  function fmtPotongan(val) {
    val = parseFloat(val) || 0;
    var cls = val >= 10 ? 'potongan-danger' : (val >= 5 ? 'potongan-warning' : 'potongan-ok');
    return '<span class="' + cls + '">' + val.toFixed(2).replace('.', ',') + '%</span>';
  }

  function statItem(label, val, cls) {
    return '<div class="ck-stat-item ' + (cls || '') + '">' +
      '<span class="ck-stat-label">' + label + '</span>' +
      '<span class="ck-stat-val">' + val + '</span>' +
      '</div>';
  }

  /* ---- Render hasil ---- */
  function renderHasil(res) {
    var p = res.pegawai;
    var r = res.rekap;
    var harian = res.harian;
    var periode = res.periode;

    // Info pegawai
    var html = '<div class="detail-content ck-result-card" data-aos="fade-up">';

    // Header pegawai
    html += '<div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-4 pb-4" style="border-bottom:1px solid var(--glass-border);">';
    html +=   '<div>';
    html +=     '<h4 class="mb-1">' + esc(p.nama) + '</h4>';
    html +=     '<div class="text-secondary small font-monospace">NIP. ' + esc(p.nip) + '</div>';
    if (p.nama_kantor) {
      html +=   '<div class="text-secondary small mt-1"><i class="bi bi-building me-1"></i>' + esc(p.nama_kantor) + '</div>';
    }
    html +=   '</div>';
    html +=   '<span class="ek-badge ek-badge-lg ek-badge-info"><i class="bi bi-calendar3 me-1"></i>' + esc(periode.nama_bulan) + '</span>';
    html += '</div>';

    // Statistik mini grid
    html += '<div class="ck-stat-grid">';
    html += statItem('Hadir', r.hadir, 'success');
    html += statItem('Tanpa Ket.', r.tk, r.tk > 0 ? 'danger' : '');
    html += statItem('Cuti', r.cuti, 'info');
    html += statItem('Dinas Luar', r.dl, 'info');
    html += statItem('Izin', r.izin);
    html += statItem('Hari Kerja', r.hari_kerja);
    html += '</div>';

    // Sub-rekap TM & PC
    html += '<div class="row g-2 mb-4">';
    html +=   '<div class="col-md-6">';
    html +=     '<div class="ck-stat-item warning">';
    html +=       '<span class="ck-stat-label">Terlambat (TM1–TMM)</span>';
    html +=       '<div class="d-flex gap-2 flex-wrap mt-1">';
    var tmCols = ['tm1','tm2','tm3','tm4','tmm'];
    tmCols.forEach(function(k) {
      if (r[k] > 0) {
        html += '<span class="ck-badge badge-warning">' + k.toUpperCase() + ': ' + r[k] + '</span>';
      }
    });
    if (! tmCols.some(function(k) { return r[k] > 0; })) {
      html += '<span class="text-muted small">Tidak ada keterlambatan</span>';
    }
    html +=       '</div>';
    html +=     '</div>';
    html +=   '</div>';
    html +=   '<div class="col-md-6">';
    html +=     '<div class="ck-stat-item warning">';
    html +=       '<span class="ck-stat-label">Pulang Cepat (PC1–PCM)</span>';
    html +=       '<div class="d-flex gap-2 flex-wrap mt-1">';
    var pcCols = ['pc1','pc2','pc3','pc4','pcm'];
    pcCols.forEach(function(k) {
      if (r[k] > 0) {
        html += '<span class="ck-badge badge-warning">' + k.toUpperCase() + ': ' + r[k] + '</span>';
      }
    });
    if (! pcCols.some(function(k) { return r[k] > 0; })) {
      html += '<span class="text-muted small">Tidak ada pulang cepat</span>';
    }
    html +=       '</div>';
    html +=     '</div>';
    html +=   '</div>';
    html += '</div>';

    // Total potongan
    html += '<div class="ck-stat-item mb-4" style="max-width:200px;">';
    html +=   '<span class="ck-stat-label">Total Potongan Bulan Ini</span>';
    html +=   '<span class="ck-stat-val">' + fmtPotongan(r.total_potongan) + '</span>';
    html += '</div>';

    // Tabel harian
    html += '<h6 class="mb-2" style="color:var(--text-secondary);font-size:0.78rem;text-transform:uppercase;letter-spacing:.06em;">';
    html +=   '<i class="bi bi-table me-1"></i> Riwayat Kehadiran Harian';
    html += '</h6>';
    html += '<div class="ck-table-wrap">';
    html +=   '<table class="ck-table">';
    html +=     '<thead>';
    html +=       '<tr>';
    html +=         '<th>No</th><th>Tanggal</th><th>Status</th><th>Jam Masuk</th>';
    html +=         '<th>Jam Keluar</th><th>Terlambat</th><th>Pulang Cepat</th>';
    html +=         '<th>Potongan</th><th>Keterangan</th>';
    html +=       '</tr>';
    html +=     '</thead>';
    html +=     '<tbody>';

    if (harian.length === 0) {
      html += '<tr><td colspan="9" class="text-center text-muted py-3">Tidak ada data harian.</td></tr>';
    } else {
      harian.forEach(function(h, i) {
        var tmInfo = h.kategori_terlambat
          ? ('<span class="ck-badge badge-warning">' + esc(h.kategori_terlambat) + '</span> ' + h.menit_terlambat + ' mnt')
          : '-';
        var pcInfo = h.kategori_pulang_cepat
          ? ('<span class="ck-badge badge-warning">' + esc(h.kategori_pulang_cepat) + '</span> ' + h.menit_pulang_cepat + ' mnt')
          : '-';

        html += '<tr>';
        html +=   '<td>' + (i + 1) + '</td>';
        html +=   '<td style="white-space:nowrap;">' + esc(h.tanggal) + '</td>';
        html +=   '<td><span class="ck-badge ' + esc(h.badge_class) + '">' + esc(h.label_status) + '</span></td>';
        html +=   '<td>' + esc(h.jam_masuk) + '</td>';
        html +=   '<td>' + esc(h.jam_keluar) + '</td>';
        html +=   '<td>' + tmInfo + '</td>';
        html +=   '<td>' + pcInfo + '</td>';
        html +=   '<td>' + fmtPotongan(h.total_potongan) + '</td>';
        html +=   '<td>' + esc(h.keterangan || '-') + '</td>';
        html += '</tr>';
      });
    }

    html +=     '</tbody>';
    html +=   '</table>';
    html += '</div>'; // ck-table-wrap
    html += '</div>'; // detail-content

    var $result = $('#ckResult');
    $result.html(html).fadeIn(300);
    if (typeof AOS !== 'undefined') AOS.refresh();
    $('html, body').animate({ scrollTop: $result.offset().top - 120 }, 500);
  }

  /* ---- NIP: hanya angka ---- */
  $('#nipInput').on('input', function () {
    this.value = this.value.replace(/\D/g, '');
  });

  /* ---- Submit form ---- */
  $('#formCekKehadiran').on('submit', function (e) {
    e.preventDefault();

    var bulan = $('#selectBulan').val();
    var tahun = $('#selectTahun').val();
    var nama  = $('#namaInput').val().trim();
    var nip   = $('#nipInput').val().trim();

    if (! nama) {
      Swal.fire({ icon: 'warning', title: 'Nama belum diisi', text: 'Masukkan nama pegawai.' });
      return;
    }
    if (! nip) {
      Swal.fire({ icon: 'warning', title: 'NIP belum diisi', text: 'Masukkan NIP pegawai.' });
      return;
    }

    var $btn = $('#btnCari');
    var origHtml = $btn.html();
    $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span> Mencari...');
    $('#ckResult').hide().empty();
    $('#ckLoading').show();

    $.ajax({
      url    : '{{ route('cekkehadiran.cari') }}',
      method : 'POST',
      dataType: 'json',
      data   : $(this).serialize(),
      success: function (res) {
        $('#ckLoading').hide();
        if (res && res.success) {
          renderHasil(res);
        } else {
          Swal.fire({
            icon : 'warning',
            title: 'Data Tidak Ditemukan',
            text : (res && res.message) || 'Data kehadiran tidak ditemukan.'
          });
        }
        refreshCaptcha();
      },
      error: function (xhr) {
        $('#ckLoading').hide();
        refreshCaptcha();
        var msg = 'Terjadi kesalahan pada server.';
        if (xhr.status === 422 && xhr.responseJSON) {
          if (xhr.responseJSON.errors) {
            var firstErr = Object.values(xhr.responseJSON.errors)[0];
            if (firstErr && firstErr[0]) msg = firstErr[0];
          } else if (xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
        } else if (xhr.status === 429) {
          msg = 'Terlalu banyak percobaan. Coba lagi beberapa saat.';
        }
        Swal.fire({ icon: 'error', title: 'Pencarian Gagal', text: msg });
      },
      complete: function () {
        $btn.prop('disabled', false).html(origHtml);
      }
    });
  });

  function refreshCaptcha() {
    var $img = $('#formCekKehadiran img[src*="captcha"]');
    if ($img.length) {
      $img.attr('src', $img.attr('src').split('?')[0] + '?_=' + Date.now());
    }
  }
}
</script>
@endpush
