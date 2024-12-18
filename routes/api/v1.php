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
        Route::post('/register', 'register');
        Route::post('/update', 'update');
    });

//notification
Route::controller(App\Api\V1\Http\Controllers\Notification\NotificationController::class)
    ->prefix('/notifications')
    ->as('notification.')
    ->group(function () {
        Route::get('/get-all', 'index');
        Route::get('/{id}', 'detail');
        Route::put('/read', 'updateStatusRead');
        Route::post('/read-all', 'updateAllStatusReadAll');
        Route::delete('delete', 'delete');
    });

// Assessment
Route::controller(\App\Api\V1\Http\Controllers\Assessment\AssessmentController::class)
    ->prefix('/assessment')
    ->as('assessment.')
    ->group(function () {
        Route::get('/', 'index');

    });
// Child
Route::controller(\App\Api\V1\Http\Controllers\Child\ChildController::class)
    ->prefix('/children')
    ->as('child.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::post('/update', 'update');
        Route::delete('/{id}', 'delete');

    });

// Package
Route::controller(\App\Api\V1\Http\Controllers\Package\PackageController::class)
    ->prefix('/packages')
    ->as('package.')
    ->group(function () {
        Route::get('/', 'index');

    });

// Journal
Route::controller(\App\Api\V1\Http\Controllers\Journal\JournalController::class)
    ->prefix('/journals')
    ->as('journal.')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');

    });

// Exercise
Route::controller(\App\Api\V1\Http\Controllers\Exercise\ExerciseController::class)
    ->prefix('/exercises')
    ->as('exercise.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/detail/{id}', 'detail');
    });

//***** -- Clinic -- ******* //
Route::controller(App\Api\V1\Http\Controllers\Clinic\ClinicController::class)
    ->prefix('/clinics')
    ->as('clinic.')
    ->group(function () {
        Route::get('/search', 'search');
    });


//***** -- Question -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\Question\QuestionController::class)
    ->prefix('/questions')
    ->as('question.')
    ->group(function () {
        Route::get('/', 'index');
    });

//***** -- BMI -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\BMI\BMIController::class)
    ->prefix('/bmis')
    ->as('bmi.')
    ->group(function () {
        Route::get('/', 'index');
    });

//***** -- Post -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\Post\PostController::class)
    ->prefix('/posts')
    ->as('post.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
    });

//***** -- Slider -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\Slider\SliderController::class)
    ->prefix('/sliders')
    ->as('slider.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
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
        Route::get('/general', 'general');
        Route::get('/system', 'system');
    });