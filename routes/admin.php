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

    //Exercises
    Route::controller(App\Admin\Http\Controllers\Exercise\ExerciseController::class)
        ->prefix('/exercises')
        ->as('exercise.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createExercise', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewExercise', 'auth:admin']], function () {
                Route::get('/physical', 'physical')->name('physical');
                Route::get('/power', 'power')->name('power');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:updateExercise', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteExercise', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });
    //WeightHeight
    Route::controller(\App\Admin\Http\Controllers\WeightHeightWho\WeightHeightWhoController::class)
        ->prefix('/weight-height-who')
        ->as('weight-height-who.')
        ->group(function (){
           Route::group(['middleware' => ['permission:createHeightWeight', 'auth:admin']], function () {
              Route::get('/add', 'create')->name('create');
              Route::post('/add','store')->name('store');
           });
           Route::group(["middleware"=>['permission:viewHeightWeight', 'auth:admin']], function () {
              Route::get('/','index')->name('index');
              Route::get('/edit/{id}','edit')->name('edit');
           });
           Route::group(['middleware' => ['permission:updateHeightWeight', 'auth:admin']], function () {
               Route::put('/edit', 'update')->name('update');
               Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
           });
           Route::group(['middleware' => ['permission:deleteHeightWeight', 'auth:admin']], function () {
               Route::delete('/delete/{id}', 'delete')->name('delete');
           });
        });
    //Bmi
    Route::controller(App\Admin\Http\Controllers\Bmi\BmiController::class)
        ->prefix('/bmi')
        ->as('bmi.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createBMI', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewBMI', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateBMI', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteBMI', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Package
    Route::controller(App\Admin\Http\Controllers\Package\PackageController::class)
        ->prefix('/packages')
        ->as('package.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createPackage', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewPackage', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updatePackage', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deletePackage', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Bmi
    Route::controller(App\Admin\Http\Controllers\VaccinationSchedule\VaccinationScheduleController::class)
        ->prefix('/vaccination-schedule')
        ->as('vaccination.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createVaccinationSchedule', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewVaccinationSchedule', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateVaccinationSchedule', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteVaccinationSchedule', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Question group
    Route::controller(App\Admin\Http\Controllers\QuestionGroup\QuestionGroupController::class)
        ->prefix('/question-group')
        ->as('question-group.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createQuestionGroup', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewQuestionGroup', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateQuestionGroup', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteQuestionGroup', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Question
    Route::controller(App\Admin\Http\Controllers\Question\QuestionController::class)
        ->prefix('/question')
        ->as('question.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createQuestion', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewQuestion', 'auth:admin']], function () {
                Route::get('/iq', 'iq')->name('iq');
                Route::get('/aq', 'aq')->name('aq');
                Route::get('/eq', 'eq')->name('eq');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateQuestion', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteQuestion', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Notification
    Route::controller(App\Admin\Http\Controllers\Notification\NotificationController::class)
        ->prefix('/thong-bao')
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

    //Clinic Type
    Route::controller(App\Admin\Http\Controllers\ClinicType\ClinicTypeController::class)
        ->prefix('/clinic-types')
        ->as('clinicType.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createClinicType', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewClinicType', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateClinicType', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteClinicType', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });


    //Clinic
    Route::controller(App\Admin\Http\Controllers\Clinic\ClinicController::class)
        ->prefix('/clinics')
        ->as('clinic.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createClinic', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewClinic', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateClinic', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteClinic', 'auth:admin']], function () {
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
    Route::prefix('/quyen')->as('permission.')->group(function () {
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
    Route::prefix('/vai-tro')->as('role.')->group(function () {
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
        ->prefix('/cai-dat')
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
    Route::prefix('/thanh-vien')->as('user.')->group(function () {
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

    //children
    Route::prefix('/quan-ly-tre-em')->as('children.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Children\ChildrenController::class)->group(function () {
            Route::group(['middleware' => ['permission:createChildren', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewChildren', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
                Route::post('/multiple', 'actionMultipleRecode')->name('multiple');

            });

            Route::group(['middleware' => ['permission:updateChildren', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteChildren', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });

    });

    //admin
    Route::prefix('/quan-tri')->as('admin.')->group(function () {
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
        ->prefix('/thong-tin-ca-nhan')
        ->as('profile.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
        });

    Route::controller(App\Admin\Http\Controllers\Auth\ChangePasswordController::class)
        ->prefix('/mat-khau')
        ->as('password.')
        ->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
        });
    Route::prefix('/tim-kiem')->as('search.')->group(function () {
        Route::prefix('/select')->as('select.')->group(function () {
            Route::get('/user', [App\Admin\Http\Controllers\User\UserSearchSelectController::class, 'selectSearch'])->name('user');
            Route::get('/province', [App\Admin\Http\Controllers\Province\ProvinceSearchSelectController::class, 'selectSearch'])->name('province');
            Route::get('/district', [App\Admin\Http\Controllers\District\DistrictSearchSelectController::class, 'selectSearch'])->name('district');
            Route::get('/ward', [App\Admin\Http\Controllers\Ward\WardSearchSelectController::class, 'selectSearch'])->name('ward');
            Route::get('/clinic-types', [App\Admin\Http\Controllers\ClinicType\ClinicTypeSearchSelectController::class, 'selectSearch'])->name('clinicType');
        });
    });

    Route::post('/logout', [App\Admin\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');
});
