<?php

namespace App\Api\V1\Http\Controllers\Purchase;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Purchase\GooglePlayRequest;
use App\Api\V1\Http\Requests\Purchase\WebhookGooglePlayRequest;
use App\Api\V1\Services\Purchase\PurchaseServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $this->middleware('auth:api');

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
            DB::beginTransaction();

            $response = $this->purchaseService->verifyPurchaseGooglePlay($request);

            DB::commit();

            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            DB::rollBack();

            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Webhook nhận thông báo từ Google Play (Refund / Cancel / Renew)
     *
     * Google gửi thông báo này khi có sự kiện thay đổi trạng thái thuê bao:
     *  - SUBSCRIPTION_CANCELED
     *  - SUBSCRIPTION_REFUNDED
     *  - SUBSCRIPTION_RECOVERED
     *  - SUBSCRIPTION_EXPIRED
     *
     * @unauthenticated
     *
     * @bodyParam message.data string required Dữ liệu Base64 mà Google gửi.
     * @bodyParam message.messageId string optional ID của thông báo.
     * @response 200 {"status":200,"message":"Webhook received"}
     */
    public function googlePlayWebhook(WebhookGooglePlayRequest $request): JsonResponse
    {
        try {
            $decoded = $request->getDecodedData();

            if (!$decoded) {
                return $this->jsonResponseError('Dữ liệu webhook không hợp lệ.', 400);
            }
            $this->purchaseService->handleWebhookNotification($decoded);

            return $this->jsonResponseSuccess(['message' => 'Webhook received']);
        } catch (Exception $e) {
            $this->logError(MessageSystem::SERVER_ERROR, $e);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }


}
