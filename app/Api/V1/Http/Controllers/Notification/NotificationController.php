<?php

namespace App\Api\V1\Http\Controllers\Notification;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Notification\NotificationRequest;
use App\Api\V1\Http\Resources\Notification\NotificationResourceCollection;
use App\Api\V1\Http\Resources\Notification\NotificationResource;
use App\Api\V1\Repositories\Notification\NotificationRepositoryInterface;

use App\Api\V1\Services\Notification\NotificationServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use Exception;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use PgSql\Lob;

/**
 * @group Thông báo
 */

class NotificationController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    public function __construct(
        NotificationRepositoryInterface $repository,
        NotificationServiceInterface $service

    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * DS thông báo của nhân viên
     *
     * DS thông báo thông báo theo nhân viên 
     *
     * Trạng thái thông báo (status) gồm:
     * - 1: Chưa đọc
     * - 2: Đã đọc
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
     *        {
     *          "id": 54,
     *          "user": {
     *              "name": "NGUYỄN MINH HUY 1",
     *              "image": "/public/uploads/images/99-thuyen_hoa.jpg"
     *           },
     *          "title": "Thông báo cho khách hàng Phạm Thị Kim Ngân",
     *          "message": "Thông báo cho khách hàng Phạm Thị Kim Ngân",
     *          "status": 1,
     *          "created_at": "28-08-2024 09:59"
     *        }
     *    ]
     * }
     *
     * @param NotificationRequest $request
     *
     * @return JsonResponse
     */
    public function index(NotificationRequest $request): JsonResponse
    {
        try {
            $response = $this->service->getNotificationByUser($request);
            return $this->jsonResponseSuccess(new NotificationResourceCollection($response));
        } catch (Exception $e) {
            $this->logError('Get user notifications failed:', $e);
            return $this->jsonResponseError('Get user notifications failed', 500);
        }
    }
}
