<?php

namespace App\Admin\Services\GooglePlay;

use App\Api\V1\Support\UseLog;
use App\Admin\Traits\Setup;
use Google\Client;
use Google\Exception;
use Google\Service\AndroidPublisher;
use Google_Service_AndroidPublisher_SubscriptionPurchaseLineItem;
use Google_Service_AndroidPublisher_SubscriptionPurchaseV2;
use Google_Service_Exception;
use Illuminate\Support\Facades\Log;

class GooglePlayService implements GooglePlayServiceInterface
{
    use Setup, UseLog;

    private $client;
    private $service;
    private $packageName;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->packageName = config('services.google_play.package_name');
        $this->initializeClient();
    }

    /**
     * @throws Exception
     */
    private function initializeClient(): void
    {
        $this->client = new Client();
        $this->client->setAuthConfig(storage_path('app/google/service-account.json'));
        $this->client->addScope(AndroidPublisher::ANDROIDPUBLISHER);

        $this->service = new AndroidPublisher($this->client);
    }

    public function verifyPurchase($productId, $purchaseToken, $isSubscription = true): array
    {
        try {
            $purchase = $this->service->purchases_subscriptionsv2->get(
                $this->packageName,
                $purchaseToken
            );

            $lineItems = $purchase->getLineItems();
            $lineItem = $lineItems[0] ?? null;
            $refundInfo = $this->checkRefundStatus($purchase, $lineItem);
            return [
                'success' => true,
                'data' => [
                    'subscriptionState' => $purchase->getSubscriptionState(),
                    'regionCode' => $purchase->getRegionCode(),
                    'orderId' => $purchase->getLatestOrderId(),
                    'lineItem' => $lineItem ? [
                        'productId' => $lineItem->getProductId(),
                        'expiryTime' => $lineItem->getExpiryTime(),
                        'autoRenewing' => $lineItem->getAutoRenewingPlan()?->getAutoRenewEnabled() ?? false,
                        'isPrepaid' => $lineItem->getPrepaidPlan() !== null,
                    ] : null,
                    'refundInfo' => $refundInfo,
                ],
            ];

        } catch (Google_Service_Exception $e) {
            $errorData = json_decode($e->getMessage(), true);

            Log::error('Subscription API Error', [
                'code' => $e->getCode(),
                'message' => $errorData['error']['message'] ?? $e->getMessage(),
                'product_id' => $productId,
                'package_name' => $this->packageName
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    /**
     * Kiểm tra trạng thái refund của subscription
     * Bao gồm cả trường hợp refund thủ công trên Console
     *
     * @param Google_Service_AndroidPublisher_SubscriptionPurchaseV2 $purchase
     * @param Google_Service_AndroidPublisher_SubscriptionPurchaseLineItem|null $lineItem
     * @return array
     */
    private function checkRefundStatus($purchase, $lineItem): array
    {
        $refundInfo = [
            'is_refunded' => false,
            'refund_type' => null,
            'cancel_time' => null,
            'cancel_reason' => null,
        ];

        // Lấy raw data
        $purchaseData = json_decode(json_encode($purchase), true);

        // Debug: Log toàn bộ purchase data
        Log::info('Full purchase data for refund check', [
            'order_id' => $purchase->getLatestOrderId(),
            'subscription_state' => $purchase->getSubscriptionState(),
            'purchase_data' => $purchaseData
        ]);

        $state = $purchase->getSubscriptionState();

        // ========================================
        // CÁCH 1: Kiểm tra canceledStateContext (cho refund tự động)
        // ========================================
        if (isset($purchaseData['canceledStateContext'])) {
            $cancelContext = $purchaseData['canceledStateContext'];

            if (isset($cancelContext['userInitiatedCancellation'])) {
                $refundInfo['is_refunded'] = true;
                $refundInfo['refund_type'] = 'user_initiated';
                $refundInfo['cancel_time'] = $cancelContext['userInitiatedCancellation']['cancelTime'] ?? null;
            }
            elseif (isset($cancelContext['systemInitiatedCancellation'])) {
                $refundInfo['is_refunded'] = true;
                $refundInfo['refund_type'] = 'system_initiated';
            }
            elseif (isset($cancelContext['developerInitiatedCancellation'])) {
                $refundInfo['is_refunded'] = true;
                $refundInfo['refund_type'] = 'developer_initiated';
            }
            elseif (isset($cancelContext['replacementWithAnotherSubscription'])) {
                $refundInfo['refund_type'] = 'replaced';
            }
        }

        // ========================================
        // CÁCH 2: Kiểm tra acknowledgementState và startTime
        // Refund thủ công thường không có canceledStateContext
        // ========================================
        if (!$refundInfo['is_refunded']) {

            // Nếu EXPIRED hoặc CANCELED và được acknowledge
            if (in_array($state, ['SUBSCRIPTION_STATE_EXPIRED', 'SUBSCRIPTION_STATE_CANCELED'])) {

                $acknowledgementState = $purchaseData['acknowledgementState'] ?? null;
                $startTime = $purchaseData['startTime'] ?? null;

                // Kiểm tra lineItem để xác định
                if ($lineItem) {
                    $lineItemData = json_decode(json_encode($lineItem), true);

                    // Check expiryTime
                    $expiryTime = $lineItemData['expiryTime'] ?? null;
                    $autoRenewing = $lineItemData['autoRenewingPlan']['autoRenewEnabled'] ?? false;

                    // Nếu:
                    // - Đã expired
                    // - Không auto renew
                    // - Thời gian expired quá sớm so với thời gian mua (ví dụ: < 1 ngày)
                    // => Có thể là refund thủ công

                    if ($expiryTime && $startTime) {
                        $start = strtotime($startTime);
                        $expiry = strtotime($expiryTime);
                        $duration = $expiry - $start;

                        // Nếu subscription chỉ tồn tại < 1 giờ → rất có thể là refund ngay
                        if ($duration < 3600 && $state === 'SUBSCRIPTION_STATE_EXPIRED') {
                            $refundInfo['is_refunded'] = true;
                            $refundInfo['refund_type'] = 'manual_console_refund';
                            $refundInfo['cancel_reason'] = 'Subscription expired immediately after purchase (likely manual refund)';
                        }
                    }
                }
            }
        }

        // ========================================
        // CÁCH 3: Kiểm tra testPurchase hoặc các flags khác
        // ========================================
        if (!$refundInfo['is_refunded']) {
            // Check testPurchase
            $testPurchase = $purchaseData['testPurchase'] ?? null;

            // Nếu là test purchase và expired ngay → refund test
            if ($testPurchase && $state === 'SUBSCRIPTION_STATE_EXPIRED') {
                $refundInfo['is_refunded'] = true;
                $refundInfo['refund_type'] = 'test_refund';
            }
        }

        // ========================================
        // CÁCH 4: GỌI API ORDERS để kiểm tra chính xác
        // Đây là cách chắc chắn nhất cho manual refund
        // ========================================
        if (!$refundInfo['is_refunded'] && $state === 'SUBSCRIPTION_STATE_EXPIRED') {
            $orderId = $purchase->getLatestOrderId();
            $refundInfo = $this->checkOrderRefundStatus($orderId, $refundInfo);
        }

        // Log nếu phát hiện refund
        if ($refundInfo['is_refunded']) {
            Log::warning('Refund detected', [
                'order_id' => $purchase->getLatestOrderId(),
                'refund_type' => $refundInfo['refund_type'],
                'subscription_state' => $state,
            ]);
        }

        return $refundInfo;
    }

    /**
     * Kiểm tra order refund status qua Orders API
     * Đây là cách chính xác nhất để phát hiện manual refund
     */
    private function checkOrderRefundStatus($orderId, $refundInfo): array
    {
        try {
            // Gọi Orders API để lấy thông tin order
            $order = $this->service->orders->get(
                $this->packageName,
                $orderId
            );

            $orderData = json_decode(json_encode($order), true);

            Log::info('Order API response', [
                'order_id' => $orderId,
                'order_data' => $orderData
            ]);

            // Kiểm tra purchaseState trong order
            // purchaseState = 1 → Refunded
            // purchaseState = 0 → Purchased
            $purchaseState = $orderData['purchaseState'] ?? null;

            if ($purchaseState === 1) {
                $refundInfo['is_refunded'] = true;
                $refundInfo['refund_type'] = 'manual_console_refund';
                $refundInfo['cancel_reason'] = 'Order refunded via Console';
            }

            // Kiểm tra refund amount
            if (isset($orderData['refundAmount'])) {
                $refundInfo['is_refunded'] = true;
                $refundInfo['refund_type'] = 'manual_console_refund';
                $refundInfo['refund_amount'] = $orderData['refundAmount'];
            }

        } catch (\Exception $e) {
            Log::error('Error checking order refund status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
        }

        return $refundInfo;
    }

}
