<?php

namespace App\Admin\Services\Notification;

use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Repositories\UserPackage\UserPackageRepositoryInterface;
use App\Admin\Services\Transaction\TransactionServiceInterface;
use App\Admin\Traits\AuthService;
use App\Admin\Traits\Roles;
use App\Enums\ApprovalStatus;
use App\Enums\Notification\MessageType;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Notification\NotificationType;
use App\Enums\Notification\NotificationOption;
use App\Enums\Package\PackageUserStatus;
use App\Jobs\SendFirebaseNotificationJob;
use App\Traits\UseLog;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationService implements NotificationServiceInterface
{
    use AuthService, Roles, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected $data;

    protected NotificationRepositoryInterface $repository;
    private AdminRepositoryInterface $adminRepository;
    private UserRepositoryInterface $userRepository;
    private UserPackageRepositoryInterface $userPackageRepository;
    protected TransactionServiceInterface $transactionService;
    protected NotificationFirebaseServiceInterface $firebaseService;

    public function __construct(
        NotificationRepositoryInterface      $repository,
        UserRepositoryInterface              $userRepository,
        AdminRepositoryInterface             $adminRepository,
        UserPackageRepositoryInterface       $userPackageRepository,
        TransactionServiceInterface          $transactionService,
        NotificationFirebaseServiceInterface $firebaseService
    )
    {
        $this->repository = $repository;
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
        $this->userPackageRepository = $userPackageRepository;
        $this->transactionService = $transactionService;
        $this->firebaseService = $firebaseService;
    }

    /**
     * Lưu trữ thông báo và gửi đến những người nhận phù hợp.
     *
     * @param Request $request Yêu cầu chứa dữ liệu thông báo đã được kiểm duyệt.
     * @return bool True nếu thông báo được lưu trữ và gửi thành công, False nếu không.
     * @throws Exception
     */
    public function store(Request $request): bool
    {
        $this->data = $request->validated();

        $type   = (int) $this->data['types'];
        $option = (int) $this->data['option'];

        /**
         * =====================================
         * CASE 1: GỬI TẤT CẢ
         * - types = All (1): gửi tất cả, option bị ẩn (null)
         * - types = Customer (2) + option = All (1)
         * =====================================
         */
        if (
            $type === NotificationType::All->value
            || $option === NotificationOption::All->value
        ) {
            $title = $this->data['title'];
            $message = $this->data['message'];

            $this->userRepository->getQueryBuilder()
                ->select('id')
                ->chunk(1000, function ($users) use ($title, $message) {
                    $notifications = [];
                    foreach ($users as $user) {
                        $notifications[] = [
                            'user_id' => $user->id,
                            'title' => $title,
                            'message' => $message,
                            'status' => NotificationStatus::NOT_READ->value,
                            'is_pushed' => false,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    $this->repository->insert($notifications);
                });

            return true;
        }

        /**
         * =====================================
         * CASE 2: GỬI LẺ (option = One)
         * =====================================
         */
        if ($option === NotificationOption::One->value) {
            $userIds = is_array($this->data['user_id'])
                ? $this->data['user_id']
                : [$this->data['user_id']];

            $title = $this->data['title'];
            $message = $this->data['message'];

            $userIdChunks = array_chunk($userIds, 1000);
            foreach ($userIdChunks as $chunk) {
                $notifications = [];
                foreach ($chunk as $userId) {
                    $notifications[] = [
                        'user_id' => $userId,
                        'title' => $title,
                        'message' => $message,
                        'status' => NotificationStatus::NOT_READ->value,
                        'is_pushed' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $this->repository->insert($notifications);
            }

            return true;
        }

        /**
         * =====================================
         * CASE 3: GỬI THEO DANH SÁCH EXCEL
         * =====================================
         */
        if ($option === NotificationOption::Excel->value) {
            if (!$request->hasFile('excel_file')) {
                throw ValidationException::withMessages([
                    'excel_file' => 'Vui lòng tải lên file Excel danh sách khách hàng.'
                ]);
            }

            $file = $request->file('excel_file');
            $rows = Excel::toArray(new \stdClass(), $file);

            if (empty($rows) || empty($rows[0])) {
                throw ValidationException::withMessages([
                    'excel_file' => 'File Excel trống hoặc không đúng định dạng.'
                ]);
            }

            $sheet = $rows[0];
            $codes = [];
            for ($i = 1; $i < count($sheet); $i++) {
                $code = isset($sheet[$i][0]) ? trim($sheet[$i][0]) : '';
                if ($code !== '') {
                    $codes[] = $code;
                }
            }

            if (empty($codes)) {
                throw ValidationException::withMessages([
                    'excel_file' => 'Không tìm thấy mã khách hàng nào trong file Excel.'
                ]);
            }

            $userIds = $this->userRepository->getQueryBuilder()
                ->whereIn('code', $codes)
                ->pluck('id')
                ->toArray();

            if (empty($userIds)) {
                throw ValidationException::withMessages([
                    'excel_file' => 'Không tìm thấy khách hàng nào khớp với mã trong file Excel.'
                ]);
            }

            $title = $this->data['title'];
            $message = $this->data['message'];

            $userIdChunks = array_chunk($userIds, 1000);
            foreach ($userIdChunks as $chunk) {
                $notifications = [];
                foreach ($chunk as $userId) {
                    $notifications[] = [
                        'user_id' => $userId,
                        'title' => $title,
                        'message' => $message,
                        'status' => NotificationStatus::NOT_READ->value,
                        'is_pushed' => false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                $this->repository->insert($notifications);
            }

            return true;
        }

        return false;
    }


    /**
     * Xử lý tùy chọn gửi thông báo dựa trên kiểu người nhận và dữ liệu yêu cầu.
     *
     * @param string $objectId Tên trường lưu trữ ID người dùng trong kho lưu trữ.
     * @return bool True nếu thông báo được tạo và gửi thành công, False nếu không.
     * @throws Exception
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
                $this->data['is_pushed'] = !empty($device_token);
                $notification = $this->repository->create($this->data);
                $this->data['device_token'] = $device_token;
                if ($notification && $device_token) {
                    $this->firebaseService->sendFirebaseNotification([$device_token], null, $notification->title, $notification->message);
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

                    $this->data['is_pushed'] = !empty($device_token);
                    $notification = $this->repository->create($this->data);

                    $this->data['device_token'] = $device_token;

                    if ($notification && $device_token) {
                        $this->firebaseService->sendFirebaseNotification([$device_token], null, $notification->title, $notification->message);
                    }
                }
            }
        }
        return true;
    }


    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {
        $this->data = $request->validated();
        $notification = $this->repository->findOrFail($request->id);

        $this->handleApprovalPackage($notification, $this->data);

        return $this->repository->update($this->data['id'], $this->data);
    }

    /**
     * @throws Exception
     */
    private function handleApprovalPackage(mixed $notification, $data): void
    {
        $data['status'] = NotificationStatus::READ;
        if ($notification->type == MessageType::PAYMENT) {
            switch ($data['approval_status'] ?? null) {
                case ApprovalStatus::ACTIVE->value:
                    $this->handlePaymentApproval($notification, $data);
                    break;

                case ApprovalStatus::REJECTED->value:
                    $this->handlePaymentRejected($notification, $data);
                    break;

                default:
                    break;
            }
        }
    }

    public function handlePaymentRejected($notification, $data): void
    {

    }

    /**
     * @throws Exception
     */
    private function handlePaymentApproval($notification, $data): void
    {
        $package = $notification->package;
        $user = $this->userRepository->findOrFail($notification->user_id_attribute);
        $startDate = now();
        $additionalDays = 0;

        // Lấy gói hiện tại nếu còn hiệu lực
        $currentUserPackage = $user->userPackages->first();


        // Nếu còn hạn thì tính số ngày dư
        if ($currentUserPackage) {
            $additionalDays = $startDate->diffInDays($currentUserPackage->end_date);
        }

        // Gộp ngày còn lại và số ngày của gói mới
        $endDate = $startDate->copy()->addDays($package->days + $additionalDays);

        // Cập nhật hoặc tạo mới user_package
        $userPackage = $currentUserPackage;

        $userPackage->update([
            'package_id' => $package->id,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => PackageUserStatus::Active,
            'current_type' => $package->type
        ]);

        // Cập nhật trạng thái thông báo
        $notifications = $this->repository->getByQueryBuilder([
            'package_id' => $package->id,
            'user_id_attribute' => $user->id
        ])
            ->where('created_at', $notification->created_at)
            ->get();

        foreach ($notifications as $notificationItem) {
            $this->repository->update($notificationItem->id, ['approval_status' => ApprovalStatus::ACTIVE]);
        }

        // Gửi thông báo firebase
        $this->firebaseService->notifyUserPackageApproved($user);

        // Tạo giao dịch thanh toán
        $this->transactionService->store($user, $package);
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
        return $this->repository->getBy(
            [
                'admin_id' => $data['admin_id'],
                'status' => NotificationStatus::NOT_READ
            ]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $filters = ['status' => NotificationStatus::NOT_READ];
            if (!empty($data['admin_id'])) {
                $filters['admin_id'] = $data['admin_id'];
            }

            $notifications = $this->repository->getBy($filters);

            foreach ($notifications as $notification) {
                $this->repository->update($notification->id, ['status' => NotificationStatus::READ]);
            }

            return response()->json(['success' => "Updated successfully"]);
        } catch (Exception $e) {
            return response()->json(['error' => 'Failed to update notification status'], 500);
        }
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
