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
                                        <img src="{{ url($template.'/images/logodiskominfotik.png') }}" alt="Logo Utama" style="width: 230px; margin-bottom: 20px; filter: drop-shadow(0px 4px 10px rgba(0,0,0,0.3));">
                                        <h2 class="slide-title-main" style="font-weight: 700; font-size: 2.2em; text-shadow: 0px 4px 10px rgba(0,0,0,0.1); letter-spacing: -1px; margin-bottom: 5px;">Buku Petunjuk Penggunaan</h2>
                                        <h3 class="slide-subtitle-main" style="font-weight: 400; margin-top: 5px; font-size: 1.4em; color: #0052D4;">(Manual Book Aplikasi Web Terpadu)</h3>
                                        <hr style="width: 25%; border-color: #0052D4; margin: 25px auto;">
                                        <p class="slide-desc-main" style="font-size: 18px; color: #7f8c8d;">Dinas Komunikasi, Informatika, dan Statistik Kabupaten Indragiri Hulu</p>
                                    </section>

                                    <!-- Slide 2: Daftar Isi -->
                                    <section data-transition="slide">
                                        <h3>Daftar Isi Manual Book</h3>
                                        <p class="slide-subtitle" style="font-size: 20px; margin-bottom: 25px;">Panduan Navigasi Buku Petunjuk Penggunaan Sistem</p>
                                        <div class="row" style="font-size: 15px;">
                                            <div class="col-4 fragment fade-up" data-fragment-index="1">
                                                <div class="tech-box border-top border-primary border-5" style="min-height: 200px;">
                                                    <h5 class="fw-bold text-primary"><i class="mdi mdi-information-outline fs-30"></i></h5>
                                                    <p class="fw-bold mb-1" style="font-size: 16px;">Bab 1: Pendahuluan</p>
                                                    <small class="text-muted">Dasar Hukum, Ruang Lingkup, Tujuan, Manfaat &amp; Urgensi Sistem.</small>
                                                </div>
                                            </div>
                                            <div class="col-4 fragment fade-up" data-fragment-index="2">
                                                <div class="tech-box border-top border-success border-5" style="min-height: 200px;">
                                                    <h5 class="fw-bold text-success"><i class="mdi mdi-account-key fs-30"></i></h5>
                                                    <p class="fw-bold mb-1" style="font-size: 16px;">Bab 2: Alur &amp; RBAC</p>
                                                    <small class="text-muted">Manajemen Akses &amp; Peran 4 Level Pengguna (Root/Admin/Op/Verifikator).</small>
                                                </div>
                                            </div>
                                            <div class="col-4 fragment fade-up" data-fragment-index="3">
                                                <div class="tech-box border-top border-info border-5" style="min-height: 200px;">
                                                    <h5 class="fw-bold text-info"><i class="mdi mdi-desktop-mac fs-30"></i></h5>
                                                    <p class="fw-bold mb-1" style="font-size: 16px;">Bab 3: Frontend View</p>
                                                    <small class="text-muted">Penjelasan Buku Tamu, Ulasan, Berita, Galeri &amp; Modul Unduhan.</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row mt-3 justify-content-center" style="font-size: 15px;">
                                            <div class="col-4 fragment fade-up" data-fragment-index="4">
                                                <div class="tech-box border-top border-warning border-5" style="min-height: 200px;">
                                                    <h5 class="fw-bold text-warning"><i class="mdi mdi-cogs fs-30"></i></h5>
                                                    <p class="fw-bold mb-1" style="font-size: 16px;">Bab 4: Backend Admin</p>
                                                    <small class="text-muted">Panduan Login, Konten Statis/Dinamis, Rapat, &amp; Presensi Pegawai.</small>
                                                </div>
                                            </div>
                                            <div class="col-4 fragment fade-up" data-fragment-index="5">
                                                <div class="tech-box border-top border-danger border-5" style="min-height: 200px;">
                                                    <h5 class="fw-bold text-danger"><i class="mdi mdi-flag-checkered fs-30"></i></h5>
                                                    <p class="fw-bold mb-1" style="font-size: 16px;">Bab 5: Penutup</p>
                                                    <small class="text-muted">Kesimpulan, Dukungan Teknis, &amp; Kontak Bantuan Diskominfotik Inhu.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 3: Bab 1 - Penjelasan Aplikasi (Dasar Hukum & Ruang Lingkup) -->
                                    <section>
                                        <h3>Bab 1: Penjelasan Aplikasi (1/2)</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Dasar Hukum &amp; Ruang Lingkup Sistem Portal &amp; Presensi</p>
                                        <div class="row" style="font-size: 14px; text-align: left;">
                                            <div class="col-6 fragment fade-right">
                                                <div class="role-card" style="border-left-color: #2980b9; min-height: 320px;">
                                                    <h5 class="fw-bold text-primary"><i class="mdi mdi-scale-balance"></i> Dasar Hukum</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled" style="line-height: 1.6; font-size: 13px;">
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success"></i> <strong>UU No. 14 Tahun 2008</strong> tentang Keterbukaan Informasi Publik (KIP).</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success"></i> <strong>Perpres No. 95 Tahun 2018</strong> tentang Sistem Pemerintahan Berbasis Elektronik (SPBE).</li>
                                                        <li class="mb-2"><i class="mdi mdi-check-circle text-success"></i> <strong>Instruksi Presiden No. 3 Tahun 2003</strong> tentang Kebijakan &amp; Strategi Nasional SPBE.</li>
                                                        <li><i class="mdi mdi-check-circle text-success"></i> <strong>Peraturan Bupati Indragiri Hulu</strong> terkait sistem presensi &amp; data kepegawaian.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-left">
                                                <div class="role-card" style="border-left-color: #27ae60; min-height: 320px;">
                                                    <h5 class="fw-bold text-success"><i class="mdi mdi-google-pages"></i> Ruang Lingkup Aplikasi</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <p class="text-muted" style="font-size: 13px;">Aplikasi terpadu ini mencakup pengelolaan dua modul utama daerah:</p>
                                                    <ol style="margin-left: 15px; line-height: 1.7; padding-left: 0; font-size: 13px;" class="text-muted">
                                                        <li class="mb-2"><strong>Portal Informasi Publik:</strong> Penyebaran berita resmi dinas, modul galeri multimedia, unduhan dokumen publik, serta interaksi umpan balik masyarakat.</li>
                                                        <li><strong>Kepegawaian &amp; Presensi:</strong> Integrasi langsung data pegawai dengan rekap presensi Simpegnas BKN, deteksi geolokasi, serta pengelolaan rapat dinas ber-TTE BSrE.</li>
                                                    </ol>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 4: Bab 1 - Penjelasan Aplikasi (Tujuan, Manfaat, & Urgensi) -->
                                    <section>
                                        <h3>Bab 1: Penjelasan Aplikasi (2/2)</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Tujuan, Manfaat, &amp; Mengapa Aplikasi Ini Diperlukan</p>
                                        <div class="row text-center" style="font-size: 14px;">
                                            <div class="col-4 fragment zoom-in" data-fragment-index="1">
                                                <div class="tech-box p-3" style="min-height: 310px; border-top: 4px solid #f39c12;">
                                                    <i class="mdi mdi-target text-warning fs-40 mb-2"></i>
                                                    <h6 class="fw-bold">Tujuan Utama</h6>
                                                    <hr style="margin: 5px 0;">
                                                    <p class="text-muted text-start" style="font-size: 12px; line-height: 1.6;">
                                                        Mewujudkan keterbukaan informasi daerah secara akurat dan transparan bagi publik, sekaligus menyediakan tools otomasi administrasi kehadiran internal dan agenda rapat dinas yang terdokumentasi.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-4 fragment zoom-in" data-fragment-index="2">
                                                <div class="tech-box p-3" style="min-height: 310px; border-top: 4px solid #2ecc71;">
                                                    <i class="mdi mdi-gift text-success fs-40 mb-2"></i>
                                                    <h6 class="fw-bold">Manfaat Nyata</h6>
                                                    <hr style="margin: 5px 0;">
                                                    <p class="text-muted text-start" style="font-size: 12px; line-height: 1.6;">
                                                        Menghilangkan duplikasi kerja, mempercepat sinkronisasi log data presensi, menghindari bentrok jadwal pemakaian ruang rapat dinas, serta menjamin legalitas notulen menggunakan TTE resmi BSrE.
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="col-4 fragment zoom-in" data-fragment-index="3">
                                                <div class="tech-box p-3" style="min-height: 310px; border-top: 4px solid #e74c3c;">
                                                    <i class="mdi mdi-help-circle-outline text-danger fs-40 mb-2"></i>
                                                    <h6 class="fw-bold">Kenapa Diperlukan?</h6>
                                                    <hr style="margin: 5px 0;">
                                                    <p class="text-muted text-start" style="font-size: 12px; line-height: 1.6;">
                                                        Pengelolaan manual rentan terhadap manipulasi log presensi dan redudansi data. Sistem ini menghadirkan pelacakan audit trail yang ketat (Loggable) dan otomatisasi cleanup file server (File Observer).
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 5: Bab 2 - Alur Penggunaan Aplikasi (RBAC) -->
                                    <section>
                                        <h3>Bab 2: Alur Penggunaan &amp; Peran (RBAC)</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Role-Based Access Control (Manajemen Peran &amp; Hak Akses Pengguna)</p>
                                        <div class="row" style="font-size: 13px; text-align: left;">
                                            <div class="col-6 fragment fade-right">
                                                <div class="role-card" style="border-left-color: #c0392b; padding: 10px; margin-bottom: 10px;">
                                                    <h6 class="fw-bold m-0 text-danger"><i class="mdi mdi-shield-crown"></i> Level 1: Root (Super Administrator)</h6>
                                                    <small class="text-muted">Memiliki akses penuh dan mutlak ke seluruh konfigurasi sistem, database override, manajemen menu, reset sistem, audit log JSON, serta pengaturan kustom level pengguna.</small>
                                                </div>
                                                <div class="role-card" style="border-left-color: #d35400; padding: 10px;">
                                                    <h6 class="fw-bold m-0 text-warning" style="color: #d35400 !important;"><i class="mdi mdi-account-cog"></i> Level 2: Administrator (Kepegawaian &amp; Portal)</h6>
                                                    <small class="text-muted">Mengelola data master pegawai, pangkat, jabatan, status. Menginisiasi sinkronisasi log presensi BKN harian, mengelola konten statis (page, slider, tautan), &amp; pengaturan sistem.</small>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-left">
                                                <div class="role-card" style="border-left-color: #2980b9; padding: 10px; margin-bottom: 10px;">
                                                    <h6 class="fw-bold m-0 text-primary"><i class="mdi mdi-pencil-box-outline"></i> Level 3: Operator (Draf &amp; Notulis)</h6>
                                                    <small class="text-muted">Membuat draf berita dinamis, mengupload berkas galeri &amp; unduhan, menginput draf agenda rapat baru, serta menulis draf notulensi hasil rapat di sistem.</small>
                                                </div>
                                                <div class="role-card" style="border-left-color: #27ae60; padding: 10px;">
                                                    <h6 class="fw-bold m-0 text-success"><i class="mdi mdi-checkbox-marked-circle-outline"></i> Level 4: Verifikator (Atasan / Reviewer)</h6>
                                                    <small class="text-muted">Melakukan tinjauan (review) dan persetujuan (approval/revisi) atas draf konten portal, persetujuan agenda rapat, serta mengeksekusi TTE BSrE (passphrase) untuk surat/notulen.</small>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 6: Bab 3 - Tampilan Aplikasi (Frontend Overview) -->
                                    <section>
                                        <h3>Bab 3: Tampilan Aplikasi Bagian Frontend</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Penjelasan Menu dan Section yang Dapat Diakses oleh Publik / Pengunjung</p>
                                        <div class="row" style="font-size: 13px; text-align: left;">
                                            <div class="col-6 fragment fade-up">
                                                <div class="role-card" style="border-left-color: #9b59b6; min-height: 310px;">
                                                    <h5 class="fw-bold text-primary" style="color: #9b59b6 !important;"><i class="mdi mdi-account-multiple-outline"></i> Modul Interaksi &amp; Ulasan</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="line-height: 1.7; font-size: 12px;">
                                                        <li class="mb-2"><strong>1. Buku Tamu Digital:</strong> Formulir bagi pengunjung eksternal untuk mendaftarkan nama, instansi, keperluan, &amp; tanda tangan digital via canvas langsung.</li>
                                                        <li class="mb-2"><strong>2. Testimoni &amp; Ulasan:</strong> Fitur penulisan feedback/ulasan kualitas layanan Diskominfotik sebagai sarana evaluasi transparan.</li>
                                                        <li><strong>3. Proteksi Spam Captcha:</strong> Fitur keamanan input form dilindungi oleh visual captcha (Mews Captcha) untuk menangkal spam bot otomatis.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-up" data-fragment-index="1">
                                                <div class="role-card" style="border-left-color: #34495e; min-height: 310px;">
                                                    <h5 class="fw-bold text-primary" style="color: #34495e !important;"><i class="mdi mdi-earth"></i> Modul Konten Dinamis</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="line-height: 1.7; font-size: 12px;">
                                                        <li class="mb-2"><strong>1. Berita Terkini:</strong> Halaman artikel resmi daerah yang terorganisir per kategori, memuat info teks, gambar, &amp; log counter pembaca.</li>
                                                        <li class="mb-2"><strong>2. Galeri Foto &amp; Video:</strong> Dokumentasi visual kegiatan dinas (foto/video responsif) yang dikelompokkan berdasarkan tema kegiatan dinas.</li>
                                                        <li><strong>3. Menu Unduhan:</strong> Wadah unduhan berkas regulasi daerah, form administrasi, sertifikat, atau materi rapat publik secara bebas.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 7: Bab 4 - Penggunaan Backend (Login & Konten Statis) -->
                                    <section>
                                        <h3>Bab 4: Penggunaan Aplikasi Backend (1/3)</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Panduan Login &amp; Pembuatan Konten Statis Aplikasi</p>
                                        <div class="row align-items-center" style="font-size: 14px;">
                                            <div class="col-6 text-start fragment fade-right">
                                                <h5 class="fw-bold text-primary"><i class="mdi mdi-login-variant"></i> Prosedur Autentikasi Login</h5>
                                                <ol class="text-muted" style="line-height: 1.7; padding-left: 20px; font-size: 13px;">
                                                    <li class="mb-2">Akses url backend dashboard sesuai konfigurasi.</li>
                                                    <li class="mb-2">Input email/username dan password terdaftar.</li>
                                                    <li class="mb-2">Input kode Captcha visual pengaman dengan benar.</li>
                                                    <li>Aktifkan <strong>Two-Factor Authentication (2FA)</strong> yang dikonfigurasi via aplikasi authenticator untuk akun admin berprivilese tinggi.</li>
                                                </ol>
                                            </div>
                                            <div class="col-6 fragment fade-left">
                                                <div class="role-card" style="border-left-color: #f1c40f; min-height: 250px;">
                                                    <h5 class="fw-bold text-warning" style="color: #f1c40f !important;"><i class="mdi mdi-file-document-outline"></i> Modul Konten Statis</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="font-size: 12px; line-height: 1.7;">
                                                        <li class="mb-2"><strong>1. Halaman (Page):</strong> Pembuatan halaman statis (seperti Sejarah Dinas, Visi Misi, Tugas Pokok) menggunakan rich text editor CKEditor.</li>
                                                        <li class="mb-2"><strong>2. Slider Banner:</strong> Pengelolaan banner gambar geser pada beranda utama frontend lengkap dengan caption judul, deskripsi &amp; link tautan tujuan.</li>
                                                        <li><strong>3. Tautan Cepat:</strong> Pengelolaan link cepat atau menu shortcut eksternal ke aplikasi pelayanan pemerintahan daerah lainnya.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 8: Bab 4 - Penggunaan Backend (Konten Dinamis & Verifikasi) -->
                                    <section>
                                        <h3>Bab 4: Penggunaan Aplikasi Backend (2/3)</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Manajemen Konten Dinamis &amp; Alur Verifikasi Publikasi</p>
                                        <div class="row" style="font-size: 13px; text-align: left;">
                                            <div class="col-6 fragment fade-up">
                                                <div class="tech-box border-top border-info border-5" style="min-height: 290px;">
                                                    <h6 class="fw-bold text-info"><i class="mdi mdi-pencil-circle-outline"></i> Input Konten Dinamis</h6>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="line-height: 1.6; font-size: 12px;">
                                                        <li class="mb-2"><strong>Berita:</strong> Tulis judul, isi berita (CKEditor), pilih kategori berita, upload thumbnail gambar utama, set status draft.</li>
                                                        <li class="mb-2"><strong>Galeri:</strong> Unggah foto kegiatan dengan deskripsi, tanggal pelaksanaan, &amp; nama album galeri.</li>
                                                        <li><strong>Unduhan:</strong> Upload berkas pdf/docx, berikan judul dokumen, deskripsi singkat, kategori berkas, &amp; counters unduhan otomatis.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-up" data-fragment-index="1">
                                                <div class="tech-box border-top border-success border-5" style="min-height: 290px;">
                                                    <h6 class="fw-bold text-success"><i class="mdi mdi-checkbox-marked-circle"></i> Alur Menu Verifikasi</h6>
                                                    <hr style="margin: 5px 0;">
                                                    <p class="text-muted m-0" style="font-size: 12px;">Menjaga akurasi dan kesesuaian informasi portal daerah:</p>
                                                    <ul class="list-unstyled mt-1 mb-0" style="line-height: 1.6; font-size: 12px;">
                                                        <li class="mb-2"><strong>Status Draf:</strong> Konten baru buatan Operator berstatus draf dan tidak tampil ke publik.</li>
                                                        <li class="mb-2"><strong>Menu Peninjauan:</strong> Verifikator meninjau isi konten di halaman detail verifikasi backend.</li>
                                                        <li><strong>Aksi Verifikasi:</strong> Setujui (terbit otomatis di web) ATAU Tolak (dikembalikan ke operator disertai alasan revisi).</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 9: Bab 4 - Penggunaan Backend (Rapat & Rekap Presensi) -->
                                    <section>
                                        <h3>Bab 4: Penggunaan Aplikasi Backend (3/3)</h3>
                                        <p class="slide-subtitle" style="font-size: 18px; margin-bottom: 20px;">Manajemen Rapat Dinas, Presensi Pegawai BKN, &amp; Rekap Absensi</p>
                                        <div class="row text-start" style="font-size: 13px;">
                                            <div class="col-6 fragment fade-right">
                                                <div class="role-card" style="border-left-color: #3498db; min-height: 310px;">
                                                    <h5 class="fw-bold text-primary"><i class="mdi mdi-calendar-clock"></i> Modul Rapat &amp; TTE BSrE</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="line-height: 1.6; font-size: 12px;">
                                                        <li class="mb-1"><strong>Jadwal Rapat:</strong> Buat agenda baru, sistem otomatis memvalidasi jadwal ruangan agar tidak bentrok.</li>
                                                        <li class="mb-1"><strong>TTE Undangan &amp; Notulen:</strong> Berkas dinas ditandatangani elektronik via API sandbox BSrE berjenjang.</li>
                                                        <li class="mb-1"><strong>Presensi QR Token:</strong> Peserta melakukan absen kanvas HTML5 via scan QR code.</li>
                                                        <li><strong>Verifikasi QR:</strong> Scan QR notulen digital mengarah ke URL verifikasi validasi berkas.</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="col-6 fragment fade-left">
                                                <div class="role-card" style="border-left-color: #2ecc71; min-height: 310px;">
                                                    <h5 class="fw-bold text-success"><i class="mdi mdi-account-circle"></i> Profil Pegawai &amp; Rekap Presensi</h5>
                                                    <hr style="margin: 5px 0;">
                                                    <ul class="list-unstyled p-0 m-0" style="line-height: 1.6; font-size: 12px;">
                                                        <li class="mb-1"><strong>Biodata Pegawai:</strong> Halaman detail pegawai (NIP, Jabatan, Pangkat, Status Kerja).</li>
                                                        <li class="mb-1"><strong>Sinkronisasi Simpegnas BKN:</strong> Tarik log kehadiran harian pegawai dari API BKN secara periodik.</li>
                                                        <li class="mb-1"><strong>Foto Geotagging:</strong> Menampilkan foto selfie check-in/check-out beserta log lokasi koordinat gps.</li>
                                                        <li><strong>Auto-Kalkulasi Potongan:</strong> Perhitungan pemotongan tunjangan harian atas keterlambatan (TM) / pulang cepat (PC) / alpa (TK).</li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <!-- Slide 10: Bab 5 - Penutup -->
                                    <section data-transition="zoom">
                                        <h1 style="color: #27ae60; font-weight: 700; font-size: 3.5em; margin-bottom: 10px;"><i class="mdi mdi-check-circle-outline"></i></h1>
                                        <h2 class="slide-title-main" style="font-size: 2.2em;">Bab 5: Penutup</h2>
                                        <p class="slide-desc-main" style="font-size: 18px; max-width: 80%; margin: 15px auto; color: #7f8c8d; line-height: 1.6;">
                                            Demikian buku petunjuk penggunaan aplikasi web terpadu Portal Website dan Presensi Kepegawaian Diskominfotik Indragiri Hulu ini disusun. Semoga mempermudah operasional harian dinas.
                                        </p>
                                        <div class="tech-box mt-3 p-2 d-inline-block" style="border-top: 3px solid #27ae60;">
                                            <span class="fw-bold text-dark"><i class="mdi mdi-headphones"></i> Dukungan Teknis:</span> 
                                            <small class="text-muted">Seksi Pengembangan Aplikasi, Bidang E-Government, Diskominfotik Inhu.</small>
                                        </div>
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
