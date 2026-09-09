<?php

namespace App\Api\V2\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\UserSession\UserSessionRepositoryInterface;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\User\UserServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Api\V2\Http\Requests\Auth\AppleLoginRequest;
use App\Api\V2\Http\Requests\Auth\GoogleLoginRequest;
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
                'loginGoogle',
                'loginApple',
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

    /**
     * Đăng nhập / Đăng ký nhanh qua Google V2
     *
     * Kiểm tra nếu email đã có tài khoản thì cho đăng nhập bình thường và cấp access_token,
     * nếu chưa có tài khoản thì tự động tạo User mới với service_type Google và đăng nhập.
     *
     * @bodyParam email string required Email người dùng Google.
     * @bodyParam fullname string Tên đầy đủ từ Google profile.
     * @bodyParam avatar string Ảnh đại diện từ Google profile.
     * @bodyParam google_id string ID tài khoản Google.
     * @bodyParam device_token string Token FCM đại diện cho thiết bị nhận thông báo.
     * @bodyParam device_id string Mã định danh phần cứng thiết bị.
     * @bodyParam device_name string Tên hiển thị của thiết bị.
     *
     * @param GoogleLoginRequest $request
     * @return JsonResponse
     */
    public function loginGoogle(GoogleLoginRequest $request): JsonResponse
    {
        try {
            return $this->loginGoogleUserV2($request);
        } catch (Exception $e) {
            $this->logError("Login Google V2 failed", $e);
            return $this->jsonResponseError($e->getMessage());
        }
    }

    /**
     * Đăng nhập hoặc đăng ký nhanh bằng Apple ID (V2)
     *
     * Xác thực thông tin Apple ID nhận từ ứng dụng iOS,
     * nếu tài khoản đã tồn tại (theo apple_id hoặc email) thì đăng nhập và liên kết;
     * nếu chưa có tài khoản thì tự động tạo User mới với service_type Apple và đăng nhập.
     *
     * @bodyParam apple_id string required Mã định danh tài khoản Apple (userIdentifier).
     * @bodyParam email string Email người dùng Apple (nếu có hoặc relay email).
     * @bodyParam fullname string Tên đầy đủ từ Apple profile.
     * @bodyParam identity_token string JWT token từ Apple.
     * @bodyParam device_token string Token FCM đại diện cho thiết bị nhận thông báo.
     * @bodyParam device_id string Mã định danh phần cứng thiết bị.
     * @bodyParam device_name string Tên hiển thị của thiết bị.
     *
     * @param AppleLoginRequest $request
     * @return JsonResponse
     */
    public function loginApple(AppleLoginRequest $request): JsonResponse
    {
        try {
            return $this->loginAppleUserV2($request);
        } catch (Exception $e) {
            $this->logError("Login Apple V2 failed", $e);
            return $this->jsonResponseError($e->getMessage());
        }
    }
}
