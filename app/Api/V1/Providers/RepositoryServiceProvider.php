<?php

namespace App\Api\V1\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    protected array $repositories = [
        'App\Api\V1\Repositories\Category\CategoryRepositoryInterface' => 'App\Api\V1\Repositories\Category\CategoryRepository',
        'App\Api\V1\Repositories\User\UserRepositoryInterface' => 'App\Api\V1\Repositories\User\UserRepository',
        'App\Api\V1\Repositories\Slider\SliderRepositoryInterface' => 'App\Api\V1\Repositories\Slider\SliderRepository',
        'App\Api\V1\Repositories\Slider\SliderItemRepositoryInterface' => 'App\Api\V1\Repositories\Slider\SliderItemRepository',
        'App\Api\V1\Repositories\Post\PostRepositoryInterface' => 'App\Api\V1\Repositories\Post\PostRepository',
        'App\Api\V1\Repositories\PostCategory\PostCategoryRepositoryInterface' => 'App\Api\V1\Repositories\PostCategory\PostCategoryRepository',
        'App\Api\V1\Repositories\Notification\NotificationRepositoryInterface' => 'App\Api\V1\Repositories\Notification\NotificationRepository',
        'App\Api\V1\Repositories\Setting\SettingRepositoryInterface' => 'App\Api\V1\Repositories\Setting\SettingRepository',
        'App\Api\V1\Repositories\Vehicle\VehicleRepositoryInterface' => 'App\Api\V1\Repositories\Vehicle\VehicleRepository',
        'App\Api\V1\Repositories\EntryStage\EntryStageRepositoryInterface' => 'App\Api\V1\Repositories\EntryStage\EntryStagePhotoRepository',
        'App\Api\V1\Repositories\StagePhoto\StagePhotoRepositoryInterface' => 'App\Api\V1\Repositories\StagePhoto\StagePhotoRepository',
        'App\Api\V1\Repositories\StagePacket\StagePacketRepositoryInterface' => 'App\Api\V1\Repositories\StagePacket\StagePacketRepository',
        'App\Api\V1\Repositories\Log\LogRepositoryInterface' => 'App\Api\V1\Repositories\Log\LogRepository',
        'App\Api\V1\Repositories\VehicleEntry\VehicleEntryRepositoryInterface' => 'App\Api\V1\Repositories\VehicleEntry\VehicleEntryRepository',
        'App\Api\V1\Repositories\VehicleEntry\Import\Manual\VehicleEntryImportManualRepositoryInterface' => 'App\Api\V1\Repositories\VehicleEntry\Import\Manual\VehicleEntryImportManualRepository',
        'App\Api\V1\Repositories\VehicleEntry\Import\Vehicle\VehicleEntryImportVehicleRepositoryInterface' => 'App\Api\V1\Repositories\VehicleEntry\Import\Vehicle\VehicleEntryImportVehicleRepository',
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
