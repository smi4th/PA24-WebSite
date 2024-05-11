<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackOfficeController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckIfAuth;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InfoController;

Route::get('/', function () {
    return view('landing');
});
/*
Route::get('/error',function () {
    return view('error');
});
*/
Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login', [AuthController::class, 'checkLogin']);

Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/register', [AuthController::class, 'checkRegister']);

Route::prefix('/backoffice')->middleware(CheckIfAuth::class)->controller(BackOfficeController::class)->group(function () {

    Route::get('/', 'index');
    Route::get('/statistics', 'statistics');
    Route::get('/suggests', 'suggests');
    Route::get('/travelers', 'travelers');
    Route::get('/prestations', 'prestations');
    Route::get('/prestations-companies', 'prestationsCompanies');
    Route::get('/providers', 'providers');
    Route::get('/supports', 'supports');
    Route::get('/permissions', 'permissions');
    Route::get('/settings', 'settings');

    Route::get('/{any}','index')->where('any', '.*');
});

Route::get('/main_travel_page', function () {
    return view('main_travel_page');
});

Route::prefix('/profile')->middleware(CheckIfAuth::class)->controller(ProfileController::class)->group(function () {
    Route::get('/', 'showProfile')->name('profile');
    route::post('/', 'updateProfile')->name('update_profile');
    Route::get('/edit-profile/{inputName}', 'editProfile')->name('edit_profile');
    Route::post('/profile/upload', 'uploadProfileImage')->name('profile.upload');
    Route::get('/{any}','index')->where('any', '.*');
});

Route::prefix('/message')->middleware(CheckIfAuth::class)->controller(MessageController::class)->group(function () {
    Route::get('/', 'index')->name('message');
    route::post('/send-message', 'sendMessage')->name('send_message');
    Route::get('/{any}','index')->where('any', '.*');
});

Route::prefix('/info')->controller(InfoController::class)->group(function () {
    Route::get('/confidentialite', 'showConfidentialite')->name('confidentialite');
    Route::get('/cookies', 'showCookies')->name('cookies');
    Route::get('/mentions-legales', 'showMentionsLegales')->name('mentions_legales');
});
