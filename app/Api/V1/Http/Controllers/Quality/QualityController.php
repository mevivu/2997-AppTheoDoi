<?php

namespace App\Api\V1\Http\Controllers\Quality;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Resources\Capability\CapabilityResource;
use App\Api\V1\Repositories\Quality\QualityRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use Exception;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;


/**
 * @group Phẩm chất
 */
class QualityController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        QualityRepositoryInterface $repository,

    )
    {
        $this->repository = $repository;

    }

    /**
     * DS phẩm chất đang hoạt động
     *
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *    "status": 200,
     *    "message": "Thực hiện thành công.",
     *    "data": [
     *      {
     *          "id": 1,
     *          "name": "Yêu nước"
     *      },
     *      {
     *          "id": 2,
     *          "name": "Nhân ái"
     *      },
     *      {
     *          "id": 3,
     *          "name": "Chăm chỉ"
     *      },
     *      {
     *          "id": 4,
     *          "name": "Trung thực"
     *      },
     *      {
     *          "id": 5,
     *          "name": "Trách nhiệm"
     *      }
     *          ]
     *              }
     * @response 400 {
     *      "status": 400,
     *      "message": "Lỗi hệ thống khi lấy phẩm chất",
     *      "data":null
     * }
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $response = $this->repository->index();
            return $this->jsonResponseSuccess(CapabilityResource::collection($response));
        } catch (Exception $e) {
            $this->logError('Get Qualities List failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy phẩm chất.', 500);
        }
    }


}
