<?php

use App\Api\V1\Http\Controllers\Auth\AuthController;
use App\Api\V1\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//notification
Route::controller(App\Api\V1\Http\Controllers\Notification\NotificationController::class)
    ->prefix('/notifications')
    ->as('notification.')
    ->group(function () {
        Route::get('/', 'index');
    });


//auth
Route::prefix('auth')->controller(AuthController::class)
    ->group(function () {
        Route::post('/login', 'login');
        Route::get('/', 'show');
        Route::post('/verification-otp', 'verificationOtp');
        Route::post('/resend-otp', 'resendOtp');
        Route::put('/update-password', 'forgotPassword');
        Route::put('/change-password', 'updatePassword');
        Route::put('/update-email', 'updateEmail');
    });

//auth
Route::prefix('users')->controller(UserController::class)
    ->group(function () {
        Route::get('/configuration', 'configuration');
        Route::post('/register', 'register');
        Route::post('/update', 'update');
        Route::get('/recent-location', 'getRecentLocation');
    });



Route::fallback(function () {
    return response()->json([
        'status' => 404,
        'message' => __('Không tìm thấy đường dẫn.')
    ], 404);
});


//***** -- Setting -- ******* //
Route::controller(App\Api\V1\Http\Controllers\Setting\SettingController::class)
    ->prefix('/settings')
    ->as('setting.')
    ->group(function () {
        Route::get('/general', 'general')->name('general');
        Route::get('/system', 'system')->name('system');
        Route::get('/c-ride', 'c_ride')->name('c_ride');
        Route::get('/c-car', 'c_car')->name('c_car');
        Route::get('/c-delivery', 'c_delivery')->name('c_delivery');
        Route::get('/c-intercity', 'c_intercity')->name('c_intercity');
    });
