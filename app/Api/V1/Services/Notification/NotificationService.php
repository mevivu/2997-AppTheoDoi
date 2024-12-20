<?php

namespace App\Api\V1\Services\Notification;

use App\Admin\Traits\Roles;

use App\Api\V1\Repositories\Notification\NotificationRepositoryInterface;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Traits\NotifiesViaFirebase;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\Request;
use Exception;


class NotificationService implements NotificationServiceInterface
{
    use NotifiesViaFirebase, Roles, UseLog;

    use AuthServiceApi;

    protected NotificationRepositoryInterface $repository;

    private UserRepositoryInterface $userRepository;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;

    }

    public function getNotificationByUser(Request $request): bool|object
    {  
        try {
            $data = $request->validated();  
            $userId = $this->getCurrentUserId();
            $limit = $data['limit'] ?? 10;
            $user = $this->repository->getNotificationByUserId("user_id", $userId, $limit);
            return $user;
        } 
        catch (Exception $e) {
            $this->logError('Failed to process get user', $e);
            return false;
        }

    }



}
;