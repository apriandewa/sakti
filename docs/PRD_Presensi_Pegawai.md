# Product Requirement Document (PRD)
## Modul Presensi Pegawai Terintegrasi API Simpegnas BKN

> [!NOTE]
> Dokumen ini mendefinisikan spesifikasi kebutuhan produk, alur integrasi API, struktur database, diagram pendukung, logika perhitungan potongan (TPP/Tunjangan Kinerja), dan standar visual untuk modul **Presensi Pegawai** yang terhubung langsung dengan **API Simpegnas BKN** pada Portal SAKTI **Pemerintah Kabupaten Indragiri Hulu**.

---

## 1. Deskripsi Umum Modul
Modul Presensi Pegawai dikembangkan untuk mengelola, menyinkronkan, menghitung, dan menampilkan rekapitulasi kehadiran harian **seluruh pegawai ASN Pemerintah Kabupaten Indragiri Hulu** secara bulanan — mencakup seluruh Organisasi Perangkat Daerah (OPD), unit kerja, dan kantor di lingkungan Pemkab Indragiri Hulu.

Administrator dapat memilih **kantor/unit kerja** yang ingin direkapitulasikan melalui dropdown dinamis. Data presensi yang ditampilkan, disinkronkan, dan dihitung selalu mengacu pada **kantor yang dipilih** pada filter halaman.

Modul ini terintegrasi secara asinkron dengan **API Simpegnas BKN** untuk menarik data kehadiran ASN harian (termasuk jam masuk, jam pulang, jenis kehadiran seperti Cuti, Dinas Luar, Izin, serta status keterlambatan dan pulang cepat). Berdasarkan data mentah dari API tersebut, sistem secara otomatis akan mengelompokkan kategori pelanggaran waktu kerja (Terlambat & Pulang Cepat), menghitung persentase/poin pemotongan tunjangan secara real-time, dan menyajikannya dalam bentuk tabel rekapitulasi interaktif (DataTable) yang responsive dan informatif.

---

## 2. Kebutuhan Fungsional & Alur Kerja

Sistem memiliki alur utama yang diakses oleh **Administrator (Level 2)** atau **Root (Level 1)**:

### 2.1 Filter & Pencarian
1. **Input Pilihan**:
   - Dropdown **Pilih Kantor** dengan **Select2 Autocomplete**:
     - Sumber data: API `https://api-absensi.simpegnas.go.id/absensi/api/get/kantor`.
     - Daftar kantor mencakup seluruh unit kerja/OPD di lingkungan **Pemerintah Kabupaten Indragiri Hulu**.
     - Setiap `<option>` menggunakan **`value` = `kantor_id` (UUID)** dan **teks tampilan = `nama_kantor`**.
     - Pengguna mengetik nama kantor/OPD pada kolom pencarian; Select2 memfilter opsi secara real-time (autocomplete) agar pemilihan kantor cepat meskipun daftar panjang.
     - Kantor yang dipilih menjadi parameter wajib untuk seluruh aksi berikutnya: tampil DataTable, sinkronisasi BKN, dan detail log harian pegawai.
     - Tidak ada kantor default yang terkunci di `.env`; pemilihan kantor sepenuhnya dilakukan oleh pengguna di antarmuka.
   - Dropdown **Pilih Bulan** (Januari s.d. Desember, default ke bulan berjalan).
   - Dropdown **Pilih Tahun** (Daftar tahun dinamis, default ke tahun berjalan).
3. **Tampilan Dropdown**: Menggunakan komponen **Select2** bawaan tema EduAdmin agar tata letak tetap proporsional dan responsif.
2. **Tombol Aksi**: Tombol **Cari** (Memicu render ulang DataTable via AJAX berdasarkan `kantor_id`, bulan, dan tahun terpilih).
3. **Impor Pegawai (CSV)**: Tombol/Fungsi untuk mengimpor data nama dan NIP pegawai secara batch menggunakan file CSV guna mempermudah inisialisasi basis data per kantor.

### 2.2 Tampilan Datatable (Tabel Rekapitulasi)
Setelah formulir pencarian disubmit, sistem akan menampilkan data rekapitulasi pegawai dalam format **DataTable** dengan struktur kolom sebagai berikut:

| No | Nama Pegawai + NIP | Hadir (HN) | Tanpa Ket (TK) | Cuti (CT) | Dinas Luar (DL) | Izin | Hari Kerja | Terlambat (TM) | Pulang Cepat (PC) | Total Potongan | Aksi |
| :---: | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| 1 | **Nama + Gelar** <br><small class="text-muted">NIP. xxxxxxxxxxxxxx</small> | [HN] | [TK] | [CT] | [DL] | [Izin] | [Total] | **5 Kolom**: <br>TM1, TM2, TM3, TM4, TMM | **5 Kolom**: <br>PC1, PC2, PC3, PC4, PC5M | [Total %] | [Detail] [Sync] |

#### Detail Rincian Kolom DataTable:
1. **No**: Angka urut baris (DT_RowIndex).
2. **Nama Pegawai + NIP (1 Kolom)**:
   - Nama lengkap pegawai beserta gelar (depan & belakang).
   - NIP pegawai (atau NIK jika Non-PNS/Tenaga Kontrak) di bawah nama dengan format font yang lebih kecil dan abu-abu (`text-muted`).
3. **Hadir (HN)**: Jumlah hari hadir kerja efektif dalam bulan tersebut.
4. **Tanpa Keterangan (TK)**: Jumlah hari tidak hadir tanpa keterangan/alasan (Alpa).
5. **Cuti (CT)**: Jumlah hari cuti kerja yang disetujui (Cuti Tahunan, Cuti Sakit, dll).
6. **Dinas Luar (DL)**: Jumlah hari dinas luar berdasarkan surat tugas resmi.
7. **Izin**: Jumlah hari tidak hadir dengan izin resmi (sakit/keperluan mendesak).
8. **Hari Kerja**: Total Hari Kerja Wajib dalam bulan tersebut (e.g. 20, 21, atau 22 hari tergantung kalender kerja bulanan).
9. **Terlambat (Late) - 5 Kolom**:
   - **TM1**: Jumlah kejadian terlambat kategori 1.
   - **TM2**: Kategori 2.
   - **TM3**: Kategori 3.
   - **TM4**: Kategori 4.
   - **TMM**: Jumlah Terlambat Masuk Tanpa Keterangan / Melebihi batas toleransi.
10. **Pulang Cepat (PC) - 5 Kolom**:
    - **PC1**: Jumlah kejadian pulang cepat kategori 1.
    - **PC2**: Kategori 2.
    - **PC3**: Kategori 3.
    - **PC4**: Kategori 4.
    - **PCM**: Kategori 5 (atau Pulang Cepat tanpa keterangan tertentu).
11. **Total Potongan**: Persentase atau poin akumulasi pemotongan berdasarkan rumus bobot pelanggaran TM & PC.
12. **Aksi**:
    - Tombol **Detail** (Ikon mata / `show`) untuk membuka modal log harian kehadiran pegawai yang bersangkutan pada bulan tersebut.
    - Tombol **Sync Pegawai** (Ikon refresh) untuk melakukan penarikan ulang data API khusus pegawai tersebut secara asinkron (AJAX).

### 2.3 Modal Log Harian & Geolokasi
* Saat melihat rincian harian pegawai, sistem akan menampilkan data **Jam Masuk** dan **Jam Pulang** yang secara akurat ditarik menggunakan variabel `time_with_timezone` (bukan hanya `time`) untuk memastikan ketepatan zona waktu lokal (Asia/Jakarta).
* Modal juga menyediakan fungsionalitas **Detail Geolocation & Jam Presensi** yang memuat secara dinamis foto swafoto/geolokasi pegawai saat melakukan check-in dan check-out. Foto ditarik sebagai data base64 via API eksternal (route `image-riwayat`) dan memiliki penanganan error adaptif yang menyembunyikan elemen gambar jika foto gagal dimuat.

### 2.4 Tab Log dan Monitoring Sinkronisasi BKN
Modul Presensi Pegawai dibagi menjadi dua *Tab* utama:
- **Tab 1: Data Presensi**: Berisi form filter (Pilih Kantor, Bulan, Tahun) dan tombol Cari, yang menampilkan DataTable Rekapitulasi Presensi.
- **Tab 2: Log dan Monitoring Sinkronisasi BKN**: Halaman khusus untuk memantau riwayat penarikan data dari API BKN.
  - **Tombol "Tarik Data BKN"** dipindahkan secara eksklusif ke tab ini. 
  - Pada tab ini, Admin dapat melihat tabel histori log sinkronisasi (`presensi_sync_logs`) yang mencakup status, waktu, eksekutor, pesan error, dll.

### 2.5 Automasi Sinkronisasi Data (Cron Job) & Penanganan Perubahan Data
Selain penarikan data secara manual melalui Tab Monitoring, sistem dilengkapi dengan fitur automasi sinkronisasi (Scheduler/Cron Job) dengan spesifikasi berikut:
1. **Jadwal Eksekusi**: Sinkronisasi otomatis dijalankan setiap hari pada pukul **18.00 WIB**. Sistem akan iterasi memanggil API Simpegnas BKN untuk menarik data presensi bagi seluruh OPD/Kantor.
2. **Pencatatan Log (`presensi_sync_logs`)**: Setiap proses sinkronisasi akan dicatat secara mendetail. Jika dilakukan manual, kolom eksekutor mencatat nama/ID admin yang login. Jika berjalan melalui *cron job*, kolom eksekutor akan tercatat sebagai "System". Tabel ini menyimpan status eksekusi, jumlah baris data, parameter bulan/tahun, serta catatan error.
3. **Penanganan Presensi Terlewat & Pembaruan Data (Retroactive Changes)**: 
   - **Masalah**: Ada kemungkinan pegawai melakukan presensi setelah jadwal cron 18.00 WIB, atau ada perubahan status kehadiran di hari/minggu sebelumnya (misal: Awalnya "TK" (Alpa) namun diubah menjadi "Izin" atau "Cuti" setelah pengajuan disetujui atasan di BKN).
   - **Resolusi**: API BKN yang digunakan (`/get/rekap-bulanan-by-kantor`) secara inheren mengembalikan data **satu bulan penuh**. Oleh karena itu, setiap kali sinkronisasi harian (jam 18.00) berjalan, sistem tidak hanya menarik H-1, melainkan **menarik dan menimpa ulang seluruh data bulan berjalan (Upsert Full Month)** dari tanggal 1 hingga hari ini. 
   - Dengan pendekatan ini, seluruh pembaruan status (Cuti/Izin yang di-approve retroaktif minggu lalu) maupun *check-out* yang telat masuk pada hari kemarin akan selalu **terkoreksi dan disinkronkan** secara akurat.
4. **Optimasi Performa (Laravel Queue & Batch Upsert)**:
   - Untuk mencegah *server overload* atau *timeout* saat menarik data puluhan instansi sekaligus pada pukul 18.00, penarikan otomatis diproses melalui **Background Jobs (Laravel Queue)** secara bertahap (memecah antrean *dispatch job* per-kantor).
   - Pembaruan ke *database* dilakukan menggunakan metode **Batch Upsert** (Insert-Update massal) alih-alih mengecek data satu per satu, sehingga sistem sanggup memproses ribuan data presensi dalam sepersekian detik dan UI admin tetap responsif.

---

## 3. Logika Perhitungan Potongan Kehadiran

Potongan tunjangan kinerja/perilaku kerja didapat dari akumulasi jumlah pelanggaran kedisiplinan (Terlambat Masuk Kerja dan Pulang Cepat) dengan rincian ketentuan pemotongan per kejadian sebagai berikut:

### 3.1 Klasifikasi Terlambat (TM) & Bobot Potongan:
* **TM1** (Terlambat Masuk Kerja Tingkat 1: 1 s.d. 30 Menit) = **0.5%** atau **0.5 Poin**
* **TM2** (Terlambat Masuk Kerja Tingkat 2: 31 s.d. 60 Menit) = **1.0%** atau **1.0 Poin**
* **TM3** (Terlambat Masuk Kerja Tingkat 3: 61 s.d. 90 Menit) = **1.25%** atau **1.25 Poin**
* **TM4 / TMM** (Terlambat Masuk Kerja Tingkat 4: > 90 Menit atau Tanpa Keterangan Masuk) = **1.5%** atau **1.5 Poin**

### 3.2 Klasifikasi Pulang Cepat (PC) & Bobot Potongan:
* **PC1** (Pulang Cepat Tingkat 1: Meninggalkan Kantor 1 s.d. 30 Menit Sebelum Waktu Pulang) = **0.5%** atau **0.5 Poin**
* **PC2** (Pulang Cepat Tingkat 2: Meninggalkan Kantor 31 s.d. 60 Menit Sebelum Waktu Pulang) = **1.0%** atau **1.0 Poin**
* **PC3** (Pulang Cepat Tingkat 3: Meninggalkan Kantor 61 s.d. 90 Menit Sebelum Waktu Pulang) = **1.25%** atau **1.25 Poin**
* **PC4 / PCM** (Pulang Cepat Tingkat 4: Meninggalkan Kantor > 90 Menit atau Tanpa Keterangan Waktu Pulang) = **1.5%** atau **1.5 Poin**

### 3.3 Klasifikasi Tanpa Keterangan (TK) & Bobot Potongan:
* **TK** (Tanpa Keterangan / Alpa) = **3.0%** atau **3.0 Poin**.
* *Catatan Logika*: Jika status hari presensi adalah 'TK', maka perhitungan pemotongan murni menggunakan bobot 3%, dan secara otomatis diabaikan atau dieksklusi dari perhitungan keterlambatan (TM) atau pulang cepat (PC).

### 3.4 Formula Total Potongan Bulanan:
$$\text{Total Potongan Pegawai (\%)} = \sum (\text{TM} \times \text{Bobot TM}) + \sum (\text{PC} \times \text{Bobot PC}) + \sum (\text{TK} \times \text{Bobot TK})$$

**Contoh Kasus**:
Pegawai bernama Ahmad dalam 1 bulan memiliki riwayat:
* TM1 (Terlambat 10 menit) sebanyak 3 kali.
* TM3 (Terlambat 70 menit) sebanyak 1 kali.
* PC4 (Pulang cepat 95 menit) sebanyak 2 kali.

Perhitungan:
* TM1 = $3 \times 0.5 = 1.5$
* TM3 = $1 \times 1.25 = 1.25$
* PC4 = $2 \times 1.5 = 3$
* TK (Misal 1 hari) = $1 \times 3.0 = 3.0$
* **Total Potongan Ahmad** = $1.5 + 1.25 + 3.0 + 3.0 = 8.75\%$

---

## 4. Arsitektur Kode & Optimasi UI
Untuk menjaga kebersihan (*clean code*) dan performa aplikasi, diterapkan beberapa standar pengembangan:
1. **Pemisahan Logika & Tampilan (MVC)**: Segala perhitungan bobot potongan dan logika proporsi grafik donat (Donut Chart offsets & proportions) dipindahkan sepenuhnya ke dalam Controller (`PresensiController`), sehingga file Blade (seperti `show.blade.php`) tetap bersih dan hanya fokus pada penyajian data.
2. **Standardisasi CSS**: Menghindari penggunaan inline styles yang ekstensif dengan memusatkan styling (seperti tata letak kartu, navigasi tabel, dsb) ke dalam satu berkas global `custom.css`. Ini memastikan penggunaan ulang (*reusability*) kode CSS lintas halaman, mempercepat load, dan menjaga keseragaman tampilan dashboard analisis absensi.
3. **Pengelolaan Waktu**: Pengambilan zona waktu dipusatkan melalui data `time_with_timezone` agar UI menampilkan detail waktu absensi yang akurat dan tak bias server.

---

## 5. Desain Visual & Skema Warna Badge

Antarmuka pengguna didesain menggunakan **Bootstrap** dan komponen visual **EduAdmin** dengan palet warna yang membedakan tipe status kehadiran secara visual (badge status) agar memudahkan pemindaian data (*scannability*):

* **HN (Hadir/Normal)** $\rightarrow$ **Hijau / Success** (`badge-success`)
* **TK (Tanpa Keterangan / Alpa)** $\rightarrow$ **Merah / Danger** (`badge-danger`)
* **PC (Pulang Cepat) & TM (Terlambat)** $\rightarrow$ **Oranye / Warning** (`badge-warning`)
* **CT (Cuti)** $\rightarrow$ **Cyan / Info** (`badge-info`)
* **DL (Dinas Luar)** $\rightarrow$ **Biru / Primary** (`badge-primary`)
* **Selain status di atas (e.g. Izin / Sakit / Tugas Belajar)** $\rightarrow$ **Abu-abu / Secondary** (`badge-secondary`)

---

## 6. Spesifikasi Integrasi API Simpegnas BKN

Untuk menjamin ketersediaan data yang valid, aplikasi berinteraksi dengan API Simpegnas BKN menggunakan service khusus yang mengonsumsi endpoint rekap bulanan per unit kerja/kantor.

### 6.1 Kredensial API (Konfigurasi `.env`)
```env
# Token Akses API Absensi Simpegnas BKN (JWT) — cakupan instansi Pemkab Indragiri Hulu
API_ABSENSI_TOKEN=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpbnN0YW5zaV9pZCI6IkE1RUIwM0UyM0IxQUY2QTBFMDQwNjQwQTA0MDI1MkFEIiwiYWxsIjpmYWxzZSwiaWF0IjoxNzgwNDczMTE2fQ.G029Pj7hIgHPBx1mtg4Y6fyxGUlautZUjlh4ZkyL-Tw
```

> [!NOTE]
> Variabel `API_ID_KANTOR` dan `API_NAMA_KANTOR` **tidak lagi digunakan** sebagai kantor tetap. Pemilihan kantor dilakukan dinamis melalui dropdown Select2 di antarmuka, dengan `kantor_id` yang dikirim sebagai parameter query setiap kali data ditarik.

### 6.2 Endpoint API yang Diintegrasikan
* **Pengambilan Daftar Kantor (Dropdown Select2)**:
  * **Metode**: `GET`
  * **URL**: `https://api-absensi.simpegnas.go.id/absensi/api/get/kantor`
  * **Header**:
    * `Authorization: Bearer {API_ABSENSI_TOKEN}`
    * `Accept: application/json`
  * **Pemetaan ke Select2**:
    * `id` / `value` option → `kantor_id` (UUID)
    * `text` / label tampilan → `nama_kantor`
  * **Contoh struktur item kantor**:
    ```json
    {
      "kantor_id": "fbc6ca94-d421-48f0-a6d7-d2bcf392c6f2",
      "nama_kantor": "Dinas Komunikasi, Informatika, dan Statistik"
    }
    ```
  * **Perilaku Autocomplete**: Select2 diinisialisasi dengan `minimumResultsForSearch: 0` dan pencarian berdasarkan `nama_kantor`, sehingga pengguna dapat mengetik sebagian nama OPD/kantor untuk menemukan opsi yang sesuai.

* **Pengambilan Data Presensi Bulanan Kantor**:
  * **Metode**: `GET`
  * **URL**: `https://api-absensi.simpegnas.go.id/absensi/api/get/rekap-bulanan-by-kantor`
  * **Parameter Query**:
    * `kantor_id` : ID Kantor target yang dipilih pengguna dari dropdown Select2 (e.g. `fbc6ca94-d421-48f0-a6d7-d2bcf392c6f2` untuk Diskominfotik, atau UUID kantor OPD lain di Pemkab Indragiri Hulu)
    * `tahun` : Tahun rekapitulasi (e.g. `2026`)
    * `bulan` : Bulan rekapitulasi (e.g. `6` - tanpa lead zero)
  * **Header**:
    * `Authorization: Bearer {API_ABSENSI_TOKEN}`
    * `Accept: application/json`

* **Pengambilan Foto Presensi (Base64)**:
  * **URL Lokal**: `image-riwayat` route.
  * **Fungsi**: Menerima request frontend (AJAX) berdasarkan parameter NIP, tanggal, dan jenis check-in/out, lalu menarik (fetch/parse) data string Base64 gambar swafoto dari server API Simpegnas untuk dirender ke tag `<img>`.

### 6.3 Contoh Skema Respon JSON dari BKN API (Rekap Bulanan Kantor):
```json
{
  "status": true,
  "responseCode": 200,
  "message": "Data Berhasil ditampilkan",
  "data": [
    {
      "nama": "RUDY INDRA SUBHAKTI",
      "nip": "197003081998031002",
      "tahun": "2026",
      "bulan": "6",
      "presensi": [
        {
          "day": 1,
          "checkIn": {
            "work_from": "",
            "status": "TMM",
            "source": "",
            "time": "",
            "time_with_timezone": "",
            "timezone": "",
            "late": 0
          },
          "checkRest": {
            "work_from": "",
            "status": "",
            "source": "",
            "time": "",
            "time_with_timezone": "",
            "timezone": "",
            "late": 0
          },
          "checkOut": {
            "work_from": "",
            "status": "PCM",
            "source": "",
            "time": "",
            "time_with_timezone": "",
            "timezone": "",
            "late": 0
          },
          "status": "LN",
          "late": 0
        },
        {
          "day": 4,
          "checkIn": {
            "work_from": "WFO",
            "status": "TM3",
            "source": "",
            "time": "01:52:37",
            "time_with_timezone": "08:52:37",
            "timezone": "Asia/Jakarta",
            "late": 83
          },
          "checkRest": {
            "work_from": "",
            "status": "",
            "source": "",
            "time": "",
            "time_with_timezone": "",
            "timezone": "",
            "late": 0
          },
          "checkOut": {
            "work_from": "WFO",
            "status": "HN",
            "source": "",
            "time": "09:00:57",
            "time_with_timezone": "16:00:57",
            "timezone": "Asia/Jakarta",
            "late": 0
          },
          "status": "TM3",
          "late": 83
        }
      ]
    }
  ]
}
```

---

## 7. Diagram Arsitektur Data & Alur (Mermaid)

### 7.1 Diagram Alur Penarikan & Pengolahan Data (Sequence Diagram)
Diagram ini menjelaskan interaksi antara Admin panel, Database Lokal, dan API Simpegnas BKN dalam menyajikan data presensi.

```mermaid
sequenceDiagram
    actor Admin as Administrator (Dashboard)
    participant App as Laravel Application
    participant DB as Database Lokal
    participant BKN as API Simpegnas BKN

    Admin->>App: Akses Halaman Presensi
    App->>BKN: Fetch Daftar Kantor (GET /get/kantor)
    BKN-->>App: Return Daftar OPD/Kantor Pemkab Indragiri Hulu
    App->>Admin: Tampilkan Dropdown Pilih Kantor (Select2 Autocomplete)
    Admin->>App: Pilih Kantor, Bulan & Tahun lalu Klik Cari
    App->>DB: Cek Cache / Data Presensi Lokal (berdasarkan kantor_id)
    alt Data Belum Ada / Admin Klik "Tarik Data BKN"
        Admin->>App: Klik Sinkronisasi (Sync Data BKN)
        App->>BKN: Request Token & Fetch Data Presensi (kantor_id, Bulan, Tahun)
        BKN-->>App: Return Respon JSON Data Kehadiran Pegawai
        App->>App: Parsing Data & Hitung Kategori TM/PC, TK & Potongan
        App->>DB: Simpan/Update Data ke 'presensi_harians'
    end
    DB-->>App: Mengembalikan Data Kehadiran Pegawai
    App->>Admin: Tampilkan Rekapitulasi di Datatable (AJAX)
    
    %% Alur load gambar foto geolokasi
    Admin->>App: Klik Riwayat / Detail Log Harian
    App->>BKN: Fetch Base64 Photo (checkIn/Out) via API
    BKN-->>App: Return Base64 String
    App->>Admin: Tampilkan Gambar pada Modal
```

### 7.2 Entity Relationship Diagram (ERD) - Modul Presensi
Modul presensi ini dihubungkan langsung dengan entitas `pegawais` yang sudah ada pada skema database portal.

```mermaid
erDiagram
    PEGAWAIS {
        uuid id PK
        string nama
        string nip UK
        string status
    }

    PRESENSI_HARIANS {
        uuid id PK
        uuid pegawai_id FK "Relasi ke pegawais"
        date tanggal
        time jam_masuk
        time jam_keluar
        string status_kehadiran "HN, TK, CT, DL, IZIN"
        string kategori_terlambat "TM1, TM2, TM3, TM4, TMM"
        integer menit_terlambat
        string kategori_pulang_cepat "PC1, PC2, PC3, PC4, PC5"
        integer menit_pulang_cepat
        decimal potongan_terlambat "Bobot %"
        decimal potongan_pulang_cepat "Bobot %"
        decimal total_potongan "Hasil jumlah potongan"
        text keterangan
        boolean is_sync
        timestamp synced_at
    }

    PRESENSI_SYNC_LOGS {
        uuid id PK
        uuid kantor_id FK "Target OPD (Opsional, jika sync per-kantor)"
        string bulan
        string tahun
        string sync_by "Admin ID / 'System'"
        string status "sukses, gagal, berjalan"
        timestamp waktu_mulai
        timestamp waktu_selesai
        integer jumlah_data_ditarik
        text catatan_pesan
    }

    PEGAWAIS ||--o{ PRESENSI_HARIANS : "memiliki"
```

---

## 8. Desain Antarmuka (Wireframe & Visual Mockup)

### 8.1 Elemen Halaman Utama (Index Presensi)
* **Header**: Judul modul "Presensi Pegawai" dengan ikon Jam/Kalender, dan Navigasi Tab (Tabs Bootstrap).
* **Tab 1: Data Presensi**:
  - **Filter Card**: Panel filter minimalis menggunakan sistem grid:
    - Kolom 1: Dropdown **"Pilih Kantor"** (Select2 autocomplete).
    - Kolom 2: Dropdown "Pilih Bulan".
    - Kolom 3: Dropdown "Pilih Tahun".
    - Kolom 4: Tombol "Cari" (warna biru - `btn-primary`).
  - **DataTable Card**: Rekapitulasi Presensi & Potongan Pegawai.
* **Tab 2: Log & Monitoring Sinkronisasi BKN**:
  - **Panel Aksi**: Tombol **"Tarik Data Simpegnas"** (warna hijau - `btn-success` dengan ikon cloud sync) beserta form pilih bulan, tahun, dan kantor yang akan ditarik.
  - **Tabel Histori Log**: DataTable yang menampilkan rekaman dari tabel `presensi_sync_logs` dengan kolom Waktu, Eksekutor (Sistem/Admin), Target Kantor, Status, dan Pesan.

### 8.2 Elemen Detail Log Harian (Modal Show Detail)
Ketika tombol **Detail** di baris pegawai diklik, sebuah modal (glassmorphism/sleek Bootstrap) akan muncul menampilkan rincian harian pegawai tersebut:
* **Profil Singkat**: Nama, NIP, Jabatan, dan Bidang.
* **Tabel Riwayat Harian**:
  - Kolom: Tanggal, Status Kehadiran (Badge Berwarna), Jam Masuk, Jam Pulang, Terlambat (menit & kategori), Pulang Cepat (menit & kategori), Potongan (%), dan Keterangan.
  - Data diurutkan berdasarkan tanggal terkecil hingga terbesar dalam bulan tersebut.
