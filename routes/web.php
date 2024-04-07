<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackOfficeController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/backoffice')->controller(BackOfficeController::class)->group(function () {
    Route::get('/', 'index');
});
