<?php

namespace App\Api\V1\Http\Controllers\Product;

use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Product\ProductRequest;
use App\Api\V1\Http\Resources\Product\ProductCollection;
use App\Api\V1\Http\Resources\Product\ProductResource;
use App\Api\V1\Repositories\Product\ProductRepositoryInterface;
use App\Api\V1\Services\Product\ProductServiceInterface;
use App\Api\V1\Validate\Validator;
use App\Http\Controllers\Controller;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Group Sản phẩm
 */
class ProductController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected ProductRepositoryInterface $repository;

    protected ProductServiceInterface $service;

    public function __construct(
        ProductRepositoryInterface $repository,
        ProductServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Danh sách sản phẩm
     *
     * Lấy danh sách sản phẩm theo ID thương hiệu,ID danh mục sản phẩm,Từ khóa tìm kiếm .
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated
     * @queryParam page int Trang hiện tại. Example: 1
     * @queryParam limit int Số lượng bản ghi trên mỗi trang. Example: 10
     * @queryParam brand_id int ID thương hiệu của sản phẩm. Examples: 1
     * @queryParam product_catalog_id int ID danh mục sản phẩm của sản phẩm. Examples: 1
     * @queryParam keyword string Từ khóa tìm kiếm trong tên của sản phẩm . Examples: 1
     *
     * @response 200{
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
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi hệ thống khi lấy danh sách sản phẩm."
     * }
     *
     * @param ProductRequest $request
     * @return JsonResponse
     */
    public function index(ProductRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            $products = $this->service->getProducts($data);

            return $this->jsonResponseSuccess(new ProductCollection($products));
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
     * @authenticated
     * @urlParam id int required ID của sản phẩm cần xem chi tiết. Example: 1
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
     *             "brand_name": "Thương hiệu A",
     *             "product_catalog_name:[
     *               "Dễ vỡ",
     *               "Điển tử"
     *               ]
     *         }
     *     ]
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Sản phẩm không tìm thấy."
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
            return $this->jsonResponseSuccess(new ProductResource($response));
        } catch (NotFoundException|BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $exception) {
            $this->logError('Deleted failed:', $exception);
            return $this->jsonResponseError('Deleted failed', 500);
        }
    }
}
