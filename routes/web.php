<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackOfficeController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckIfAuth;
use App\Http\Middleware\CheckIfStaff;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PrestationController;

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

Route::prefix('/backoffice')->middleware(CheckIfAuth::class,CheckIfStaff::class)->controller(BackOfficeController::class)->group(function () {

    Route::get('/', 'index');
    Route::get('/statistics', 'statistics');
    Route::get('/suggests', 'suggests');

    Route::get('/users', 'users');
    Route::get('/staff', 'staff');

    Route::get('/users/{id}/edit/{information}', 'updateUser');
    Route::get('/users/{id}/delete', 'deleteUser');

    Route::get('/prestations-companies', 'prestationsCompanies');
    Route::get('/providers', 'providers');
    Route::get('/supports', 'supports');
    Route::get('/permissions', 'permissions');
    Route::get('/settings', 'settings');

    Route::get('/{any}','index')->where('any', '.*');
});

Route::prefix('/travel')->controller(LocationController::class)->middleware(CheckIfAuth::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{id}', 'showLocation');
    Route::get('/reservation/{id}', 'showReservation');
    Route::post('/reservation/{id}', 'doReservationLocation');
    Route::get('/{any}','index')->where('any', '.*');
});

/** TODO */
Route::prefix('/prestations')->middleware(CheckIfAuth::class)->controller(PrestationController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{type}', 'showPrestation');
    Route::get('/{type}/{id}', 'showSubPrestation');
    Route::post('/{type}/{id}/reservation', 'doReservationPrestation');
    Route::get('/{any}','index')->where('any', '.*');
});

Route::prefix('/profile')->middleware(CheckIfAuth::class)->controller(ProfileController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{any}','index')->where('any', '.*');
});
