<?php

namespace App\Admin\Services\GooglePlay;

use App\Api\V1\Support\UseLog;
use App\Admin\Traits\Setup;
use Google\Client;
use Google\Exception;
use Google\Service\AndroidPublisher;
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


}
