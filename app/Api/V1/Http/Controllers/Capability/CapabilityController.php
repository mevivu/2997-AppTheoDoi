<?php

namespace App\Api\V1\Http\Controllers\Capability;

use App\Admin\Http\Controllers\Controller;

use App\Api\V1\Http\Resources\Capability\CapabilityResource;
use App\Api\V1\Repositories\Capability\CapabilityRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use Exception;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;


/**
 * @group Năng lực
 */
class CapabilityController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        CapabilityRepositoryInterface $repository,

    )
    {
        $this->repository = $repository;

    }

    /**
     * DS Năng lực đang hoạt động
     *
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
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
     *          "name": "Năng lực giao tiếp và hợp tác"
     *      },
     *      {
     *          "id": 2,
     *          "name": "Năng lực tự chủ và tự học"
     *      },
     *      {
     *          "id": 3,
     *          "name": "Năng lực giải quyết vấn đề và sáng tạo"
     *      }
     *          ]
     *      }
     * @response 400 {
     *      "status": 500,
     *      "message": "Lỗi hệ thống khi lấy năng lực.",
     *      "data":null
     *  }
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $response = $this->repository->index();
            return $this->jsonResponseSuccess(CapabilityResource::collection($response));
        } catch (Exception $e) {
            $this->logError('Get Capabilities List failed:', $e);
            return $this->jsonResponseError('Lỗi hệ thống khi lấy năng lực.', 500);
        }
    }


}
