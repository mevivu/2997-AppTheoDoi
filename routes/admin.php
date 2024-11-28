<?php

use Illuminate\Support\Facades\Route;

Route::get('/', [App\Admin\Http\Controllers\Home\HomeController::class, 'index']);

// login
Route::controller(App\Admin\Http\Controllers\Auth\LoginController::class)
    ->middleware('guest:admin')
    ->prefix('/login')
    ->as('login.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'login')->name('post');
    });

Route::group(['middleware' => 'admin.auth.admin:admin'], function () {


    //Notification
    Route::controller(App\Admin\Http\Controllers\Notification\NotificationController::class)
        ->prefix('/notifications')
        ->as('notification.')
        ->group(function () {
            Route::get('/not-read-admin', 'getNotificationsForAdmin')->name('getNotificationAdmin');
            Route::patch('/status', 'updateStatus')->name('status');
            Route::post('/update-device-token', 'updateDeviceToken')->name('updateDeviceToken');


            Route::group(['middleware' => ['permission:createNotification', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewNotification', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/multiple', 'actionMultipleRecode')->name('multiple');
            });

            Route::group(['middleware' => ['permission:updateNotification', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteNotification', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });



    //***** -- Module -- ******* //
    Route::prefix('/module')->as('module.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Module\ModuleController::class)->group(function () {
            Route::get('/them', 'create')->name('create');
            Route::get('/', 'index')->name('index');
            Route::get('/summary', 'summary')->name('summary');
            Route::get('/sua/{id}', 'edit')->name('edit');
            Route::put('/sua', 'update')->name('update');
            Route::post('/them', 'store')->name('store');
            Route::post('/multiple', 'actionMultipleRecord')->name('multiple');
            Route::delete('/xoa/{id}', 'delete')->name('delete');
        });
    });
    //***** -- Module -- ******* //

    //***** -- Permission -- ******* //
    Route::prefix('/permission')->as('permission.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Permission\PermissionController::class)->group(function () {
            Route::get('/them', 'create')->name('create');
            Route::get('/', 'index')->name('index');
            Route::get('/sua/{id}', 'edit')->name('edit');
            Route::put('/sua', 'update')->name('update');
            Route::post('/them', 'store')->name('store');
            Route::post('/multiple', 'actionMultipleRecord')->name('multiple');
            Route::delete('/xoa/{id}', 'delete')->name('delete');
        });
    });
    //***** -- Permission -- ******* //

    //***** -- Role -- ******* //
    Route::prefix('/role')->as('role.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Role\RoleController::class)->group(function () {

            Route::group(['middleware' => ['permission:createRole', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewRole', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateRole', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteRole', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });
    //***** -- Role -- ******* //

    //Settings
    Route::controller(App\Admin\Http\Controllers\Setting\SettingController::class)
        ->prefix('/settings')
        ->as('setting.')
        ->group(function () {
            Route::group(['middleware' => ['permission:settingGeneral', 'auth:admin']], function () {
                Route::get('/general', 'general')->name('general');
                Route::get('/systems', 'system')->name('system');
                Route::get('/c_rides', 'c_ride')->name('c_ride');
                Route::get('/c_cars', 'c_car')->name('c_car');
                Route::get('/c_deliverys', 'c_delivery')->name('c_delivery');
                Route::get('/c_intercitys', 'c_intercity')->name('c_intercity');
                Route::get('/c_multi', 'c_multi')->name('c_multi');
            });

            Route::get('/user-shopping', 'userShopping')->name('user_shopping');
            Route::put('/update', 'update')->name('update');
        });



    //user
    Route::prefix('/users')->as('user.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\User\UserController::class)->group(function () {
            Route::group(['middleware' => ['permission:createUser', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewUser', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/{id}/history', 'history')->name('history');
                Route::get('/sua/{id}', 'edit')->name('edit');
                Route::post('/multiple', 'actionMultipleRecode')->name('multiple');

            });

            Route::group(['middleware' => ['permission:updateUser', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteUser', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });

    });

    //admin
    Route::prefix('/admins')->as('admin.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Admin\AdminController::class)->group(function () {
            Route::group(['middleware' => ['permission:createAdmin', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewAdmin', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateAdmin', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteAdmin', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //ckfinder
    Route::prefix('/quan-ly-file')->as('ckfinder.')->group(function () {
        Route::any('/ket-noi', '\CKSource\CKFinderBridge\Controller\CKFinderController@requestAction')
            ->name('connector');
        Route::any('/duyet', '\CKSource\CKFinderBridge\Controller\CKFinderController@browserAction')
            ->name('browser');
    });

    Route::get('/dashboard', [App\Admin\Http\Controllers\Dashboard\DashboardController::class, 'index'])->name('dashboard');

    //auth
    Route::controller(App\Admin\Http\Controllers\Auth\ProfileController::class)
        ->prefix('/profile')
        ->as('profile.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
        });

    Route::controller(App\Admin\Http\Controllers\Auth\ChangePasswordController::class)
        ->prefix('/password')
        ->as('password.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
        });
    Route::prefix('/search')->as('search.')->group(function () {
        Route::prefix('/select')->as('select.')->group(function () {
            Route::get('/user', [App\Admin\Http\Controllers\User\UserSearchSelectController::class, 'selectSearch'])->name('user');
        });
    });

    Route::post('/logout', [App\Admin\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');
});
