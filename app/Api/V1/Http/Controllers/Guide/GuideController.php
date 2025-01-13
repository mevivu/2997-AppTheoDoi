<?php

namespace App\Api\V1\Http\Controllers\Guide;

use App\Api\V1\Http\Requests\Guide\GuideRequest;
use App\Api\V1\Http\Resources\Guide\GuideResource;
use App\Api\V1\Repositories\Guide\GuideRepositoryInterface;
use App\Api\V1\Services\Guide\GuideServiceInterface;
use App\Enums\ActiveStatus;
use App\Http\Controllers\Controller;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;

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
     *             "id": 1,
     *             "title": "Hướng dẫn",
     *             "description": "Chi tiết hướng dẫn",
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
     * Chi tiết bài hướng dẫn
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @response {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 1,
     *              "title": "Hướng dẫn",
     *              "description": "Chi tiết hướng dẫn",
     *              "steps": [
     *               {
     *               "order": 1,
     *               "title": "Bước 1",
     *                  "description": "Mô tả bước 1"
     *               },
     *               {
     *               "order": 2,
     *               "title": "Bước 2",
     *               "description": "Mô tả bước 2"
     *               }
     *               ]
     *          }
     *      ]
     *  }
     */

    public function show($id): JsonResponse
    {
        try {
            $guide = $this->repository->find($id);

            if ($guide) {
                return $this->jsonResponseSuccess(new GuideResource($guide));
            }
            return $this->jsonResponseError('Không tìm thấy hướng dẫn', 404);
        } catch (\Exception $e) {
            $this->logError('Get guide failed:', $e);
            return $this->jsonResponseError('Get guide failed', 500);
        }
    }
}
