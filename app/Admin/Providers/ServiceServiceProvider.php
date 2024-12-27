<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    protected array $services = [
        'App\Admin\Services\Module\ModuleServiceInterface' => 'App\Admin\Services\Module\ModuleService',
        'App\Admin\Services\Permission\PermissionServiceInterface' => 'App\Admin\Services\Permission\PermissionService',
        'App\Admin\Services\Role\RoleServiceInterface' => 'App\Admin\Services\Role\RoleService',
        'App\Admin\Services\Admin\AdminServiceInterface' => 'App\Admin\Services\Admin\AdminService',
        'App\Admin\Services\User\UserServiceInterface' => 'App\Admin\Services\User\UserService',
        'App\Admin\Services\Children\ChildrenServiceInterface' => 'App\Admin\Services\Children\ChildrenService',
        'App\Admin\Services\Notification\NotificationServiceInterface' => 'App\Admin\Services\Notification\NotificationService',
        'App\Admin\Services\Notification\NotificationFirebaseServiceInterface' => 'App\Admin\Services\Notification\NotificationFirebaseService',
        'App\Admin\Services\Exercise\ExerciseServiceInterface' => 'App\Admin\Services\Exercise\ExerciseService',
        'App\Admin\Services\Bmi\BmiServiceInterface' => 'App\Admin\Services\Bmi\BmiService',
        'App\Admin\Services\ClinicType\ClinicTypeServiceInterface' => 'App\Admin\Services\ClinicType\ClinicTypeSizeService',
        'App\Admin\Services\Clinic\ClinicServiceInterface' => 'App\Admin\Services\Clinic\ClinicSizeService',
        'App\Admin\Services\QuestionGroup\QuestionGroupServiceInterface' => 'App\Admin\Services\QuestionGroup\QuestionGroupService',
        'App\Admin\Services\Question\QuestionServiceInterface' => 'App\Admin\Services\Question\QuestionService',
        'App\Admin\Services\Quiz\QuizServiceInterface' => 'App\Admin\Services\Quiz\QuizSizeService',
        'App\Admin\Services\WeightHeightWho\WeightHeightWhoServiceInterface' => 'App\Admin\Services\WeightHeightWho\WeightHeightWhoService',
        'App\Admin\Services\VaccinationSchedule\VaccinationScheduleServiceInterface' => 'App\Admin\Services\VaccinationSchedule\VaccinationScheduleSizeService',
        'App\Admin\Services\Package\PackageServiceInterface' => 'App\Admin\Services\Package\PackageService',
        'App\Admin\Services\Post\PostServiceInterface' => 'App\Admin\Services\Post\PostService',
        'App\Admin\Services\PostCategory\PostCategoryServiceInterface' => 'App\Admin\Services\PostCategory\PostCategoryService',
        'App\Admin\Services\Expected\ExpectedServiceInterface' => 'App\Admin\Services\Expected\ExpectedService',
        'App\Admin\Services\Journal\JournalServiceInterface' => 'App\Admin\Services\Journal\JournalService',
        'App\Admin\Services\Pregnancy\PregnancyServiceInterface' => 'App\Admin\Services\Pregnancy\PregnancyService',
        'App\Admin\Services\Support\SupportServiceInterface' => 'App\Admin\Services\Support\SupportService',
        'App\Admin\Services\Transaction\TransactionServiceInterface' => 'App\Admin\Services\Transaction\TransactionService',
        'App\Admin\Services\Classes\ClassesServiceInterface' => 'App\Admin\Services\Classes\ClassesService',
        'App\Admin\Services\Quality\QualityServiceInterface' => 'App\Admin\Services\Quality\QualityService',
        'App\Admin\Services\Subject\SubjectServiceInterface' => 'App\Admin\Services\Subject\SubjectService',
        'App\Admin\Services\VaccinationType\VaccinationTypeServiceInterface' => 'App\Admin\Services\VaccinationType\VaccinationTypeService',
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
        foreach ($this->services as $interface => $implement) {
            $this->app->singleton($interface, $implement);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
