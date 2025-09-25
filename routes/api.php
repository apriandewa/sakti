<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

// Route::post('/login', 'Auth\LoginController@login')->name('login');
Route::group(['middleware'=>['auth:sanctum']], function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});