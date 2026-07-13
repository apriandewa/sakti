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

		/* DISABLED: AgendaRapat and VerifikasiRapat features depend on Pegawai model which has been removed
		//agenda-rapat
		Route::prefix('agenda-rapat')->as('agenda-rapat')->group(function () {
			Route::get('data', 'AgendaRapat\AgendaRapatController@data');
			Route::get('delete/{id}', 'AgendaRapat\AgendaRapatController@delete');
			Route::post('{id}/kirim', 'AgendaRapat\AgendaRapatController@kirim')->name('.kirim');
			Route::post('check-konflik', 'AgendaRapat\AgendaRapatController@checkKonflik')->name('.check-konflik');
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

			// Tanda Tangan Elektronik (BSrE)
			Route::post('{id}/sign/{jenis}', 'AgendaRapat\AgendaRapatController@signDokumen')->name('.sign-dokumen');
			Route::get('{id}/download-signed/{jenis}', 'AgendaRapat\AgendaRapatController@downloadSigned')->name('.download-signed');

			// Peserta (daftar hadir) management
			Route::get('peserta/{pesertaId}/edit', 'AgendaRapat\AgendaRapatController@editPeserta')->name('.peserta.edit');
			Route::put('peserta/{pesertaId}', 'AgendaRapat\AgendaRapatController@updatePeserta')->name('.peserta.update');
			Route::get('peserta/delete/{pesertaId}', 'AgendaRapat\AgendaRapatController@deletePeserta')->name('.peserta.delete');
			Route::delete('peserta/{pesertaId}', 'AgendaRapat\AgendaRapatController@destroyPeserta')->name('.peserta.destroy');
		});
		Route::resource('agenda-rapat', 'AgendaRapat\AgendaRapatController');
		//end-agenda-rapat

		//verifikasi-rapat
		Route::prefix('verifikasi-rapat')->as('verifikasi-rapat')->group(function () {
			Route::get('data', 'VerifikasiRapat\VerifikasiRapatController@data');
		});
		Route::resource('verifikasi-rapat', 'VerifikasiRapat\VerifikasiRapatController');
		//end-verifikasi-rapat
		*/

		//presensi
		Route::prefix('presensi')->as('presensi.')->group(function () {
			Route::get('data', 'Presensi\PresensiController@data')->name('data');
			Route::get('kantor', 'Presensi\PresensiController@kantor')->name('kantor');
			Route::get('{id}/show', 'Presensi\PresensiController@show')->name('show');
			Route::post('sync', 'Presensi\PresensiController@sync')->name('sync');
			Route::post('sync-pegawai', 'Presensi\PresensiController@syncPegawai')->name('sync-pegawai');
			Route::get('foto', 'Presensi\PresensiController@fotoPresensi')->name('foto');
			Route::post('import-csv', 'Presensi\PresensiController@importCsv')->name('import-csv');
		});
		Route::resource('presensi', 'Presensi\PresensiController')->only(['index']);
		//end-presensi

		//{{route replacer}} DON'T REMOVE THIS LINE
    });
});
