<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackOfficeController;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\CheckIfAuth;
use App\Http\Middleware\CheckIfStaff;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\InfoController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\PrestationController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\ChatBotController;

Route::get('/', function () {
    return view('landing');
});
/*
Route::get('/error',function () {
    return view('error');
});
*/

Route::middleware(CheckIfStaff::class)->group(function () {
    Route::get('/chatbot/admin', [ChatBotController::class, 'adminIndex']);
    Route::post('/chatbot/admin', [ChatBotController::class, 'store']);
    Route::put('/chatbot/admin', [ChatBotController::class, 'update']);;
    Route::delete('/chatbot/admin/{uuid}', [ChatBotController::class, 'destroy']);
});


Route::get('/login', [AuthController::class, 'login'])->name('auth.login');
Route::post('/login', [AuthController::class, 'checkLogin']);

Route::get('/logout', [AuthController::class, 'logout'])->name('auth.logout');

Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
Route::post('/register', [AuthController::class, 'checkRegister']);

Route::post('/delete-account', [AuthController::class, 'deleteAccount'])->name('auth.delete');

Route::post('/delete-account', [AuthController::class, 'deleteAccount'])->name('auth.delete');

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
    Route::get('/createLocation', 'showCreateLocation');
    Route::post('/createLocation', 'doCreateLocation');
    Route::get('/{id}', 'showLocation');
    Route::get('/{id}/delete', 'removeLocation')->middleware(CheckIfStaff::class);
    Route::get('/{id}/approuve', 'approuveLocation')->middleware(CheckIfStaff::class);
    Route::get('/reservation/{id}', 'showReservation');
    Route::post('/reservation/{id}', 'doReservationLocation');
    Route::get('/{any}','index')->where('any', '.*');
});

Route::prefix('/prestations')->middleware(CheckIfAuth::class)->controller(PrestationController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/{type}', 'showSubPrestation');
    Route::get('/{type}/{id}', 'showPrestation');
    Route::post('/{type}/{id}/reservation', 'doReservationPrestation');
    Route::get('/{any}','index')->where('any', '.*');
});

Route::prefix('/basketPayment')->middleware(CheckIfAuth::class)->controller(StripePaymentController::class)->group(function () {
    Route::get('/', 'checkout');
    Route::get('/subscription', 'subscription');
    Route::get('/successSubscription', 'successSubscription');
    Route::post('/webhook', 'webhook');
    Route::get('/success', 'successPayment')->name('success');
    Route::get('/cancel', 'cancel')->name('cancel');
    Route::get('/{any}','index')->where('any', '.*');
});

Route::prefix('/demandSupport')->middleware(CheckIfAuth::class)->controller(TicketController::class)->group(function () {
    Route::get('/', 'showDemandSupport');
    Route::post('/createDemand', 'doDemandSupport')->name('createDemand');
});

Route::prefix('/profile')->middleware(CheckIfAuth::class)->controller(ProfileController::class)->group(function () {
    Route::get('/', 'showProfile')->name('profile');
    route::post('/', 'updateProfile')->name('update_profile');
    Route::get('/edit-profile/{inputName}', 'editProfile')->name('edit_profile');
    Route::post('/profile/upload', 'uploadProfileImage')->name('profile.upload');
    Route::get('/ticket/{id}', 'showTicket');
    Route::get('/{any}','index')->where('any', '.*');

});

Route::prefix('/tickets')->middleware(CheckIfAuth::class)->controller(TicketController::class)->group(function () {
    Route::get('/', 'showTickets');
    Route::post('/addTicket', 'addTickets');
});

Route::prefix('/reviews')->middleware(CheckIfAuth::class)->controller(ProfileController::class)->group(function () {
    Route::get('/', 'showReviews');
    Route::post('/addReview', 'addReviews');
    Route::get('/{id}/delete', 'removeReviews')->middleware(CheckIfStaff::class);
});

Route::prefix('/planning')->middleware(CheckIfAuth::class)->controller(PlanningController::class)->group(function () {
    Route::get('/', 'showPlanning')->name('planning');
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
