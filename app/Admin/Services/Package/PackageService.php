<?php

namespace App\Admin\Services\Package;

use App\Admin\Repositories\Package\PackageRepositoryInterface;
use App\Admin\Services\User\UserServiceInterface;
use App\Admin\Traits\Setup;
use App\Api\V1\Support\UseLog;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageUserStatus;
use App\Models\UserDevice;
use App\Models\UserPackage;
use Exception;
use Illuminate\Http\Request;

class PackageService implements PackageServiceInterface
{
    use Setup, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected PackageRepositoryInterface $repository;
    protected UserServiceInterface $userService;

    public function __construct(
        PackageRepositoryInterface $repository,
        UserServiceInterface $userService,
    ) {
        $this->repository = $repository;
        $this->userService = $userService;
    }


    /**
     * Chuẩn hóa dữ liệu trước khi lưu
     */
    protected function prepareData(array $data): array
    {
        $data['description'] = json_encode($data['description'] ?? []);
        $discountType = $data['discount_type'] ?? 'none';

        if (empty($discountType) || $discountType === 'none') {
            $data['discount_type'] = 'none';
            $data['discount_value'] = 0;
        } else {
            $data['discount_value'] = (float) ($data['discount_value'] ?? 0);
        }

        if (!empty($data['discount_code'])) {
            $data['discount_code'] = strtoupper(trim($data['discount_code']));
        } else {
            $data['discount_code'] = null;
        }

        return $data;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $this->prepareData($request->validated());
        $data['status'] = PackageStatus::Draft;
        return $this->repository->create($data);
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {
        $data = $this->prepareData($request->validated());
        $package = $this->repository->findOrFail($data['id']);
        $oldMaxDevices = (int) ($package->max_devices ?? 1);
        $newMaxDevices = (int) ($data['max_devices'] ?? 1);

        $result = $this->repository->update($data['id'], $data);

        if ($result && $newMaxDevices < $oldMaxDevices) {
            $this->enforceDeviceLimitOnPackageDowngrade((int) $data['id'], $newMaxDevices);
        }

        return $result;
    }

    /**
     * Thu hồi các thiết bị vượt quá số lượng cho phép khi giảm giới hạn max_devices của gói cước.
     * Chỉ giữ lại N thiết bị đầu tiên được liên kết sớm nhất (created_at ASC, id ASC).
     */
    protected function enforceDeviceLimitOnPackageDowngrade(int $packageId, int $newMaxDevices): void
    {
        try {
            $processedUserIds = [];

            UserPackage::where('package_id', $packageId)
                ->where('status', PackageUserStatus::Active)
                ->where('end_date', '>=', now())
                ->chunk(100, function ($userPackages) use ($newMaxDevices, &$processedUserIds) {
                    foreach ($userPackages as $userPackage) {
                        $user = $userPackage->user;
                        if (!$user || isset($processedUserIds[$user->id])) {
                            continue;
                        }
                        $processedUserIds[$user->id] = true;

                        // Lấy hạn mức thực tế theo các gói đang kích hoạt của user
                        $allowed = $user->getMaxDevicesAllowed();

                        // Lấy danh sách thiết bị đang hoạt động, xếp theo thứ tự đăng ký sớm nhất
                        $activeDevices = UserDevice::where('user_id', $user->id)
                            ->where('is_active', true)
                            ->orderBy('created_at', 'asc')
                            ->orderBy('id', 'asc')
                            ->get();

                        if ($activeDevices->count() > $allowed) {
                            // Giữ lại $allowed thiết bị đầu tiên, giải phóng các thiết bị từ vị trí thứ $allowed trở đi
                            $excessDevices = $activeDevices->slice($allowed);
                            foreach ($excessDevices as $device) {
                                $revoked = $this->userService->revokeDevice($user->id, $device->id);
                                if ($revoked) {
                                    $this->logInfo("Auto-revoked device ID {$device->id} (Device ID: {$device->device_id}, Name: {$device->device_name}) for User ID {$user->id} due to package limit reduction to {$allowed} devices.");
                                }
                            }
                        }
                    }
                });
        } catch (Exception $e) {
            $this->logError("Failed to enforce device limits after package downgrade for package {$packageId}:", $e);
        }
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);

    }

    public function actionMultipleRecords(Request $request): bool
    {
        $this->data = $request->all();

        switch ($this->data['action']) {
            case PackageStatus::Active->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', PackageStatus::Active);
                }
                return true;
            case PackageStatus::Draft->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', PackageStatus::Draft);
                }
                return true;
            case PackageStatus::Deleted->value:
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', PackageStatus::Deleted);
                }
                return true;

            default:
                return false;
        }
    }
}
