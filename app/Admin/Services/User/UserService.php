<?php

namespace App\Admin\Services\User;

use App\Admin\Http\Requests\User\DepositWalletRequest;
use App\Admin\Http\Requests\User\WithdrawWalletRequest;
use App\Admin\Repositories\AffiliateHistory\AffiliateHistoryRepositoryInterface;
use App\Admin\Repositories\Package\PackageRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Repositories\UserSession\UserSessionRepositoryInterface;
use App\Admin\Services\Notification\NotificationFirebaseServiceInterface;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Api\V1\Support\UseLog;
use App\Enums\DeleteStatus;
use App\Enums\Notification\MessageType;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Package\PackageType;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use App\Enums\User\UserStatus;
use App\Models\FeatureUsage;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\UserPackage;
use App\Models\UserSession;
use App\Traits\NotifiesViaFirebase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Admin\Traits\Setup;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService implements UserServiceInterface
{
    use Setup, Roles, UseLog, NotifiesViaFirebase;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected UserRepositoryInterface $repository;

    protected PackageRepositoryInterface $packageRepository;

    protected NotificationFirebaseServiceInterface $notificationFirebaseService;

    protected UserSessionRepositoryInterface $userSessionRepository;


    public function __construct(
        UserRepositoryInterface    $repository,
        PackageRepositoryInterface $packageRepository,
        NotificationFirebaseServiceInterface $notificationFirebaseService,
        UserSessionRepositoryInterface $userSessionRepository
    )
    {
        $this->repository = $repository;
        $this->packageRepository = $packageRepository;
        $this->notificationFirebaseService = $notificationFirebaseService;
        $this->userSessionRepository = $userSessionRepository;
    }

    /**
     * @throws Exception
     */
    public function store(Request $request): object|false
    {
        $data = $request->validated();
        $data['code'] = $this->createCodeUser();
        $data['longitude'] = $request['lng'];
        $data['latitude'] = $request['lat'];
        $data['password'] = bcrypt($data['password']);
        $data['email'] = AESHelper::encrypt($data['email']);
        $data['username'] = $data['email'];
        $data['phone'] = !empty($data['phone']) ? AESHelper::encrypt($data['phone']) : null;
        $data['address'] = AESHelper::encrypt($data['address']);
        $user = $this->repository->create($data);

        $data['user_id'] = $user->id;

        //create role
        $this->repository->assignRoles($user, [$this->getRoleCustomer()]);
        return $user;
    }

    /**
     * @throws Exception
     */
    public function update(Request $request): object|bool
    {

        $data = $request->validated();
        $data['longitude'] = $request['lng'];
        $data['latitude'] = $request['lat'];
        $packageId = $data['package_id'] ?? null;
        $startDate = $data['start_date'] ?? null;
        $endDate = $data['end_date'] ?? null;
        if (isset($data['email'])) {
            $data['email'] = AESHelper::encrypt($data['email']);
            $data['username'] = $data['email'];
        }
        if (isset($data['phone'])) {
            $data['phone'] = !empty($data['phone']) ? AESHelper::encrypt($data['phone']) : null;
        }
        if (isset($data['address'])) {
            $data['address'] = AESHelper::encrypt($data['address']);
        }
        if (isset($data['password']) && $data['password']) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }
        $user = $this->repository->findOrFail($data['id']);
        $oldStatus = $user->status;
        $user->update($data);
        if ($user->status !== $oldStatus && ($user->status === UserStatus::Inactive || $user->status === UserStatus::Lock)) {
            try {
                $this->notificationFirebaseService->notifyUserLocked($user);
            } catch (Exception $e) {
                $this->logError('Failed to send lock notification', $e);
            }
        }
        $package = $this->packageRepository->findOrFail($packageId);
        $currentType = $package->type;
        $currentUserPackage = $user->userPackages()->where('status', PackageUserStatus::Active)->first();
        if ($currentUserPackage) {
            $oldType = $currentUserPackage->current_type;
            $currentUserPackage->update([
                'package_id' => $packageId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'current_type' => $currentType
            ]);

            // Thu hồi các thiết bị dư thừa nếu số thiết bị đang hoạt động vượt quá hạn mức gói mới
            $allowed = $user->getMaxDevicesAllowed();
            $activeDevices = UserDevice::where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('created_at', 'asc')
                ->orderBy('id', 'asc')
                ->get();

            if ($activeDevices->count() > $allowed) {
                $excessDevices = $activeDevices->slice($allowed);
                foreach ($excessDevices as $device) {
                    $this->revokeDevice($user->id, $device->id);
                }
            }
        }

        return $user;
    }

    /**
     * @throws Exception
     */
    public function delete($id): object
    {
        return $this->repository->delete($id);

    }

    /**
     * Xóa vĩnh viễn tài khoản người dùng và toàn bộ dữ liệu liên quan
     * @throws Exception
     */
    public function forceDelete($id): bool
    {
        $user = $this->repository->findOrFail($id);

        DB::transaction(function () use ($user) {
            // 1. Gỡ người giới thiệu nếu tài khoản này là người giới thiệu của tài khoản khác
            User::where('referrer_id', $user->id)->update(['referrer_id' => null]);

            // 2. Xóa các phiên đăng nhập và thiết bị
            UserSession::where('user_id', $user->id)->delete();
            UserDevice::where('user_id', $user->id)->delete();

            // 3. Thu hồi Sanctum tokens nếu có
            if (method_exists($user, 'tokens')) {
                $user->tokens()->delete();
            }

            // 4. Gỡ bỏ quyền và vai trò
            if (method_exists($user, 'roles')) {
                $user->roles()->detach();
            }
            if (method_exists($user, 'permissions')) {
                $user->permissions()->detach();
            }

            // 5. Xóa dữ liệu sử dụng tính năng và thông báo
            FeatureUsage::where('user_id', $user->id)->delete();
            Notification::where('user_id', $user->id)
                ->orWhere('user_id_attribute', $user->id)
                ->delete();

            // 6. Xóa các gói dịch vụ và lịch sử giao dịch
            $user->userPackages()->delete();
            $user->transactions()->delete();

            // 7. Xóa dữ liệu hồ sơ trẻ em và các file ảnh đính kèm
            foreach ($user->children as $child) {
                if ($child->journals) {
                    foreach ($child->journals as $journal) {
                        if ($journal->image && !str_starts_with($journal->image, 'http') && file_exists(public_path($journal->image))) {
                            @unlink(public_path($journal->image));
                        }
                    }
                }
                if ($child->avatar && !str_starts_with($child->avatar, 'http') && file_exists(public_path($child->avatar))) {
                    @unlink(public_path($child->avatar));
                }
                $child->delete();
            }

            // 8. Xóa ảnh đại diện của người dùng nếu có
            if ($user->avatar && !str_starts_with($user->avatar, 'http') && file_exists(public_path($user->avatar))) {
                @unlink(public_path($user->avatar));
            }

            // 9. Xóa người dùng vĩnh viễn khỏi database
            $user->delete();
        });

        return true;
    }

    public function actionMultipleRecode(Request $request): bool
    {
        $this->data = $request->all();
        switch ($this->data['action']) {
            case 'active':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', UserStatus::Active);
                }
                return true;
            case 'inactive':
                foreach ($this->data['id'] as $value) {
                    $user = $this->repository->find($value);
                    if ($user && $user->status !== UserStatus::Inactive) {
                        $this->repository->updateAttribute($value, 'status', UserStatus::Inactive);
                        try {
                            $this->notificationFirebaseService->notifyUserLocked($user);
                        } catch (Exception $e) {
                            $this->logError('Failed to send lock notification', $e);
                        }
                    }
                }
                return true;
            case 'lock':
                foreach ($this->data['id'] as $value) {
                    $user = $this->repository->find($value);
                    if ($user && $user->status !== UserStatus::Lock) {
                        $this->repository->updateAttribute($value, 'status', UserStatus::Lock);
                        try {
                            $this->notificationFirebaseService->notifyUserLocked($user);
                        } catch (Exception $e) {
                            $this->logError('Failed to send lock notification', $e);
                        }
                    }
                }
                return true;
            case 'delete':
                foreach ($this->data['id'] as $value) {
                    $this->forceDelete($value);
                }
                return true;

            default:
                return false;
        }
    }


    public function clearNormalTokens(): bool
    {
        try {
            $userPackages = UserPackage::where('status', PackageUserStatus::Active)
                ->whereIn('current_type', [PackageType::Normal, PackageType::Trial])
                ->get();

            foreach ($userPackages as $userPackage) {
                $this->userSessionRepository->deleteAllSessionTokens($userPackage->user_id);
            }
            return true;
        } catch (Exception $e) {
            $this->logError('Failed to clear normal tokens:', $e);
            return false;
        }
    }

    public function revokeDevice(int $userId, int $deviceId): bool
    {
        try {
            $device = UserDevice::where('id', $deviceId)
                ->where('user_id', $userId)
                ->first();

            if (!$device) {
                return false;
            }

            $device->update(['is_active' => false]);

            // Invalidate session liên quan đến thiết bị này
            $sessions = UserSession::where('user_id', $userId)
                ->where('status', DeleteStatus::NotDeleted)
                ->where(function ($query) use ($device) {
                    $query->where('device_token', $device->device_id)
                        ->orWhere('device_token', $device->device_token);
                })
                ->get();

            foreach ($sessions as $session) {
                try {
                    JWTAuth::setToken($session->access_token)->invalidate();
                } catch (\Exception $e) {
                    // ignore if already invalid/expired
                }
                $session->update(['status' => DeleteStatus::Deleted]);
            }

            return true;
        } catch (Exception $e) {
            $this->logError('Failed to revoke device:', $e);
            return false;
        }
    }

    public function revokeAllDevices(int $userId): bool
    {
        try {
            // Đánh dấu tất cả thiết bị của user là đã giải phóng
            UserDevice::where('user_id', $userId)->update(['is_active' => false]);

            // Invalidate toàn bộ session của user
            $this->userSessionRepository->deleteAllSessionTokens($userId);

            return true;
        } catch (Exception $e) {
            $this->logError('Failed to revoke all devices:', $e);
            return false;
        }
    }

    /**
     * Nạp tiền vào ví của thành viên qua Database Transaction & Pessimistic Lock
     *
     * @param DepositWalletRequest $request
     * @param mixed $adminUser
     * @return array
     * @throws Exception
     */
    public function depositWallet(DepositWalletRequest $request, $adminUser): array
    {
        $userId = (int) $request->user_id;
        $amount = (float) $request->amount;
        $reason = trim((string) $request->admin_note);
        $sendNotification = (bool) ($request->send_notification ?? true);

        return DB::transaction(function () use ($userId, $amount, $reason, $adminUser, $sendNotification) {
            $lockedUser = $this->repository->findForUpdate($userId);

            if (!$lockedUser) {
                throw new Exception('Không tìm thấy tài khoản thành viên.');
            }

            $currentBalance = (float) ($lockedUser->wallet_balance ?? 0);
            $newBalance = $currentBalance + $amount;

            // 1. Cập nhật số dư ví
            $this->repository->update($lockedUser->id, [
                'wallet_balance' => $newBalance,
            ]);

            // 2. Sinh mã giao dịch duy nhất
            $code = 'DP' . date('Ymd') . strtoupper(Str::random(5));

            // 3. Ghi nhận biến động vào affiliate_histories
            $affiliateHistoryRepository = app(AffiliateHistoryRepositoryInterface::class);
            $affiliateHistoryRepository->create([
                'user_id' => $lockedUser->id,
                'source_user_id' => null,
                'amount' => $amount,
                'balance_after' => $newBalance,
                'type' => 'admin_deposit',
                'description' => "Ban Quản Trị nạp tiền vào ví: {$reason}",
            ]);

            // 4. Tạo giao dịch tài chính hệ thống trong transactions
            $transaction = Transaction::create([
                'code' => $code,
                'user_id' => $lockedUser->id,
                'package_id' => null,
                'amount' => $amount,
                'type' => TransactionType::Deposit,
                'status' => TransactionStatus::Confirmed,
                'admin_note' => "Admin [{$adminUser->name}]: {$reason}",
                'processed_by' => $adminUser->id ?? null,
                'processed_at' => now(),
            ]);

            // 5. Gửi thông báo đẩy Firebase & lưu chuông thông báo nếu được chọn
            if ($sendNotification) {
                try {
                    $amountFmt = number_format($amount, 0, ',', '.') . 'đ';
                    $balanceFmt = number_format($newBalance, 0, ',', '.') . 'đ';
                    $title = config('notifications.admin_deposit_wallet.title', 'Biến động số dư ví');
                    $messageTemplate = config(
                        'notifications.admin_deposit_wallet.message',
                        'Ví của bạn vừa được cộng +{amount} từ Ban Quản Trị. Lý do: {reason}. Số dư hiện tại: {balance}.'
                    );

                    $body = str_replace(
                        ['{amount}', '{reason}', '{balance}'],
                        [$amountFmt, $reason, $balanceFmt],
                        $messageTemplate
                    );

                    $this->sendFirebaseNotificationToUser(
                        $lockedUser,
                        $title,
                        $body,
                        MessageType::AFFILIATE,
                        [
                            'type' => 'wallet_deposit',
                            'transaction_id' => (string) $transaction->id,
                            'code' => $transaction->code,
                        ]
                    );
                } catch (\Throwable $e) {
                    $this->logError('Lỗi gửi Firebase notification khi nạp tiền ví User: ' . $e->getMessage(), $e);
                }
            }

            return [
                'user_id' => $lockedUser->id,
                'fullname' => $lockedUser->fullname,
                'code' => $lockedUser->code,
                'amount' => $amount,
                'amount_formatted' => number_format($amount, 0, ',', '.') . ' đ',
                'old_balance' => $currentBalance,
                'old_balance_formatted' => number_format($currentBalance, 0, ',', '.') . ' đ',
                'new_balance' => $newBalance,
                'new_balance_formatted' => number_format($newBalance, 0, ',', '.') . ' đ',
                'transaction_code' => $code,
            ];
        });
    }

    /**
     * Rút / Trừ tiền từ ví của thành viên
     */
    public function withdrawWallet(WithdrawWalletRequest $request, $adminUser): array
    {
        $userId = (int) $request->user_id;
        $amount = (float) $request->amount;
        $reason = trim((string) $request->admin_note);
        $sendNotification = (bool) ($request->send_notification ?? true);

        return DB::transaction(function () use ($userId, $amount, $reason, $adminUser, $sendNotification) {
            $lockedUser = $this->repository->findForUpdate($userId);

            if (!$lockedUser) {
                throw new Exception('Không tìm thấy tài khoản thành viên.');
            }

            $currentBalance = (float) ($lockedUser->wallet_balance ?? 0);

            if ($amount > $currentBalance) {
                $curFmt = number_format($currentBalance, 0, ',', '.') . 'đ';
                $amtFmt = number_format($amount, 0, ',', '.') . 'đ';
                throw new Exception("Số dư ví của thành viên ({$curFmt}) không đủ để thực hiện rút {$amtFmt}.");
            }

            $newBalance = $currentBalance - $amount;

            // 1. Cập nhật số dư ví
            $this->repository->update($lockedUser->id, [
                'wallet_balance' => $newBalance,
            ]);

            // 2. Sinh mã giao dịch duy nhất
            $code = 'WD' . date('Ymd') . strtoupper(Str::random(5));

            // 3. Ghi nhận biến động vào affiliate_histories
            $affiliateHistoryRepository = app(AffiliateHistoryRepositoryInterface::class);
            $affiliateHistoryRepository->create([
                'user_id' => $lockedUser->id,
                'source_user_id' => null,
                'amount' => -$amount,
                'balance_after' => $newBalance,
                'type' => 'admin_withdraw',
                'description' => "Ban Quản Trị rút/trừ tiền từ ví: {$reason}",
            ]);

            // 4. Tạo giao dịch tài chính hệ thống trong transactions
            $transaction = Transaction::create([
                'code' => $code,
                'user_id' => $lockedUser->id,
                'package_id' => null,
                'amount' => $amount,
                'type' => TransactionType::Withdraw,
                'status' => TransactionStatus::Confirmed,
                'admin_note' => "Admin [{$adminUser->name}]: {$reason}",
                'processed_by' => $adminUser->id ?? null,
                'processed_at' => now(),
            ]);

            // 5. Gửi thông báo đẩy Firebase & lưu chuông thông báo nếu được chọn
            if ($sendNotification) {
                try {
                    $amountFmt = number_format($amount, 0, ',', '.') . 'đ';
                    $balanceFmt = number_format($newBalance, 0, ',', '.') . 'đ';
                    $title = config('notifications.admin_withdraw_wallet.title', 'Biến động số dư ví');
                    $messageTemplate = config(
                        'notifications.admin_withdraw_wallet.message',
                        'Ví của bạn vừa bị trừ -{amount} từ Ban Quản Trị. Lý do: {reason}. Số dư hiện tại: {balance}.'
                    );

                    $body = str_replace(
                        ['{amount}', '{reason}', '{balance}'],
                        [$amountFmt, $reason, $balanceFmt],
                        $messageTemplate
                    );

                    $this->sendFirebaseNotificationToUser(
                        $lockedUser,
                        $title,
                        $body,
                        MessageType::AFFILIATE,
                        [
                            'type' => 'wallet_withdraw',
                            'transaction_id' => (string) $transaction->id,
                            'code' => $transaction->code,
                        ]
                    );
                } catch (\Throwable $e) {
                    $this->logError('Lỗi gửi Firebase notification khi rút tiền ví User: ' . $e->getMessage(), $e);
                }
            }

            return [
                'user_id' => $lockedUser->id,
                'fullname' => $lockedUser->fullname,
                'code' => $lockedUser->code,
                'amount' => $amount,
                'amount_formatted' => number_format($amount, 0, ',', '.') . ' đ',
                'old_balance' => $currentBalance,
                'old_balance_formatted' => number_format($currentBalance, 0, ',', '.') . ' đ',
                'new_balance' => $newBalance,
                'new_balance_formatted' => number_format($newBalance, 0, ',', '.') . ' đ',
                'transaction_code' => $code,
            ];
        });
    }
}
