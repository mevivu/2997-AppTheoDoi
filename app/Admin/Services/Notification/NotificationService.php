<?php

namespace App\Admin\Services\Notification;

use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Admin\Traits\Roles;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Notification\NotificationType;
use App\Enums\Notification\NotificationOption;
use App\Models\Admin;
use App\Traits\NotifiesViaFirebase;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationService implements NotificationServiceInterface
{
    use NotifiesViaFirebase, AuthService, Roles;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected $data;

    protected $repository;
    private AdminRepositoryInterface $adminRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(
        NotificationRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        AdminRepositoryInterface $adminRepository,
    ) {
        $this->repository = $repository;
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * Lưu trữ thông báo và gửi đến những người nhận phù hợp.
     *
     * @param Request $request  Yêu cầu chứa dữ liệu thông báo đã được kiểm duyệt.
     * @return bool True nếu thông báo được lưu trữ và gửi thành công, False nếu không.
     */
    public function store(Request $request)
    {
        $this->data = $request->validated();

        switch ($this->data['types']) {
            case NotificationType::All->value:
                $recipients = [$this->userRepository, $this->adminRepository];

                foreach ($recipients as $repository) {
                    $users = $repository->getAll();
                    foreach ($users as $user) {
                        switch ($repository) {
                            case $this->userRepository:
                                $this->data['admin_id'] = null;
                                $this->data['user_id'] = $user->id;
                                break;
                            case $this->adminRepository:
                                $this->data['user_id'] = null;
                                $this->data['admin_id'] = $user->id;
                                break;
                            default:
                                $this->data['admin_id'] = $user->id;
                                break;
                        }

                        $notification = $this->repository->create($this->data);

                        $device_token = $user->device_token;
                        $this->data['device_token'] = $device_token;

                        if ($notification && $device_token) {
                            $this->sendFirebaseNotification([$device_token], null, $notification->title, $notification->message);
                        }
                    }
                }

                break;
            case NotificationType::Customer->value:
                $this->data['admin_id'] = null;
                $notification = $this->handleNotificationOption('user_id');
                break;

            default:
                $this->data['driver_id'] = null;
                $notification = $this->handleNotificationOption('user_id');
                break;
        }

        return $notification ? true : false;
    }

    /**
     * Xử lý tùy chọn gửi thông báo dựa trên kiểu người nhận và dữ liệu yêu cầu.
     *
     * @param string $objectId Tên trường lưu trữ ID người dùng trong kho lưu trữ.
     * @return bool True nếu thông báo được tạo và gửi thành công, False nếu không.
     */
    private function handleNotificationOption(string $objectId)
    {
        if ($this->data['option'] == NotificationOption::All->value) {
            $customers = $this->userRepository->getAll();

            foreach ($customers as $item) {
                $this->data[$objectId] = $item->id;
                switch ($this->data['option']) {
                    case NotificationOption::All->value:
                        $device_token = $item->device_token;
                        break;
                    case NotificationOption::One->value:
                        $device_token = $item->device_token;
                        break;
                    default:
                        $device_token = $item->device_token;
                        break;
                }
                $notification = $this->repository->create($this->data);
                $this->data['device_token'] = $device_token;
                if ($notification && $device_token) {
                    $this->sendFirebaseNotification([$device_token], null, $notification->title, $notification->message);
                }
            }
        } else {
            if (isset($this->data[$objectId])) {
                $objectIds = is_array($this->data[$objectId]) ? $this->data[$objectId] : [$this->data[$objectId]];

                switch ($this->data['types']) {
                    case NotificationType::Customer->value:
                        $users = $this->userRepository->findMany($objectIds);
                        break;
                    default:
                        break;
                }

                $this->data['admin'] = null;

                foreach ($users as $user) {
                    $this->data[$objectId] = $user->id;

                    $device_token = $user->user ? $user->user->device_token : $user->device_token;

                    $notification = $this->repository->create($this->data);

                    $this->data['device_token'] = $device_token;

                    if ($notification && $device_token) {
                        $this->sendFirebaseNotification([$device_token], null, $notification->title, $notification->message);
                    }
                }
            }
        }
        return true; // Thông báo được xử lý thành công
    }


    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {
        $this->data = $request->validated();
        $notification = $this->repository->findOrFail($request->id);

        $userId = $notification->user_id;
        $adminId = $notification->admin_id;


        if ($userId) {
            $user = User::findOrFail($userId);
            $device_token = $user->device_token;
        } else {
            $admin = Admin::findOrFail($adminId);
            $device_token = $admin->device_token;
        }

        if ($device_token) {
            $this->sendFirebaseNotification([$device_token], null, $this->data['title'], $this->data['message']);
        }
        return $this->repository->update($this->data['id'], $this->data);
    }

    /**
     * @throws Exception
     */
    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }

    /**
     * @throws Exception
     */
    public function updateDeviceToken($request): JsonResponse
    {
        try {
            $data = $request->validate([
                'device_token' => 'required|string'
            ]);
            $admin = $this->getCurrentAdmin();

            if ($admin->device_token == null || $admin->device_token != $data['device_token']) {
                $this->adminRepository->update($admin->id, [
                    'device_token' => $data['device_token'],
                ]);
                return response()->json(['message' => 'Update device token success.'], 200);
            } else {
                return response()->json(['message' => 'Device token is up to date.'], 200);
            }
        } catch (Exception $e) {
            return response()->json(['message' => 'Failed to update token.', 'error' => $e->getMessage()], 500);
        }
    }


    /**
     * Gets notifications for admin
     *
     * @param Request $request
     * @return mixed
     */
    public function getNotifications(Request $request): mixed
    {
        $data = $request->validated();
        return $this->repository->getBy(['admin_id' => $data['admin_id'], 'status' => NotificationStatus::NOT_READ]);
    }

    /**
     * Gets notifications for store
     *
     * @param Request $request
     * @return mixed
     */

    public function updateStatus(Request $request): JsonResponse
    {
        $data = $request->validated();

        $filters = [];
        if (!empty($data['admin_id'])) {
            $filters['admin_id'] = $data['admin_id'];
        }

        $notifications = $this->repository->getBy($filters);

        foreach ($notifications as $notification) {
            $this->repository->update($notification->id, ['status' => NotificationStatus::READ]);
        }

        return response()->json(['success' => "Updated successfully"]);
    }

    public function actionMultipleRecode(Request $request): bool
    {
        $this->data = $request->all();
        switch ($this->data['action']) {
            case 'read':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', NotificationStatus::READ);
                }
                return true;
            case 'not_read':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', NotificationStatus::NOT_READ);
                }
                return true;
            case 'deleted':
                foreach ($this->data['id'] as $value) {
                    $this->repository->delete($value);
                }
                return true;
            default:
                return false;
        }
    }
}