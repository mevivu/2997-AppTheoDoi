<?php

namespace App\Api\V2\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\UserSession\UserSessionRepositoryInterface;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\User\UserServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V2\Http\Requests\Auth\LoginRequest;
use App\Traits\JwtService;
use App\Traits\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;

/**
 * @group Người dùng V2
 */
class AuthController extends Controller
{
    use JwtService, Response, AuthServiceApi, UseLog;

    private static string $GUARD_API = 'api';

    private $login;

    protected UserRepositoryInterface $userRepository;

    protected UserSessionRepositoryInterface $sessionRepository;

    public function __construct(
        UserRepositoryInterface        $userRepository,
        UserSessionRepositoryInterface $sessionRepository,
        UserServiceInterface           $service,
    ) {
        $this->userRepository = $userRepository;
        $this->sessionRepository = $sessionRepository;
        $this->service = $service;
        $this->middleware('auth:api', [
            'except' => [
                'login',
            ]
        ]);
    }

    /**
     * Đăng nhập V2 (Quản lý đa thiết bị & kiểm tra giới hạn thiết bị)
     *
     * API này dùng để đăng nhập cho phiên bản ứng dụng V2, kiểm tra số thiết bị đã liên kết
     * và trả về lỗi DEVICE_LIMIT_EXCEEDED (HTTP 403) nếu vượt quá số lượng cho phép của gói.
     *
     * @bodyParam email string required Email người dùng. Example: user@example.com
     * @bodyParam password string required Mật khẩu. Example: 123456
     * @bodyParam device_token string Token FCM đại diện cho thiết bị nhận thông báo.
     * @bodyParam device_id string Mã định danh phần cứng thiết bị (UUID/Android ID/IDFV).
     * @bodyParam device_name string Tên hiển thị của thiết bị (ví dụ: iPhone 14 Pro, Samsung S23).
     *
     * @response 200 {
     *     "access_token": "eyJ0eXAi...",
     *     "refresh_token": "eyJ0eXAi...",
     *     "status": 1,
     *     "expires_in": 5184000,
     *     "package": {...}
     * }
     *
     * @response 403 {
     *     "status": 403,
     *     "code": "DEVICE_LIMIT_EXCEEDED",
     *     "message": "Tài khoản của bạn đã đạt giới hạn tối đa :max thiết bị cho gói hiện tại...",
     *     "data": {
     *         "max_devices": 1,
     *         "current_devices": 1,
     *         "can_upgrade": true
     *     }
     * }
     *
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            return $this->loginUserV2($request);
        } catch (Exception $e) {
            $this->logError("Login V2 failed", $e);
            return $this->jsonResponseError($e->getMessage());
        }
    }
}
