<?php

use App\Api\V2\Http\Controllers\Auth\AuthController;
use App\Api\V2\Http\Controllers\RatingPQ\RatingPQController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V2 Routes
|--------------------------------------------------------------------------
|
| Định nghĩa các routes cho phiên bản API V2.
| Các endpoints V2 được định nghĩa trước để ưu tiên so với các wildcard route của V1,
| đồng thời kế thừa các routes còn lại từ V1.
|
*/

// Kế thừa toàn bộ các routes từ v1.php trước
require base_path('routes/api/v1.php');

// Rating PQ V2: Endpoint tổng hợp thông tin đánh giá thể chất (gộp 1 API)
Route::prefix('rating-pqs')->controller(RatingPQController::class)
    ->group(function () {
        Route::get('/general-info', 'getGeneralInfo');
    });

// Auth V2: Ghi đè endpoint login để trỏ tới V2 AuthController
Route::prefix('auth')->controller(AuthController::class)
    ->group(function () {
        Route::post('/login', 'login');
    });
