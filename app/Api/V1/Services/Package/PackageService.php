<?php

namespace App\Api\V1\Services\Package;

use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Api\V1\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\V1\Repositories\Package\PackageRepositoryInterface;
use App\Api\V1\Repositories\UserPackage\UserPackageRepositoryInterface;
use App\Api\V1\Services\Notification\NotificationServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Notification\MessageType;
use App\Enums\Package\PackageStatus;
use App\Traits\NotifiesViaFirebase;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;


class PackageService implements PackageServiceInterface
{
    use AuthSupport, AuthServiceApi, NotifiesViaFirebase;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected PackageRepositoryInterface $repository;
    protected UserPackageRepositoryInterface $userPackageRepository;
    protected NotificationServiceInterface $notificationService;
    protected AdminRepositoryInterface $adminRepository;
    protected NotificationRepositoryInterface $notificationRepository;

    protected FileService $fileService;


    public function __construct(
        PackageRepositoryInterface      $repository,
        UserPackageRepositoryInterface  $userPackageRepository,
        NotificationServiceInterface    $notificationService,
        AdminRepositoryInterface        $adminRepository,
        NotificationRepositoryInterface $notificationRepository,
        FileService                     $fileService
    )
    {
        $this->repository = $repository;
        $this->userPackageRepository = $userPackageRepository;
        $this->notificationService = $notificationService;
        $this->adminRepository = $adminRepository;
        $this->notificationRepository = $notificationRepository;
        $this->fileService = $fileService;

    }


    public function index()
    {
        return $this->repository->getBy([
            'status' => PackageStatus::Active,
        ]);
    }

    /**
     * @throws Exception
     */
    public function purchasePackage(Request $request): bool
    {
        $data = $request->validated();
        $user = $this->getCurrentUser();
        $image = $data['payment_confirmation_image'];
        if ($image) {
            $data['payment_confirmation_image'] = $this->fileService
                ->uploadAvatar('images/package', $image);
        }
        $this->notificationService->sendCustomerPaymentNotification($user);
        $this->notificationService->sendNotificationsPaymentToAdmins($user, $data['payment_confirmation_image']);
        return true;

    }

//    public function purchasePackage(Request $request): ?Model
//    {
//        $data = $request->validated();
//        $user = $this->getCurrentUser();
//        $image = $data['payment_confirmation_image'];
//        if ($image) {
//            $data['payment_confirmation_image'] = $this->fileService
//                ->uploadAvatar('images/package', $image, $image->payment_confirmation_image);
//        }
//        $packageId = $data['id'];
//        $package = $this->repository->findOrFail($packageId);
//        $startDate = now();
//        $endDate = $startDate->copy()->add($package->type->duration());
//        $userPackage = $this->userPackageRepository->findByField('user_id', $user->id);
//        if ($userPackage) {
//            $userPackage->update(
//                [
//                    'package_id' => $packageId,
//                    'start_date' => $startDate,
//                    'end_date' => $endDate,
//                    'current_type' => $package->type
//
//                ]
//            );
//        }
//
//        return $userPackage;
//
//    }
//

}
