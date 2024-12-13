<?php

namespace App\Api\V1\Http\Controllers\Slider;

use App\Api\V1\Http\Requests\Slider\SliderRequest;
use App\Api\V1\Http\Resources\Slider\SliderResource;
use App\Api\V1\Repositories\Slider\SliderRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Slider\SliderStatus;
use App\Http\Controllers\Controller;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;

/**
 * Group Slider
 */
class SliderController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    protected $repository;

    public function __construct(
        SliderRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Danh sách slider
     *
     * Lấy danh sách slider
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
     *             "name": "Slider giới thiệu",
     *             "desc": "Slider giới thiệu",
     *             "plain_key": "slider_introduce",
     *             "items": []
     *         },
     *         {
     *             "id": 1,
     *             "name": "Slider Home",
     *             "desc": "Slider Home",
     *             "plain_key": "slider_home",
     *             "items": [
     *                 {
     *                     "id": 1,
     *                     "title": "Slider 1",
     *                     "link": "#",
     *                     "position": 0,
     *                     "image": "/public/uploads/files/99-thuyen_hoa.jpg",
     *                     "mobile_image": "/public/uploads/files/99-thuyen_hoa.jpg"
     *                 }
     *             ]
     *         }
     *     ]
     * }
     *
     *
     * @param \App\Api\V1\Http\Requests\Post\PostRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $posts = $this->repository->getByQueryBuilder([
                'status' => SliderStatus::Active(),
            ])->get();
            return $this->jsonResponseSuccess(SliderResource::collection($posts));
        } catch (\Exception $e) {
            $this->logError('Get posts failed:', $e);
            return $this->jsonResponseError('Get posts failed', 500);
        }
    }

    /**
     * Chi tiết slider
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @response {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": {
     *         "id": 1,
     *         "name": "Slider Home",
     *         "desc": "Slider Home",
     *         "plain_key": "slider_home",
     *         "items": [
     *             {
     *                 "id": 1,
     *                 "title": "Slider 1",
     *                 "link": "#",
     *                 "position": 0,
     *                 "image": "/public/uploads/files/99-thuyen_hoa.jpg",
     *                 "mobile_image": "/public/uploads/files/99-thuyen_hoa.jpg"
     *             }
     *         ]
     *     }
     * }
     *
     * @param mixed $id
     * @return JsonResponse
     */

    public function show($id)
    {
        try {
            $slider = $this->repository->find($id);

            if ($slider) {
                return $this->jsonResponseSuccess(new SliderResource($slider));
            }

            return $this->jsonResponseError('Không tìm thấy slider', 404);
        } catch (\Exception $e) {
            $this->logError('Get slider failed:', $e);
            return $this->jsonResponseError('Get slider failed', 500);
        }
    }
}