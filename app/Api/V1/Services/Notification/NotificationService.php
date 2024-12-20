<?php

namespace App\Api\V1\Services\Notification;


use App\Admin\Repositories\Admin\AdminRepositoryInterface;
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

    protected AdminRepositoryInterface $adminRepository;

    private UserRepositoryInterface $userRepository;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface         $userRepository,
        AdminRepositoryInterface        $adminRepository
    )
    {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->adminRepository = $adminRepository;

    }

    public function getNotificationByUser(Request $request): bool|object
    {
        try {
            $data = $request->validated();
            $userId = $this->getCurrentUserId();
            $limit = $data['limit'] ?? 10;
            $page = $data['page'] ?? 1;
            $user = $this->repository->getNotificationByUserId("user_id", $userId, $limit, $page);
            return $user;
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
        // TODO: Implement UpdateAllStatusIsRead() method.
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

    public function delete(Request $request): bool
    {
        // TODO: Implement delete() method.
        $data = $request->validated();
        try {
            $this->repository->delete($data['id']);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function sendApprovalNotificationToAdmin(User $user): void
    {
        $title = config('notifications.package_purchase_pending.title');
        $bodyTemplate = config('notifications.package_purchase_pending.message');
        $body = str_replace('{fullname}', $user->fullname, $bodyTemplate);
        $admins = $this->adminRepository->getAll();
        $deviceTokens = $admins->pluck('device_token')->filter()->all();
        if (!empty($deviceTokens)) {
            $this->sendFirebaseNotification($deviceTokens, null, $title, $body);
        }

    }


    public function sendCustomerPaymentNotification(User $user): void
    {
        $title = config('notifications.package_purchase_pending.title');
        $bodyTemplate = config('notifications.package_purchase_pending.message');
        $body = str_replace('{fullname}', $user->fullname, $bodyTemplate);
        $this->sendFirebaseNotificationToUser($user, $title, $body, MessageType::UNCLASSIFIED);

    }


}


