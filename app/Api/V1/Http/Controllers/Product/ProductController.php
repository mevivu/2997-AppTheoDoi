<?php

namespace App\Api\V1\Http\Controllers\Product;

use App\Api\V1\Http\Requests\Product\ProductRequest;
use App\Api\V1\Http\Resources\Product\ProductResource;
use App\Api\V1\Repositories\Product\ProductRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Http\Controllers\Controller;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;

/**
 * Group Sản phẩm
 */
class ProductController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected $repository;

    public function __construct(
        ProductRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Danh sách sản phẩm
     *
     * Lấy danh sách sản phẩm đã xuất bản
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @queryParam page int Trang hiện tại. Example: 1
     * @queryParam limit int Số lượng bản ghi trên mỗi trang. Example: 10
     *
     * @response {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": [
     *         {
     *             "id": 2,
     *             "name": "sản phẩm",
     *             "description": "mô tả sản phẩm",
     *             "image": "/image.png",
     *             "brand_name": "Thương hiệu A"
     *             "product_catalog_name:[
     *              "Dễ vỡ",
     *              "Điển tử"
     *              ]
     *
     *         }
     *     ]
     * }
     *
     * @param \App\Api\V1\Http\Requests\Product\ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $page = $data['page'] ?? 1;
            $limit = $data['limit'] ?? 10;
            $relations = $data['relations'] ?? [];


            $filters = [
                'status' => ActiveStatus::Active->value,
            ];

            if (!empty($data['brand_id'])) {
                $filters['brand_id'] = $data['brand_id'];
            }
            if (!empty($data['keyword'])) {
                $filters[] = ['name', 'LIKE', "%{$data['keyword']}%"];
            }

            $products = $this->repository
                ->getByQueryBuilder($filters, $relations)
                ->whereHas('productCatalogs', function ($query) use ($data) {
                    if (!empty($data['product_catalog_id'])) {
                        $query->where('product_catalog_id', $data['product_catalog_id']);
                    }
                })
                ->paginate($limit, ['*'], 'page', $page);

            return $this->jsonResponseSuccess(ProductResource::collection($products));
        } catch (\Exception $e) {
            $this->logError('Get products failed:', $e);
            return $this->jsonResponseError('Get products failed', 500);
        }
    }

    /**
     * Chi tiết sản phẩm
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @response {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": [
     *         {
     *             "id": 2,
     *             "name": "sản phẩm",
     *             "description": "san-pham",
     *             "image": "/image.png",
     *         }
     *     ]
     * }
     *
     * @param mixed $id
     * @return JsonResponse
     */
    public function show($id)
    {
        try {
            $product = $this->repository->find($id);

            if ($product) {
                return $this->jsonResponseSuccess(new ProductResource($product));
            }

            return $this->jsonResponseError('Không tìm thấy sản phẩm', 404);
        } catch (\Exception $e) {
            $this->logError('Get product failed:', $e);
            return $this->jsonResponseError('Get product failed', 500);
        }
    }
}
