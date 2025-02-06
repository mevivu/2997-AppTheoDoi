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
        'App\Api\V1\Repositories\Package\PackageRepositoryInterface' => 'App\Api\V1\Repositories\Package\PackageRepository',
        'App\Api\V1\Repositories\UserPackage\UserPackageRepositoryInterface' => 'App\Api\V1\Repositories\UserPackage\UserPackageRepository',
        'App\Api\V1\Repositories\Journal\JournalRepositoryInterface' => 'App\Api\V1\Repositories\Journal\JournalRepository',
        'App\Api\V1\Repositories\Pregnancy\PregnancyRepositoryInterface' => 'App\Api\V1\Repositories\Pregnancy\PregnancyRepository',
        'App\Api\V1\Repositories\Rating\RatingRepositoryInterface' => 'App\Api\V1\Repositories\Rating\RatingRepository',
        'App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface' => 'App\Api\V1\Repositories\RatingPQ\RatingPQRepository',
        'App\Api\V1\Repositories\Answer\AnswerRepositoryInterface' => 'App\Api\V1\Repositories\Answer\AnswerRepository',
        'App\Api\V1\Repositories\Classes\ClassesRepositoryInterface' => 'App\Api\V1\Repositories\Classes\ClassesRepository',
        'App\Api\V1\Repositories\Support\SupportRepositoryInterface' => 'App\Api\V1\Repositories\Support\SupportRepository',
        'App\Api\V1\Repositories\SubjectGrade\SubjectGradeRepositoryInterface' => 'App\Api\V1\Repositories\SubjectGrade\SubjectGradeRepository',
        'App\Api\V1\Repositories\Quality\QualityRepositoryInterface' => 'App\Api\V1\Repositories\Quality\QualityRepository',
        'App\Api\V1\Repositories\Capability\CapabilityRepositoryInterface' => 'App\Api\V1\Repositories\Capability\CapabilityRepository',
        'App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface' => 'App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepository',
        'App\Api\V1\Repositories\ChildQuality\ChildQualityRepositoryInterface' => 'App\Api\V1\Repositories\ChildQuality\ChildQualityRepository',
        'App\Api\V1\Repositories\ChildCapability\ChildCapabilityRepositoryInterface' => 'App\Api\V1\Repositories\ChildCapability\ChildCapabilityRepository',
        'App\Api\V1\Repositories\Quiz\QuizRepositoryInterface' => 'App\Api\V1\Repositories\Quiz\QuizRepository',
        'App\Api\V1\Repositories\ClassGrade\ClassGradeRepositoryInterface' => 'App\Api\V1\Repositories\ClassGrade\ClassGradeRepository',
        'App\Api\V1\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface' => 'App\Api\V1\Repositories\VaccinationSchedule\VaccinationScheduleRepository',
        'App\Api\V1\Repositories\Guide\GuideRepositoryInterface' =>'App\Api\V1\Repositories\Guide\GuideRepository',
        'App\Api\V1\Repositories\Product\ProductRepositoryInterface' => 'App\Api\V1\Repositories\Product\ProductRepository',
        'App\Api\V1\Repositories\ProductCatalog\ProductCatalogRepositoryInterface' => 'App\Api\V1\Repositories\ProductCatalog\ProductCatalogRepository',
        'App\Api\V1\Repositories\Brand\BrandRepositoryInterface' => 'App\Api\V1\Repositories\Brand\BrandRepository',
        'App\Api\V1\Repositories\WeightHeightWho\WhoRepositoryInterface' => 'App\Api\V1\Repositories\WeightHeightWho\WhoRepository',
        'App\Api\V1\Repositories\Transaction\TransactionRepositoryInterface' => 'App\Api\V1\Repositories\Transaction\TransactionRepository',



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
