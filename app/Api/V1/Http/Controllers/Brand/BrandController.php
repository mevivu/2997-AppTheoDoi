<?php

namespace App\Api\V1\Http\Controllers\Brand;

use App\Api\V1\Http\Requests\Brand\BrandRequest;
use App\Api\V1\Http\Resources\Brand\BrandResource;
use App\Api\V1\Repositories\Brand\BrandRepositoryInterface;
use App\Api\V1\Services\Brand\BrandServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Group Thương hiệu
 */
class BrandController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected $repository;
    protected $service;

    public function __construct(
        BrandRepositoryInterface $repository,
        BrandServiceInterface    $service
    )
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Danh sách thương hiệu
     *
     * Lấy danh sách thương hiệu.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated
     * @queryParam page int Trang hiện tại. Example: 1
     * @queryParam limit int Số lượng bản ghi trên mỗi trang. Example: 10
     *
     * @response 200{
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": [
     *         {
     *             "id": 1,
     *             "name": "Samsung",
     *             "description":"Mô tả thương hiệu 1"
     *             "country": "Hàn Quốc",
     *
     *         },
     *         {
     *             "id": 2,
     *             "name": "Sony",
     *             "description":"Mô tả thương hiệu 2"
     *             "country": "Nhật Bản",
     *         }
     *     ]
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách thương hiệu."
     * }
     *
     * @param BrandRequest $request
     * @return JsonResponse
     */
    public function index(BrandRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $brands = $this->service->getBrands($data);

            return $this->jsonResponseSuccess(BrandResource::collection($brands));
        } catch (\Exception $e) {
            $this->logError('Get brands failed:', $e);
            return $this->jsonResponseError('Get brands failed', 500);
        }
    }
}
