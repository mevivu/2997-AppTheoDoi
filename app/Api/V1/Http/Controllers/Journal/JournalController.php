<?php

namespace App\Api\V1\Http\Controllers\Journal;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Journal\JournalRequest;
use App\Api\V1\Http\Resources\Journal\JournalCollection;
use App\Api\V1\Http\Resources\Journal\JournalResource;
use App\Api\V1\Repositories\Journal\JournalRepositoryInterface;
use App\Api\V1\Services\Journal\JournalServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
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


}
