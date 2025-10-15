<?php

namespace App\Api\V1\Http\Controllers\Purchase;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Purchase\GooglePlayRequest;
use App\Api\V1\Services\Purchase\PurchaseServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Thanh toán
 */
class PurchaseController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected PurchaseServiceInterface $googlePlayService;

    public function __construct(
        PurchaseServiceInterface $googlePlayService

    )
    {
        $this->googlePlayService = $googlePlayService;
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
            $response = $this->googlePlayService->verifyPurchaseGooglePlay($request);
            return $response;
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }


}
