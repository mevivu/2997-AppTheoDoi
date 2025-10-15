<?php

namespace App\Api\V1\Services\Purchase;

use App\Admin\Services\GooglePlay\GooglePlayServiceInterface;
use App\Admin\Services\Transaction\TransactionServiceInterface;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Http\Requests\Purchase\GooglePlayRequest;
use App\Api\V1\Repositories\Package\PackageRepositoryInterface;
use App\Api\V1\Repositories\Transaction\TransactionRepositoryInterface;
use App\Api\V1\Services\Notification\NotificationServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\AuthSupport;
use App\Enums\GooglePlay\SubscriptionState;
use App\Enums\Package\PackageUserStatus;
use App\Enums\Transaction\TransactionEnumService;
use App\Traits\MessageSystem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;


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


    public function __construct(
        PackageRepositoryInterface     $packageRepository,
        GooglePlayServiceInterface     $googlePlayService,
        TransactionServiceInterface    $transactionService,
        TransactionRepositoryInterface $transactionRepository,
        NotificationServiceInterface   $notificationService
    )
    {
        $this->packageRepository = $packageRepository;
        $this->googlePlayService = $googlePlayService;
        $this->transactionService = $transactionService;
        $this->transactionRepository = $transactionRepository;
        $this->notificationService = $notificationService;
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
     * @return JsonResponse
     */
    public function verifyPurchaseGooglePlay(GooglePlayRequest $request): JsonResponse
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
            $this->handlePurchase($user, $package, $purchaseData);
        }
        return response()->json([
            'success' => true,
            'message' => MessageSystem::VERIFY_SUCCESS,
            'data' => [
                'package' => $package,
                'product_id' => $productId,
                'order_id' => $purchaseData['orderId'] ?? null,
                'expiry_time' => $purchaseData['lineItem']['expiryTime'] ?? null,
                'auto_renewing' => $purchaseData['lineItem']['autoRenewing'] ?? null,
                'status' => $purchaseData['subscriptionState'] ?? null,
            ]
        ]);
    }

    private function handlePurchase($user, $package, $purchaseData): void
    {
        $orderId = $purchaseData['orderId'] ?? null;
        if ($orderId && $this->transactionRepository->existsByOrderIdAndService($orderId, TransactionEnumService::GOOGLE_PLAY)) {
            return;
        }
        $this->updateOrCreateUserPackage($user, $package);
        $this->transactionService->store($user, $package, TransactionEnumService::GOOGLE_PLAY, $orderId);
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


}
