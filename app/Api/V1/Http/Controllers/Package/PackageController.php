<?php

namespace App\Api\V1\Http\Controllers\Package;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Repositories\Package\PackageRepositoryInterface;
use App\Api\V1\Services\Package\PackageServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Gói dịch vụ
 */
class PackageController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        PackageRepositoryInterface $repository,
        PackageServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
        $this->middleware('auth:api');

    }

    /**
     * Lấy DS các gói dịch vụ
     *
     * API này cho phép người dùng lấy danh sách toàn bộ các gói dịch vụ hiện có. Đáp ứng với các bộ lọc và phân trang nếu được cung cấp.
     *
     * @authenticated
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Lấy danh sách các gói dịch vụ thành công.",
     *     "data": {
     *         "total": 10,
     *         "per_page": 10,
     *         "current_page": 1,
     *         "packages": [
     *             {
     *                 "id": 1,
     *                 "name": "Gói cơ bản",
     *                 "description": "Mô tả gói cơ bản",
     *                 "price": 100000
     *             }
     *         ]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách các gói dịch vụ."
     * }
     *
     * @return JsonResponse
     */

    public function index(): JsonResponse
    {
        try {
            $response = $this->service->index();
            return $this->jsonResponseSuccess($response);
        } catch (Exception $exception) {
            $this->logError('Get packages failed:', $exception);
            return $this->jsonResponseError('Get  packages failed', 500);
        }

    }


}
