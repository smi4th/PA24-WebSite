<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackOfficeController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/backoffice')->controller(BackOfficeController::class)->group(function () {

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
