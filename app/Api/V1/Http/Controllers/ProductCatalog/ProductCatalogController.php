<?php

namespace App\Api\V1\Http\Controllers\ProductCatalog;


use App\Api\V1\Http\Requests\ProductCatalog\ProductCatalogRequest;
use App\Api\V1\Http\Resources\ProductCatalog\ProductCatalogResource;
use App\Api\V1\Repositories\ProductCatalog\ProductCatalogRepositoryInterface;
use App\Api\V1\Services\ProductCatalog\ProductCatalogServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Group Danh mục sản phẩm
 */
class ProductCatalogController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected $repository;
    protected $service;

    public function __construct(
        ProductCatalogRepositoryInterface $repository,
        ProductCatalogServiceInterface    $service
    )
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Danh sách danh mục sản phẩm
     *
     * Lấy danh sách danh mục sản phẩm.
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
     *             "name": "Điện tử",
     *         },
     *         {
     *             "id": 2,
     *             "name": "Gia dụng",
     *         }
     *     ]
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách danh mục sản phẩm."
     * }
     *
     * @param ProductCatalogRequest $request
     * @return JsonResponse
     */
    public function index(ProductCatalogRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $productCatalogs = $this->service->getProductCatalogs($data);

            return $this->jsonResponseSuccess(ProductCatalogResource::collection($productCatalogs));
        } catch (\Exception $e) {
            $this->logError('Get product catalogs failed:', $e);
            return $this->jsonResponseError('Get product catalogs failed', 500);
        }
    }
}
