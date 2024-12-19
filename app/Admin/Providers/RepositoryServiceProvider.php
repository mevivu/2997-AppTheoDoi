<?php

namespace App\Admin\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected array $repositories = [
        'App\Admin\Repositories\Module\ModuleRepositoryInterface' => 'App\Admin\Repositories\Module\ModuleRepository',
        'App\Admin\Repositories\Permission\PermissionRepositoryInterface' => 'App\Admin\Repositories\Permission\PermissionRepository',
        'App\Admin\Repositories\Role\RoleRepositoryInterface' => 'App\Admin\Repositories\Role\RoleRepository',
        'App\Admin\Repositories\Admin\AdminRepositoryInterface' => 'App\Admin\Repositories\Admin\AdminRepository',
        'App\Admin\Repositories\User\UserRepositoryInterface' => 'App\Admin\Repositories\User\UserRepository',
        'App\Admin\Repositories\Children\ChildrenRepositoryInterface' => 'App\Admin\Repositories\Children\ChildrenRepository',
        'App\Admin\Repositories\Setting\SettingRepositoryInterface' => 'App\Admin\Repositories\Setting\SettingRepository',
        'App\Admin\Repositories\Notification\NotificationRepositoryInterface' => 'App\Admin\Repositories\Notification\NotificationRepository',
        'App\Admin\Repositories\Otp\OtpRepositoryInterface' => 'App\Admin\Repositories\Otp\OtpRepository',
        'App\Admin\Repositories\Exercise\ExerciseRepositoryInterface' => 'App\Admin\Repositories\Exercise\ExerciseRepository',
        'App\Admin\Repositories\Bmi\BmiRepositoryInterface' => 'App\Admin\Repositories\Bmi\BmiRepository',
        'App\Admin\Repositories\ClinicType\ClinicTypeRepositoryInterface' => 'App\Admin\Repositories\ClinicType\ClinicTypeRepository',
        'App\Admin\Repositories\Clinic\ClinicRepositoryInterface' => 'App\Admin\Repositories\Clinic\ClinicRepository',
        'App\Admin\Repositories\Province\ProvinceRepositoryInterface' => 'App\Admin\Repositories\Province\ProvinceRepository',
        'App\Admin\Repositories\District\DistrictRepositoryInterface' => 'App\Admin\Repositories\District\DistrictRepository',
        'App\Admin\Repositories\Ward\WardRepositoryInterface' => 'App\Admin\Repositories\Ward\WardRepository',
        'App\Admin\Repositories\QuestionGroup\QuestionGroupRepositoryInterface' => 'App\Admin\Repositories\QuestionGroup\QuestionGroupRepository',
        'App\Admin\Repositories\Question\QuestionRepositoryInterface' => 'App\Admin\Repositories\Question\QuestionRepository',
        'App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface' => 'App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepository',
        'App\Admin\Repositories\Package\PackageRepositoryInterface' => 'App\Admin\Repositories\Package\PackageRepository',
        'App\Admin\Repositories\WeightHeightWho\WeightHeightWhoRepositoryInterface' => 'App\Admin\Repositories\WeightHeightWho\WeightHeightWhoRepository',
        'App\Admin\Repositories\Post\PostRepositoryInterface' => 'App\Admin\Repositories\Post\PostRepository',
        'App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface' => 'App\Admin\Repositories\PostCategory\PostCategoryRepository',
        'App\Admin\Repositories\Slider\SliderRepositoryInterface' => 'App\Admin\Repositories\Slider\SliderRepository',
        'App\Admin\Repositories\Slider\SliderItemRepositoryInterface' => 'App\Admin\Repositories\Slider\SliderItemRepository',
        'App\Admin\Services\Slider\SliderServiceInterface' => 'App\Admin\Services\Slider\SliderService',
        'App\Admin\Services\Slider\SliderItemServiceInterface' => 'App\Admin\Services\Slider\SliderItemService',
        'App\Admin\Repositories\Answer\AnswerRepositoryInterface' => 'App\Admin\Repositories\Answer\AnswerRepository',
        'App\Admin\Repositories\Expected\ExpectedRepositoryInterface' => 'App\Admin\Repositories\Expected\ExpectedRepository',
        'App\Admin\Repositories\Journal\JournalRepositoryInterface' => 'App\Admin\Repositories\Journal\JournalRepository',
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register(): void
    {
        //
        foreach ($this->repositories as $interface => $implement) {
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
