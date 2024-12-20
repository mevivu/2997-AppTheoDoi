<?php

namespace App\Api\V1\Http\Controllers\Journal;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Journal\JournalRequest;
use App\Api\V1\Http\Requests\Journal\JournalUpdateRequest;
use App\Api\V1\Http\Resources\Exercise\ExerciseDetailCollection;
use App\Api\V1\Http\Resources\Journal\JournalCollection;
use App\Api\V1\Http\Resources\Journal\JournalResource;
use App\Api\V1\Repositories\Journal\JournalRepositoryInterface;
use App\Api\V1\Services\Journal\JournalServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Api\V1\Validate\Validator;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Nhật ký
 */
class JournalController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        JournalRepositoryInterface $repository,
        JournalServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Lấy Nhật Ký Theo Thể Loại và Theo Con
     *
     * API này cho phép người dùng lấy danh sách các nhật ký dựa trên thể loại và ID của trẻ.
     *
     * Thể loại nhật ký (`type`) bao gồm:
     * - `prescription`: Nhật ký toa thuốc.
     * - `moment`: Nhật ký khoảnh khắc .
     *
     * @authenticated
     * @queryParam child_id int required ID của trẻ. Example: 1
     * @queryParam type string required Thể loại của nhật ký. Examples: moment
     * @queryParam limit int optional Số lượng nhật ký trên mỗi trang, mặc định là 10. Example: 10
     * @queryParam page int optional Trang cần hiển thị, mặc định là 1. Example: 1
     * @queryParam date string required Ngày. Example: 2024-12-18
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy danh sách nhật ký thành công.",
     *     "data": {
     *         "total": 10,
     *         "per_page": 10,
     *         "current_page": 1,
     *         "journals": [
     *             {
     *                 "id": 1,
     *                 "child_id": 1,
     *                 "description": "Mô tả nhật ký",
     *                 "type": "moment",
     *                 "created_at": "2024-01-01",
     *                 "updated_at": "2024-01-02"
     *             }
     *         ]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách nhật ký."
     * }
     *
     * @param JournalRequest $request
     * @return JsonResponse
     */

    public function index(JournalRequest $request): JsonResponse
    {
        try {
            $response = $this->service->index($request);
            return $this->jsonResponseSuccess(new JournalCollection($response));
        } catch (Exception $exception) {
            $this->logError('Get journals failed:', $exception);
            return $this->jsonResponseError('Get journals failed', 500);
        }
    }

    /**
     * Tạo Nhật Ký theo loại Cho Con
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
     * @param JournalRequest $request
     * @return JsonResponse
     */
    public function store(JournalRequest $request): JsonResponse
    {
        try {
            $response = $this->service->store($request);
            return $this->jsonResponseSuccess(new JournalResource($response));
        } catch (Exception $exception) {
            $this->logError('Create prescription journal failed:', $exception);
            return $this->jsonResponseError('Create prescription journal failed', 500);
        }
    }

    /**
     * Cập nhật Nhật Ký Cho Con
     *
     * API này cho phép người dùng cập nhật một nhật ký đã tồn tại.
     * Người dùng phải xác thực để truy cập API này và chỉ có thể cập nhật nhật ký của mình.
     *
     * @authenticated
     * @bodyParam title string optional Tiêu đề của nhật ký. Example: "Toa Thuốc Điều Chỉnh"
     * @bodyParam content string optional Nội dung chi tiết của nhật ký. Example: "Đã thêm thông tin về liều lượng mới..."
     * @bodyParam image string optional Đường dẫn hình ảnh mới liên quan đến nhật ký. Example: "/images/prescriptions/updated.jpg"
     * @bodyParam id int required ID của nhật ký cần cập nhật. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Nhật ký đã được cập nhật thành công.",
     *     "data": {
     *         "id": 1,
     *         "child_id": 1,
     *         "title": "Toa Thuốc Điều Chỉnh",
     *         "content": "Đã thêm thông tin về liều lượng mới...",
     *         "image": "/images/prescriptions/updated.jpg",
     *         "type": "prescription",
     *         "created_at": "2024-01-01",
     *         "updated_at": "2024-01-05"
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
     *     "message": "Lỗi hệ thống khi cập nhật nhật ký."
     * }
     *
     * @param JournalUpdateRequest $request
     * @return JsonResponse
     */
    public function update(JournalUpdateRequest $request): JsonResponse
    {
        try {
            $response = $this->service->update($request);
            return $this->jsonResponseSuccess(new JournalResource($response));
        } catch (Exception $exception) {
            $this->logError('Create prescription journal failed:', $exception);
            return $this->jsonResponseError('Create prescription journal failed', 500);
        }

    }

    /**
     * Lấy chi tiết Nhật Ký
     *
     * API này cho phép người dùng lấy chi tiết của một nhật ký cụ thể dựa trên ID của nhật ký đó.
     * Người dùng phải xác thực để truy cập API này.
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
            return $this->jsonResponseSuccess(new JournalResource($response));
        } catch (NotFoundException|BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $exception) {
            $this->logError('Deleted failed:', $exception);
            return $this->jsonResponseError('Deleted failed', 500);
        }
    }


    /**
     * Xóa Nhật Ký
     *
     * API này cho phép người dùng xóa một nhật ký đã tồn tại.
     * Người dùng phải xác thực để truy cập API này và chỉ có thể xóa nhật ký của mình.
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
