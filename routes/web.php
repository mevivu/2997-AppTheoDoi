<?php

use App\Http\Controllers\InviteController;
use App\Http\Controllers\PublicPageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [PublicPageController::class, 'home'])->name('home');
Route::get('/privacy-policy', [PublicPageController::class, 'privacyPolicy'])->name('privacy-policy');
Route::get('/chinh-sach-bao-mat', [PublicPageController::class, 'privacyPolicy']);

// Trang trung gian link giới thiệu (OneLink af_ios_url) cho trình duyệt nhúng Zalo/Facebook.
Route::get('/invite', [InviteController::class, 'show'])->name('invite');

