@extends('backend.main.index')
@push('title', $title)
@section('content')
<div class="content-wrapper">
    <div class="container-full">
        <div class="content-header">
            <div class="d-flex align-items-center">
                <div class="me-auto">
                    <h3 class="page-title">{{ $title }}</h3>
                    <div class="d-inline-block align-items-center">
                        <nav>
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="mdi mdi-home-outline"></i></a></li>
                                <li class="breadcrumb-item" aria-current="page">Documentation</li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $title }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="row">
                <div class="col-12">
                    <div class="box">
                        <div class="box-body p-0">
                            <!-- Reveal.js Container -->
                            <div class="reveal" style="height: 75vh; width: 100%; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); background-color: #ffffff;">
                                <!-- Particles Background -->
                                <div id="particles-js" style="position: absolute; width: 100%; height: 100%; top: 0; left: 0; z-index: 1; pointer-events: none;"></div>
                                
                                <!-- Global Logo Overlay -->
                                <div class="global-logo" style="position: absolute; top: 20px; right: 20px; z-index: 50;">
                                    <img src="{{ url($template.'/images/logodiskominfotik.png') }}" alt="Logo" style="width: 120px; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.2));">
                                </div>
                                <div class="slides" style="z-index: 2;">
                                    <!-- Slide 1: Cover -->
                                    <section data-transition="zoom">
                                        <img src="{{ url($template.'/images/logodiskominfotik.png') }}" alt="Logo Utama" style="width: 250px; margin-bottom: 20px; filter: drop-shadow(0px 4px 10px rgba(0,0,0,0.5));">
                                        <h2 class="slide-title-main" style="font-weight: 600; text-shadow: 0px 4px 10px rgba(0,0,0,0.1); letter-spacing: -1px; font-size: 1.5em;">Rancangan Portal Website</h2>
                                        <h4 class="slide-subtitle-main" style="font-weight: 300; margin-top: 10px;">Dinas Komunikasi Informatika dan Statistik</h4>
                                        <h4 class="slide-subtitle-main" style="font-weight: 300; margin-top: 10px;">Kabupaten Indragiri Hulu</h4>
                                        <hr style="width: 30%; border-color: #0052D4; margin: 30px auto;">
                                        <p class="slide-desc-main" style="font-size: 20px;">Oleh : Tim Pengembangan Aplikasi Bidang Penyelenggaraan E-Government dan Persandian</p>
                                    </section>

                                    <!-- Slide 2: Latar Belakang -->
                                    <section data-transition="slide">
                                        <h3>Modul Utama</h3>
                                        <p class="slide-subtitle" style="font-size: 22px; margin-bottom: 30px;">Berikut adalah beberapa fitur unggulan dari Portal Website ini:</p>
                                        <div class="row">
                                            <div class="col-6 fragment fade-up" data-fragment-index="1">
                                                <div class="tech-box border-top border-primary border-5">
                                                    <i class="mdi mdi-web fs-50 text-primary"></i>
                                                    <h5 style="font-size: 20px;">Portal Informasi</h5>
                                                    <p class="fs-16">Berita, Galeri, dan Unduhan (informasi publik) yang resmi, akurat, cepat dan terpercaya.</p>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-up" data-fragment-index="2">
                                                <div class="tech-box border-top border-success border-5">
                                                    <i class="mdi mdi-calendar-check fs-50 text-success"></i>
                                                    <h5 style="font-size: 20px;">Agenda Rapat</h5>
                                                    <p class="fs-16">Digitalisasi Agenda Rapat untuk kemudahan pengelolaan rapat dan arsip digital yang lebih baik</p>
                                                </div>
                                            </div>
                                            <div class="col-6 mt-3 fragment fade-up" data-fragment-index="3">
                                                <div class="tech-box border-top border-info border-5">
                                                    <i class="mdi mdi-account-multiple fs-50 text-info"></i>
                                                    <h5 style="font-size: 20px;">Informasi Kepegawaian</h5>
                                                    <p class="fs-16">Manajemen Data Kepegawaian yang akurat dan terintegrasi dengan BKN, lengkap dengan rekap presensi Simpegnas.</p>
                                                </div>
                                            </div>
                                            <div class="col-6 mt-3 fragment fade-up" data-fragment-index="4">
                                                <div class="tech-box border-top border-warning border-5">
                                                    <i class="mdi mdi-fingerprint fs-50 text-warning"></i>
                                                    <h5 style="font-size: 20px;">Integrasi TTE BSrE</h5>
                                                    <p class="fs-16">Transformasi digital melalui Tanda Tangan Elektronik resmi dan dalam pembuatan dokumen resmi pemerintah</p>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 3: Alur Penggunaan Konten -->
                                    <section>
                                        <h3>Alur Penggunaan: Konten Portal</h3>
                                        <p class="slide-subtitle" style="font-size: 20px;">Sistem persetujuan (approval) publikasi untuk menjaga kualitas informasi.</p>
                                        
                                        <div style="margin-top: 50px;" class="d-flex justify-content-center align-items-center">
                                            <div class="text-center fragment fade-right">
                                                <div class="flow-step"><i class="mdi mdi-pencil"></i></div>
                                                <p class="mt-2 fw-bold fs-18">1. Operator</p>
                                                <small>Membuat Draf</small>
                                            </div>
                                            <div class="flow-arrow fragment fade-right" data-fragment-index="1"><i class="mdi mdi-arrow-right-thick"></i></div>
                                            <div class="text-center fragment fade-right" data-fragment-index="2">
                                                <div class="flow-step" style="border-color: #f39c12; color: #f39c12;"><i class="mdi mdi-magnify"></i></div>
                                                <p class="mt-2 fw-bold fs-18">2. Verifikator</p>
                                                <small>Review & Revisi</small>
                                            </div>
                                            <div class="flow-arrow fragment fade-right" data-fragment-index="3"><i class="mdi mdi-arrow-right-thick"></i></div>
                                            <div class="text-center fragment fade-right" data-fragment-index="4">
                                                <div class="flow-step" style="border-color: #27ae60; color: #27ae60;"><i class="mdi mdi-earth"></i></div>
                                                <p class="mt-2 fw-bold fs-18">3. Sistem</p>
                                                <small>Publikasi Online</small>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 4: Alur Rapat -->
                                    <section>
                                        <h3>Alur Penggunaan: Agenda Rapat & TTE</h3>
                                        <div class="row align-items-center mt-4">
                                            <div class="col-12">
                                                <ul class="list-styled" style="font-size: 20px;">
                                                    <li class="fragment custom blur"><strong>Pembuatan Draf Rapat:</strong> Operator mengatur jadwal, sistem mencegah bentrok jadwal ruangan.</li>
                                                    <li class="fragment custom blur"><strong>Penyebaran Undangan:</strong> Dilengkapi QR Code Token untuk absensi. Verifikator melakukan TTE Undangan.</li>
                                                    <li class="fragment custom blur"><strong>Presensi Digital:</strong> Peserta scan QR, masuk link token, dan Tanda Tangan Canvas HTML5. Sistem buka akses unduh materi.</li>
                                                    <li class="fragment custom blur"><strong>Sirkulasi Notulen:</strong> Ditulis oleh Notulis &rarr; Disetujui Pimpinan &rarr; Ditandatangani elektronik berjenjang (BSrE).</li>
                                                    <li class="fragment custom blur"><strong>Arsip Final:</strong> Dokumen ber-QR divalidasi keabsahannya via <code>/rapat/verifikasi</code>.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 5: User Management / RBAC -->
                                    <section>
                                        <h3>Manajemen Pengguna (RBAC)</h3>
                                        <p class="slide-subtitle" style="font-size: 20px; margin-bottom: 20px;">Role-Based Access Control dengan 4 Tingkatan Keamanan</p>
                                        
                                        <div class="row">
                                            <div class="col-6 fragment fade-up">
                                                <div class="role-card" style="border-left-color: #e74c3c;">
                                                    <h4><i class="mdi mdi-crown text-danger"></i> Level 1: Root</h4>
                                                    <p>Akses tanpa batas, manajemen menu, reset sistem, override seluruh data notulen.</p>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-up" data-fragment-index="1">
                                                <div class="role-card" style="border-left-color: #f39c12;">
                                                    <h4><i class="mdi mdi-shield-account text-warning"></i> Level 2: Administrator</h4>
                                                    <p>Manajemen kepegawaian, slider/page dinamis, dan pengaturan sistem global.</p>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-up" data-fragment-index="2">
                                                <div class="role-card" style="border-left-color: #3498db;">
                                                    <h4><i class="mdi mdi-account-edit text-info"></i> Level 3: Operator</h4>
                                                    <p>Hanya mengelola (CRUD) konten dan draf rapat mandiri.</p>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-up" data-fragment-index="3">
                                                <div class="role-card" style="border-left-color: #27ae60;">
                                                    <h4><i class="mdi mdi-check-decagram text-success"></i> Level 4: Verifikator</h4>
                                                    <p>Persetujuan draf konten dan eksekutor TTE (BSrE Passphrase).</p>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 6: Presensi BKN -->
                                    <section>
                                        <h3 class="text-primary">Integrasi Presensi Simpegnas BKN</h3>
                                        <p class="slide-subtitle-dark" style="font-size: 20px;">Kalkulasi Kedisiplinan & Pemotongan Tunjangan Otomatis</p>
                                        
                                        <div class="row mt-4">
                                            <div class="col-5">
                                                <img src="{{ url($template.'/images/svg-icon/color-svg/custom-14.svg') }}" alt="API" style="width: 100%; max-width: 250px; background: none; border: none; box-shadow: none;">
                                            </div>
                                            <div class="col-7 text-start">
                                                <ul class="list-styled fs-22">
                                                    <li class="fragment fade-left"><strong>Sync Data Harian:</strong> Tarik base64 foto check-in & geolokasi via <code>image-riwayat</code>.</li>
                                                    <li class="fragment fade-left"><strong>Akurasi Waktu:</strong> Menggunakan <code>time_with_timezone</code> untuk kepastian log (Asia/Jakarta).</li>
                                                    <li class="fragment fade-left"><strong>Auto-Kalkulasi:</strong> Hitung kategori Terlambat (TM1-TMM) dan Pulang Cepat (PC1-PC5).</li>
                                                    <li class="fragment fade-left"><strong>Penalti Ketidakhadiran:</strong> Otomatisasi bobot potongan 3.0% untuk status Tanpa Keterangan (TK).</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </section>
                                    
                                    <!-- Slide 7: Logika Perhitungan Potongan (Presensi) -->
                                    <section>
                                        <h3>Logika Potongan Kehadiran (Simpegnas BKN)</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 15px;">Persentase pemotongan tunjangan berdasarkan tingkat pelanggaran waktu harian.</p>

                                        <div class="row" style="font-size: 14px;">
                                            <div class="col-4 fragment fade-up">
                                                <div class="role-card" style="border-left-color: #f1c40f; padding: 10px; min-height: 250px;">
                                                    <h5 class="fw-bold text-warning"><i class="mdi mdi-clock-alert"></i> Terlambat (TM)</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="font-size: 13px; line-height: 1.6;">
                                                        <li><strong>TM1 (1-30 m):</strong> 0.5%</li>
                                                        <li><strong>TM2 (31-60 m):</strong> 1.0%</li>
                                                        <li><strong>TM3 (61-90 m):</strong> 1.25%</li>
                                                        <li><strong>TM4/TMM (>90 m):</strong> 1.5%</li>
                                                    </ul>
                                                    <small class="text-muted d-block mt-2">Dihitung otomatis per kejadian log harian.</small>
                                                </div>
                                            </div>
                                            <div class="col-4 fragment fade-up" data-fragment-index="1">
                                                <div class="role-card" style="border-left-color: #e67e22; padding: 10px; min-height: 250px;">
                                                    <h5 class="fw-bold text-warning" style="color: #e67e22 !important;"><i class="mdi mdi-logout-variant"></i> Pulang Cepat (PC)</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="font-size: 13px; line-height: 1.6;">
                                                        <li><strong>PC4/PCM (1-30 m):</strong> 0.5%</li>
                                                        <li><strong>PC3 (31-60 m):</strong> 1.0%</li>
                                                        <li><strong>PC2 (61-90 m):</strong> 1.25%</li>
                                                        <li><strong>PC1/PC5 (>90 m):</strong> 1.5%</li>
                                                    </ul>
                                                    <small class="text-muted d-block mt-2">Dideteksi dari jam keluar aktual pegawai.</small>
                                                </div>
                                            </div>
                                            <div class="col-4 fragment fade-up" data-fragment-index="2">
                                                <div class="role-card" style="border-left-color: #e74c3c; padding: 10px; min-height: 250px;">
                                                    <h5 class="fw-bold text-danger"><i class="mdi mdi-alert-circle"></i> Tanpa Keterangan (TK)</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="font-size: 13px; line-height: 1.6;">
                                                        <li><strong>TK (Alpa):</strong> 3.0% / hari</li>
                                                        <li class="mt-2 text-danger"><strong>Aturan Eksklusi:</strong> Jika status 'TK', kalkulasi TM &amp; PC pada hari tersebut diabaikan secara total.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="tech-box mt-3 p-2 fragment zoom-in" data-fragment-index="3" style="border-top: 3px solid #0052D4;">
                                            <p class="m-0 fw-bold" style="font-size: 15px;">
                                                Formula Potongan Bulanan = &sum;(TM &times; Bobot) + &sum;(PC &times; Bobot) + &sum;(TK &times; 3%)
                                            </p>
                                            <small class="text-muted" style="font-size: 12px;">Contoh: 3x TM1 (1.5%) + 1x TM3 (1.25%) + 2x PC4 (1.0%) + 1x TK (3.0%) = Total Potongan 6.75%</small>
                                        </div>
                                    </section>

                                    <!-- Slide 8: Detail Integrasi API BKN & Geolokasi -->
                                    <section>
                                        <h3>Detail Integrasi API &amp; Geolokasi</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Penarikan data asinkron dan verifikasi visual lokasi presensi.</p>

                                        <div class="row align-items-center">
                                            <div class="col-7 text-start">
                                                <div class="role-card fragment fade-right" style="border-left-color: #3498db; padding: 10px; margin-bottom: 10px;">
                                                    <h6 class="fw-bold m-0"><i class="mdi mdi-api"></i> Endpoint Rekap Bulanan Kantor</h6>
                                                    <p style="font-size: 13px; margin: 2px 0 0 0;"><code>GET /absensi/api/get/rekap-bulanan-by-kantor</code></p>
                                                    <small class="text-muted" style="font-size: 11px;">Query params: <code>kantor_id</code>, <code>tahun</code>, <code>bulan</code>. Header: JWT Bearer Token.</small>
                                                </div>
                                                <div class="role-card fragment fade-right" data-fragment-index="1" style="border-left-color: #2ecc71; padding: 10px; margin-bottom: 10px;">
                                                    <h6 class="fw-bold m-0"><i class="mdi mdi-camera"></i> Foto Selfie &amp; Geotagging</h6>
                                                    <small class="text-muted" style="font-size: 12px;">Data ditarik secara dinamis dari API Simpegnas via route lokal <code>image-riwayat</code> dalam format Base64.</small>
                                                </div>
                                                <div class="role-card fragment fade-right" data-fragment-index="2" style="border-left-color: #9b59b6; padding: 10px; margin-bottom: 0;">
                                                    <h6 class="fw-bold m-0"><i class="mdi mdi-image-broken-variant"></i> Adaptive Error Handling</h6>
                                                    <small class="text-muted" style="font-size: 12px;">Sistem otomatis mendeteksi kegagalan load gambar (base64 rusak/null) and menyembunyikan elemen gambar secara aman di modal detail.</small>
                                                </div>
                                            </div>
                                            <div class="col-5 fragment zoom-in" data-fragment-index="3">
                                                <div class="tech-box p-3 text-center" style="border-top: 4px solid #3498db;">
                                                    <i class="mdi mdi-map-marker-radius text-danger" style="font-size: 40px;"></i>
                                                    <h6 class="fw-bold mt-2">Akurasi Geolokasi</h6>
                                                    <p class="text-muted m-0" style="font-size: 12px;">Menggunakan parameter <code>time_with_timezone</code> untuk kepastian log zona waktu Asia/Jakarta.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 9: Siklus Rapat & Penandatanganan Elektronik -->
                                    <section>
                                        <h3>Siklus Rapat &amp; Penandatanganan TTE BSrE</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Digitalisasi rapat dari draf agenda hingga penandatanganan dokumen resmi negara.</p>

                                        <div class="row">
                                            <div class="col-6 fragment fade-up">
                                                <div class="role-card" style="border-left-color: #1abc9c; min-height: 260px;">
                                                    <h5 class="fw-bold text-success" style="color: #1abc9c !important;"><i class="mdi mdi-calendar-clock"></i> Siklus Agenda Terpadu</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="font-size: 13px; line-height: 1.7;">
                                                        <li><i class="mdi mdi-check text-success"></i> <strong>Deteksi Bentrok Ruangan:</strong> Validasi asinkron (AJAX) jadwal ruangan.</li>
                                                        <li><i class="mdi mdi-check text-success"></i> <strong>Tab Rapat Terstruktur:</strong> Undangan, Daftar Hadir, Materi, Dokumentasi, Notulen, &amp; Riwayat.</li>
                                                        <li><i class="mdi mdi-check text-success"></i> <strong>Absensi Kanvas HTML5:</strong> Scan QR code, isi presensi, tanda tangan kanvas, lalu akses unduh materi.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-up" data-fragment-index="1">
                                                <div class="role-card" style="border-left-color: #e67e22; min-height: 260px;">
                                                    <h5 class="fw-bold text-warning" style="color: #e67e22 !important;"><i class="mdi mdi-key-variant"></i> TTE BSrE &amp; Validasi Publik</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="font-size: 13px; line-height: 1.7;">
                                                        <li><i class="mdi mdi-check text-warning" style="color: #e67e22 !important;"></i> <strong>TTE Sekuensial:</strong> Notulen ditandatangani berjenjang oleh Notulis lalu Pimpinan Rapat.</li>
                                                        <li><i class="mdi mdi-check text-warning" style="color: #e67e22 !important;"></i> <strong>Proteksi Unduhan:</strong> Setelah ditandatangani elektronik, tautan file manual/un-signed otomatis disembunyikan.</li>
                                                        <li><i class="mdi mdi-check text-warning" style="color: #e67e22 !important;"></i> <strong>Verifikasi Publik:</strong> Scan QR Code pada PDF mengarah ke <code>/rapat/verifikasi</code> untuk validasi metadata secara real-time.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 10: File Observer & Trait Loggable -->
                                    <section>
                                        <h3>Otomatisasi Berkas &amp; Audit Trail</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Pemeliharaan integritas storage server dan pelacakan riwayat modifikasi data.</p>

                                        <div class="row align-items-center">
                                            <div class="col-6 fragment fade-right">
                                                <div class="tech-box p-3 text-start" style="border-top: 4px solid #e74c3c; min-height: 220px;">
                                                    <h5 class="fw-bold text-danger"><i class="mdi mdi-folder-remove"></i> Laravel File Observer</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <p style="font-size: 13px; line-height: 1.6;" class="text-muted m-0">
                                                        Mendengarkan event <code>deleted</code> pada model Eloquent (seperti Berita, Unduhan, Galeri, Pegawai, Rapat).
                                                    </p>
                                                    <p style="font-size: 13px; line-height: 1.6;" class="text-muted mt-2 mb-0">
                                                        Secara otomatis menghapus file fisik terkait dari direktori <code>storage/</code> server demi menghindari tumpukan sampah berkas (*orphan files*).
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-left" data-fragment-index="1">
                                                <div class="tech-box p-3 text-start" style="border-top: 4px solid #9b59b6; min-height: 220px;">
                                                    <h5 class="fw-bold text-primary" style="color: #9b59b6 !important;"><i class="mdi mdi-history"></i> Trait Loggable (Audit Trail)</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <p style="font-size: 13px; line-height: 1.6;" class="text-muted m-0">
                                                        Mencatat log perubahan model secara instan ke dalam database dalam bentuk JSON terstruktur.
                                                    </p>
                                                    <p style="font-size: 13px; line-height: 1.6;" class="text-muted mt-2 mb-0">
                                                        Merekam data <strong>sebelum (before)</strong> dan <strong>sesudah (after)</strong> modifikasi, lengkap dengan detail Aktor (`user_id`), IP Address, User Agent (OS/Browser), URL, dan HTTP Method.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                                                        <!-- NEW: SDLC Slide -->
                                    <section>
                                        <h3>Siklus Hidup Pengembangan (SDLC)</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">7 Fase Metodologi Agile / Iterative Waterfall sesuai PRD</p>
                                        <div class="row text-center mt-3" style="font-size: 13px;">
                                            <div class="col fragment fade-up">
                                                <div class="flow-step" style="width:60px; height:60px; line-height:55px; font-size:22px; margin:0 auto; background:#e8f4f8; border-color:#3498db; color:#3498db;"><i class="mdi mdi-lightbulb-on"></i></div>
                                                <h6 class="mt-2 fw-bold" style="font-size:14px;">1. Inisialisasi</h6>
                                                <small class="text-muted">Perencanaan &amp; PRD</small>
                                            </div>
                                            <div class="col fragment fade-up" data-fragment-index="1">
                                                <div class="flow-step" style="width:60px; height:60px; line-height:55px; font-size:22px; margin:0 auto; background:#fcf3cf; border-color:#f1c40f; color:#f1c40f;"><i class="mdi mdi-magnify"></i></div>
                                                <h6 class="mt-2 fw-bold" style="font-size:14px;">2. Analisis</h6>
                                                <small class="text-muted">Kebutuhan &amp; TTE</small>
                                            </div>
                                            <div class="col fragment fade-up" data-fragment-index="2">
                                                <div class="flow-step" style="width:60px; height:60px; line-height:55px; font-size:22px; margin:0 auto; background:#eaeded; border-color:#95a5a6; color:#95a5a6;"><i class="mdi mdi-vector-curve"></i></div>
                                                <h6 class="mt-2 fw-bold" style="font-size:14px;">3. Desain</h6>
                                                <small class="text-muted">ERD, DFD &amp; UI</small>
                                            </div>
                                            <div class="col fragment fade-up" data-fragment-index="3">
                                                <div class="flow-step" style="width:60px; height:60px; line-height:55px; font-size:22px; margin:0 auto; background:#eef2f5; border-color:#0052D4; color:#0052D4;"><i class="mdi mdi-code-tags"></i></div>
                                                <h6 class="mt-2 fw-bold" style="font-size:14px;">4. Coding</h6>
                                                <small class="text-muted">Laravel MVC &amp; Obs</small>
                                            </div>
                                        </div>
                                        <div class="row text-center mt-3" style="font-size: 13px;">
                                            <div class="col fragment fade-up" data-fragment-index="4">
                                                <div class="flow-step" style="width:60px; height:60px; line-height:55px; font-size:22px; margin:0 auto; background:#fbeee6; border-color:#e67e22; color:#e67e22;"><i class="mdi mdi-bug"></i></div>
                                                <h6 class="mt-2 fw-bold" style="font-size:14px;">5. Pengujian</h6>
                                                <small class="text-muted">QA &amp; API Testing</small>
                                            </div>
                                            <div class="col fragment fade-up" data-fragment-index="5">
                                                <div class="flow-step" style="width:60px; height:60px; line-height:55px; font-size:22px; margin:0 auto; background:#e8f8f5; border-color:#1abc9c; color:#1abc9c;"><i class="mdi mdi-cloud-upload"></i></div>
                                                <h6 class="mt-2 fw-bold" style="font-size:14px;">6. Deploy</h6>
                                                <small class="text-muted">Server &amp; Storage</small>
                                            </div>
                                            <div class="col fragment fade-up" data-fragment-index="6">
                                                <div class="flow-step" style="width:60px; height:60px; line-height:55px; font-size:22px; margin:0 auto; background:#f5eef8; border-color:#9b59b6; color:#9b59b6;"><i class="mdi mdi-shield-check"></i></div>
                                                <h6 class="mt-2 fw-bold" style="font-size:14px;">7. Audit Log</h6>
                                                <small class="text-muted">Loggable Trait</small>
                                            </div>
                                        </div>
                                    </section>

<!-- NEW: Use Case Slide -->
<section>
    <h3>Pemodelan Use Case (Hak Akses Aktor)</h3>
    <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 15px;">Pemetaan 10 Use Case Utama kepada 4 Aktor Sistem sesuai PRD</p>
    <div class="row" style="font-size: 13px;">
        <div class="col-3 fragment fade-up">
            <div class="role-card" style="border-left-color: #34495e; padding: 8px 10px; min-height: 240px;">
                <h6 class="fw-bold"><i class="mdi mdi-account-multiple text-primary"></i> 1. Publik</h6>
                <hr style="margin: 5px 0;">
                <ul class="list-unstyled p-0 m-0" style="font-size: 11px; line-height: 1.5;">
                    <li>&bull; Lihat Konten &amp; Pegawai</li>
                    <li>&bull; Isi Buku Tamu &amp; Ulasan</li>
                    <li>&bull; Absen Kanvas HTML5</li>
                    <li>&bull; Scan Verifikasi TTE</li>
                </ul>
            </div>
        </div>
        <div class="col-3 fragment fade-up" data-fragment-index="1">
            <div class="role-card" style="border-left-color: #3498db; padding: 8px 10px; min-height: 240px;">
                <h6 class="fw-bold"><i class="mdi mdi-account-edit text-info"></i> 2. Operator</h6>
                <hr style="margin: 5px 0;">
                <ul class="list-unstyled p-0 m-0" style="font-size: 11px; line-height: 1.5;">
                    <li>&bull; Login &amp; 2FA Fortify</li>
                    <li>&bull; CRUD Konten Sendiri</li>
                    <li>&bull; Buat Draf Agenda Rapat</li>
                    <li>&bull; Tulis Draf Notulen</li>
                </ul>
            </div>
        </div>
        <div class="col-3 fragment fade-up" data-fragment-index="2">
            <div class="role-card" style="border-left-color: #2ecc71; padding: 8px 10px; min-height: 240px;">
                <h6 class="fw-bold"><i class="mdi mdi-account-check text-success"></i> 3. Verifikator</h6>
                <hr style="margin: 5px 0;">
                <ul class="list-unstyled p-0 m-0" style="font-size: 11px; line-height: 1.5;">
                    <li>&bull; Login &amp; 2FA Fortify</li>
                    <li>&bull; Approval Konten &amp; Rapat</li>
                    <li>&bull; Eksekusi TTE BSrE</li>
                    <li>&bull; Tanda Tangan Notulen</li>
                </ul>
            </div>
        </div>
        <div class="col-3 fragment fade-up" data-fragment-index="3">
            <div class="role-card" style="border-left-color: #e74c3c; padding: 8px 10px; min-height: 240px;">
                <h6 class="fw-bold"><i class="mdi mdi-account-key text-danger"></i> 4. Admin/Root</h6>
                <hr style="margin: 5px 0;">
                <ul class="list-unstyled p-0 m-0" style="font-size: 11px; line-height: 1.5;">
                    <li>&bull; Manajemen User &amp; RBAC</li>
                    <li>&bull; CRUD Kepegawaian</li>
                    <li>&bull; Override Notulen</li>
                    <li>&bull; Lihat Audit Log (JSON)</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- NEW: DFD Context Level Slide -->
<section>
    <h3>Data Flow Diagram (DFD Level 0 - Context)</h3>
    <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Aliran Data Masuk/Keluar Antara Entitas Luar &amp; Sistem Portal</p>

    <div class="row align-items-center" style="font-size: 12px;">
        <div class="col-4">
            <div class="tech-box mb-2 p-2 text-start" style="border-left: 4px solid #3498db;">
                <h6 class="fw-bold text-primary m-0"><i class="mdi mdi-account-edit"></i> Operator</h6>
                <small class="text-muted">&bull; <strong>Masuk:</strong> Draf Konten, Agenda &amp; Notulen</small><br>
                <small class="text-muted">&bull; <strong>Keluar:</strong> Notifikasi &amp; Status Verifikasi</small>
            </div>
            <div class="tech-box p-2 text-start" style="border-left: 4px solid #9b59b6;">
                <h6 class="fw-bold text-primary m-0" style="color: #9b59b6 !important;"><i class="mdi mdi-account-multiple"></i> Pengunjung</h6>
                <small class="text-muted">&bull; <strong>Masuk:</strong> Buku Tamu, Ulasan, Absen Kanvas</small><br>
                <small class="text-muted">&bull; <strong>Keluar:</strong> Portal, Pegawai, Verifikasi TTE</small>
            </div>
        </div>

        <div class="col-4 text-center">
            <div class="flow-step" style="width: 120px; height: 120px; line-height: 110px; border-radius: 50%; border-width: 4px; margin: 0 auto; background: #eef2f5;">
                <i class="mdi mdi-server-network" style="font-size: 45px;"></i>
            </div>
            <h6 class="fw-bold mt-2" style="font-size:15px;">Sistem Web Terpadu<br><small class="text-muted">(Laravel + API BKN + BSrE)</small></h6>
        </div>

        <div class="col-4">
            <div class="tech-box mb-2 p-2 text-start" style="border-left: 4px solid #2ecc71;">
                <h6 class="fw-bold text-success m-0"><i class="mdi mdi-account-check"></i> Verifikator</h6>
                <small class="text-muted">&bull; <strong>Masuk:</strong> Approval Konten, Passphrase TTE</small><br>
                <small class="text-muted">&bull; <strong>Keluar:</strong> Dokumen Rapat Pending Sign</small>
            </div>
            <div class="tech-box p-2 text-start" style="border-left: 4px solid #e74c3c;">
                <h6 class="fw-bold text-danger m-0"><i class="mdi mdi-account-key"></i> Admin / Root</h6>
                <small class="text-muted">&bull; <strong>Masuk:</strong> Data User, Kepegawaian, Override</small><br>
                <small class="text-muted">&bull; <strong>Keluar:</strong> JSON Audit Log &amp; Statistik</small>
            </div>
        </div>
    </div>
</section>

<!-- NEW: ERD Diagram Slide -->
<section>
    <h3>Arsitektur Database Relasional (ERD)</h3>
    <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Pembagian Struktur Tabel Basis Data Relasional Utama MySQL sesuai PRD</p>
    <div class="row" style="font-size: 13px;">
        <div class="col-6 fragment fade-up">
            <div class="role-card" style="border-left-color: #8e44ad; padding: 10px; min-height: 180px; margin-bottom: 10px;">
                <h6 class="fw-bold m-0"><i class="mdi mdi-account-card-details"></i> 1. Domain Kepegawaian &amp; Akses</h6>
                <hr style="margin: 5px 0;">
                <small><strong>users</strong> (N) &larr; (1) <strong>levels</strong> / <strong>access_groups</strong></small><br>
                <small><strong>pegawais</strong> (1) &harr; (1) <strong>users</strong> (Link Akun)</small><br>
                <small><strong>pegawais</strong> (N) &larr; (1) <strong>pangkats</strong> / <strong>jabatans</strong> / <strong>statuses</strong></small>
            </div>
            <div class="role-card" style="border-left-color: #16a085; padding: 10px; min-height: 180px;">
                <h6 class="fw-bold m-0"><i class="mdi mdi-earth"></i> 2. Domain Konten Portal &amp; File</h6>
                <hr style="margin: 5px 0;">
                <small><strong>beritas / unduhas / galeris</strong> (N) &larr; (1) <strong>kategoris</strong></small><br>
                <small><strong>files</strong> (Relasi Polimorfik) &larr; Berita / Unduhan / Rapat</small><br>
                <small><strong>tamus / testimonis</strong> (N) &larr; (1) <strong>users</strong> (Verifikator)</small>
            </div>
        </div>
        <div class="col-6 fragment fade-up" data-fragment-index="1">
            <div class="role-card" style="border-left-color: #e67e22; padding: 10px; min-height: 180px; margin-bottom: 10px;">
                <h6 class="fw-bold m-0"><i class="mdi mdi-calendar-clock"></i> 3. Domain Rapat &amp; TTE BSrE</h6>
                <hr style="margin: 5px 0;">
                <small><strong>agenda_rapats</strong> (1) &rarr; (N) <strong>peserta_rapats</strong> (Daftar Hadir)</small><br>
                <small><strong>agenda_rapats</strong> (1) &harr; (1) <strong>notulens</strong> (Notulensi Rapat)</small><br>
                <small><strong>agenda_rapats</strong> (1) &rarr; (N) <strong>dokumen_ttes</strong> (Undangan / Notulen)</small>
            </div>
            <div class="role-card" style="border-left-color: #27ae60; padding: 10px; min-height: 180px;">
                <h6 class="fw-bold m-0" style="color: #27ae60 !important;"><i class="mdi mdi-fingerprint"></i> 4. Domain Presensi Simpegnas</h6>
                <hr style="margin: 5px 0;">
                <small><strong>presensi_harians</strong> (N) &larr; (1) <strong>pegawais</strong> (Relasi NIP)</small><br>
                <small><strong>presensi_harians</strong> menyimpan detail jam masuk, jam keluar, status, menit terlambat, menit pulang cepat, &amp; potongan.</small>
            </div>
        </div>
    </div>
</section>

<!-- Slide 7: Tech Stack & Security -->
                                    <section>
                                        <h3>Standar Keamanan & Arsitektur</h3>
                                        <div class="row text-center mt-5 align-items-center">
                                            <div class="col-4 fragment zoom-in">
                                                <i class="mdi mdi-two-factor-authentication fs-60 text-danger"></i>
                                                <h5 class="fw-bold mt-2">Fortify 2FA</h5>
                                                <p style="font-size: 16px;">Otentikasi TOTP Authenticator.</p>
                                            </div>
                                            <div class="col-4 fragment zoom-in" data-fragment-index="1">
                                                <i class="mdi mdi-eye-check fs-60 text-primary"></i>
                                                <h5 class="fw-bold mt-2">Loggable Audit</h5>
                                                <p style="font-size: 16px;">Pelacakan otomatis event CRUD model.</p>
                                            </div>
                                            <div class="col-4 fragment zoom-in" data-fragment-index="2">
                                                <i class="mdi mdi-robot-dead fs-60 text-warning"></i>
                                                <h5 class="fw-bold mt-2">Mews Captcha</h5>
                                                <p style="font-size: 16px;">Penangkal intervensi bot/spam.</p>
                                            </div>
                                        </div>
                                        <div class="row text-center mt-3">
                                            <div class="col-6 fragment zoom-in" data-fragment-index="3">
                                                <div class="tech-box border-top border-info border-3 p-3">
                                                    <h6 class="fw-bold m-0"><i class="mdi mdi-file-find"></i> File Observers</h6>
                                                    <small>Pencegahan orphan files pada storage disk.</small>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment zoom-in" data-fragment-index="4">
                                                <div class="tech-box border-top border-dark border-3 p-3">
                                                    <h6 class="fw-bold m-0"><i class="mdi mdi-code-tags"></i> MVC Murni</h6>
                                                    <small>Pemisahan utuh logika bisnis dari Controller & Blade.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- NEW: Status & Roadmap Slide -->
                                    <section>
                                        <h3>Status &amp; Rencana Implementasi</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 15px;">Evaluasi Kesiapan Sistem dan Rencana Rilis Fitur Mendatang</p>

                                        <div class="row" style="font-size: 13px;">
                                            <div class="col-6 fragment fade-right">
                                                <div class="role-card" style="border-left-color: #2ecc71; padding: 10px; min-height: 290px;">
                                                    <h5 class="fw-bold text-success" style="color: #2ecc71 !important;"><i class="mdi mdi-clipboard-check"></i> Status Implementasi Sekarang</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0 text-start" style="font-size: 12px; line-height: 1.8; padding-left: 5px;">
                                                        <li><i class="mdi mdi-checkbox-marked-circle text-success"></i> <strong>Portal &amp; Kepegawaian (100%):</strong> CRUD Dinamis, Pegawai, Jabatan, &amp; Pangkat terintegrasi.</li>
                                                        <li><i class="mdi mdi-checkbox-marked-circle text-success"></i> <strong>Sistem Agenda Rapat (100%):</strong> Deteksi bentrok jadwal, absensi kanvas HTML5, &amp; draft notulensi.</li>
                                                        <li><i class="mdi mdi-checkbox-marked-circle text-success"></i> <strong>Integrasi Presensi BKN (100%):</strong> Pull API, mapping NIP, auto-potongan TM/PC/TK, &amp; view foto riwayat.</li>
                                                        <li><i class="mdi mdi-checkbox-marked-circle text-success"></i> <strong>Keamanan &amp; Audit (100%):</strong> Fortify 2FA, Loggable Trait, Mews Captcha, &amp; File Observer.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-left" data-fragment-index="1">
                                                <div class="role-card" style="border-left-color: #0052D4; padding: 10px; min-height: 290px;">
                                                    <h5 class="fw-bold text-primary" style="color: #0052D4 !important;"><i class="mdi mdi-trending-up"></i> Rencana Kerja &amp; Roadmap Rilis</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0 text-start" style="font-size: 12px; line-height: 1.8; padding-left: 5px;">
                                                        <li><span class="badge bg-success" style="padding: 3px 6px; font-size: 9px;">FASE 1 - Juli 2026</span><br>
                                                            <small class="text-muted">Uji coba simulasi TTE sekuensial (Notulis &rarr; Pimpinan) dengan API sandbox BSrE.</small>
                                                        </li>
                                                        <li class="mt-2"><span class="badge bg-primary" style="padding: 3px 6px; font-size: 9px;">FASE 2 - Agustus 2026</span><br>
                                                            <small class="text-muted">Implementasi sistem cron job (automated daily sync) untuk penarikan berkas &amp; presensi BKN.</small>
                                                        </li>
                                                        <li class="mt-2"><span class="badge bg-warning text-dark" style="padding: 3px 6px; font-size: 9px;">FASE 3 - September 2026</span><br>
                                                            <small class="text-muted">Integrasi API Gateway WhatsApp/Telegram untuk otomasi notifikasi undangan &amp; pengingat presensi.</small>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </section>


                                    <!-- Slide 8: Closing -->
                                    <section data-transition="zoom">
                                        <h1 style="color: #27ae60; font-weight: 600; font-size: 4em;"><i class="mdi mdi-check-circle"></i></h1>
                                        <h2 class="slide-title-main" style="margin-top: 10px;">Terima Kasih</h2>
                                        <p class="slide-desc-main" style="font-size: 22px; max-width: 70%; margin: 20px auto;">Dokumen spesifikasi (PRD) dan rencana implementasi lengkap dapat Anda eksplorasi secara detail melalui menu sub-dokumentasi di sidebar kiri.</p>
                                    </section>
                                </div>
                                
                                <!-- Custom Navigation Bar -->
                                <div class="custom-nav-bar">
                                    <button class="custom-nav-btn" onclick="Reveal.prev()"><i class="mdi mdi-chevron-left"></i></button>
                                    <div class="custom-nav-dots"></div>
                                    <button class="custom-nav-btn" onclick="Reveal.next()"><i class="mdi mdi-chevron-right"></i></button>
                                </div>
                            </div>
                            <!-- End Reveal.js Container -->
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@push('css')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Reveal.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/reveal.js/4.3.1/reveal.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/reveal.js/4.3.1/theme/white.min.css" id="theme">
    <style>
        .reveal {
            background-color: transparent !important;
            color: #2c3e50 !important;
            font-family: 'Poppins', sans-serif !important;
        }
        .reveal h1, .reveal h2, .reveal h3, .reveal h4, .reveal h5, .reveal h6 {
            text-transform: none;
            font-family: 'Poppins', sans-serif !important;
            font-weight: 600;
            color: #2c3e50 !important;
        }
        .reveal p, .reveal small, .reveal span, .reveal li {
            color: inherit;
            font-family: 'Poppins', sans-serif !important;
        }
        .slide-title-main { color: #2c3e50 !important; }
        .slide-subtitle-main { color: #34495e !important; }
        .slide-desc-main { color: #7f8c8d !important; }
        .slide-subtitle { color: #7f8c8d !important; }
        .slide-subtitle-dark { color: #34495e !important; }
        
        .tech-box {
            background: rgba(255, 255, 255, 0.8) !important;
            backdrop-filter: blur(5px);
            color: #2c3e50 !important;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s;
        }
        .tech-box h4, .tech-box h5, .tech-box h6 { color: #2c3e50 !important; }
        .tech-box p, .tech-box small { color: #7f8c8d !important; }
        .tech-box:hover {
            transform: translateY(-10px);
        }
        .flow-step {
            display: inline-block;
            background: #eef2f5;
            border: 2px solid #0052D4;
            border-radius: 50%;
            width: 80px;
            height: 80px;
            line-height: 75px;
            font-size: 30px;
            color: #0052D4;
            margin: 0 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .flow-arrow {
            display: inline-block;
            font-size: 30px;
            color: #bdc3c7;
            vertical-align: middle;
        }
        .role-card {
            border-left: 5px solid #0052D4;
            background: rgba(248, 249, 250, 0.85) !important;
            backdrop-filter: blur(5px);
            color: #2c3e50 !important;
            padding: 12px 15px;
            margin-bottom: 12px;
            border-radius: 0 8px 8px 0;
            text-align: left;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .role-card h4 {
            margin-top: 0;
            margin-bottom: 5px;
            color: #2c3e50 !important;
            font-size: 20px;
        }
        .role-card p {
            font-size: 15px;
            color: #7f8c8d !important;
            margin-bottom: 0;
        }
        .list-styled {
            list-style: none;
            padding-left: 0;
        }
        .list-styled li {
            position: relative;
            padding-left: 30px;
            margin-bottom: 10px;
            font-size: 20px;
            text-align: left;
            color: #34495e !important;
        }
        .list-styled li:before {
            content: '✔';
            position: absolute;
            left: 0;
            color: #27ae60;
            font-weight: bold;
        }
        /* Custom elements inside slides without background */
        .reveal h5.fw-bold { color: #2c3e50 !important; }
        .reveal p[style*="16px"] { color: #7f8c8d !important; }

        /* Dark Theme Overrides */
        body.dark-skin .reveal {
            color: #ecf0f1 !important;
        }
        body.dark-skin .reveal h1, 
        body.dark-skin .reveal h2, 
        body.dark-skin .reveal h3, 
        body.dark-skin .reveal h4, 
        body.dark-skin .reveal h5, 
        body.dark-skin .reveal h6,
        body.dark-skin .reveal h5.fw-bold,
        body.dark-skin .slide-title-main,
        body.dark-skin .text-primary {
            color: #ffffff !important;
        }
        body.dark-skin .reveal p, 
        body.dark-skin .reveal p[style*="16px"],
        body.dark-skin .reveal small, 
        body.dark-skin .reveal span,
        body.dark-skin .slide-subtitle-main,
        body.dark-skin .slide-desc-main,
        body.dark-skin .slide-subtitle,
        body.dark-skin .slide-subtitle-dark {
            color: #bdc3c7 !important;
        }
        body.dark-skin .tech-box {
            background: rgba(30, 30, 46, 0.8) !important;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5) !important;
        }
        body.dark-skin .tech-box h4, 
        body.dark-skin .tech-box h5, 
        body.dark-skin .tech-box h6 {
            color: #ffffff !important;
        }
        body.dark-skin .tech-box p, 
        body.dark-skin .tech-box small,
        body.dark-skin .tech-box strong {
            color: #bdc3c7 !important;
        }
        body.dark-skin .tech-box strong {
            color: #ffffff !important;
        }
        body.dark-skin .flow-step {
            background: #2b2b40 !important;
            box-shadow: 0 4px 6px rgba(0,0,0,0.5) !important;
        }
        body.dark-skin .flow-arrow {
            color: #7f8c8d !important;
        }
        body.dark-skin .role-card {
            background: rgba(40, 40, 56, 0.85) !important;
            box-shadow: 0 2px 5px rgba(0,0,0,0.5) !important;
        }
        body.dark-skin .role-card h4, 
        body.dark-skin .role-card h5 {
            color: #ffffff !important;
        }
        body.dark-skin .role-card p,
        body.dark-skin .role-card small {
            color: #bdc3c7 !important;
        }
        body.dark-skin .list-styled li {
            color: #ecf0f1 !important;
        }

        /* Custom Navigation Bar Styles */
        .custom-nav-bar {
            position: absolute;
            bottom: 25px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            align-items: center;
            gap: 12px;
            z-index: 100;
            background: rgba(0, 0, 0, 0.05);
            backdrop-filter: blur(20px);
            padding: 8px 16px;
            border-radius: 50px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        .custom-nav-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background: rgba(0, 0, 0, 0.03);
            color: #2c3e50;
            font-size: 1.1rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .custom-nav-btn:hover {
            background: rgba(0, 82, 212, 0.1);
            border-color: rgba(0, 82, 212, 0.3);
            color: #0052D4;
        }
        .custom-nav-dots {
            display: flex;
            gap: 6px;
            align-items: center;
        }
        .custom-nav-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .custom-nav-dot.active {
            background: #0052D4;
            width: 24px;
            border-radius: 4px;
        }

        /* Dark skin overrides for Navigation Bar */
        body.dark-skin .custom-nav-bar {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.15);
        }
        body.dark-skin .custom-nav-btn {
            border-color: rgba(255, 255, 255, 0.15);
            background: rgba(255, 255, 255, 0.05);
            color: #ecf0f1;
        }
        body.dark-skin .custom-nav-btn:hover {
            background: rgba(52, 152, 219, 0.2);
            border-color: rgba(52, 152, 219, 0.4);
            color: #3498db;
        }
        body.dark-skin .custom-nav-dot {
            background: rgba(255, 255, 255, 0.2);
        }
        body.dark-skin .custom-nav-dot.active {
            background: #3498db;
        }
    </style>
@endpush

@push('js')
    <!-- Reveal.js JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/reveal.js/4.3.1/reveal.min.js"></script>
    <script src="{{ url('portal/js/particles.min.js') }}"></script>
    <script>
        // Initialize Reveal.js
        Reveal.initialize({
            hash: true,
            controls: false, // Disable default controls
            progress: false, // Disable default progress bar
            slideNumber: false, // Disable default slide numbers
            center: true,
            width: 1100,
            height: 700,
            margin: 0.1,
            transition: 'slide', // none/fade/slide/convex/concave/zoom
            backgroundTransition: 'fade',
            autoAnimate: true,
            plugins: []
        });

        // Generate custom navigation dots
        const totalSlides = Reveal.getTotalSlides();
        const dotsContainer = document.querySelector('.custom-nav-dots');
        
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('span');
            dot.classList.add('custom-nav-dot');
            if (i === 0) dot.classList.add('active');
            dot.addEventListener('click', () => {
                Reveal.slide(i);
            });
            dotsContainer.appendChild(dot);
        }

        // Listen for slide change to update active dot
        Reveal.on('slidechanged', event => {
            const dots = document.querySelectorAll('.custom-nav-dot');
            dots.forEach((dot, idx) => {
                if (idx === event.indexh) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        });

        // Initialize Particles.js
        particlesJS('particles-js', {
          "particles": {
            "number": { "value": 60, "density": { "enable": true, "value_area": 800 } },
            "color": { "value": ["#0284c7", "#059669", "#0ea5e9", "#0d9488", "#6366f1"] },
            "shape": { "type": ["circle", "triangle"], "stroke": { "width": 0, "color": "#000000" } },
            "opacity": { "value": 0.4, "random": true, "anim": { "enable": true, "speed": 1, "opacity_min": 0.1, "sync": false } },
            "size": { "value": 4, "random": true, "anim": { "enable": true, "speed": 2, "size_min": 0.5, "sync": false } },
            "line_linked": { "enable": true, "distance": 150, "color": "#0284c7", "opacity": 0.2, "width": 1 },
            "move": { "enable": true, "speed": 1.5, "direction": "none", "random": true, "straight": false, "out_mode": "out", "bounce": false }
          },
          "interactivity": {
            "detect_on": "canvas",
            "events": {
              "onhover": { "enable": true, "mode": "bubble" },
              "onclick": { "enable": true, "mode": "push" },
              "resize": true
            },
            "modes": {
              "bubble": { "distance": 200, "size": 6, "duration": 2, "opacity": 0.6, "speed": 3 },
              "push": { "particles_nb": 4 }
            }
          },
          "retina_detect": true
        });
    </script>
@endpush
