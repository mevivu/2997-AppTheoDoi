<?php

namespace App\Api\V1\Http\Controllers\Pregnancy;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Pregnancy\PregnancyRequest;
use App\Api\V1\Http\Requests\Pregnancy\PregnancyUpdateRequest;
use App\Api\V1\Http\Resources\Pregnancy\PregnancyCollection;
use App\Api\V1\Http\Resources\Pregnancy\PregnancyResource;
use App\Api\V1\Repositories\Pregnancy\PregnancyRepositoryInterface;
use App\Api\V1\Services\Pregnancy\PregnancyServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @group Thai kì
 */
class PregnancyController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        PregnancyRepositoryInterface $repository,
        PregnancyServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Lấy danh sách theo dõi thai kỳ theo con
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam limit int optional Số lượng bản ghi trên mỗi trang, mặc định là 10. Example: 10
     * @queryParam page int optional Trang cần hiển thị, mặc định là 1. Example: 1
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
     *                 "start_date": "2024-01-01",
     *                 "end_date": "2024-09-01",
     *                 "week": 20,
     *                 "weight": 0.5,
     *                 "length": 30,
     *                 "head_circumference": 15,
     *                 "status": "active",
     *                 "created_at": "2024-01-01",
     *                 "updated_at": "2024-01-02"
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
     * @param PregnancyRequest $request
     * @return JsonResponse
     */

    public function index(PregnancyRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new PregnancyCollection($response));
        } catch (Exception $exception) {
            $this->logError('Get journals failed:', $exception);
            return $this->jsonResponseError('Get journals failed', 500);
        }
    }

    /**
     * Tạo mới theo dõi thai kì
     *
     * @authenticated
     * @bodyParam child_id int required ID của trẻ. Example: 1
     * @bodyParam start_date date required Ngày bắt đầu thai kì. Example: 2024-01-01
     * @bodyParam end_date date required Ngày dự sinh. Example: 2024-09-01
     * @bodyParam week int optional Tuần thai hiện tại. Example: 20
     * @bodyParam weight float optional Cân nặng của em bé. Example: 0.5
     * @bodyParam length int optional Chiều dài của em bé. Example: 30
     * @bodyParam head_circumference int optional Chu vi đầu của em bé. Example: 15
     * @bodyParam image file optional Hình ảnh liên quan. Example: /images/pregnancies/example.jpg
     * @bodyParam status string required Trạng thái của thai kì. Example: active
     *
     * @response 201 {
     *     "status": 201,
     *     "message": "Theo dõi thai kì đã được tạo thành công.",
     *     "data": {
     *         "id": 1,
     *         "child_id": 1,
     *         "start_date": "2024-01-01",
     *         "end_date": "2024-09-01",
     *         "week": 20,
     *         "weight": 0.5,
     *         "length": 30,
     *         "head_circumference": 15,
     *         "image": "/images/pregnancies/example.jpg",
     *         "status": "active",
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
     * @param PregnancyRequest $request
     * @return JsonResponse
     */

    public function store(PregnancyRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->store($request);
            DB::commit();
            return $this->jsonResponseSuccess(new PregnancyResource($response));
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError('Create pregnancy failed:', $exception);
            return $this->jsonResponseError('Create pregnancy failed', 500);
        }
    }

    /**
     * Cập nhật thông tin thai kỳ.
     *
     * @authenticated
     * @bodyParam id int required ID của bản ghi thai kỳ cần cập nhật. Example: 1
     * @bodyParam child_id int required ID của trẻ. Example: 1
     * @bodyParam start_date date required Ngày bắt đầu thai kỳ. Example: "2024-01-01"
     * @bodyParam end_date date required Ngày dự sinh. Example: "2024-09-01"
     * @bodyParam week int optional Tuần thai hiện tại. Example: 20
     * @bodyParam weight float optional Cân nặng của em bé (kg). Example: 0.5
     * @bodyParam length int optional Chiều dài của em bé (cm). Example: 30
     * @bodyParam head_circumference int optional Chu vi đầu của em bé (cm). Example: 15
     * @bodyParam image file optional Hình ảnh mới của bản ghi thai kỳ. Example: "/images/pregnancies/updated.jpg"
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thai kỳ đã được cập nhật thành công.",
     *     "data": {
     *         "id": 1,
     *         "child_id": 1,
     *         "start_date": "2024-01-01",
     *         "end_date": "2024-09-01",
     *         "week": 20,
     *         "weight": 0.5,
     *         "length": 30,
     *         "head_circumference": 15,
     *         "image": "/images/pregnancies/updated.jpg",
     *         "created_at": "2024-01-01",
     *         "updated_at": "2024-01-05"
     *     }
     * }
     * @response 404 {
     *     "status": 404,
     *     "message": "Bản ghi không tìm thấy."
     * }
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi cập nhật."
     * }
     *
     * @param PregnancyUpdateRequest $request
     * @return JsonResponse
     */
    public function update(PregnancyUpdateRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $response = $this->service->update($request);
            DB::commit();
            return $this->jsonResponseSuccess(new PregnancyResource($response));
        } catch (Exception $exception) {
            DB::rollBack();
            $this->logError('Create prescription journal failed:', $exception);
            return $this->jsonResponseError('Create prescription journal failed', 500);
        }

    }

    /**
     * Lấy chi tiết thai dõi theo kì
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
            return $this->jsonResponseSuccess(new PregnancyResource($response));
        } catch (NotFoundException|BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $exception) {
            $this->logError('Deleted failed:', $exception);
            return $this->jsonResponseError('Deleted failed', 500);
        }
    }


    /**
     * Xóa thai dõi theo kì
     *
     *
     * @authenticated
     * @urlParam id int required ID của nhật ký cần xóa. Example: 1
     *
     * @response 204 {
     *     "status": 204,
     *     "message": "Nhật ký đã được xóa thành công."
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Nhật ký không tìm thấy."
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
            $this->logError('Deleted failed:', $exception);
            return $this->jsonResponseError('Deleted failed', 500);
        }
    }

}
