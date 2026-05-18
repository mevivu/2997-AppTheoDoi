<?php

namespace App\Admin\Services\AppleStore;

use App\Api\V1\Support\UseLog;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AppleStoreService implements AppleStoreServiceInterface
{
    use UseLog;

    private string $issuerId;
    private string $keyId;
    private string $privateKey;
    private string $bundleId;

    private const PRODUCTION_URL = 'https://api.storekit.apple.com/inApps/v1/transactions/';
    private const SANDBOX_URL = 'https://api.storekit-sandbox.apple.com/inApps/v1/transactions/';

    public function __construct()
    {
        $this->issuerId = config('services.apple_store.issuer_id', '');
        $this->keyId = config('services.apple_store.key_id', '');
        $this->privateKey = config('services.apple_store.private_key', '');
        $this->bundleId = config('services.apple_store.bundle_id', '');
    }

    private function generateJWT(): ?string
    {
        if (empty($this->issuerId) || empty($this->keyId) || empty($this->privateKey)) {
            Log::error('AppleStoreService: Missing JWT credentials.');
            return null;
        }

        $payload = [
            'iss' => $this->issuerId,
            'iat' => time(),
            'exp' => time() + 5 * 60, // 5 minutes
            'aud' => 'appstoreconnect-v1',
            'bid' => $this->bundleId
        ];
        
        $privateKeyContent = $this->privateKey;
        // If privateKey is a path to a file, read it
        if (file_exists($privateKeyContent)) {
            $privateKeyContent = file_get_contents($privateKeyContent);
        }

        try {
            return JWT::encode($payload, $privateKeyContent, 'ES256', $this->keyId);
        } catch (\Exception $e) {
            Log::error('AppleStoreService JWT Encode Exception', ['message' => $e->getMessage()]);
            return null;
        }
    }

    public function verifyPurchase(string $productId, string $transactionId, bool $isSubscription = true): array
    {
        try {
            $jwt = $this->generateJWT();
            if (!$jwt) {
                return [
                    'success' => false,
                    'error' => 'Could not generate Apple Store JWT.',
                    'code' => 500,
                ];
            }

            // Try Production first
            $response = $this->sendVerifyRequest(self::PRODUCTION_URL . $transactionId, $jwt);

            // If 404, it might be a Sandbox transaction
            if (isset($response['status_code']) && $response['status_code'] === 404) {
                Log::info('AppleStoreService: Transaction not found in Production, trying Sandbox.');
                $response = $this->sendVerifyRequest(self::SANDBOX_URL . $transactionId, $jwt);
            }

            if (isset($response['status_code']) && $response['status_code'] !== 200) {
                Log::error('AppleStoreService: Verify failed', ['response' => $response]);
                return [
                    'success' => false,
                    'error' => 'Transaction validation failed.',
                    'code' => $response['status_code'] ?? 400,
                ];
            }

            if (!isset($response['signedTransactionInfo'])) {
                Log::error('AppleStoreService: signedTransactionInfo missing', ['response' => $response]);
                return [
                    'success' => false,
                    'error' => 'No transaction info found.',
                    'code' => 404,
                ];
            }

            $decodedTransaction = $this->decodeJws($response['signedTransactionInfo']);
            
            if (!$decodedTransaction) {
                return [
                    'success' => false,
                    'error' => 'Failed to decode signedTransactionInfo.',
                    'code' => 500,
                ];
            }

            // Verify bundle ID (optional but recommended)
            $receiptBundleId = $decodedTransaction['bundleId'] ?? null;
            if ($this->bundleId && $receiptBundleId !== $this->bundleId) {
                return [
                    'success' => false,
                    'error' => 'Bundle ID mismatch.',
                    'code' => 400,
                ];
            }

            if (($decodedTransaction['productId'] ?? '') !== $productId) {
                return [
                    'success' => false,
                    'error' => 'Product ID mismatch.',
                    'code' => 400,
                ];
            }

            $type = $decodedTransaction['type'] ?? '';
            $nowMs = time() * 1000;
            $expiresDateMs = null;

            if ($type === 'Non-Renewing Subscription' || $type === 'Consumable' || $type === 'Non-Consumable') {
                // Non-Renewing Subscriptions and other one-time purchases don't have an Apple-managed expiration date.
                // We consider it active at the moment of verification.
                $subscriptionState = 'SUBSCRIPTION_STATE_ACTIVE';
            } else {
                // Auto-Renewable Subscription
                $expiresDateMs = (int)($decodedTransaction['expiresDate'] ?? 0);
                $subscriptionState = ($expiresDateMs > $nowMs)
                    ? 'SUBSCRIPTION_STATE_ACTIVE'
                    : 'SUBSCRIPTION_STATE_EXPIRED';
            }

            return [
                'success' => true,
                'data' => [
                    'subscriptionState' => $subscriptionState,
                    'orderId' => $decodedTransaction['originalTransactionId'] ?? $decodedTransaction['transactionId'] ?? null,
                    'lineItem' => [
                        'productId' => $decodedTransaction['productId'],
                        'expiryTime' => $expiresDateMs, // Nullable for Non-Renewing
                    ],
                ]
            ];

        } catch (\Exception $e) {
            Log::error('AppleStoreService Exception', [
                'message' => $e->getMessage(),
                'product_id' => $productId,
                'transaction_id' => $transactionId,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => $e->getCode(),
            ];
        }
    }

    private function sendVerifyRequest(string $url, string $jwt): array
    {
        $response = Http::withToken($jwt)->get($url);
        $data = $response->json() ?? [];
        $data['status_code'] = $response->status();
        return $data;
    }

    private function decodeJws(string $jws): ?array
    {
        $parts = explode('.', $jws);
        if (count($parts) !== 3) {
            return null;
        }
        $payload = base64_decode(strtr($parts[1], '-_', '+/'));
        return json_decode($payload, true);
    }
}
