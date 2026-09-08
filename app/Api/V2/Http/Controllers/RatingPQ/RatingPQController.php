<?php

namespace App\Api\V2\Http\Controllers\RatingPQ;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Services\RatingPQ\RatingPQServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V2\Http\Requests\RatingPQ\RatingPQGeneralInfoRequest;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Đánh giá thể chất V2
 */
class RatingPQController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        RatingPQServiceInterface $service
    ) {
        $this->service = $service;
        $this->middleware('auth:api');
    }

    /**
     * Lấy thông tin tổng hợp đánh giá thể chất (V2 - Gộp 1 API)
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam year int optional Năm cần lấy thống kê biểu đồ. Example: 2026
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thao tác thành công.",
     *     "data": {
     *         "overall": {...},
     *         "latest_record": {...},
     *         "statistics": [...]
     *     }
     * }
     *
     * @param RatingPQGeneralInfoRequest $request
     * @return JsonResponse
     */
    public function getGeneralInfo(RatingPQGeneralInfoRequest $request): JsonResponse
    {
        try {
            $response = $this->service->getGeneralInfo($request);
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }
}
