<?php

namespace App\Api\V1\Http\Controllers\HeightPrediction;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\HeightPrediction\HeightChartRequest;
use App\Api\V1\Http\Requests\HeightPrediction\HeightPredictionRequest;
use App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;


/**
 * @group Dự đoán chiều cao
 */
class HeightPredictionController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        HeightPredictionServiceInterface    $service

    )
    {
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Dự báo chiều cao
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy danh sách theo dõi thai kỳ thành công.",
     *     "data": {
     *         "total": 10,
     *         "per_page": 10,
     *         "current_page": 1,
     *         "records": [
     *             {
     *                 "id": 1,
     *                 "child_id": 1,
     *             }
     *         ]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách theo dõi thai kỳ."
     * }
     *
     * @param HeightPredictionRequest $request
     * @return JsonResponse
     */

    public function index(HeightPredictionRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError('Get journals failed:', $exception);
            return $this->jsonResponseError('Get journals failed', 500);
        }
    }

    /**
     * Lấy dữ liệu phác đồ chiều cao (3 đường biểu đồ)
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam target_height numeric required Chiều cao mục tiêu. Example: 162
     * @queryParam puberty_months numeric optional Số tháng đã dậy thì. Example: 0
     *
     * @param HeightChartRequest $request
     * @return JsonResponse
     */
    public function chart(HeightChartRequest $request): JsonResponse
    {
        try {
            $response = $this->service->chart($request);
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError('Get height chart failed:', $exception);
            return $this->jsonResponseError('Get height chart failed', 500);
        }
    }
}
