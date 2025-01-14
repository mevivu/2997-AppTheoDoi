<?php

namespace App\Api\V1\Providers;

use Illuminate\Support\ServiceProvider;

class ServiceServiceProvider extends ServiceProvider
{
    protected array $services = [
        'App\Api\V1\Services\User\UserServiceInterface' => 'App\Api\V1\Services\User\UserService',
        'App\Api\V1\Services\Auth\StoreServiceInterface' => 'App\Api\V1\Services\Auth\StoreService',
        'App\Api\V1\Services\Notification\NotificationServiceInterface' => 'App\Api\V1\Services\Notification\NotificationService',
        'App\Api\V1\Services\Vehicle\VehicleServiceInterface' => 'App\Api\V1\Services\Vehicle\VehicleService',
        'App\Api\V1\Services\VehicleEntry\VehicleEntryServiceInterface' => 'App\Api\V1\Services\VehicleEntry\VehicleEntryService',
        'App\Api\V1\Services\VehicleEntry\Import\Manual\VehicleEntryImportManualServiceInterface' => 'App\Api\V1\Services\VehicleEntry\Import\Manual\VehicleEntryImportManualService',
        'App\Api\V1\Services\VehicleEntry\Import\Vehicle\VehicleEntryImportVehicleServiceInterface' => 'App\Api\V1\Services\VehicleEntry\Import\Vehicle\VehicleEntryImportVehicleService',
        'App\Api\V1\Services\Clinic\ClinicServiceInterface' => 'App\Api\V1\Services\Clinic\ClinicService',
        'App\Api\V1\Services\Exercise\ExerciseServiceInterface' => 'App\Api\V1\Services\Exercise\ExerciseService',
        'App\Api\V1\Services\Child\ChildServiceInterface' => 'App\Api\V1\Services\Child\ChildService',
        'App\Api\V1\Services\Assessment\AssessmentServiceInterface' => 'App\Api\V1\Services\Assessment\AssessmentService',
        'App\Api\V1\Services\Package\PackageServiceInterface' => 'App\Api\V1\Services\Package\PackageService',
        'App\Api\V1\Services\Journal\JournalServiceInterface' => 'App\Api\V1\Services\Journal\JournalService',
        'App\Api\V1\Services\Pregnancy\PregnancyServiceInterface' => 'App\Api\V1\Services\Pregnancy\PregnancyService',
        'App\Api\V1\Services\Rating\RatingServiceInterface' => 'App\Api\V1\Services\Rating\RatingService',
        'App\Api\V1\Services\Quiz\QuizServiceInterface' => 'App\Api\V1\Services\Quiz\QuizService',
        'App\Api\V1\Services\ChildEvaluation\ChildEvaluationServiceInterface' => 'App\Api\V1\Services\ChildEvaluation\ChildEvaluationService',
        'App\Api\V1\Services\VaccinationSchedule\VaccinationScheduleServiceInterface' => 'App\Api\V1\Services\VaccinationSchedule\VaccinationScheduleService',
        'App\Api\V1\Services\Guide\GuideServiceInterface' => 'App\Api\V1\Services\Guide\GuideService',
        'App\Api\V1\Services\Product\ProductServiceInterface' => 'App\Api\V1\Services\Product\ProductService',
        'App\Api\V1\Services\ProductCatalog\ProductCatalogServiceInterface' => 'App\Api\V1\Services\ProductCatalog\ProductCatalogService',
        'App\Api\V1\Services\Brand\BrandServiceInterface' => 'App\Api\V1\Services\Brand\BrandService',

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
