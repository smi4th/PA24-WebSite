<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackOfficeController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckIfAuth;

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

Route::get('/profile', function () {
    return view('profile.main_profile');
})->middleware(CheckIfAuth::class);

