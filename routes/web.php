<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\jsController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\UnduhanController;
use App\Http\Controllers\Frontend\GaleriController;
use App\Http\Controllers\Frontend\InformasiController;
use App\Http\Controllers\Frontend\PresensiController;
use App\Http\Controllers\Frontend\PegawaiController;
use App\Http\Controllers\Frontend\PageController;
use App\Http\Controllers\Frontend\KunjunganController;
use App\Http\Controllers\Frontend\UlasanController;
use App\Http\Controllers\Frontend\BeritaController as FrontendBeritaController;
use App\Http\Controllers\Backend\Berita\BeritaController as BackendBeritaController;
use App\Http\Controllers\Backend\Galeri\GaleriController as BackendGaleriController;
use App\Http\Controllers\Backend\Unduhan\UnduhanController as BackendUnduhanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===== Frontend =====
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/berita', [FrontendBeritaController::class, 'index'])->name('berita');
Route::get('/berita/{slug}', [FrontendBeritaController::class, 'show'])->name('berita.detail');

Route::get('/unduhan', [UnduhanController::class, 'index'])->name('unduhan');
Route::get('/unduhan/{slug}', [UnduhanController::class, 'show'])->name('unduhan.detail');
// untuk download dan view file unduhan
Route::get('/unduhan/{slug}/download/{file}', [UnduhanController::class, 'download'])->name('unduhan.download');
Route::post('/unduhan/{slug}/view/{file}', [UnduhanController::class, 'view'])->name('unduhan.view');

Route::get('/galeri', [GaleriController::class, 'index'])->name('galeri');
Route::get('/galeri/{slug}', [GaleriController::class, 'show'])->name('galeri.detail');

Route::get('/informasi', [InformasiController::class, 'index'])->name('informasi');
Route::get('/berkala', [InformasiController::class, 'berkala'])->name('informasi.berkala');
Route::get('/tersedia', [InformasiController::class, 'tersedia'])->name('informasi.tersedia');
Route::get('/informasi/{slug}', [InformasiController::class, 'show'])->name('informasi.detail');

Route::get('/tamu', [KunjunganController::class, 'create'])->name('kunjungan.create');
Route::post('/tamu', [KunjunganController::class, 'store'])->name('kunjungan.store');
Route::get('/tamu/{slug}', [KunjunganController::class, 'show'])->name('kunjungan.detail');

Route::get('/ulasan', [UlasanController::class, 'create'])->name('ulasan.create');
Route::post('/ulasan', [UlasanController::class, 'store'])->name('ulasan.store');

Route::get('/presensi', [PresensiController::class, 'index'])->name('presensi');

Route::prefix('presensi')->group(function () {
    Route::get('/detail/{nip}', [PresensiController::class, 'detail'])->name('presensi.detail');
    Route::get('/profil/{nip}', [PresensiController::class, 'profil'])->name('presensi.profil');
});

Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai');
Route::get('/kantor', [PegawaiController::class, 'kantor'])->name('kantor');
Route::get('/kehadiran', [PegawaiController::class, 'kehadiran'])->name('kehadiran');


Route::get('/statistik', [InformasiController::class, 'statistik'])->name('informasi.statistik');

Route::get('/page', [PageController::class, 'index'])->name('page');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.detail');

Route::get('/struktur', [HomeController::class, 'struktur'])->name('struktur');
Route::get('/testimoni', [HomeController::class, 'testimoni'])->name('testimoni');
Route::get('/penghargaan', [HomeController::class, 'penghargaan'])->name('penghargaan');
Route::get('/tentang-kami', [HomeController::class, 'tentangKami'])->name('tentang-kami');
Route::get('/kontak', [HomeController::class, 'kontak'])->name('kontak');

// ===== Profile (auth) =====
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== File Management =====
Route::group(['prefix' => config('master.app.url.frontend')], function () {
    Route::prefix('file')->as('file.')->group(function () {
        Route::get('stream/{id}/{name}', "Backend\File\FileController@publicStream");
        // Route::get('download/{id}/{name}', "File\FileController@downloadFile");
        // Route::get('delete/{id}/{name}', "File\FileController@deleteFile");
        // Route::post('upload-image-editor', "File\FileController@handleEditorImageUpload");
    });
});

// ===== Socialite Login =====
Route::get('auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// ===== Javascript Loader =====
Route::prefix('js')->as('js.')->group(function () {
    Route::any('/{layout}/{page}/{file}', [jsController::class, 'javaScript']);
});

// ===== Admin Action (Kirim Berita, Galeri, Unduhan) =====
Route::post('/admin/berita/kirim/{id}', [BackendBeritaController::class, 'kirim'])->name('berita.kirim');
Route::post('/admin/galeri/kirim/{id}', [BackendGaleriController::class, 'kirim'])->name('galeri.kirim');
Route::post('/admin/unduhan/kirim/{id}', [BackendUnduhanController::class, 'kirim'])->name('unduhan.kirim');

// ===== Auth Routes (Jetstream/Fortify) =====
require __DIR__ . '/auth.php';

// ===== Jetstream Profile override =====
Route::middleware(['auth:sanctum', config('jetstream.auth_session', 'verified'), 'backend'])->group(function () {
    Route::get('/admin/user/profile', [\Laravel\Jetstream\Http\Controllers\Livewire\UserProfileController::class, 'show'])->name('profile.show');
});
