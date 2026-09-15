<?php

namespace App\Api\V2\Http\Controllers\HeightPrediction;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Services\HeightPrediction\HeightPredictionServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V2\Http\Requests\HeightPrediction\HeightChartV2Request;
use App\Api\V2\Http\Requests\HeightPrediction\HeightPredictionV2Request;
use App\Traits\MessageSystem;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Dự đoán chiều cao V2
 */
class HeightPredictionV2Controller extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        HeightPredictionServiceInterface $service
    ) {
        $this->service = $service;
        $this->middleware('auth:api');
    }

    /**
     * Dự báo chiều cao V2
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam puberty_months numeric required Số tháng đã dậy thì (0 nếu chưa dậy thì). Example: 0
     *
     * @param HeightPredictionV2Request $request
     * @return JsonResponse
     */
    public function index(HeightPredictionV2Request $request): JsonResponse
    {
        try {
            $response = $this->service->indexV2($request);
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError('Get height prediction v2 failed:', $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Lấy dữ liệu phác đồ chiều cao V2 (2 đường biểu đồ: Dự đoán + WHO)
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam puberty_months numeric required Số tháng đã dậy thì (0 nếu chưa dậy thì). Example: 0
     *
     * @param HeightChartV2Request $request
     * @return JsonResponse
     */
    public function chart(HeightChartV2Request $request): JsonResponse
    {
        try {
            $response = $this->service->chartV2($request);
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError('Get height chart v2 failed:', $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }
}
