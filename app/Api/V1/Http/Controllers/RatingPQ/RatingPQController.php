<?php

namespace App\Api\V1\Http\Controllers\RatingPQ;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Rating\RatingRequest;
use App\Api\V1\Http\Requests\RatingPQ\RatingPQRequest;
use App\Api\V1\Http\Resources\Rating\RatingCollection;
use App\Api\V1\Http\Resources\RatingPQ\RatingPQResource;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Services\RatingPQ\RatingPQServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Đánh giá thể chất
 */
class RatingPQController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        RatingPQRepositoryInterface $repository,
        RatingPQServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Lấy danh sách Đánh giá theo loại theo con
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam limit int optional Số lượng bản ghi trên mỗi trang, mặc định là 10. Example: 10
     * @queryParam page int optional Trang cần hiển thị, mặc định là 1. Example: 1
     * @queryParam type string required Loại câu hỏi đang được đánh giá. Example: iq
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
     * @param RatingRequest $request
     * @return JsonResponse
     */

    public function index(RatingRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new RatingCollection($response));
        } catch (Exception $exception) {
            $this->logError('Get journals failed:', $exception);
            return $this->jsonResponseError('Get journals failed', 500);
        }
    }

    /**
     * Tạo đánh giá theo loại IQ
     *
     * @authenticated
     * @bodyParam child_id int required ID của trẻ mà đánh giá được tạo cho. Example: 1
     * @bodyParam type string required Loại câu hỏi đang được đánh giá. Example: iq
     * @bodyParam answers array required Một mảng các câu trả lời với ID câu hỏi và ID câu trả lời.
     * @bodyParam answers[].question_id int required ID của câu hỏi đang được trả lời.
     * @bodyParam answers[].answer_id int required ID của câu trả lời được cung cấp.
     * @bodyParam tag string required Thẻ để phân loại đánh giá. Example: Bố
     *
     * @response 201 {
     *     "status": 201,
     *     "message": "Theo dõi thai kì đã được tạo thành công.",
     *     "data": {
     *         "id": 1,
     *         "child_id": 1,
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
     * @param RatingPQRequest $request
     * @return JsonResponse
     */

    public function store(RatingPQRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->store($request);
            DB::commit();
            return $this->jsonResponseSuccess(new RatingPQResource($response));
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError('Create pregnancy failed:', $exception);
            return $this->jsonResponseError('Create pregnancy failed', 500);
        }
    }


}
