<?php

namespace App\Api\V1\Http\Controllers\Assessment;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Assessment\AssessmentRequest;
use App\Api\V1\Repositories\Assessment\AssessmentRepositoryInterface;
use App\Api\V1\Services\Assessment\AssessmentServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Đánh giá tổng quan con
 */
class AssessmentController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        AssessmentRepositoryInterface $repository,
        AssessmentServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Lấy Tổng Quan Đánh Giá Theo Trẻ
     *
     * API này cho phép người dùng lấy danh sách các đánh giá liên quan đến một trẻ em cụ thể.
     *
     * @authenticated
     * @queryParam  child_id int ID con. Example: 2
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy danh sách đánh giá thành công.",
     *     "data": {
     *         "total": 10,
     *         "per_page": 10,
     *         "current_page": 1,
     *         "assessments": [
     *             {
     *                 "id": 1,
     *                 "child_id": 5,
     *                 "description": "Đánh giá ban đầu cho IQ",
     *                 "score": 100,
     *                 "type": "IQ",
     *                 "checked": "OFF"
     *             }
     *         ]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách đánh giá."
     * }
     *
     * @param AssessmentRequest $request
     * @return JsonResponse
     */

    public function index(AssessmentRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError('Get assessments failed:', $exception);
            return $this->jsonResponseError('Get  assessments failed', 500);
        }

    }


}
