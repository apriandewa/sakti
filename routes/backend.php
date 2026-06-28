<?php
use Illuminate\Support\Facades\Route;
Route::group([
    'prefix' => config('master.app.url.backend'),
    'middleware' => ['auth', 'verified']
], function () {
    //public route
    Route::resource('dashboard', "Dashboard\DashboardController")->name('index', 'dashboard');
    Route::get('/list-menu', "Menu\MenuController@listMenu")->name('menu.list-menu');
    Route::get('announcement-detail/{id}/{slug}', "Announcement\AnnouncementController@detail")->name('announcement');

    Route::get('sidebar-notification', 'Notification\NotificationController@getSideBarNotification');
    Route::get('get-notification', 'Notification\NotificationController@getNotification');
    Route::get('clear-notification', 'Notification\NotificationController@markAsRead');
    Route::post('logout','Auth\AuthController@logout')->name('logout');
    //end public route

    Route::prefix('file')->as('file')->group(function () {
        Route::get('stream/{id}/{name}', "File\FileController@getFile");
        Route::get('download/{id}/{name}', "File\FileController@downloadFile");
        Route::get('delete/{id}/{name}', "File\FileController@deleteFile");
        Route::delete('delete/{id}/{name}', "File\FileController@deleteFile");
        Route::post('upload-image-editor','File\FileController@handleEditorImageUpload');
    });
    Route::group(['middleware'=>['userRoles']], function () {
        //user
        Route::prefix('user')->as('user')->group(function () {
            Route::get('data', "User\UserController@data");
            Route::get('delete/{id}', "User\UserController@delete");
        });
        Route::resource('user', "User\UserController");
        //end-user
        //menu
        Route::post('/sorted', "Menu\MenuController@sorted")->name('menu.sorted');
        Route::prefix('menu')->as('menu')->group(function () {
            Route::get('/data', "Menu\MenuController@data");
            Route::get('delete/{id}', "Menu\MenuController@delete");
        });
        Route::resource('menu', "Menu\MenuController");
        //end-menu
        //access-group
        Route::prefix('access-group')->as('access-group')->group(function () {
            Route::get('data', "AccessGroup\AccessGroupController@data");
            Route::get('delete/{id}', "AccessGroup\AccessGroupController@delete");
        });
        Route::resource('access-group', "AccessGroup\AccessGroupController");
        //end-access-group
        //level
        Route::prefix('level')->as('level')->group(function () {
            Route::get('data', "Level\LevelController@data");
            Route::get('delete/{id}', "Level\LevelController@delete");
        });
        Route::resource('level', "Level\LevelController");
        //end-level
        //log
        Route::prefix('log')->as('log')->group(function () {
            Route::get('data', "Log\LogController@data");
            Route::get('delete/{id}', "Log\logController@delete");
        });
        Route::resource('log', "Log\LogController");
        //end-log
        //access-menu
        Route::prefix('access-menu')->as('access-menu')->group(function () {
            Route::get('data', "AccessMenu\AccessMenuController@data");
            Route::get('delete/{id}', "AccessMenu\AccessMenuController@delete");
        });
        Route::resource('access-menu', "AccessMenu\AccessMenuController");
        //end-access-menu

    	//announcement
		Route::prefix('announcement')->as('announcement')->group(function () {
			Route::get('data', 'Announcement\AnnouncementController@data');
			Route::get('delete/{id}', 'Announcement\AnnouncementController@delete');
		});
		Route::resource('announcement', 'Announcement\AnnouncementController');
		//end-announcement

		//pangkat
		Route::prefix('pangkat')->as('pangkat')->group(function () {
			Route::get('data', 'Pangkat\PangkatController@data');
			Route::get('delete/{id}', 'Pangkat\PangkatController@delete');
		});
		Route::resource('pangkat', 'Pangkat\PangkatController');
		//end-pangkat

		//status-pegawai
		Route::prefix('status-pegawai')->as('status-pegawai')->group(function () {
			Route::get('data', 'StatusPegawai\StatusPegawaiController@data');
			Route::get('delete/{id}', 'StatusPegawai\StatusPegawaiController@delete');
		});
		Route::resource('status-pegawai', 'StatusPegawai\StatusPegawaiController');
		//end-status-pegawai

		//jabatan
		Route::prefix('jabatan')->as('jabatan')->group(function () {
			Route::get('data', 'Jabatan\JabatanController@data');
			Route::get('delete/{id}', 'Jabatan\JabatanController@delete');
		});
		Route::resource('jabatan', 'Jabatan\JabatanController');
		//end-jabatan

		//pegawai
		Route::prefix('pegawai')->as('pegawai.')->group(function () {
			Route::get('data', 'Pegawai\PegawaiController@data')->name('data');
			Route::get('delete/{id}', 'Pegawai\PegawaiController@delete')->name('delete');
			Route::get('get-jabatan-nama/{parent_id}', 'Pegawai\PegawaiController@getJabatanNama')->name('get-jabatan-nama');
		});
		Route::resource('pegawai', 'Pegawai\PegawaiController');
		//end-pegawai

		//presensi
		Route::prefix('presensi')->as('presensi.')->group(function () {
			Route::get('data', 'Presensi\PresensiController@data')->name('data');
			Route::post('sync', 'Presensi\PresensiController@sync')->name('sync');
			Route::get('show/{pegawai_id}/{month}/{year}', 'Presensi\PresensiController@showDetail')->name('show-detail');
			Route::get('image/{nip}', 'Presensi\PresensiController@image')->name('image');
			Route::get('riwayat/{nip}/{date}', 'Presensi\PresensiController@riwayatDetail')->name('riwayat-detail');
			Route::get('sync-logs', 'Presensi\PresensiController@syncLogs')->name('sync-logs');
		});
		Route::resource('presensi', 'Presensi\PresensiController');
		//end-presensi

        //notification
		Route::prefix('notification')->as('notification')->group(function () {
			Route::get('data', 'Notification\NotificationController@data');
			Route::get('delete/{id}', 'Notification\NotificationController@delete');
		});
		Route::resource('notification', 'Notification\NotificationController');
		//end-notification

        //documentation
        Route::prefix('documentation')->as('documentation.')->group(function () {
            Route::get('prd-portal', 'Documentation\DocumentationController@prdPortal')->name('prd-portal');
            Route::get('prd-presensi', 'Documentation\DocumentationController@prdPresensi')->name('prd-presensi');
            Route::get('plan-portal', 'Documentation\DocumentationController@planPortal')->name('plan-portal');
            Route::get('plan-presensi', 'Documentation\DocumentationController@planPresensi')->name('plan-presensi');
            Route::get('slides', 'Documentation\DocumentationController@slides')->name('slides');
            Route::get('manual-book', 'Documentation\DocumentationController@manualBook')->name('manual-book');
        });
        //end-documentation
	});
});


