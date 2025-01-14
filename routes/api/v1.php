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
        Route::put('/update-device-token', 'updateDeviceToken')->name('updateToken');

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
        Route::delete('/{id}', 'delete');
    });
Route::controller(App\Api\V1\Http\Controllers\Support\SupportController::class)
    ->prefix('/supports')
    ->as('support.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
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
Route::controller(\App\Api\V1\Http\Controllers\Classes\ClassesController::class)
    ->prefix("/class")
    ->as('classes.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/subject/{id}', 'findSubjectsByClasses');
    });

// Package
Route::controller(\App\Api\V1\Http\Controllers\Package\PackageController::class)
    ->prefix('/packages')
    ->as('package.')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/buy', 'purchasePackage');
    });

// Journal
Route::controller(\App\Api\V1\Http\Controllers\Journal\JournalController::class)
    ->prefix('/journals')
    ->as('journal.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::post('/update', 'update');
        Route::delete('/{id}', 'delete');
    });
//Quality
Route::controller(\App\Api\V1\Http\Controllers\Quality\QualityController::class)
    ->prefix('/qualities')
    ->as('quality.')
    ->group(function () {
        Route::get('/', 'index');
    });
//Capabilities
Route::controller(\App\Api\V1\Http\Controllers\Capability\CapabilityController::class)
    ->prefix('/capabilities')
    ->as('capability.')
    ->group(function () {
        Route::get('/', 'index');
    });
// pregnancy
Route::controller(\App\Api\V1\Http\Controllers\Pregnancy\PregnancyController::class)
    ->prefix('/pregnancy')
    ->as('pregnancy.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::post('/update', 'update');
        Route::delete('/{id}', 'delete');
    });

// Rating
Route::controller(\App\Api\V1\Http\Controllers\Rating\RatingController::class)
    ->prefix('/ratings')
    ->as('rating.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/iq', 'storeIQ');
        Route::post('/eq-aq', 'storeEQAndAQ');
        Route::delete('/{id}', 'delete');
    });

// ChildEvaluation
Route::controller(\App\Api\V1\Http\Controllers\ChildEvaluation\ChildEvaluationController::class)
    ->prefix('/child-evaluations')
    ->as('childEvaluation.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/search', 'search');
        Route::get('/info', 'findByClassGrade');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::put('/', 'update');
    });

// Vaccination Schedule
Route::controller(\App\Api\V1\Http\Controllers\VaccinationSchedule\VaccinationScheduleController::class)
    ->prefix('/vaccination-schedule')
    ->as('vaccinationSchedule.')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{id}', 'show');
        Route::post('/', 'store');
        Route::post('/update', 'update');
        Route::delete('/{id}', 'delete');
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

//***** -- Quiz -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\Quiz\QuizController::class)
    ->prefix('/quizzes')
    ->as('quiz.')
    ->group(function () {
        Route::get('/iq', 'getListIQ');
        Route::get('/', 'getListAQAndEQ');
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

//***** -- Product -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\Product\ProductController::class)
    ->prefix('/products')
    ->as('product.')
    ->group(function () {
        Route::get('/', 'index'); // Lấy danh sách sản phẩm
        Route::get('/{id}', 'show'); // Lấy chi tiết sản phẩm
    });

//***** -- Guide -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\Guide\GuideController::class)
    ->prefix('/guides')
    ->as('guide.')
    ->group(function () {
        Route::get('/', 'index'); // Lấy danh sách bài hướng dẫn
        Route::get('/{id}', 'show'); // Lấy chi tiết bài hướng dẫn
    });

//***** -- Product_catalog -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\ProductCatalog\ProductCatalogController::class)
    ->prefix('/product-catalogs')
    ->as('productCatalog.')
    ->group(function () {
        Route::get('/', 'index'); // Lấy danh sách danh mục sản phẩm
    });

//***** -- Brand -- ******* //
Route::controller(\App\Api\V1\Http\Controllers\Brand\BrandController::class)
    ->prefix('/brands')
    ->as('brand.')
    ->group(function () {
        Route::get('/', 'index'); // Lấy danh sách các thương hiệu
    });
