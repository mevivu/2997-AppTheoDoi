<?php

namespace App\Api\V1\Http\Controllers\Guide;

use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Exception\NotFoundException;
use App\Api\V1\Http\Requests\Guide\GuideRequest;
use App\Api\V1\Http\Resources\Guide\GuideResource;
use App\Api\V1\Repositories\Guide\GuideRepositoryInterface;
use App\Api\V1\Services\Guide\GuideServiceInterface;
use App\Api\V1\Validate\Validator;
use App\Http\Controllers\Controller;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;
use Exception;

/**
 * Group Hướng dẫn
 */
class GuideController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected $repository;
    protected $service;

    public function __construct(
        GuideRepositoryInterface $repository,
        GuideServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Danh sách bài hướng dẫn
     *
     * Lấy danh sách hướng dẫn theo loại
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated
     * @queryParam page int Trang hiện tại. Example: 1
     * @queryParam limit int Số lượng bản ghi trên mỗi trang. Example: 10
     * @queryParam type string required Loại của hướng dẫn. Examples: strength
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": [
     *         {
     *             "id": 1,
     *             "title": "Hướng dẫn",
     *             "description": "Chi tiết hướng dẫn",
     *             "type":strength
     *             "steps": [
     *              {
     *              "order": 1,
     *              "title": "Bước 1",
     *                 "description": "Mô tả bước 1"
     *              },
     *              {
     *              "order": 2,
     *              "title": "Bước 2",
     *              "description": "Mô tả bước 2"
     *              }
     *              ]
     *         }
     *     ]
     * }
     * @response 500 {
     *      "status": 500,
     *      "message": "Lỗi hệ thống khi lấy danh sách nhật ký."
     *  }
     *
     * @param GuideRequest $request
     * @return JsonResponse
     * /
     */
    public function index(GuideRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();
            $guides = $this->service->getGuides($data);
            return $this->jsonResponseSuccess(GuideResource::collection($guides));
        } catch (\Exception $e) {
            $this->logError('Get guides failed:', $e);
            return $this->jsonResponseError('Get guides failed', 500);
        }
    }

    /**
     * Lấy chi tiết Hướng Dẫn
     *
     * API này cho phép người dùng lấy chi tiết của một hướng dẫn cụ thể dựa trên ID của hướng dẫn đó.
     * Người dùng phải xác thực để truy cập API này.
     *
     * @authenticated
     * @urlParam id int required ID của hướng dẫn cần xem chi tiết. Example: 1
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Chi tiết hướng dẫn.",
     *     "data": {
     *         "id": 1,
     *         "title": "Bài 1",
     *         "description": "Chi tiết hướng dẫn",
     *         "type": "Sức mạnh",
     *         "step":[
     *              {
     *                  "order":1,
     *                  "title":"Bước 1",
     *                  "description":"Mô tả 1"
     *              },
     *              {
     *                  "order":2,
     *                   "title":"Bước 2",
     *                   "description":"Mô tả 2"
     *              }
     *          ]
     *     }
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Hướng dẫn không tìm thấy."
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
            return $this->jsonResponseSuccess(new GuideResource($response));
        } catch (NotFoundException|BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage());
        } catch (Exception $exception) {
            $this->logError('Deleted failed:', $exception);
            return $this->jsonResponseError('Deleted failed', 500);
        }
    }
}
