<?php

namespace App\Api\V1\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected array $repositories = [
        'App\Api\V1\Repositories\User\UserRepositoryInterface' => 'App\Api\V1\Repositories\User\UserRepository',
        'App\Api\V1\Repositories\Slider\SliderRepositoryInterface' => 'App\Api\V1\Repositories\Slider\SliderRepository',
        'App\Api\V1\Repositories\Slider\SliderItemRepositoryInterface' => 'App\Api\V1\Repositories\Slider\SliderItemRepository',
        'App\Api\V1\Repositories\Post\PostRepositoryInterface' => 'App\Api\V1\Repositories\Post\PostRepository',
        'App\Api\V1\Repositories\PostCategory\PostCategoryRepositoryInterface' => 'App\Api\V1\Repositories\PostCategory\PostCategoryRepository',
        'App\Api\V1\Repositories\Notification\NotificationRepositoryInterface' => 'App\Api\V1\Repositories\Notification\NotificationRepository',
        'App\Api\V1\Repositories\Setting\SettingRepositoryInterface' => 'App\Api\V1\Repositories\Setting\SettingRepository',
        'App\Api\V1\Repositories\Clinic\ClinicRepositoryInterface' => 'App\Api\V1\Repositories\Clinic\ClinicRepository',
        'App\Api\V1\Repositories\Exercise\ExerciseRepositoryInterface' => 'App\Api\V1\Repositories\Exercise\ExerciseRepository',
        'App\Api\V1\Repositories\Question\QuestionRepositoryInterface' => 'App\Api\V1\Repositories\Question\QuestionRepository',
        'App\Api\V1\Repositories\BMI\BMIRepositoryInterface' => 'App\Api\V1\Repositories\BMI\BMIRepository',
        'App\Api\V1\Repositories\Child\ChildRepositoryInterface' => 'App\Api\V1\Repositories\Child\ChildRepository',
        'App\Api\V1\Repositories\Assessment\AssessmentRepositoryInterface' => 'App\Api\V1\Repositories\Assessment\AssessmentRepository',
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
