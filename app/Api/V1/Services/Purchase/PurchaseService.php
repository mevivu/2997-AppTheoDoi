<?php

namespace App\Api\V1\Services\Purchase;

use App\Admin\Repositories\UserSession\UserSessionRepositoryInterface;
use App\Admin\Services\GooglePlay\GooglePlayServiceInterface;
use App\Admin\Services\Transaction\TransactionServiceInterface;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Http\Requests\Purchase\AppleStoreRequest;
use App\Api\V1\Http\Requests\Purchase\GooglePlayRequest;
use App\Api\V1\Http\Resources\Package\AuthPackageResource;
use App\Api\V1\Http\Resources\Package\PackageResource;
use App\Api\V1\Repositories\Package\PackageRepositoryInterface;
use App\Api\V1\Repositories\Transaction\TransactionRepositoryInterface;
use App\Api\V1\Services\Notification\NotificationServiceInterface;
use App\Admin\Services\AppleStore\AppleStoreServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\DeleteStatus;
use App\Enums\GooglePlay\SubscriptionState;
use App\Enums\Package\PackageType;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Transaction\TransactionEnumService;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\User\UserStatus;
use App\Models\User;
use App\Models\UserDevice;
use App\Traits\MessageSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Throwable;
use Tymon\JWTAuth\Facades\JWTAuth;


class PurchaseService implements PurchaseServiceInterface
{
    use AuthSupport, AuthServiceApi;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected PackageRepositoryInterface $packageRepository;

    protected GooglePlayServiceInterface $googlePlayService;
    protected TransactionServiceInterface $transactionService;
    protected TransactionRepositoryInterface $transactionRepository;
    protected NotificationServiceInterface $notificationService;
    protected AppleStoreServiceInterface $appleStoreService;
    protected UserSessionRepositoryInterface $userSessionRepository;


    public function __construct(
        PackageRepositoryInterface     $packageRepository,
        GooglePlayServiceInterface     $googlePlayService,
        TransactionServiceInterface    $transactionService,
        TransactionRepositoryInterface $transactionRepository,
        NotificationServiceInterface   $notificationService,
        AppleStoreServiceInterface     $appleStoreService,
        UserSessionRepositoryInterface $userSessionRepository
    )
    {
        $this->packageRepository = $packageRepository;
        $this->googlePlayService = $googlePlayService;
        $this->transactionService = $transactionService;
        $this->transactionRepository = $transactionRepository;
        $this->notificationService = $notificationService;
        $this->appleStoreService = $appleStoreService;
        $this->userSessionRepository = $userSessionRepository;
    }


    /**
     * @param GooglePlayRequest $request
     * Trạng thái
     * SUBSCRIPTION_STATE_ACTIVE    Đang hoạt động (đã thanh toán và còn hạn) ✅
     * SUBSCRIPTION_STATE_EXPIRED    Đã hết hạn ❌
     * SUBSCRIPTION_STATE_CANCELED    Người dùng đã hủy (sẽ hết hạn vào cuối kỳ hiện tại) ⚠️
     * SUBSCRIPTION_STATE_ON_HOLD    Google đang giữ (ví dụ do lỗi thanh toán)
     * SUBSCRIPTION_STATE_IN_GRACE_PERIOD    Gia hạn tạm thời do lỗi thanh toán
     * SUBSCRIPTION_STATE_PAUSED    Người dùng tạm dừng thuê bao
     * @return array
     */
    public function verifyPurchaseGooglePlay(GooglePlayRequest $request): array
    {
        $data = $request->validated();
        $user = $this->getCurrentUser();
        if (!$user) {
            throw new BadRequestException('Vui lòng đăng nhập trước khi thực hiện giao dịch.');
        }

        return $this->executeGooglePlayVerification($user, $data['product_id'], $data['purchase_token']);
    }

    public function verifyPurchaseAppleStore(AppleStoreRequest $request): array
    {
        $data = $request->validated();
        $user = $this->getCurrentUser();
        if (!$user) {
            throw new BadRequestException('Vui lòng đăng nhập trước khi thực hiện giao dịch.');
        }

        return $this->executeAppleStoreVerification($user, $data['product_id'], $data['transaction_id']);
    }

    public function verifyUpgradeGooglePlay(GooglePlayRequest $request): array
    {
        $data = $request->validated();
        $upgradeContext = $this->decodeAndValidateUpgradeToken($data['upgrade_token'] ?? null);
        /** @var User $user */
        $user = $upgradeContext['user'];

        $result = $this->executeGooglePlayVerification($user, $data['product_id'], $data['purchase_token']);

        $authSession = $this->issuePostUpgradeSession($user->fresh(), $upgradeContext['payload'], $request);
        if ($authSession) {
            $result['auth'] = $authSession;
        }

        return $result;
    }

    public function verifyUpgradeAppleStore(AppleStoreRequest $request): array
    {
        $data = $request->validated();
        $upgradeContext = $this->decodeAndValidateUpgradeToken($data['upgrade_token'] ?? null);
        /** @var User $user */
        $user = $upgradeContext['user'];

        $result = $this->executeAppleStoreVerification($user, $data['product_id'], $data['transaction_id']);

        $authSession = $this->issuePostUpgradeSession($user->fresh(), $upgradeContext['payload'], $request);
        if ($authSession) {
            $result['auth'] = $authSession;
        }

        return $result;
    }

    private function executeGooglePlayVerification($user, string $productId, string $purchaseToken): array
    {
        $package = $this->packageRepository->findByField('code', $productId);
        if (!$package) {
            throw new BadRequestException('Không tìm thấy gói dịch vụ với mã: ' . $productId);
        }

        $verificationResult = $this->googlePlayService->verifyPurchase($productId, $purchaseToken, true);

        if (!$verificationResult['success']) {
            throw new BadRequestException(MessageSystem::VERIFY_ERROR . $verificationResult['error']);
        }

        $purchaseData = $verificationResult['data'];
        $statusValue = $purchaseData['subscriptionState'] ?? null;
        $statusEnum = $statusValue ? SubscriptionState::tryFrom($statusValue) : null;
        $isActive = $statusEnum ? $statusEnum->isActive() : ($statusValue === 'SUBSCRIPTION_STATE_ACTIVE');

        $purchased = false;
        if ($isActive) {
            $purchased = DB::transaction(function () use ($user, $package, $purchaseData, $purchaseToken) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first() ?? $user;
                return $this->handlePurchase($lockedUser, $package, $purchaseData, $purchaseToken);
            });
        }

        if ($purchased) {
            try {
                $this->notificationService->sendPaymentSuccessNotification($user, $package->name);
            } catch (Throwable $e) {
                Log::warning('Failed to send Google Play payment notification: ' . $e->getMessage());
            }
        }

        $activeUserPackage = $user->fresh()->reconcileActivePackage();

        return [
            'package' => new PackageResource($package),
            'user_package' => $activeUserPackage ? new AuthPackageResource($activeUserPackage) : null,
            'product_id' => $productId,
            'order_id' => $purchaseData['orderId'] ?? null,
            'expiry_time' => $purchaseData['lineItem']['expiryTime'] ?? null,
            'auto_renewing' => $purchaseData['lineItem']['autoRenewing'] ?? null,
            'status' => $purchaseData['subscriptionState'] ?? null,
        ];
    }

    private function executeAppleStoreVerification($user, string $productId, string $transactionId): array
    {
        $package = $this->packageRepository->findByField('code', $productId);
        if (!$package) {
            throw new BadRequestException('Không tìm thấy gói dịch vụ với mã: ' . $productId);
        }

        $verificationResult = $this->appleStoreService->verifyPurchase($productId, $transactionId, true);

        if (!$verificationResult['success']) {
            throw new BadRequestException(MessageSystem::VERIFY_ERROR . ($verificationResult['error'] ?? 'Unknown error'));
        }

        $purchaseData = $verificationResult['data'];
        $statusValue = $purchaseData['subscriptionState'] ?? null;
        $isActive = $statusValue === 'SUBSCRIPTION_STATE_ACTIVE';

        $purchased = false;
        if ($isActive) {
            $purchased = DB::transaction(function () use ($user, $package, $purchaseData, $transactionId) {
                $lockedUser = User::where('id', $user->id)->lockForUpdate()->first() ?? $user;
                return $this->handlePurchase($lockedUser, $package, $purchaseData, $transactionId, TransactionEnumService::APPLE);
            });
        }

        if ($purchased) {
            try {
                $this->notificationService->sendPaymentSuccessNotification($user, $package->name);
            } catch (Throwable $e) {
                Log::warning('Failed to send Apple Store payment notification: ' . $e->getMessage());
            }
        }

        $activeUserPackage = $user->fresh()->reconcileActivePackage();

        return [
            'package' => new PackageResource($package),
            'user_package' => $activeUserPackage ? new AuthPackageResource($activeUserPackage) : null,
            'product_id' => $productId,
            'order_id' => $purchaseData['orderId'] ?? null,
            'expiry_time' => $purchaseData['lineItem']['expiryTime'] ?? null,
            'auto_renewing' => $purchaseData['lineItem']['autoRenewing'] ?? null,
            'status' => $purchaseData['subscriptionState'] ?? null,
        ];
    }

    private function decodeAndValidateUpgradeToken(?string $upgradeToken): array
    {
        if (empty($upgradeToken)) {
            throw new BadRequestException('Phiên nâng cấp không hợp lệ hoặc đã hết hạn. Vui lòng đăng nhập lại để đồng bộ gói VIP.');
        }

        try {
            $decodedJson = Crypt::decryptString($upgradeToken);
            $payload = json_decode($decodedJson, true);
        } catch (Throwable $e) {
            throw new BadRequestException('Mã xác thực nâng cấp không hợp lệ. Vui lòng đăng nhập lại.');
        }

        if (!is_array($payload)
            || ($payload['purpose'] ?? '') !== 'device_limit_upgrade'
            || empty($payload['user_id'])
            || empty($payload['exp'])
            || (int) $payload['exp'] < time()
        ) {
            throw new BadRequestException('Phiên nâng cấp đã hết hạn. Vui lòng đăng nhập lại để kích hoạt gói VIP.');
        }

        $user = User::find((int) $payload['user_id']);
        if (!$user || $user->status === UserStatus::Lock || $user->status === UserStatus::Inactive) {
            throw new BadRequestException('Tài khoản không khả dụng hoặc đã bị khóa.');
        }

        return [
            'user' => $user,
            'payload' => $payload,
        ];
    }

    private function issuePostUpgradeSession(User $user, array $payload, Request $request): ?array
    {
        try {
            $deviceId = $payload['device_id'] ?? ('web_' . md5($request->ip() . ($request->userAgent() ?? '')));
            $deviceName = $payload['device_name'] ?? 'Thiết bị di động';
            $deviceToken = $payload['device_token'] ?? null;
            $platform = $payload['platform'] ?? 'other';

            $userDevice = UserDevice::where('user_id', $user->id)
                ->where('device_id', $deviceId)
                ->first();

            if ($userDevice && $userDevice->is_active) {
                $userDevice->update([
                    'device_name' => $deviceName ?: $userDevice->device_name,
                    'device_token' => $deviceToken ?: $userDevice->device_token,
                    'platform' => $platform ?: $userDevice->platform,
                    'ip_address' => $request->ip(),
                    'last_active_at' => now(),
                ]);
            } else {
                $activeCount = UserDevice::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->count();
                $maxAllowed = $user->getMaxDevicesAllowed();
                if ($activeCount >= $maxAllowed) {
                    return null;
                }

                if ($userDevice) {
                    $userDevice->update([
                        'is_active' => true,
                        'device_name' => $deviceName ?: $userDevice->device_name,
                        'device_token' => $deviceToken ?: $userDevice->device_token,
                        'platform' => $platform ?: $userDevice->platform,
                        'ip_address' => $request->ip(),
                        'last_active_at' => now(),
                    ]);
                } else {
                    UserDevice::create([
                        'user_id' => $user->id,
                        'device_id' => $deviceId,
                        'device_name' => $deviceName ?: 'Thiết bị di động',
                        'platform' => $platform,
                        'device_token' => $deviceToken,
                        'ip_address' => $request->ip(),
                        'is_active' => true,
                        'last_active_at' => now(),
                    ]);
                }
            }

            $accessToken = JWTAuth::fromUser($user);
            $refreshToken = JWTAuth::getJWTProvider()->encode([
                'user_id' => $user->id,
                'random' => rand() . time(),
                'is_refresh_token' => true,
                'exp' => time() + config('jwt.refresh_ttl'),
            ]);

            $this->userSessionRepository->create([
                'user_id' => $user->id,
                'access_token' => $accessToken,
                'device_token' => $deviceId,
                'status' => DeleteStatus::NotDeleted,
            ]);

            if (!empty($deviceToken)) {
                $user->update(['device_token' => $deviceToken]);
            }

            return [
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'user_id' => $user->id,
            ];
        } catch (Throwable $e) {
            Log::warning('Failed to issue post-upgrade session: ' . $e->getMessage());
            return null;
        }
    }

    // Xử lý giao dịch mua thành công
    private function handlePurchase($user, $package, $purchaseData, $purchaseToken, TransactionEnumService $serviceType = TransactionEnumService::GOOGLE_PLAY): bool
    {
        $orderId = $purchaseData['orderId'] ?? null;
        $originalOrderId = $purchaseData['originalTransactionId'] ?? null;

        if ($orderId) {
            $existingByOrder = $this->transactionRepository->findByOrderIdAndService($orderId, $serviceType);
            if ($existingByOrder) {
                if ((int) $existingByOrder->user_id !== (int) $user->id) {
                    throw new BadRequestException('Giao dịch này đã được kích hoạt trên một tài khoản khác.');
                }
                // Nếu giao dịch đã tồn tại trong bảng transactions của chính user này, đảm bảo user_packages đã được đồng bộ lên VIP
                $user->reconcileActivePackage();
                return false;
            }
        }

        if ($purchaseToken) {
            $existingByToken = $this->transactionRepository->findByField('purchase_token', $purchaseToken);
            if ($existingByToken && (int) $existingByToken->user_id !== (int) $user->id) {
                throw new BadRequestException('Giao dịch này đã được kích hoạt trên một tài khoản khác.');
            }
            if ($originalOrderId && $originalOrderId !== $orderId && $this->transactionRepository->existsByOrderIdAndService($originalOrderId, $serviceType) && $existingByToken) {
                $user->reconcileActivePackage();
                return false;
            }
        }

        $this->updateOrCreateUserPackage($user, $package);
        $this->transactionService->store($user, $package, $serviceType, $orderId, $purchaseToken);
        $user->reconcileActivePackage();
        return true;
    }

    private function updateOrCreateUserPackage($user, $package): void
    {
        $user->activatePackage($package);
    }


    public function handleWebhookNotification($data): void
    {
        $notificationType = $data['subscriptionNotification']['notificationType'] ?? null;
        $purchaseToken = $data['subscriptionNotification']['purchaseToken'] ?? null;
        $productId = $data['subscriptionNotification']['subscriptionId'] ?? null;
        Log::info("Subscription notification type: {$notificationType}", [
            'subscription_id' => $productId,
            'purchase_token' => $purchaseToken,
        ]);
        $typeNames = [
            1 => 'SUBSCRIPTION_RECOVERED',
            2 => 'SUBSCRIPTION_RENEWED',
            3 => 'SUBSCRIPTION_CANCELED',
            4 => 'SUBSCRIPTION_PURCHASED',
            5 => 'SUBSCRIPTION_ON_HOLD',
            6 => 'SUBSCRIPTION_IN_GRACE_PERIOD',
            7 => 'SUBSCRIPTION_RESTARTED',
            8 => 'SUBSCRIPTION_PRICE_CHANGE_CONFIRMED',
            9 => 'SUBSCRIPTION_DEFERRED',
            10 => 'SUBSCRIPTION_PAUSED',
            11 => 'SUBSCRIPTION_PAUSE_SCHEDULE_CHANGED',
            12 => 'SUBSCRIPTION_REVOKED',
            13 => 'SUBSCRIPTION_EXPIRED',
        ];
        $typeName = $typeNames[$notificationType] ?? 'UNKNOWN';

        switch ($notificationType) {
            case 1:
            case 2:
            case 4:
            case 7:
                $this->processWebhookActiveSubscription($productId, $purchaseToken, $typeName);
                break;

            case 12:
                $this->processRefund($productId, $purchaseToken);
                break;

            default:
                Log::info("Unhandled notification type: {$notificationType} ({$typeName})");
        }

    }

    private function processWebhookActiveSubscription(?string $productId, ?string $purchaseToken, string $typeName): void
    {
        if (empty($productId) || empty($purchaseToken)) {
            Log::warning("Webhook {$typeName}: Missing productId or purchaseToken");
            return;
        }

        $package = $this->packageRepository->findByField('code', $productId);
        if (!$package) {
            Log::warning("Webhook {$typeName}: Package not found for code {$productId}");
            return;
        }

        $verificationResult = $this->googlePlayService->verifyPurchase($productId, $purchaseToken, true);
        if (!$verificationResult['success']) {
            Log::warning("Webhook {$typeName}: Google Play verify failed", [
                'error' => $verificationResult['error'] ?? null,
            ]);
            return;
        }

        $purchaseData = $verificationResult['data'];
        $statusValue = $purchaseData['subscriptionState'] ?? null;
        $statusEnum = $statusValue ? SubscriptionState::tryFrom($statusValue) : null;
        $isActive = $statusEnum ? $statusEnum->isActive() : ($statusValue === 'SUBSCRIPTION_STATE_ACTIVE');
        if (!$isActive) {
            Log::info("Webhook {$typeName}: Subscription is not active ({$statusValue})");
            return;
        }

        $user = null;
        $existingTx = $this->transactionRepository->findByField('purchase_token', $purchaseToken);
        if ($existingTx && $existingTx->user) {
            $user = $existingTx->user;
        }

        if (!$user && !empty($purchaseData['linkedPurchaseToken'])) {
            $linkedTx = $this->transactionRepository->findByField('purchase_token', $purchaseData['linkedPurchaseToken']);
            if ($linkedTx && $linkedTx->user) {
                $user = $linkedTx->user;
            }
        }

        $obfuscatedAccountId = $purchaseData['obfuscatedExternalAccountId'] ?? null;
        if (!$user && !empty($obfuscatedAccountId) && ctype_digit((string) $obfuscatedAccountId)) {
            $user = User::find((int) $obfuscatedAccountId);
        }

        if (!$user) {
            Log::warning("Webhook {$typeName}: Cannot map purchaseToken to user yet", [
                'order_id' => $purchaseData['orderId'] ?? null,
                'obfuscated_account_id' => $obfuscatedAccountId,
            ]);
            return;
        }

        $purchased = DB::transaction(function () use ($user, $package, $purchaseData, $purchaseToken) {
            $lockedUser = User::where('id', $user->id)->lockForUpdate()->first() ?? $user;
            return $this->handlePurchase($lockedUser, $package, $purchaseData, $purchaseToken, TransactionEnumService::GOOGLE_PLAY);
        });

        if ($purchased) {
            Log::info("Webhook {$typeName}: Recorded purchase/renewal for user {$user->id}", [
                'order_id' => $purchaseData['orderId'] ?? null,
            ]);
            try {
                $this->notificationService->sendPaymentSuccessNotification($user, $package->name);
            } catch (Throwable $e) {
                Log::warning('Webhook notification send failed: ' . $e->getMessage());
            }
        }
    }

    private function processRefund($productId, $purchaseToken): void
    {
        $package = $this->packageRepository->findByField('code', $productId);
        Log::info("Package found for refund: " . ($package ? $package->name : 'Not Found'));
        $days = $package?->days;
        $transaction = $this->transactionRepository->findByField('purchase_token', $purchaseToken);
        if (!$transaction) {
            Log::warning("Refund failed: Transaction not found for token {$purchaseToken}");
            return;
        }
        $user = $transaction->user ?? null;
        if (!$user) {
            Log::warning("Refund failed: User not found for transaction ID {$transaction->id}");
            return;
        }
        $transaction->update(['status' => TransactionStatus::Refunded]);

        $userPackage = $user->userPackages()
            ->where('package_id', $package->id)
            ->where('status', PackageUserStatus::Active)
            ->first();

        if (!$userPackage) {
            Log::warning("Refund warning: Active user package not found for user ID {$user->id}");
            return;
        }
        $now = now();
        if ($userPackage->end_date < $now) {
            Log::info("Refund process: Package end_date ({$userPackage->end_date}) < now ({$now}) → reset type normal");

            $userPackage->update([
                'current_type' => PackageType::Normal,
                'end_date' => $now,
            ]);
            // Invalidate all active sessions for the user on downgrade
            $this->userSessionRepository->deleteAllSessionTokens($user->id);
        } else {
            $newEndDate = Carbon::parse($userPackage->end_date)->subDays($days);
            Log::info("Refund process: Adjusted end_date from {$userPackage->end_date} to {$newEndDate}");

            if ($newEndDate < $now) {
                $userPackage->update([
                    'current_type' => PackageType::Normal,
                    'end_date' => $now,
                ]);
                // Invalidate all active sessions for the user on downgrade
                $this->userSessionRepository->deleteAllSessionTokens($user->id);
            } else {
                $userPackage->update([
                    'end_date' => $newEndDate,
                ]);
            }
        }
        $this->notificationService->sendRefundNotification($transaction->user, $package->name);
    }
}
