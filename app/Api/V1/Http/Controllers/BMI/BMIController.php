<?php

namespace App\Api\V1\Http\Controllers\BMI;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\BMI\BMIRequest;
use App\Api\V1\Http\Resources\BMI\BMIResourceCollection;
use App\Api\V1\Http\Resources\Question\QuestionResourceCollection;
use App\Api\V1\Repositories\BMI\BMIRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use Exception;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;


/**
 * @group BMI
 */
class BMIController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        BMIRepositoryInterface $repository,

    )
    {
        $this->repository = $repository;

    }

    /**
     * DS Thông tin BMI
     *
     * DS Thông tin BMI
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
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *       {
     *              "id": 1,
     *              "age": 23,
     *              "question": "7.00",
     *              "gender": 1
     *       }
     *            ]
     * }
     *
     * @param BMIRequest $request
     *
     * @return JsonResponse
     */
    public function index(BMIRequest $request): JsonResponse
    {
        try {
            $response = $this->repository->index(
                $request->limit ?? 10,
                $request->page ?? 1,
            );
            if ($response) {
                return $this->jsonResponseSuccess(new BMIResourceCollection($response));
            } else {
                return $this->jsonResponseError('Get BMI failed', 500);
            }

        } catch (Exception $e) {
            $this->logError('Get user Questions failed:', $e);
            return $this->jsonResponseError('Get BMI failed', 500);
        }
    }


}
