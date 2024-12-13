<?php

namespace App\Api\V1\Http\Controllers\Exercise;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Exercise\ExerciseRequest;


use App\Api\V1\Http\Resources\Exercise\ExerciseCollection;
use App\Api\V1\Http\Resources\Exercise\ExerciseDetailCollection;

use App\Api\V1\Repositories\Exercise\ExerciseRepositoryInterface;
use App\Api\V1\Services\Exercise\ExerciseServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;


/**
 * @group Bài tập
 */
class ExerciseController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        ExerciseRepositoryInterface $repository,
        ExerciseServiceInterface    $service

    )
    {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * DS Exercise
     *
     * DS bài tập
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @queryParam page integer
     * Trang hiện tại, page > 0. Example: 1
     *
     * @queryParam limit integer
     * Số lượng thông báo trong 1 trang, limit > 0. Example: 1
     *
     *
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *        {
     *               "id": 1,
     *               "name": "Bài tập thể chất 1",
     *                "status": "active"
     *        },
     *        {
     *            "id": 2,
     *            "name": "Bài tập sức mạnh 1",
     *             "status": "active"
     *        }
     *    ]
     * }
     *
     * @param \Illuminate\Http\Request $request
     * *
     * * @return \Illuminate\Http\Response
     */
    public function index(ExerciseRequest $request): JsonResponse
    {
        try {
            return $this->jsonResponseSuccess(new ExerciseCollection($this->service->index($request)));
        } catch (\Exception $exception) {
            $this->logError('Get Exercises failed:', $exception);
            return $this->jsonResponseError('Get Exercises failed', 500);
        }

    }

    /**
     * Chi tiết bài tập
     *
     * lấy chi tiết  bài tập
     *
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *        {
     *             "id": 1,
     *               "name": "Bài tập thể chất 1",
     *               "description": "<p>B&agrave;i tập thể chất 1</p>",
     *               "status": "active",
     *               "exercise_type": "physical"
     *        }
     *
     *    ]
     * }
     *
     * @param \Illuminate\Http\Request $request
     * *
     * * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        try {
            return $this->jsonResponseSuccess(new ExerciseDetailCollection($this->repository->findOrFail($id)));
        } catch (\Exception $exception) {
            $this->logError('Get detail Exercises failed:', $exception);
            return $this->jsonResponseError('Get detail Exercises failed', 500);
        }
    }
}
