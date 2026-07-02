<?php

namespace App\Api\V1\Http\Controllers\Purchase;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Purchase\AppleStoreRequest;
use App\Api\V1\Http\Requests\Purchase\GooglePlayRequest;
use App\Api\V1\Http\Requests\Purchase\WebhookGooglePlayRequest;
use App\Api\V1\Services\Purchase\PurchaseServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @group Thanh toán
 */
class PurchaseController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected PurchaseServiceInterface $purchaseService;

    public function __construct(
        PurchaseServiceInterface $purchaseService
    )
    {
        $this->purchaseService = $purchaseService;
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'googlePlayWebhook',
            ]
        ]);
    }


    /**
     * Xác minh giao dịch mua trên Google Play
     *
     * API này cho phép xác minh giao dịch mua gói dịch vụ thông qua Google Play.
     * Người dùng cần được xác thực và phải cung cấp thông tin giao dịch hợp lệ
     * bao gồm ID gói dịch vụ, mã sản phẩm và mã giao dịch (purchase token).
     *
     * @authenticated
     *
     * @bodyParam product_id string required Mã sản phẩm trên Google Play .
     * @bodyParam purchase_token string required Mã xác thực giao dịch do Google Play cung cấp.
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Xác minh giao dịch Google Play thành công."
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Thông tin không hợp lệ hoặc giao dịch không hợp lệ."
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Gói dịch vụ không tồn tại."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi xác minh giao dịch Google Play."
     * }
     *
     * @param GooglePlayRequest $request
     * @return JsonResponse
     */
    public function verifyPurchaseGooglePlay(GooglePlayRequest $request): JsonResponse
    {
        try {
            $response = $this->purchaseService->verifyPurchaseGooglePlay($request);

            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Xác minh giao dịch mua trên Apple Store
     *
     * @param AppleStoreRequest $request
     * @return JsonResponse
     */
    public function verifyPurchaseAppleStore(AppleStoreRequest $request): JsonResponse
    {
        try {
            $response = $this->purchaseService->verifyPurchaseAppleStore($request);

            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    public function googlePlayWebhook(WebhookGooglePlayRequest $request): JsonResponse
    {
        Log::info('🔔 Google Play Webhook Handler Started');

        try {
            $decoded = $request->getDecodedData();

            if (!$decoded) {
                Log::error('❌ Could not decode webhook data', [
                    'raw_data' => $request->input('message.data'),
                ]);

                return $this->jsonResponseSuccess([
                    'message' => 'Webhook received but data invalid',
                    'ack' => true
                ]);
            }

            Log::info('✅ Webhook data decoded successfully', [
                'data' => $decoded
            ]);

            $this->purchaseService->handleWebhookNotification($decoded);

            Log::info('✅ Webhook processed successfully');

            return $this->jsonResponseSuccess([
                'message' => 'Webhook received',
                'ack' => true
            ]);

        } catch (Exception $e) {
            Log::error('❌ Webhook processing failed', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->jsonResponseSuccess([
                'message' => 'Webhook received but processing failed',
                'ack' => true,
                'error' => $e->getMessage()
            ]);
        }
    }


}
