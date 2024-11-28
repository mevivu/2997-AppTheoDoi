<?php


use Illuminate\Support\Facades\Route;

Route::view('/', 'stores.index');


Route::group(['middleware' => 'store.auth.store:store'], function () {




});

