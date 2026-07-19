<?php

use App\Admin\Http\Controllers\Transaction\TransactionController;
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

    Route::get('/thong-ke-firebase', [App\Admin\Http\Controllers\FirebaseReport\FirebaseReportController::class, 'index'])->name('firebase.report');

    //WeightHeight
    Route::controller(\App\Admin\Http\Controllers\ProductCatalog\ProductCatalogController::class)
        ->prefix('/product_catalog')
        ->as('category.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createProductCatalog', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(["middleware" => ['permission:viewProductCatalog', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });
            Route::group(['middleware' => ['permission:updateProductCatalog', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });
            Route::group(['middleware' => ['permission:deleteProductCatalog', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    Route::controller(\App\Admin\Http\Controllers\Address\AddressController::class)
        ->prefix('/address')
        ->as('address.')
        ->group(function () {
            Route::group(['middleware' => ['auth:admin']], function () {
                Route::get('/export-province', 'exportProvince')->name('exportProvince');
                Route::get('/export-ward', 'exportWard')->name('exportWard');
            });
        });

    // Develop Guide
    Route::controller(\App\Admin\Http\Controllers\Develop\DevelopController::class)
        ->prefix('/develop')
        ->as('develop.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createDevelopGuide', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(["middleware" => ['permission:viewDevelopGuide', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::get('/{developGuideId}/steps', 'getStepsByDevelopGuideId')->name('steps');
            });
            Route::group(['middleware' => ['permission:updateDevelopGuide', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });
            Route::group(['middleware' => ['permission:deleteDevelopGuide', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Step
    Route::controller(\App\Admin\Http\Controllers\Step\StepController::class)
        ->prefix('/steps')
        ->as('step.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createDevelopGuide', 'auth:admin']], function () {
                Route::get('/{developGuideId}/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(["middleware" => ['permission:viewDevelopGuide', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });
            Route::group(['middleware' => ['permission:updateDevelopGuide', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });
            Route::group(['middleware' => ['permission:deleteDevelopGuide', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Guide
    Route::controller(\App\Admin\Http\Controllers\Guide\GuideController::class)
        ->prefix('/guides')
        ->as('guide.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createGuide', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(["middleware" => ['permission:viewGuide', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');

            });
            Route::group(['middleware' => ['permission:updateGuide', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });
            Route::group(['middleware' => ['permission:deleteGuide', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //RatingPQ
    Route::controller(\App\Admin\Http\Controllers\RatingPQ\RatingPQController::class)
        ->prefix('/rating-pqs')
        ->as('ratingPQ.')
        ->group(function () {

            Route::group(["middleware" => ['permission:viewRatingPQ', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
            });

            Route::group(['middleware' => ['permission:deletePQ', 'auth:admin']], function () {
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });

        });

    //Rating
    Route::controller(\App\Admin\Http\Controllers\Rating\RatingController::class)
        ->prefix('/ratings')
        ->as('rating.')
        ->group(function () {

            Route::group(["middleware" => ['permission:viewEQ', 'auth:admin']], function () {
                Route::get('/eqs', 'eq')->name('eq');
                Route::get('/iqs', 'iq')->name('iq');
                Route::get('/aqs', 'aq')->name('aq');
            });

            Route::group(['middleware' => ['permission:deleteEQAQIQ', 'auth:admin']], function () {
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });

        });

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
    //Classes
    Route::controller(\App\Admin\Http\Controllers\Classes\ClassesController::class)
        ->prefix('/lop')
        ->as('classes.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createClasses', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewClasses', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });
            Route::group(['middleware' => ['permission:updateClasses', 'auth:admin']], function () {
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
                Route::put('/edit', 'update')->name('update');
            });
            Route::group(['middleware' => ['permission:deleteClasses', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });
    //WeightHeight
    Route::controller(\App\Admin\Http\Controllers\WeightHeightWho\WeightHeightWhoController::class)
        ->prefix('/weight-height-who')
        ->as('weight-height-who.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createHeightWeight', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(["middleware" => ['permission:viewHeightWeight', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::post('/import', 'import')->name('import');
                Route::get('/export', 'export')->name('export');
                Route::get('/edit/{id}', 'edit')->name('edit');
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
                Route::post('/import', 'import')->name('import');
                Route::get('/export', 'export')->name('export');
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
                Route::get('/add-admin', 'create-admin')->name('create-admin');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewVaccinationSchedule', 'auth:admin']], function () {
                Route::get('/user', 'user')->name('user');
                Route::get('/admin', 'admin')->name('admin');
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
            Route::group(['middleware' => ['permission:createQuestionGroup', 'auth:admin']], function () {
                Route::get('/add/iq', 'createIq')->name('createIq');
                Route::get('/add/aq', 'createAq')->name('createAq');
                Route::get('/add/eq', 'createEq')->name('createEq');

                Route::post('/add/iq', 'storeIq')->name('storeIq');
                Route::post('/add/aq-eq', 'storeEqAq')->name('storeAqEq');
            });

            Route::group(['middleware' => ['permission:viewQuestionGroup', 'auth:admin']], function () {
                Route::get('/iq', 'iq')->name('iq');
                Route::get('/aq', 'aq')->name('aq');
                Route::get('/eq', 'eq')->name('eq');

                Route::get('/edit/iq/{id}', 'editIq')->name('editIq');
                Route::get('/edit/eq-aq/{id}', 'editEqAq')->name('editEqAq');
                Route::get('/search', 'getQuestionsByType')->name('type');
                Route::post('/questions/by-ids', 'getQuestionsByIds')->name('by-ids');

            });

            Route::group(['middleware' => ['permission:updateQuestionGroup', 'auth:admin']], function () {
                Route::put('/edit/iq', 'updateIq')->name('updateIq');
                Route::put('/edit/aq-eq', 'updateEqAq')->name('updateAqEq');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteQuestionGroup', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });
    //pregnancy
    Route::controller(\App\Admin\Http\Controllers\Pregnancy\PregnancyController::class)
        ->prefix('/thai-ki')
        ->as('pregnancy.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createPregnancy', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewPregnancy', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });
            Route::group(['middleware' => ['permission:updatePregnancy', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });
            Route::group(['middleware' => ['permission:deletePregnancy', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });
    //Transaction
    Route::controller(TransactionController::class)
        ->prefix('/giao-dich')
        ->as('transaction.')
        ->group(function () {
            Route::group(['middleware' => ['permission:viewTransaction', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
            });
        });
    //Quiz
    Route::controller(\App\Admin\Http\Controllers\Quiz\QuizController::class)
        ->prefix('/bai-kiem-tra')
        ->as('quiz.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createQuiz', 'auth:admin']], function () {
                Route::get('/add/iq', 'createIq')->name('createIq');
                Route::get('/add/aq', 'createAq')->name('createAq');
                Route::get('/add/eq', 'createEq')->name('createEq');
                Route::get('/add/pq', 'createPq')->name('createPq');
                Route::post('/add', 'store')->name('store');
                Route::post('/add-iq', 'storeIQ')->name('storeIQ');
            });
            Route::group(['middleware' => ['permission:viewQuiz', 'auth:admin']], function () {
                Route::get('/iq', 'iq')->name('iq');
                Route::get('/eq', 'eq')->name('eq');
                Route::get('/aq', 'aq')->name('aq');
                Route::get('/pq', 'pq')->name('pq');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });
            Route::group(['middleware' => ['permission:updateQuiz', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::put('/edit-iq', 'updateIQ')->name('updateIQ');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });
            Route::group(['middleware' => ['permission:deleteQuiz', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });
    //Journals
    Route::controller(\App\Admin\Http\Controllers\Journal\JournalController::class)
        ->prefix('/nhat-ky')
        ->as('journal.')
        ->group(function () {
            Route::group(['middleware' => ['permission:CreateJournal', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewJournal', 'auth:admin']], function () {
                Route::get('/prescription', 'prescription')->name('prescription');
                Route::get('/moment', 'moment')->name('moment');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });
            Route::group(['middleware' => ['permission:updateJournal', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
            });
            Route::group(['middleware' => ['permission:deleteJournal', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });
    //Notification
    Route::controller(App\Admin\Http\Controllers\Notification\NotificationController::class)
        ->prefix('/thong-bao')
        ->as('notification.')
        ->group(function () {
            Route::get('/not-read-admin', 'getNotificationsForAdmin')->name('getNotificationAdmin');
            Route::post('/status', 'updateStatus')->name('status');
            Route::post('/update-device-token', 'updateDeviceToken')->name('updateDeviceToken');


            Route::group(['middleware' => ['permission:createNotification', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
                Route::get('/download-template', 'downloadTemplate')->name('downloadTemplate');
            });
            Route::group(['middleware' => ['permission:viewNotification', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/by-user', 'getNotificationByUser')->name('user');
                Route::get('/by-package', 'getNotificationByPackage')->name('package');
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
                Route::get('/export-template', 'exportTemplate')->name('exportTemplate');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::post('/import', 'import')->name('import');
            });

            Route::group(['middleware' => ['permission:updateClinic', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteClinic', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Support
    Route::controller(App\Admin\Http\Controllers\Support\SupportController::class)
        ->prefix('/supports')
        ->as('support.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createSupport', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewSupport', 'auth:admin']], function () {
                Route::get('/help-center', 'helpCenter')->name('help-center');
                Route::get('/guide', 'guide')->name('guide');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateSupport', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteSupport', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });
    //vaccinationType
    Route::controller(\App\Admin\Http\Controllers\VaccinationType\VaccinationTypeController::class)
        ->prefix('/loai-tiem-chung')
        ->as('vaccinationType.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createTypeVaccination', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewTypeVaccination', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });
            Route::group(['middleware' => ['permission:updateTypeVaccination', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });
            Route::group(['middleware' => ['permission:deleteTypeVaccination', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });
    //Quality
    Route::controller(App\Admin\Http\Controllers\Quality\QualityController::class)
        ->prefix('/pham-chat')
        ->as('quality.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createQuality', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewQuality', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateQuality', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteQuality', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Capabilities
    Route::controller(App\Admin\Http\Controllers\Capability\CapabilityController::class)
        ->prefix('/nang-luc')
        ->as('capability.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createCapability', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewCapability', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateCapability', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteCapability', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //Subject
    Route::controller(App\Admin\Http\Controllers\Subject\SubjectController::class)
        ->prefix('/mon-hoc')
        ->as('subject.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createSubject', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewSubject', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateSubject', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteSubject', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    Route::controller(App\Admin\Http\Controllers\Expected\ExpectedController::class)
        ->prefix('/expected')
        ->as('expected.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createExpected', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewExpected', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateExpected', 'auth:admin']], function () {
                Route::put('/edit', 'update')->name('update');
                Route::post('/multiple', 'actionMultipleRecords')->name('multiple');
            });

            Route::group(['middleware' => ['permission:deleteExpected', 'auth:admin']], function () {
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
                Route::post('/clear-normal-tokens', 'clearNormalTokens')->name('clearNormalTokens');
            });

            Route::group(['middleware' => ['permission:deleteUser', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //sliders
    Route::prefix('/sliders')->as('slider.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Slider\SliderItemController::class)
            ->as('item.')
            ->group(function () {
                Route::get('/{slider_id}/item/them', 'create')->name('create');
                Route::get('/{slider_id}/item', 'index')->name('index');
                Route::get('/item/sua/{id}', 'edit')->name('edit');
                Route::put('/item/sua', 'update')->name('update');
                Route::post('/item/them', 'store')->name('store');
                Route::delete('/{slider_id}/item/xoa/{id}', 'delete')->name('delete');
            });
        Route::controller(App\Admin\Http\Controllers\Slider\SliderController::class)->group(function () {
            Route::group(['middleware' => ['permission:createSlider', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewSlider', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateSlider', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deleteSlider', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //Post
    Route::prefix('/bai-viet')->as('post.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\Post\PostController::class)->group(function () {

            Route::group(['middleware' => ['permission:createPost', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewPost', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
                Route::post('/multiple', 'actionMultipleRecode')->name('multiple');
            });

            Route::group(['middleware' => ['permission:updatePost', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deletePost', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });

    //Post category
    Route::prefix('/danh-muc-bai-viet')->as('post_category.')->group(function () {
        Route::controller(App\Admin\Http\Controllers\PostCategory\PostCategoryController::class)->group(function () {
            Route::group(['middleware' => ['permission:createPostCategory', 'auth:admin']], function () {
                Route::get('/them', 'create')->name('create');
                Route::post('/them', 'store')->name('store');
            });
            Route::group(['middleware' => ['permission:viewPostCategory', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/sua/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updatePostCategory', 'auth:admin']], function () {
                Route::put('/sua', 'update')->name('update');
            });

            Route::group(['middleware' => ['permission:deletePostCategory', 'auth:admin']], function () {
                Route::delete('/xoa/{id}', 'delete')->name('delete');
            });
        });
    });
    //GPA
    Route::controller(\App\Admin\Http\Controllers\GPA\GPAController::class)
        ->prefix('/gpa')
        ->as('gpa.')
        ->group(function () {
            Route::group(['middleware' => ['permission:viewGPA', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
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
            Route::get('/children', [\App\Admin\Http\Controllers\Children\ChildrenSelectController::class, 'selectSearch'])->name('children');
            Route::get('/children-born', [\App\Admin\Http\Controllers\Children\ChildrenUnBornSelectController::class, 'selectSearch'])->name('childrenBorn');
            Route::get('/subject', [App\Admin\Http\Controllers\Subject\SubjectSearchSelectController::class, 'selectSearch'])->name('subject');
            Route::get('/classes', [App\Admin\Http\Controllers\Classes\ClassesSearchSelectController::class, 'selectSearch'])->name('classes');
            Route::get('/vaccinationType', [\App\Admin\Http\Controllers\VaccinationType\VaccinationTypeSelectController::class, 'selectSearch'])->name('vaccinationType');
        });
    });

    // Brand
    Route::controller(App\Admin\Http\Controllers\Brand\BrandController::class)
        ->prefix('/brands')
        ->as('brand.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createBrand', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });

            Route::group(['middleware' => ['permission:viewBrand', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateBrand', 'auth:admin']], function () {
                Route::put('/edit/{id}', 'update')->name('update');
                Route::post('/action-multiple', 'actionMultipleRecords')->name('actionMultiple');
            });

            Route::group(['middleware' => ['permission:deleteBrand', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    // Product
    Route::controller(App\Admin\Http\Controllers\Product\ProductController::class)
        ->prefix('/products')
        ->as('product.')
        ->group(function () {
            Route::group(['middleware' => ['permission:createProduct', 'auth:admin']], function () {
                Route::get('/add', 'create')->name('create');
                Route::post('/add', 'store')->name('store');
            });

            Route::group(['middleware' => ['permission:viewProduct', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });

            Route::group(['middleware' => ['permission:updateProduct', 'auth:admin']], function () {
                Route::put('/edit/{id}', 'update')->name('update');
                Route::post('/action-multiple', 'actionMultipleRecords')->name('actionMultiple');
            });

            Route::group(['middleware' => ['permission:deleteProduct', 'auth:admin']], function () {
                Route::delete('/delete/{id}', 'delete')->name('delete');
            });
        });

    //App Versions
    Route::controller(App\Admin\Http\Controllers\AppVersion\AppVersionController::class)
        ->prefix('/app-versions')
        ->as('app-version.')
        ->group(function () {
            Route::group(['middleware' => ['permission:mevivuDev', 'auth:admin']], function () {
                Route::get('/', 'index')->name('index');
                Route::get('/edit/{id}', 'edit')->name('edit');
                Route::put('/edit', 'update')->name('update');
            });
        });


    Route::post('/logout', [App\Admin\Http\Controllers\Auth\LogoutController::class, 'logout'])->name('logout');
});
