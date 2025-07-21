<?php

namespace App\Api\V1\Http\Controllers\RatingPQ;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\RatingPQ\RatingPQLastedRequest;
use App\Api\V1\Http\Requests\RatingPQ\RatingPQMonthRequest;
use App\Api\V1\Http\Requests\RatingPQ\RatingPQRequest;
use App\Api\V1\Http\Resources\RatingPQ\RatingPQCollection;
use App\Api\V1\Http\Resources\RatingPQ\RatingPQLastedResource;
use App\Api\V1\Http\Resources\RatingPQ\RatingPQResource;
use App\Api\V1\Repositories\RatingPQ\RatingPQRepositoryInterface;
use App\Api\V1\Services\RatingPQ\RatingPQServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use App\Traits\MessageSystem;
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
     * Lấy DS thống kê theo tháng và theo con
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam limit int optional Số lượng bản ghi trên mỗi trang, mặc định là 10. Example: 10
     * @queryParam page int optional Trang cần hiển thị, mặc định là 1. Example: 1
     * @queryParam month int required Tháng cần lấy dữ liệu. Example: 7
     * @queryParam year int required Năm cần lấy dữ liệu. Example: 2025
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
     * @param RatingPQMonthRequest $request
     * @return JsonResponse
     */

    public function getMonthlyEnduranceStats(RatingPQMonthRequest $request): JsonResponse
    {
        try {
            $response = $this->service->getMonthlyEnduranceData($request);
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
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
     * @param RatingPQRequest $request
     * @return JsonResponse
     */

    public function index(RatingPQRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new RatingPQCollection($response));
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Lấy đánh giá tổng thể của trẻ (mới nhất)
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy đánh giá tổng thể thành công.",
     *     "data": {
     *         "child_id": 1,
     *         "average_height": 120,
     *         "average_weight": 35,
     *         "average_strength": 15,
     *         "average_endurance": 14,
     *         "average_bmi": 18.5,
     *         "bmi_result": "Bình thường"
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy đánh giá tổng thể."
     * }
     *
     * @param RatingPQLastedRequest $request
     * @return JsonResponse
     */
    public function getOverallStats(RatingPQLastedRequest $request): JsonResponse
    {
        try {
            $response = $this->service->getOverallStats($request);
            if ($response == null) {
                return $this->jsonResponseError('No data found', 404);
            }
            return $this->jsonResponseSuccess(new RatingPQLastedResource($response));
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }


    /**
     * Tạo đánh giá thể chất
     *
     * @authenticated
     *
     * @bodyParam assessment_date string required Ngày đánh giá. Example: 2023-01-16
     * @bodyParam height int required Chiều cao của trẻ (cm). Example: 110
     * @bodyParam weight int required Cân nặng của trẻ (kg). Example: 35
     * @bodyParam strength int required Điểm sức mạnh của trẻ. Example: 15
     * @bodyParam endurance int required Điểm sức bền của trẻ. Example: 10
     * @bodyParam child_id int required ID của trẻ mà đánh giá được tạo cho. Example: 1
     *
     * @response 201 {
     *     "status": 201,
     *     "message": "Đánh giá đã được tạo thành công.",
     *     "data": {
     *         "id": 1,
     *         "child_id": 1,
     *         "assessment_date": "2023-01-16",
     *         "height": 110,
     *         "weight": 35,
     *         "strength": 15,
     *         "endurance": 10,
     *         "bmi": 28.9,
     *         "bmi_result": "Hơi béo"
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
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Cập nhật đánh giá thể chất
     *
     * @authenticated
     *
     * @bodyParam id int required ID đánh giá. Example: 1
     * @bodyParam assessment_date string required Ngày đánh giá. Example: 2023-01-16
     * @bodyParam height int required Chiều cao của trẻ (cm). Example: 110
     * @bodyParam weight int required Cân nặng của trẻ (kg). Example: 35
     * @bodyParam strength int required Điểm sức mạnh của trẻ. Example: 15
     * @bodyParam endurance int required Điểm sức bền của trẻ. Example: 10
     * @bodyParam child_id int required ID của trẻ mà đánh giá được tạo cho. Example: 1
     *
     * @response 201 {
     *     "status": 201,
     *     "message": "Đánh giá đã được tạo thành công.",
     *     "data": {
     *         "id": 1,
     *         "child_id": 1,
     *         "assessment_date": "2023-01-16",
     *         "height": 110,
     *         "weight": 35,
     *         "strength": 15,
     *         "endurance": 10,
     *         "bmi": 28.9,
     *         "bmi_result": "Hơi béo"
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
     * @param RatingPQRequest $request
     * @return JsonResponse
     */

    public function update(RatingPQRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->update($request);
            DB::commit();
            return $this->jsonResponseSuccess(new RatingPQResource($response));
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Lấy chi tiết Đánh giá
     *
     *
     * @authenticated
     * @urlParam id int required ID của nhật ký cần xem chi tiết. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Chi tiết nhật ký.",
     *     "data": {
     *         "id": 1,
     *         "child_id": 1,
     *         "title": "Toa Thuốc Hàng Tuần",
     *         "content": "Nội dung chi tiết của toa thuốc...",
     *         "image": "/images/prescriptions/example.jpg",
     *         "type": "prescription",
     *         "created_at": "2024-01-01",
     *         "updated_at": "2024-01-02"
     *     }
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Nhật ký không tìm thấy."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống."
     * }
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            Validator::validateExists($this->repository, $id);
            $response = $this->repository->findOrFail($id);
            return $this->jsonResponseSuccess(new RatingPQResource($response));
        } catch (NotFoundException|BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }

    /**
     * Xóa
     *
     * @authenticated
     * @pathParam  id int required ID của nhật ký cần xóa. Example: 1
     *
     * @response 204 {
     *     "status": 204,
     *     "message": "Đánh giá đã được xóa thành công."
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Đánh giá không tìm thấy."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi xóa nhật ký."
     * }
     *
     * @param int $id
     * @return JsonResponse
     */
    public function delete(int $id): JsonResponse
    {
        try {
            Validator::validateExists($this->repository, $id);
            $this->service->delete($id);
            return $this->jsonResponseSuccessNoData();
        } catch (NotFoundException|BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $exception) {
            $this->logError(MessageSystem::SERVER_ERROR, $exception);
            return $this->jsonResponseError(MessageSystem::SERVER_ERROR, 500);
        }
    }


}
