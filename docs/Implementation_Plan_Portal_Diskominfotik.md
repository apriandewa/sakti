# Implementation Plan
## Proyek Portal Website & Sistem Informasi Agenda Rapat Diskominfotik

> [!NOTE]
> Dokumen ini memetakan rencana tahapan kerja, arsitektur sistem, struktur database, model-model, layanan (services), dan alur antarmuka pengguna (UI/UX) untuk modul Kepegawaian dan Agenda Rapat Elektronik dengan integrasi TTE BSrE.

---

## 1. Persiapan Environment & Dependensi
1. **Instalasi Paket Ekspor PDF**:
   Gunakan paket DomPDF (`barryvdh/laravel-dompdf`) yang dikonfigurasi agar mendukung pemrosesan gambar eksternal (remote assets) demi rendering logo Kop Surat Dinas:
   ```bash
   composer require barryvdh/laravel-dompdf
   ```
2. **Konfigurasi API BSrE (TTE)**:
   Tambahkan variabel lingkungan (environment variables) di `.env` untuk keperluan integrasi dengan portal sandi BSSN:
   ```env
   BSRE_API_URL=https://esign-api.bssn.go.id/api
   BSRE_USERNAME=username_sandi_dinas
   BSRE_PASSWORD=password_sandi_dinas
   ```
   Integrasikan variabel di atas ke file `config/services.php`:
   ```php
    'bsre' => [
        'url'      => env('BSRE_API_URL'),
        'username' => env('BSRE_USERNAME'),
        'password' => env('BSRE_PASSWORD'),
    ],
];
```

3. **Konfigurasi KOMPASS SSO**:
   Tambahkan variabel lingkungan (environment variables) di `.env` untuk keperluan integrasi client SSO:
   ```env
   KOMPASS_SSO_URL=http://localhost:8585
   KOMPASS_CLIENT_ID=client_id_dari_server
   KOMPASS_CLIENT_SECRET=client_secret_dari_server
   KOMPASS_REDIRECT_URI=http://localhost:8181/auth/sso/callback
   ```

---

## 2. Struktur Migrasi Database

### 2.1 Modul Kepegawaian (Replaced Old Structure)
1. **Tabel `pangkats`**:
   * `id` (UUID, Primary Key)
   * `nama` (String) - Nama pangkat (e.g. Pembina)
   * `gol` (String) - Golongan/ruang (e.g. IV/a)
   * `status` (String, Default: 'aktif')
2. **Tabel `statuses` (Status Pegawai)**:
   * `id` (UUID, Primary Key)
   * `nama` (String) - Kategori status (e.g. PNS, PPPK, Tenaga Kontrak)
   * `status` (String, Default: 'aktif')
3. **Tabel `jabatans`**:
   * `id` (UUID, Primary Key)
   * `nama` (String) - Nama jabatan (e.g. Kepala Dinas, Pejabat Struktural)
   * `parent_id` (UUID, Foreign Key ke `jabatans`, Nullable) - Untuk hierarki jenis jabatan
   * `status` (String, Default: 'aktif')
4. **Tabel `pegawais`**:
   * `id` (UUID, Primary Key)
   * `user_id` (UUID, FK ke `users`, Nullable)
   * `nama` (String)
   * `gelar_depan`, `gelar_belakang` (String, Nullable)
   * `nip`, `nik` (String, Unique, Nullable)
   * `status_id` (UUID, FK ke `statuses`, Nullable)
   * `pangkat_id` (UUID, FK ke `pangkats`, Nullable)
   * `jabatan_jenis_id` (UUID, FK ke `jabatans`, Nullable)
   * `jabatan_nama_id` (UUID, FK ke `jabatans`, Nullable)
   * `bidang_id` (UUID, FK ke `pages` category bidang, Nullable)
   * `jenis_kelamin` (String) - 'Laki-laki' / 'Perempuan'
   * `agama`, `pendidikan_terakhir` (String)
   * `alamat` (Text)
   * `telpon` (String)
   * `status` (String, Default: 'aktif')
   * `periode` (String) - Tahun periode data kepegawaian

### 2.2 Modul Agenda Rapat & TTE
1. **Tabel `agenda_rapats`**:
   * `id` (UUID, Primary Key)
   * `nama` (String) - Judul Rapat
   * `tanggal` (Date)
   * `jam_mulai`, `jam_selesai` (Time)
   * `tempat` (String)
   * `tipe_rapat` (String, Default: 'offline') - 'offline' / 'online' / 'hybrid'
   * `zoom_meeting_id`, `zoom_password` (String, Nullable)
   * `acara` (Text)
   * `deskripsi`, `catatan` (Text, Nullable)
   * `status` (String, Default: 'DRAFT')
   * `surat_nomor`, `surat_sifat`, `surat_lampiran`, `surat_hal` (String, Nullable)
   * `jenis_tujuan_surat` (String, Default: 'tunggal') - 'tunggal' / 'lampiran'
   * `surat_tujuan` (Text, Nullable) - Daftar nama/pihak yang diundang
   * `surat_tujuan_lampiran` (Text, Nullable)
   * `pegawai_id` (UUID, FK ke `pegawais`, Nullable) - Pejabat Penanda Tangan Undangan
   * `jenis_tanda_tangan` (Enum: 'manual', 'elektronik', Default: 'manual')
   * `dasar_dari`, `dasar_no`, `dasar_hal` (String, Nullable)
   * `dasar_tgl` (Date, Nullable)
   * `barcode_token` (String, Unique, Nullable) - Token QR presensi
   * `user_id` (UUID, FK ke `users`, Nullable)
2. **Tabel `rapat_verifikasis`**:
   * `id` (UUID, Primary Key)
   * `agenda_rapat_id` (UUID, FK ke `agenda_rapats`)
   * `user_id` (UUID, FK ke `users`) - Verifikator
   * `status` (String) - 'DITERIMA', 'REVISI', 'DITOLAK'
   * `catatan` (Text, Nullable)
3. **Tabel `rapat_pesertas`**:
   * `id` (UUID, Primary Key)
   * `agenda_rapat_id` (UUID, FK ke `agenda_rapats`)
   * `nama` (String)
   * `nip` (String, Nullable)
   * `jabatan`, `instansi`, `no_hp` (String, Nullable)
   * `tanda_tangan` (LongText) - Base64 gambar tanda tangan kanvas
   * `waktu_hadir` (Timestamp)
4. **Tabel `rapat_notulens`**:
   * `id` (UUID, Primary Key)
   * `agenda_rapat_id` (UUID, FK ke `agenda_rapats`)
   * `isi_notulen` (Text)
   * `pimpinan_rapat` (String)
   * `pimpinan_rapat_id` (UUID, FK ke `pegawais`, Nullable)
   * `notulis` (String)
   * `notulis_id` (UUID, FK ke `pegawais`, Nullable)
   * `hasil_rapat` (Text, Nullable)
   * `status` (String, Default: 'DRAFT') - 'DRAFT', 'MENUNGGU_PERSETUJUAN', 'DISETUJUI', 'REVISI'
   * `catatan_revisi` (Text, Nullable)
   * `user_id` (UUID, FK ke `users`, Nullable)
5. **Tabel `dokumen_ttes`**:
   * `id` (UUID, Primary Key)
   * `agenda_rapat_id` (UUID, FK ke `agenda_rapats`)
   * `jenis_dokumen` (Enum: 'undangan', 'daftar_hadir', 'notulen_notulis', 'notulen_pimpinan')
   * `pegawai_id` (UUID, FK ke `pegawais`) - Pejabat Penanda Tangan
   * `signed_file` (String, Nullable) - Path file PDF signed
   * `original_file` (String, Nullable) - Path file PDF draft/original
   * `status` (Enum: 'pending', 'signed', 'failed', Default: 'pending')
   * `bsre_response` (Text, Nullable)
   * `signed_at` (Timestamp, Nullable)

---

## 3. Model & Relasi Eloquent

1. **Model `Pegawai`**:
   * Cast foto profil: `getFotoUrlAttribute()` mendeteksi attachment alias `foto_pegawai` secara polimorfik, jika nihil kembalikan `avatar-laki.svg` atau `avatar-perempuan.svg` sesuai jenis kelamin.
   * `getJabatanStylingAttribute()`: Format dinamis warna/gradient kartu profil berdasarkan parent jabatan (Pejabat Struktural, Pejabat Fungsional, Staf Pelaksana, PPPK).
   * Relasi: `belongsTo` ke `StatusPegawai`, `Pangkat`, `Jabatan` (jenis & nama), dan `Page` (bidang).
2. **Model `AgendaRapat`**:
   * Mengimplementasikan trait `Loggable` untuk pencatatan riwayat perubahan data.
   * Helper `getStatusMapAttribute()`: Pemetaan badge dan deskripsi status rapat.
   * Helper `getAbsensiUrlAttribute()`: Mengembalikan rute token absensi `/rapat/absensi/{token}`.
   * Relasi: `hasMany` ke `RapatPeserta`, `RapatVerifikasi`, `DokumenTte`, `hasOne` ke `RapatNotulen`, dan `morphMany` ke `File` untuk dasar surat, dokumentasi, dan materi rapat.

---

## 4. Implementasi Layanan (Services)

1. **`BsreSignService`** (`app/Services/BsreSignService.php`):
   * Mengirim request HTTP POST menggunakan Basic Auth ke portal BSSN (`/sign/pdf`).
   * Parameter input: `nik`, `passphrase`, `file` (binary PDF draft), dan `tampilan = invisible` (signature tidak merusak visual, dibuktikan dengan barcode verifikasi).
   * Menangkap dan mengembalikan binary stream PDF yang ditandatangani serta log respons BSrE.
2. **`VerificationService`** (`app/Services/VerificationService.php`):
   * Mengatur metadata log verifikasi tanda tangan elektronik untuk pencatatan internal database.

---

## 5. Rute Portal (Routes)

### 5.1 Rute Backend (`routes/backend.php`)
Menyediakan pengelolaan modul pendukung Kepegawaian di bawah pengaman grup admin role:
```php
Route::prefix('pangkat')->as('pangkat')->group(function () {
    Route::get('data', 'Pangkat\PangkatController@data');
    Route::get('delete/{id}', 'Pangkat\PangkatController@delete');
});
Route::resource('pangkat', 'Pangkat\PangkatController');

Route::prefix('status-pegawai')->as('status-pegawai')->group(function () {
    Route::get('data', 'StatusPegawai\StatusPegawaiController@data');
    Route::get('delete/{id}', 'StatusPegawai\StatusPegawaiController@delete');
});
Route::resource('status-pegawai', 'StatusPegawai\StatusPegawaiController');

Route::prefix('jabatan')->as('jabatan')->group(function () {
    Route::get('data', 'Jabatan\JabatanController@data');
    Route::get('delete/{id}', 'Jabatan\JabatanController@delete');
});
Route::resource('jabatan', 'Jabatan\JabatanController');

Route::prefix('pegawai')->as('pegawai.')->group(function () {
    Route::get('data', 'Pegawai\PegawaiController@data')->name('data');
    Route::get('delete/{id}', 'Pegawai\PegawaiController@delete')->name('delete');
    Route::get('get-jabatan-nama/{parent_id}', 'Pegawai\PegawaiController@getJabatanNama')->name('get-jabatan-nama');
});
Route::resource('pegawai', 'Pegawai\PegawaiController');
```

### 5.2 Rute Rapat & TTE Backend (`routes/mvc-route.php`)
```php
Route::prefix('agenda-rapat')->as('agenda-rapat')->group(function () {
    Route::get('data', 'AgendaRapat\AgendaRapatController@data');
    Route::get('delete/{id}', 'AgendaRapat\AgendaRapatController@delete');
    Route::post('{id}/kirim', 'AgendaRapat\AgendaRapatController@kirim')->name('.kirim');
    Route::post('check-konflik', 'AgendaRapat\AgendaRapatController@checkKonflik')->name('.check-konflik');
    
    // Notulen Workflow
    Route::post('{id}/notulen', 'AgendaRapat\AgendaRapatController@storeNotulen')->name('.store-notulen');
    Route::put('{id}/notulen', 'AgendaRapat\AgendaRapatController@updateNotulen')->name('.update-notulen');
    Route::post('{id}/notulen/kirim', 'AgendaRapat\AgendaRapatController@kirimNotulen')->name('.kirim-notulen');
    Route::post('{id}/notulen/setuju', 'AgendaRapat\AgendaRapatController@setujuNotulen')->name('.setuju-notulen');
    Route::post('{id}/notulen/revisi', 'AgendaRapat\AgendaRapatController@revisiNotulen')->name('.revisi-notulen');
    
    Route::post('{id}/dokumentasi', 'AgendaRapat\AgendaRapatController@storeDokumentasi')->name('.store-dokumentasi');
    Route::post('{id}/materi', 'AgendaRapat\AgendaRapatController@storeMateri')->name('.store-materi');
    Route::get('{id}/export-undangan', 'AgendaRapat\AgendaRapatController@exportUndangan')->name('.export-undangan');
    Route::get('{id}/export-notulen', 'AgendaRapat\AgendaRapatController@exportNotulen')->name('.export-notulen');
    Route::get('{id}/export-daftar-hadir', 'AgendaRapat\AgendaRapatController@exportDaftarHadir')->name('.export-daftar-hadir');

    // TTE & Download Controls
    Route::post('{id}/sign/{jenis}', 'AgendaRapat\AgendaRapatController@signDokumen')->name('.sign-dokumen');
    Route::get('{id}/download-signed/{jenis}', 'AgendaRapat\AgendaRapatController@downloadSigned')->name('.download-signed');
});
Route::resource('agenda-rapat', 'AgendaRapat\AgendaRapatController');
```

### 5.3 Rute Publik Frontend (`routes/web.php`)
```php
// Daftar Profil Pegawai
Route::get('/pegawai', [App\Http\Controllers\Frontend\PegawaiController::class, 'index'])->name('frontend.pegawai.index');
Route::get('/pegawai/{id}', [App\Http\Controllers\Frontend\PegawaiController::class, 'show'])->name('frontend.pegawai.show');

// Absensi Online & Verifikasi Rapat TTE
Route::get('/rapat/absensi/{token}', [App\Http\Controllers\Frontend\RapatAbsensiController::class, 'show'])->name('rapat.absensi');
Route::post('/rapat/absensi/{token}', [App\Http\Controllers\Frontend\RapatAbsensiController::class, 'store'])->name('rapat.absensi.store');
Route::get('/rapat/verifikasi', [App\Http\Controllers\Frontend\HomeController::class, 'verifikasiRapat'])->name('rapat.verifikasi');
```

### 5.4 Rute SSO Client (`routes/web.php`)
```php
use App\Http\Controllers\Auth\SsoController;

Route::get('auth/sso', [SsoController::class, 'redirectToSso'])->name('sso.login');
Route::get('auth/sso/callback', [SsoController::class, 'handleSsoCallback'])->name('sso.callback');
```

---

## 6. Spesifikasi Visual & Alur Kerja Detail (UI/UX Workflows)

### 6.1 Reorder Tab Agenda Rapat Detail (`show.blade.php`)
Tab navigasi detail agenda diurutkan secara logis sebagai berikut:
1. **Undangan**: Menampilkan rancangan surat, status tanda tangan elektronik, tombol ekspor PDF, serta form input passphrase TTE.
2. **Daftar Hadir**: Tabel daftar hadir peserta, tombol ekspor PDF daftar hadir, dan form TTE daftar hadir.
3. **Materi**: Daftar upload berkas penunjang (Word, PPT, PDF).
4. **Dokumentasi**: Galeri foto kegiatan rapat.
5. **Notulen**: Form isi notulen rapat dengan status alur persetujuan.
6. **Riwayat**: Log aktivitas perubahan status rapat.

### 6.2 Restriksi Input Notulen
* Hanya dapat diakses oleh user level **1 (Root)** dan **2 (Administrator)**. 
* Operator (level 3) tidak memiliki input text area Notulen demi alasan pembatasan kewenangan isi surat resmi dinas.

### 6.3 Siklus Persetujuan Notulen Rapat
1. Notulis menyusun draf notulen -> Klik **Simpan Draf**.
2. Notulis mengirim notulen untuk ditinjau -> Klik **Kirim ke Pimpinan** (status berubah menjadi `MENUNGGU_PERSETUJUAN`).
3. Pimpinan Rapat (melalui akunnya) meninjau:
   * Jika setuju: Klik **Setujui Notulen** (status menjadi `DISETUJUI`).
   * Jika tidak setuju: Klik **Minta Revisi** dan masukkan alasan revisi (status menjadi `REVISI`).
4. Setelah berstatus `DISETUJUI`, proses TTE Notulen dapat dijalankan secara sekuensial:
   * **Notulis** menandatangani terlebih dahulu (`notulen_notulis`).
   * Setelah itu, **Pimpinan Rapat** menandatangani (`notulen_pimpinan`).

### 6.4 Logika Kontrol Unduhan PDF (TTE vs Manual)
* Jika metode tanda tangan dipilih **Elektronik** dan status dokumen adalah `signed` (berhasil ditandatangani via BSrE):
  * Sembunyikan tombol "Download Undangan (PDF)" atau "Export PDF Daftar Hadir" standar.
  * Tampilkan tombol "Download Signed PDF" atau "Verifikasi TTE". Hal ini memastikan dokumen fisik/kertas yang disebarkan adalah berkas resmi dengan segel elektronik BSrE.
* Jika tanda tangan dipilih **Manual**, tampilkan tombol cetak format standar dengan visualisasi ruang tanda tangan basah secara fisik.

### 6.5 Penyempurnaan PDF Export
1. **PDF Notulen**:
   * Menambahkan halaman khusus di akhir dokumen untuk melampirkan foto dokumentasi rapat dalam susunan rapi 2 kolom.
   * Mencantumkan daftar tautan unduhan bahan rapat aktif yang dapat diklik langsung dari PDF untuk memudahkan pembaca berkas fisik mengakses materi digital.
2. **PDF Daftar Hadir**:
   * Mengganti teks penghitungan peserta dari "Total Kehadiran" menjadi "Jumlah Peserta".
   * Menyediakan area tanda tangan Pimpinan Rapat yang memuat scan barcode segel elektronik jika berstatus TTE, atau kolom kosong tanda tangan basah jika manual.

### 6.6 Halaman Verifikasi Publik (`/rapat/verifikasi`)
* Halaman ini didesain sebagai destinasi pemindaian QR code yang tersemat pada footer dokumen PDF bertanda tangan elektronik BSrE.
* Menampilkan informasi resmi mengenai validitas dokumen: Nama dokumen, Penyelenggara rapat, Nama penanda tangan, NIP, Jabatan, Tanggal penandatanganan, dan Status verifikasi BSrE.
* Memberikan kepastian hukum bagi pihak ketiga yang menerima dokumen cetak dari Diskominfotik.

### 6.7 Alur Kerja & Sinkronisasi SSO Client (`SsoController.php`)
Sistem diintegrasikan secara dinamis dengan portal SSO KOMPASS menggunakan `SsoController` dengan logika berikut:
1. **Redirect ke Server (`redirectToSso`)**:
   - Menghasilkan token state CSRF acak dan menyimpannya di sesi (`sso_state`).
   - Melakukan redirect ke `/oauth/authorize` pada server KOMPASS dengan query parameter `client_id`, `redirect_uri`, `response_type=code`, dan `state`.
2. **Penanganan Callback (`handleSsoCallback`)**:
   - Mengambil dan menghapus state CSRF dari sesi untuk divalidasi dengan request.
   - Menukarkan Authorization Code yang diterima dengan Access Token via POST request ke `/oauth/token`. Jika gagal (misal: kredensial salah), browser dialihkan ke halaman error terpusat di server KOMPASS (`/sso/error`) dengan info error dan solusi.
   - Mengambil data profil pengguna via GET request ke `/api/user` menggunakan token tersebut. Jika gagal, dialihkan ke `/sso/error`.
   - Mencari data pengguna lokal berdasarkan `email`. Jika tidak ditemukan (belum terdaftar secara lokal), browser dialihkan ke halaman error terpusat di server KOMPASS (`/sso/error`) dengan jenis error "Akun Belum Terdaftar" agar admin setempat mendaftarkannya terlebih dahulu.
   - Jika ditemukan, lakukan sinkronisasi peran (`role`): Mencari model `Level` dan `AccessGroup` lokal berdasarkan kode peran yang dikirim oleh server SSO (`admin`, `verifikator`, `user`), lalu memperbarui peran pengguna lokal tersebut.
   - Melakukan otentikasi sesi (`Auth::login`) dan mengarahkan pengguna ke halaman dashboard admin.

