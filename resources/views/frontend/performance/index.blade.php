@extends('frontend.main')

@php
    $title = 'e-Performance Pegawai';
@endphp

@section('container')
<main class="main performance-page">

  {{-- ==========================================
       PAGE TITLE / BREADCRUMB
       ========================================== --}}
  <div class="page-title-section bg-gradient-dark">
    <div class="container">
      <h1 data-aos="fade-up" class="d-flex align-items-center gap-2">
        <i class="bi bi-speedometer2 text-warning"></i> e-Performance Pegawai
      </h1>
      <p class="text-white-50 mb-3" data-aos="fade-up" data-aos-delay="80" style="max-width:680px;">
        Layanan kalkulasi terpadu penilaian kinerja (SKP e-Kinerja BKN 70%) dan rekapitulasi kehadiran
        (Presensi Simpegnas BKN 30%) ASN Pemerintah Kabupaten Indragiri Hulu.
      </p>
      <nav data-aos="fade-up" data-aos-delay="150">
        <ol class="breadcrumb mb-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}">Beranda</a></li>
          <li class="breadcrumb-item active" aria-current="page">e-Performance</li>
        </ol>
      </nav>
    </div>
  </div>

  {{-- ==========================================
       FORM PENCARIAN
       ========================================== --}}
  <section class="section-light-dark py-5">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8">

          <div class="glass-card search-card shadow-lg p-4 p-md-5 rounded-4 mb-5" data-aos="fade-up">
            <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
              <div class="service-icon-box bg-warning bg-opacity-10 text-warning p-3 rounded-circle fs-3">
                <i class="bi bi-person-bounding-box"></i>
              </div>
              <div>
                <h4 class="mb-1 fw-bold">Cari Data Performance Pegawai</h4>
                <p class="text-secondary small mb-0">
                  Pilih bulan & tahun, lalu masukkan Nama dan NIP sesuai data kepegawaian Anda.
                </p>
              </div>
            </div>

            <form id="formPerformance" class="form-cyber" autocomplete="off">
              @csrf

              {{-- Baris 1: Bulan & Tahun --}}
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="selectBulanPerf" class="form-label fw-semibold">Bulan</label>
                  <select id="selectBulanPerf" name="bulan" class="form-select form-control" required>
                    @foreach($bulanList as $num => $label)
                      <option value="{{ $num }}" {{ $num == now()->month ? 'selected' : '' }}>
                        {{ $label }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="selectTahunPerf" class="form-label fw-semibold">Tahun</label>
                  <select id="selectTahunPerf" name="tahun" class="form-select form-control" required>
                    @foreach($tahunList as $y => $_)
                      <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                  </select>
                </div>
              </div>

              {{-- Baris 2: Nama & NIP --}}
              <div class="row g-3 mb-3">
                <div class="col-md-6">
                  <label for="namaInputPerf" class="form-label fw-semibold">Nama Pegawai</label>
                  <input type="text" id="namaInputPerf" name="nama" class="form-control"
                         placeholder="Nama sesuai SK Kepegawaian" required>
                  <div class="form-text text-muted small mt-1">Minimal sebagian nama tanpa gelar.</div>
                </div>
                <div class="col-md-6">
                  <label for="nipInputPerf" class="form-label fw-semibold">NIP</label>
                  <input type="text" id="nipInputPerf" name="nip" class="form-control"
                         inputmode="numeric" placeholder="Nomor Induk Pegawai" required>
                </div>
              </div>

              {{-- Captcha Component --}}
              <div class="mb-4">
                <x-captcha />
              </div>

              {{-- Error Alert --}}
              <div id="alertErrorPerf" class="alert alert-danger d-none rounded-3 mb-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <span id="alertErrorMsgPerf"></span>
              </div>

              {{-- Submit --}}
              <div class="d-grid">
                <button type="submit" id="btnCariPerf" class="btn btn-warning btn-lg fw-bold text-dark py-3 rounded-3">
                  <span id="btnTextPerf"><i class="bi bi-speedometer2 me-2"></i> Tampilkan e-Performance</span>
                  <span id="btnSpinnerPerf" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                </button>
              </div>

            </form>
          </div>

        </div>
      </div>

      {{-- ==========================================
           HASIL E-PERFORMANCE (ANIMATED SPEEDOMETER & BREAKDOWN)
           ========================================== --}}
      <div id="perfResultSection" class="d-none">

        <!-- Header Card Pegawai -->
        <div class="card glass-card border-0 shadow-lg rounded-4 mb-4" data-aos="fade-up">
          <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
              <div class="d-flex align-items-center gap-3">
                <div class="avatar-icon bg-warning bg-opacity-20 text-warning p-3 rounded-circle fs-2">
                  <i class="bi bi-person-fill"></i>
                </div>
                <div>
                  <h3 id="resNama" class="fw-bold mb-1"></h3>
                  <div class="d-flex flex-wrap align-items-center gap-2 text-secondary fs-6">
                    <span><i class="bi bi-card-heading me-1"></i>NIP: <strong id="resNip"></strong></span>
                    <span>•</span>
                    <span><i class="bi bi-building me-1"></i><span id="resKantor"></span></span>
                  </div>
                  <div class="text-muted small mt-1">
                    <i class="bi bi-briefcase me-1"></i>Jabatan: <span id="resJabatan"></span>
                  </div>
                </div>
              </div>
              <div class="text-md-end">
                <span class="badge bg-dark px-3 py-2 fs-6 rounded-pill">
                  <i class="bi bi-calendar-event me-1"></i> Periode: <span id="resPeriode"></span>
                </span>
              </div>
            </div>
          </div>
        </div>

        <!-- SPEEDOMETER GAUGE CARD -->
        <div class="card glass-card border-0 shadow-lg rounded-4 mb-4 text-center p-4" data-aos="zoom-in">
          <div class="card-body">
            <h4 class="fw-bold mb-2">Nilai Performance Gabungan</h4>
            <p class="text-secondary small mb-4">
              Komposisi: <strong>Evaluasi SKP / Kinerja (70%)</strong> + <strong>Presensi / Kehadiran (30%)</strong>
            </p>

            <!-- Custom Animated Speedometer SVG -->
            <div class="speedometer-container position-relative mx-auto my-3" style="max-width: 380px;">
              <svg viewBox="0 0 200 120" class="speedometer-svg w-100">
                <defs>
                  <!-- Gradient Arc Spectrum -->
                  <linearGradient id="gaugeGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                    <stop offset="0%" stop-color="#ef4444" />    <!-- Red (Critical) -->
                    <stop offset="40%" stop-color="#f59e0b" />   <!-- Warning -->
                    <stop offset="70%" stop-color="#06b6d4" />   <!-- Cyan -->
                    <stop offset="90%" stop-color="#3b82f6" />   <!-- Blue -->
                    <stop offset="100%" stop-color="#10b981" />  <!-- Emerald -->
                  </linearGradient>
                  <!-- Shadow Filter for Needle -->
                  <filter id="needleShadow" x="-20%" y="-20%" width="140%" height="140%">
                    <feDropShadow dx="0" dy="2" stdDeviation="2" flood-color="#000" flood-opacity="0.4"/>
                  </filter>
                </defs>

                <!-- Background Track Arc -->
                <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="rgba(200, 200, 200, 0.2)" stroke-width="16" stroke-linecap="round" />

                <!-- Color Gradient Arc -->
                <path d="M 20 100 A 80 80 0 0 1 180 100" fill="none" stroke="url(#gaugeGradient)" stroke-width="16" stroke-linecap="round" />

                <!-- Ticks / Scale Marks -->
                <line x1="20" y1="100" x2="28" y2="100" stroke="#888" stroke-width="2"/>
                <line x1="43" y1="43" x2="49" y2="49" stroke="#888" stroke-width="2"/>
                <line x1="100" y1="20" x2="100" y2="28" stroke="#888" stroke-width="2"/>
                <line x1="157" y1="43" x2="151" y2="49" stroke="#888" stroke-width="2"/>
                <line x1="180" y1="100" x2="172" y2="100" stroke="#888" stroke-width="2"/>

                <text x="15" y="115" font-size="8" fill="#aaa" text-anchor="middle">0%</text>
                <text x="100" y="12" font-size="8" fill="#aaa" text-anchor="middle">50%</text>
                <text x="185" y="115" font-size="8" fill="#aaa" text-anchor="middle">100%</text>

                <!-- Animated Needle Pointer -->
                <g id="speedometerNeedleGroup" transform="rotate(-90 100 100)" style="transition: transform 1.8s cubic-bezier(0.34, 1.56, 0.64, 1);" filter="url(#needleShadow)">
                  <polygon points="96,100 100,28 104,100" fill="#f59e0b"/>
                  <circle cx="100" cy="100" r="8" fill="#1e293b" stroke="#f59e0b" stroke-width="3"/>
                </g>
              </svg>

              <!-- Center Score Display -->
              <div class="score-center-display text-center mt-n3">
                <div id="resFinalScore" class="display-4 fw-black text-warning font-monospace" style="letter-spacing:-1px;">0.00%</div>
                <div id="resGradeBadge" class="badge bg-warning fs-6 px-4 py-2 rounded-pill shadow-sm text-uppercase">
                  MEMUAT...
                </div>
                <div id="resGradeCategory" class="text-secondary small mt-2"></div>
              </div>
            </div>

            <!-- Score Summary Cards -->
            <div class="row g-3 mt-4">
              <div class="col-md-6">
                <div class="p-3 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 h-100 text-start">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-primary"><i class="bi bi-award-fill me-1"></i> Kinerja SKP (70%)</span>
                    <span id="resWeightedKinerja" class="fs-5 fw-bold text-primary">0%</span>
                  </div>
                  <div class="small text-secondary">
                    Kontribusi 70% dari nilai Kinerja BKN (<strong id="resRawKinerja">0%</strong>).
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="p-3 rounded-4 bg-info bg-opacity-10 border border-info border-opacity-25 h-100 text-start">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="fw-bold text-info"><i class="bi bi-clock-history me-1"></i> Kehadiran (30%)</span>
                    <span id="resWeightedKehadiran" class="fs-5 fw-bold text-info">0%</span>
                  </div>
                  <div class="small text-secondary">
                    Kontribusi 30% dari skor Kehadiran (<strong id="resRawKehadiran">0%</strong>).
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <!-- BREAKDOWN DETAILS (2 COLUMNS: KINERJA & PRESENSI) -->
        <div class="row g-4 mb-5">

          <!-- KANAN / KIRI: DETAIL KINERJA SKP BKN -->
          <div class="col-lg-6" data-aos="fade-right">
            <div class="card glass-card border-0 shadow-lg rounded-4 h-100">
              <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <div class="d-flex align-items-center justify-content-between">
                  <h5 class="fw-bold mb-0 text-warning d-flex align-items-center gap-2">
                    <i class="bi bi-award-fill fs-4"></i> Detail Penilaian SKP (70%)
                  </h5>
                  <span class="badge bg-warning text-dark">e-Kinerja BKN</span>
                </div>
              </div>
              <div class="card-body p-4">

                <div class="p-3 rounded-3 bg-dark bg-opacity-25 mb-3 border border-secondary border-opacity-25">
                  <div class="text-secondary small mb-1">Predikat / Hasil Akhir SKP</div>
                  <div id="resHasilAkhir" class="fs-4 fw-bold text-white mb-1">-</div>
                  <div id="resKinerjaNote" class="badge bg-secondary bg-opacity-50 fw-normal"></div>
                </div>

                <div class="row g-3 mb-3">
                  <div class="col-6">
                    <div class="p-3 rounded-3 bg-light bg-opacity-10 border border-secondary border-opacity-10">
                      <div class="text-secondary small">Hasil Kerja</div>
                      <div id="resHasilKerja" class="fw-semibold text-white mt-1">-</div>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="p-3 rounded-3 bg-light bg-opacity-10 border border-secondary border-opacity-10">
                      <div class="text-secondary small">Perilaku Kerja</div>
                      <div id="resPerilakuKerja" class="fw-semibold text-white mt-1">-</div>
                    </div>
                  </div>
                </div>

                <ul class="list-group list-group-flush rounded-3 text-secondary small">
                  <li class="list-group-item bg-transparent text-secondary px-0 d-flex justify-content-between">
                    <span>Pejabat Penilai</span>
                    <strong id="resPenilai" class="text-white text-end ms-2">-</strong>
                  </li>
                  <li class="list-group-item bg-transparent text-secondary px-0 d-flex justify-content-between">
                    <span>Waktu Dinilai</span>
                    <strong id="resWaktuDinilai" class="text-white">-</strong>
                  </li>
                </ul>

              </div>
            </div>
          </div>

          <!-- DETAIL REKAPITULASI PRESENSI & POTONGAN -->
          <div class="col-lg-6" data-aos="fade-left">
            <div class="card glass-card border-0 shadow-lg rounded-4 h-100">
              <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <div class="d-flex align-items-center justify-content-between">
                  <h5 class="fw-bold mb-0 text-info d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history fs-4"></i> Detail Presensi & Potongan (30%)
                  </h5>
                  <span class="badge bg-info text-dark">Simpegnas BKN</span>
                </div>
              </div>
              <div class="card-body p-4">

                <div class="d-flex align-items-center justify-content-between p-3 rounded-3 bg-dark bg-opacity-25 mb-3 border border-secondary border-opacity-25">
                  <div>
                    <div class="text-secondary small mb-1">Total Pemotongan TPP</div>
                    <div id="resTotalPotongan" class="fs-4 fw-bold text-danger">0.00%</div>
                  </div>
                  <div class="text-end">
                    <div class="text-secondary small mb-1">Skor Kehadiran Bersih</div>
                    <div id="resScoreKehadiran" class="fs-4 fw-bold text-success">100.00%</div>
                  </div>
                </div>

                <!-- Grid Counts -->
                <h6 class="fw-bold mb-2 small text-uppercase text-secondary">Rincian Keterlambatan & Pulang Cepat:</h6>
                <div class="row g-2 mb-3">
                  <div class="col-3">
                    <div class="p-2 rounded bg-dark text-center border border-secondary border-opacity-10">
                      <div class="text-muted extra-small">TL 1</div>
                      <div id="resCountTL1" class="fw-bold text-warning">0</div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="p-2 rounded bg-dark text-center border border-secondary border-opacity-10">
                      <div class="text-muted extra-small">TL 2</div>
                      <div id="resCountTL2" class="fw-bold text-warning">0</div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="p-2 rounded bg-dark text-center border border-secondary border-opacity-10">
                      <div class="text-muted extra-small">TL 3</div>
                      <div id="resCountTL3" class="fw-bold text-warning">0</div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="p-2 rounded bg-dark text-center border border-secondary border-opacity-10">
                      <div class="text-muted extra-small">TL 4</div>
                      <div id="resCountTL4" class="fw-bold text-danger">0</div>
                    </div>
                  </div>

                  <div class="col-3">
                    <div class="p-2 rounded bg-dark text-center border border-secondary border-opacity-10">
                      <div class="text-muted extra-small">PSW 1</div>
                      <div id="resCountPSW1" class="fw-bold text-warning">0</div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="p-2 rounded bg-dark text-center border border-secondary border-opacity-10">
                      <div class="text-muted extra-small">PSW 2</div>
                      <div id="resCountPSW2" class="fw-bold text-warning">0</div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="p-2 rounded bg-dark text-center border border-secondary border-opacity-10">
                      <div class="text-muted extra-small">PSW 3</div>
                      <div id="resCountPSW3" class="fw-bold text-warning">0</div>
                    </div>
                  </div>
                  <div class="col-3">
                    <div class="p-2 rounded bg-dark text-center border border-secondary border-opacity-10">
                      <div class="text-muted extra-small">PSW 4</div>
                      <div id="resCountPSW4" class="fw-bold text-danger">0</div>
                    </div>
                  </div>
                </div>

                <div class="d-flex flex-wrap gap-2 text-secondary small pt-2 border-top border-secondary border-opacity-25">
                  <span class="badge bg-secondary bg-opacity-20 text-white">Hadir: <strong id="resCountHadir">0</strong> hr</span>
                  <span class="badge bg-danger bg-opacity-20 text-danger">Alpa: <strong id="resCountAlpa">0</strong> hr</span>
                  <span class="badge bg-info bg-opacity-20 text-info">Cuti: <strong id="resCountCuti">0</strong> hr</span>
                  <span class="badge bg-primary bg-opacity-20 text-primary">DL: <strong id="resCountDL">0</strong> hr</span>
                </div>

              </div>
            </div>
          </div>

        </div>

      </div>

    </div>
  </section>

</main>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var waitReady = setInterval(function () {
    if (window.jQuery && window.Swal) {
      clearInterval(waitReady);
      initPerformance(window.jQuery, window.Swal);
    }
  }, 50);
});

function initPerformance($, Swal) {
  'use strict';

  /* ---- NIP: hanya angka ---- */
  $('#nipInputPerf').on('input', function () {
    this.value = this.value.replace(/\D/g, '');
  });

  // Form Submit AJAX
  $('#formPerformance').on('submit', function(e) {
    e.preventDefault();

    var bulan = $('#selectBulanPerf').val();
    var tahun = $('#selectTahunPerf').val();
    var nama  = $('#namaInputPerf').val().trim();
    var nip   = $('#nipInputPerf').val().trim();
    var captcha = $('#captcha').val() ? $('#captcha').val().trim() : '';

    if (! nama) {
      Swal.fire({ icon: 'warning', title: 'Nama Belum Diisi', text: 'Masukkan nama pegawai sesuai SK.' });
      return;
    }
    if (! nip) {
      Swal.fire({ icon: 'warning', title: 'NIP Belum Diisi', text: 'Masukkan Nomor Induk Pegawai.' });
      return;
    }
    if (! captcha) {
      Swal.fire({ icon: 'warning', title: 'Kode Keamanan Belum Diisi', text: 'Masukkan hasil kode keamanan.' });
      return;
    }

    var $btn = $('#btnCariPerf');
    var $btnText = $('#btnTextPerf');
    var $btnSpinner = $('#btnSpinnerPerf');
    var $alertError = $('#alertErrorPerf');
    var $alertMsg = $('#alertErrorMsgPerf');
    var $resultSec = $('#perfResultSection');

    $alertError.addClass('d-none');
    $btn.prop('disabled', true);
    $btnText.addClass('d-none');
    $btnSpinner.removeClass('d-none');

    $.ajax({
      url: '{{ route("performance.cari") }}',
      type: 'POST',
      data: $(this).serialize(),
      dataType: 'json',
      success: function(res) {
        $btn.prop('disabled', false);
        $btnText.removeClass('d-none');
        $btnSpinner.addClass('d-none');

        if (!res.success) {
          var errorMsg = res.message || 'Data performance tidak ditemukan.';
          $alertMsg.text(errorMsg);
          $alertError.removeClass('d-none');
          
          Swal.fire({
            icon: 'warning',
            title: 'Data Tidak Ditemukan',
            text: errorMsg
          });

          $('#reload-captcha').trigger('click');
          $('#captcha').val('');
          return;
        }

        // Populate Pegawai Info
        $('#resNama').text(res.pegawai.nama);
        $('#resNip').text(res.pegawai.nip);
        $('#resKantor').text(res.pegawai.nama_kantor || '-');
        $('#resJabatan').text(res.pegawai.jabatan || '-');
        $('#resPeriode').text(res.periode.nama_bulan);

        // Populate Performance Final Score & Grade
        var score = res.performance.final_score;
        $('#resFinalScore').text(score.toFixed(2) + '%').css('color', res.performance.color);
        
        $('#resGradeBadge')
          .attr('class', 'badge fs-6 px-4 py-2 rounded-pill shadow-sm text-uppercase ' + res.performance.badge_class)
          .text(res.performance.grade_label);
        
        $('#resGradeCategory').text(res.performance.grade_category);

        $('#resWeightedKinerja').text(res.performance.weighted_kinerja.toFixed(2) + '%');
        $('#resRawKinerja').text(res.kinerja.raw_score.toFixed(1) + '%');

        $('#resWeightedKehadiran').text(res.performance.weighted_kehadiran.toFixed(2) + '%');
        $('#resRawKehadiran').text(res.presensi.raw_score.toFixed(2) + '%');

        // Update Speedometer Needle (-90deg = 0%, +90deg = 100%)
        var angle = -90 + ((score / 100) * 180);
        $('#speedometerNeedleGroup').css('transform', 'rotate(' + angle + 'deg)');

        // Populate Kinerja Details
        $('#resHasilAkhir').text(res.kinerja.hasil_akhir);
        $('#resKinerjaNote').text(res.kinerja.status_note);
        $('#resHasilKerja').text(res.kinerja.hasil_kerja);
        $('#resPerilakuKerja').text(res.kinerja.perilaku_kerja);
        $('#resPenilai').text(res.kinerja.pejabat_penilai);
        $('#resWaktuDinilai').text(res.kinerja.waktu_dinilai);

        // Populate Presensi Details
        $('#resTotalPotongan').text('-' + res.presensi.total_potongan.toFixed(2) + '%');
        $('#resScoreKehadiran').text(res.presensi.raw_score.toFixed(2) + '%');

        $('#resCountTL1').text(res.presensi.count_tl1);
        $('#resCountTL2').text(res.presensi.count_tl2);
        $('#resCountTL3').text(res.presensi.count_tl3);
        $('#resCountTL4').text(res.presensi.count_tl4);

        $('#resCountPSW1').text(res.presensi.count_psw1);
        $('#resCountPSW2').text(res.presensi.count_psw2);
        $('#resCountPSW3').text(res.presensi.count_psw3);
        $('#resCountPSW4').text(res.presensi.count_psw4);

        $('#resCountHadir').text(res.presensi.count_hadir);
        $('#resCountAlpa').text(res.presensi.count_alpa);
        $('#resCountCuti').text(res.presensi.count_cuti);
        $('#resCountDL').text(res.presensi.count_dl);

        // Show Result with animation
        $resultSec.removeClass('d-none');
        $('html, body').animate({
          scrollTop: $resultSec.offset().top - 80
        }, 600);

        $('#reload-captcha').trigger('click');
        $('#captcha').val('');
      },
      error: function(xhr) {
        $btn.prop('disabled', false);
        $btnText.removeClass('d-none');
        $btnSpinner.addClass('d-none');

        var msg = 'Terjadi kesalahan sistem. Silakan coba lagi.';
        if (xhr.status === 422 && xhr.responseJSON) {
          if (xhr.responseJSON.errors) {
            var firstErr = Object.values(xhr.responseJSON.errors)[0];
            if (firstErr && firstErr[0]) msg = firstErr[0];
          } else if (xhr.responseJSON.message) {
            msg = xhr.responseJSON.message;
          }
        } else if (xhr.status === 404 && xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        } else if (xhr.status === 429) {
          msg = 'Terlalu banyak permintaan. Silakan tunggu 1 menit lalu coba lagi.';
        } else if (xhr.responseJSON && xhr.responseJSON.message) {
          msg = xhr.responseJSON.message;
        }

        $alertMsg.html(msg);
        $alertError.removeClass('d-none');

        Swal.fire({
          icon: 'error',
          title: 'Pencarian Gagal',
          html: msg
        });

        $('#reload-captcha').trigger('click');
        $('#captcha').val('');
      }
    });
  });
}
</script>
@endpush
