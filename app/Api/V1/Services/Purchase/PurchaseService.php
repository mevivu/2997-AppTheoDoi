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
use App\Enums\GooglePlay\SubscriptionState;
use App\Enums\Package\PackageType;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Transaction\TransactionEnumService;
use App\Enums\Transaction\TransactionStatus;
use App\Traits\MessageSystem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


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
        $productId = $data['product_id'];
        $purchaseToken = $data['purchase_token'];
        $package = $this->packageRepository->findByField('code', $productId);

        $verificationResult = $this->googlePlayService->verifyPurchase($productId, $purchaseToken, true);

        if (!$verificationResult['success']) {
            throw new BadRequestException(MessageSystem::VERIFY_ERROR . $verificationResult['error']);
        }

        $purchaseData = $verificationResult['data'];
        $statusValue = $purchaseData['subscriptionState'];
        $statusEnum = SubscriptionState::tryFrom($statusValue);
        $isActive = $statusEnum->isActive();

        if ($isActive) {
            $this->handlePurchase($user, $package, $purchaseData, $purchaseToken);
        }
        return [
            'package' => new PackageResource($package),
            'user_package' => new AuthPackageResource($user->userPackages->first()),
            'product_id' => $productId,
            'order_id' => $purchaseData['orderId'] ?? null,
            'expiry_time' => $purchaseData['lineItem']['expiryTime'] ?? null,
            'auto_renewing' => $purchaseData['lineItem']['autoRenewing'] ?? null,
            'status' => $purchaseData['subscriptionState'] ?? null,
        ];
    }

    public function verifyPurchaseAppleStore(AppleStoreRequest $request): array
    {
        $data = $request->validated();
        $user = $this->getCurrentUser();
        $productId = $data['product_id'];
        $transactionId = $data['transaction_id'];
        $package = $this->packageRepository->findByField('code', $productId);

        $verificationResult = $this->appleStoreService->verifyPurchase($productId, $transactionId, true);

        if (!$verificationResult['success']) {
            throw new BadRequestException(MessageSystem::VERIFY_ERROR . ($verificationResult['error'] ?? 'Unknown error'));
        }

        $purchaseData = $verificationResult['data'];
        $statusValue = $purchaseData['subscriptionState'];
        $isActive = $statusValue === 'SUBSCRIPTION_STATE_ACTIVE';

        if ($isActive) {
            $this->handlePurchase($user, $package, $purchaseData, $transactionId, TransactionEnumService::APPLE);
        }
        return [
            'package' => new PackageResource($package),
            'user_package' => new AuthPackageResource($user->userPackages->first()),
            'product_id' => $productId,
            'order_id' => $purchaseData['orderId'] ?? null,
            'expiry_time' => $purchaseData['lineItem']['expiryTime'] ?? null,
            'auto_renewing' => $purchaseData['lineItem']['autoRenewing'] ?? null,
            'status' => $purchaseData['subscriptionState'] ?? null,
        ];
    }

    // Xử lý giao dịch mua thành công
    private function handlePurchase($user, $package, $purchaseData, $purchaseToken, TransactionEnumService $serviceType = TransactionEnumService::GOOGLE_PLAY): void
    {
        $orderId = $purchaseData['orderId'] ?? null;
        if ($orderId && $this->transactionRepository->existsByOrderIdAndService($orderId, $serviceType)) {
            return;
        }
        $this->updateOrCreateUserPackage($user, $package);
        $this->transactionService->store($user, $package, $serviceType, $orderId, $purchaseToken);
        $this->notificationService->sendPaymentSuccessNotification($user, $package->name);
    }

    private function updateOrCreateUserPackage($user, $package): void
    {
        $currentType = $package->type;
        $currentUserPackage = $user->userPackages()->where('status', PackageUserStatus::Active)->first();
        $startDate = $currentUserPackage
            ? Carbon::parse($currentUserPackage->end_date)
            : now();
        $endDate = Carbon::parse($startDate)->addDays($package->days);
        if ($currentUserPackage) {
            $currentUserPackage->update([
                'package_id' => $package->id,
                'end_date' => $endDate,
                'current_type' => $currentType,
            ]);
        }
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
        $typeName = $typeNames[$notificationType];

        switch ($notificationType) {
            case 12:
                $this->processRefund($productId, $purchaseToken);
                break;

            default:
                Log::info("Unhandled notification type: {$notificationType}");
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
