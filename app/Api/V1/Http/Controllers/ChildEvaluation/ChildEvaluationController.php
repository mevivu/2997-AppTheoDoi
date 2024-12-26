<?php

namespace App\Api\V1\Http\Controllers\ChildEvaluation;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\ChildEvaluation\ChildEvaluationRequest;
use App\Api\V1\Http\Resources\Child\ChildResource;
use App\Api\V1\Repositories\ChildEvaluation\ChildEvaluationRepositoryInterface;
use App\Api\V1\Services\ChildEvaluation\ChildEvaluationServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Con
 */
class ChildEvaluationController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ChildEvaluationRepositoryInterface $repository,
        ChildEvaluationServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Tạo mới Đánh giá năng lực theo lớp và kì
     *
     * @authenticated
     * @bodyParam child_id int required ID của trẻ. Example: 1
     * @bodyParam title string required Tiêu đề của nhật ký toa thuốc. Example: "Toa Thuốc Hàng Tuần"
     * @bodyParam content string required Nội dung chi tiết của nhật ký toa thuốc. Example: "Toa thuốc bao gồm..."
     * @bodyParam image string optional Đường dẫn hình ảnh liên quan đến toa thuốc. Example: "/images/prescriptions/example.jpg"
     * @bodyParam type string required Thể loại của nhật ký. Examples: moment
     *
     * @response 201 {
     *     "status": 201,
     *     "message": "Nhật ký toa thuốc đã được tạo thành công.",
     *     "data": {
     *         "id": 1,
     *         "child_id": 1,
     *         "title": "Toa Thuốc Hàng Tuần",
     *         "content": "Toa thuốc bao gồm...",
     *         "image": "/images/prescriptions/example.jpg",
     *         "type": "prescription",
     *         "created_at": "2024-01-01",
     *         "updated_at": "2024-01-01"
     *     }
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Lỗi dữ liệu nhập vào."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống."
     * }
     *
     * @param ChildEvaluationRequest $request
     * @return JsonResponse
     */
    public function store(ChildEvaluationRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->store($request);
            DB::commit();
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError('Child Store failed:', $exception);
            return $this->jsonResponseError('Get user notifications failed', 500);
        }

    }


}
