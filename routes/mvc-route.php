<?php
use Illuminate\Support\Facades\Route;
Route::group(['prefix'=>config('mvc.route_prefix')], function () { // remove this line if you dont have route group prefix
	Route::group(['middleware'=>['auth','userRoles', 'verified']], function () {
        //tautan
		Route::prefix('tautan')->as('tautan')->group(function () {
			Route::get('data', 'Tautan\TautanController@data');
			Route::get('delete/{id}', 'Tautan\TautanController@delete');
		});
		Route::resource('tautan', 'Tautan\TautanController');
		//end-tautan
		//penghargaan
		Route::prefix('penghargaan')->as('penghargaan')->group(function () {
			Route::get('data', 'Penghargaan\PenghargaanController@data');
			Route::get('delete/{id}', 'Penghargaan\PenghargaanController@delete');
		});
		Route::resource('penghargaan', 'Penghargaan\PenghargaanController');
		//end-penghargaan
		//testimoni
		Route::prefix('testimoni')->as('testimoni')->group(function () {
			Route::get('data', 'Testimoni\TestimoniController@data');
			Route::get('delete/{id}', 'Testimoni\TestimoniController@delete');
			Route::put('{id}/status', 'Testimoni\TestimoniController@updateStatus')->name('.update-status');
		});
		Route::resource('testimoni', 'Testimoni\TestimoniController');
		//end-testimoni

		//unduhan
		Route::prefix('unduhan')->as('unduhan')->group(function () {
			Route::get('data', 'Unduhan\UnduhanController@data');
			Route::get('delete/{id}', 'Unduhan\UnduhanController@delete');
		});
		Route::resource('unduhan', 'Unduhan\UnduhanController');
		//end-unduhan
		//slider
		Route::prefix('slider')->as('slider')->group(function () {
			Route::get('data', 'Slider\SliderController@data');
			Route::get('delete/{id}', 'Slider\SliderController@delete');
		});
		Route::resource('slider', 'Slider\SliderController');
		//end-slider
		//struktur
		Route::prefix('struktur')->as('struktur')->group(function () {
			Route::get('data', 'Struktur\StrukturController@data');
			Route::get('delete/{id}', 'Struktur\StrukturController@delete');
		});
		Route::resource('struktur', 'Struktur\StrukturController');
		//end-struktur
		//galeri
		Route::prefix('galeri')->as('galeri')->group(function () {
			Route::get('data', 'Galeri\GaleriController@data');
			Route::get('delete/{id}', 'Galeri\GaleriController@delete');
		});
		Route::resource('galeri', 'Galeri\GaleriController');
		//end-galeri
		//berita
		Route::prefix('berita')->as('berita')->group(function () {
			Route::get('data', 'Berita\BeritaController@data');
			Route::get('delete/{id}', 'Berita\BeritaController@delete');
		});
		Route::resource('berita', 'Berita\BeritaController');
		//end-berita
		//verifikasi
		Route::prefix('verifikasi')->as('verifikasi')->group(function () {
			Route::get('data', 'Verifikasi\VerifikasiController@data');
			Route::get('delete/{id}', 'Verifikasi\VerifikasiController@delete');
		});
		Route::resource('verifikasi', 'Verifikasi\VerifikasiController');
		//end-verifikasi
		//page
		Route::prefix('page')->as('page')->group(function () {
			Route::get('data', 'Page\PageController@data');
			Route::get('delete/{id}', 'Page\PageController@delete');
		});
		Route::resource('page', 'Page\PageController');
		//end-page
		//pengaturan
		Route::prefix('pengaturan')->as('pengaturan')->group(function () {
			Route::get('data', 'Pengaturan\PengaturanController@data');
			Route::get('delete/{id}', 'Pengaturan\PengaturanController@delete');
		});
		Route::resource('pengaturan', 'Pengaturan\PengaturanController');
		//end-pengaturan
		Route::prefix('tamu')->as('tamu.')->group(function () {

			Route::get('data', 'Tamu\TamuController@data');

			Route::get('delete/{id}', 'Tamu\TamuController@delete');

			Route::get('grafik', 'Tamu\TamuController@grafik')
				->name('grafik');

			// update status tamu
			Route::put('{id}/status', 'Tamu\TamuController@updateStatus')
				->name('update-status');

		});

		Route::resource('tamu', 'Tamu\TamuController');
		//end-tamu
		//kategori
		Route::prefix('kategori')->as('kategori')->group(function () {
			Route::get('data', 'Kategori\KategoriController@data');
			Route::get('delete/{id}', 'Kategori\KategoriController@delete');
		});
		Route::resource('kategori', 'Kategori\KategoriController');
		//end-kategori

		//{{route replacer}} DON'T REMOVE THIS LINE
    });
});
