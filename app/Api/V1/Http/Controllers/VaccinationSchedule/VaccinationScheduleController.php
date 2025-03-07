<?php

namespace App\Api\V1\Http\Controllers\VaccinationSchedule;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\VaccinationSchedule\VaccinationScheduleRequest;
use App\Api\V1\Http\Requests\VaccinationSchedule\VaccinationScheduleUpdateRequest;
use App\Api\V1\Http\Resources\VaccinationSchedule\VaccinationScheduleCollection;
use App\Api\V1\Http\Resources\VaccinationSchedule\VaccinationScheduleResource;
use App\Api\V1\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Api\V1\Services\VaccinationSchedule\VaccinationScheduleServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use Exception;
use Illuminate\Http\JsonResponse;


/**
 * @group Lịch tiêm chủng
 */
class VaccinationScheduleController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        VaccinationScheduleRepositoryInterface $repository,
        VaccinationScheduleServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');
    }

    /**
     * Lấy lịch tiêm chủng
     *
     * @authenticated
     * @queryParam limit int optional Số lượng lịch tiêm chủng trên mỗi trang, mặc định là 10. Example: 10
     * @queryParam page int optional Trang cần hiển thị, mặc định là 1. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": {
     *         "vaccinations": [
     *             {
     *                 "id": 6,
     *                 "child_id": null,
     *                 "name": "Mũi 2",
     *                 "description": "Mũi 1",
     *                 "vaccination_status": "not_vaccinated",
     *                 "performed_on": "2024-12-20",
     *                 "image": null
     *             }
     *         ],
     *         "links": {
     *             "first": "http://localhost:8080/2997-AppTheoDoi/api/v1/vaccination-schedule?page=1",
     *             "last": "http://localhost:8080/2997-AppTheoDoi/api/v1/vaccination-schedule?page=1",
     *             "prev": null,
     *             "next": null
     *         },
     *         "meta": {
     *             "current_page": 1,
     *             "from": 1,
     *             "to": 1,
     *             "limit": 10,
     *             "total": 1,
     *             "count": 1,
     *             "total_pages": 1
     *         }
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách tiêm chủng."
     * }
     *
     * @param VaccinationScheduleRequest $request
     * @return JsonResponse
     */


    public function index(VaccinationScheduleRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new VaccinationScheduleCollection($response));
        } catch (Exception $exception) {
            $this->logError('Get VaccinationSchedule failed:', $exception);
            return $this->jsonResponseError('Get VaccinationSchedule failed', 500);
        }
    }

    /**
     * Tạo lịch tiêm chủng
     *
     * @authenticated
     * @bodyParam child_id int required ID của trẻ. Ví dụ: 1
     * @bodyParam title string required Tiêu đề của lịch tiêm chủng. Ví dụ: "Mũi 1"
     * @bodyParam description string required Mô tả chi tiết về lịch tiêm chủng. Ví dụ: "Tiêm phòng viêm gan B"
     * @bodyParam image string optional Đường dẫn hình ảnh liên quan đến lịch tiêm chủng. Ví dụ: "/images/vaccines/example.jpg"
     * @bodyParam performed_on string required Ngày thực hiện tiêm chủng. Ví dụ: "2024-12-30"
     * @bodyParam vaccination_status string required Trạng thái tiêm chủng. Ví dụ: "not_vaccinated", "vaccinated"
     * @bodyParam vaccination_type_id int required ID của loại vắc xin. Ví dụ: 1
     *
     * @response 201 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": {
     *         "id": 25,
     *         "child_id": null,
     *         "name": "Name Vaccin 1",
     *         "description": "Vaccin for child",
     *         "vaccination_status": "vaccinated",
     *         "vaccination_type_id": "1",
     *         "performed_on": "2024-12-30",
     *         "image": [
     *             "/public/uploads/files/fVDCLZyJbAaVBKzCHE1kEfyn2UDRY75e3vzHmzRp.png"
     *         ],
     *          "vaccination_types": {
     *              "name": "Sơ sinh"
     *          }
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
     * @param VaccinationScheduleRequest $request
     * @return JsonResponse
     */

    public function store(VaccinationScheduleRequest $request): JsonResponse
    {
        try {
            $response = $this->service->store($request);
            return $this->jsonResponseSuccess(new VaccinationScheduleResource($response));
        } catch (Exception $exception) {
            $this->logError('Create prescription vaccination failed:', $exception);
            return $this->jsonResponseError('Create prescription vaccination failed', 500);
        }
    }


    /**
     * Cập nhật lịch tiêm chủng
     *
     * API này cho phép người dùng cập nhật một nhật ký tiêm chủng đã tồn tại. Người dùng phải xác thực để truy cập API này và chỉ có thể cập nhật nhật ký của chính mình.
     *
     * @authenticated
     * @bodyParam id int required ID của lịch tiêm chủng cần cập nhật. Ví dụ: 1
     * @bodyParam title string optional Tiêu đề của lịch tiêm chủng. Ví dụ: "Mũi 2"
     * @bodyParam description string optional Nội dung chi tiết của lịch tiêm chủng. Ví dụ: "Tiêm phòng viêm gan B"
     * @bodyParam performed_on string optional Ngày thực hiện tiêm chủng. Ví dụ: "2024-12-30"
     * @bodyParam vaccination_status string optional Trạng thái tiêm chủng. Ví dụ: "not_vaccinated", "vaccinated"
     * @bodyParam vaccination_type_id int optional ID của loại vắc xin. Ví dụ: 1
     * @bodyParam image string optional Đường dẫn hình ảnh mới liên quan đến lịch tiêm chủng. Ví dụ: "/images/vaccines/updated_image.jpg"
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Cập nhật thành công.",
     *     "data": {
     *         "id": 23,
     *         "child_id": null,
     *         "name": "Name Vaccin 23",
     *         "description": "Vaccin for child",
     *         "vaccination_status": "vaccinated",
     *         "performed_on": "2024-12-29",
     *         "image": [
     *             "/public/uploads/files/KjuSoEKJ0GNA6aUVaXHSD1eo1ZF0qkxzz3vDp1F5.png"
     *         ]
     *     }
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Lịch tiêm chủng không tìm thấy."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi cập nhật lịch tiêm chủng."
     * }
     *
     * @param VaccinationScheduleUpdateRequest $request
     * @return JsonResponse
     */

    public function update(VaccinationScheduleUpdateRequest $request): JsonResponse
    {
        try {
            $response = $this->service->update($request);
            return $this->jsonResponseSuccess(new VaccinationScheduleResource($response));
        } catch (Exception $exception) {
            $this->logError('Create prescription Vaccination failed:', $exception);
            return $this->jsonResponseError('Create prescription Vaccination failed', 500);
        }
    }

    /**
     * Xóa lịch tiêm chủng
     *
     * API này cho phép người dùng xóa một nhật ký tiêm chủng đã tồn tại. Người dùng phải xác thực để truy cập API này và chỉ có thể xóa lịch tiêm chủng của chính mình.
     *
     * @authenticated
     * @urlParam id int required ID của lịch tiêm chủng cần xóa. Ví dụ: 1
     *
     * @response 204 {
     *     "status": 204,
     *     "message": "Lịch tiêm chủng đã được xóa thành công."
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Lịch tiêm chủng không tìm thấy."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi xóa lịch tiêm chủng."
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
