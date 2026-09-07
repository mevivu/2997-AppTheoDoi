<?php

use App\Api\V2\Http\Controllers\Auth\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API V2 Routes
|--------------------------------------------------------------------------
|
| Định nghĩa các routes cho phiên bản API V2.
| Endpoint đăng nhập V2 ghi đè lên endpoint login của V1 để áp dụng logic
| kiểm tra thiết bị mới, đồng thời kế thừa các routes còn lại từ V1.
|
*/

// Kế thừa toàn bộ các routes từ v1.php
require base_path('routes/api/v1.php');

// Auth V2: Ghi đè endpoint login để trỏ tới V2 AuthController
Route::prefix('auth')->controller(AuthController::class)
    ->group(function () {
        Route::post('/login', 'login');
    });

