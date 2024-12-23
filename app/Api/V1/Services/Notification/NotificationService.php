<?php

namespace App\Api\V1\Services\Notification;


use App\Admin\Services\File\FileService;
use App\Admin\Traits\Roles;
use App\Api\V1\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationStatus;
use App\Models\User;
use App\Traits\NotifiesViaFirebase;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\Request;
use Exception;


class NotificationService implements NotificationServiceInterface
{
    use NotifiesViaFirebase, Roles, UseLog;

    use AuthServiceApi;

    protected NotificationRepositoryInterface $repository;
    protected UserRepositoryInterface $userRepository;

    protected FileService $fileService;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface         $userRepository,
        FileService                     $fileService
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->fileService = $fileService;


    }

    public function getNotificationByUser(Request $request): bool|object
    {
        try {
            $data = $request->validated();
            $userId = $this->getCurrentUserId();
            $limit = $data['limit'] ?? 10;
            $page = $data['page'] ?? 1;
            return $this->repository->getNotificationByUserId("user_id", $userId, $limit, $page);
        } catch (Exception $e) {
            $this->logError('Failed to process get user', $e);
            return false;
        }

    }


    public function updateStatusIsRead(Request $request): bool
    {
        try {

            $this->repository->update($request->id, ["status" => NotificationStatus::READ]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function updateAllStatusIsRead(Request $request): bool
    {
        try {
            $response = $this->repository->getNotificationIsNotRead($this->getCurrentUserId());
            foreach ($response as $notification) {

                $notification->update(["status" => NotificationStatus::READ]);
            }
            return true;
        } catch (Exception $e) {

            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function delete($id): void
    {
        $notification = $this->repository->findOrFail($id);
        if ($notification->payment_confirmation_image) {
            $this->fileService->deleteModelImages($notification, ['payment_confirmation_image']);
        }
        $notification->delete();
    }

    /**
     * @throws Exception
     */


    public function sendCustomerPaymentNotification(User $user): void
    {
        $title = config('notifications.package_purchase_pending.title');
        $bodyTemplate = config('notifications.package_purchase_pending.message');
        $body = str_replace('{fullname}', $user->fullname, $bodyTemplate);
        $this->sendFirebaseNotificationToUser($user, $title, $body, MessageType::UNCLASSIFIED);

    }



}


