<?php

use App\Http\Controllers\Login\LoginController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::controller(App\Http\Controllers\Home\HomeController::class)
    ->prefix('/')
    ->as('home')
    ->group(function(){
//        Route::get('/', 'index')->name('index')->middleware('signed');

        Route::get('/', 'index')->name('index');

    });


Route::controller(LoginController::class)
    ->prefix('/login')
    ->as('login')
    ->group(function(){
        Route::get('/', 'index')->name('index');

    });

