<?php

namespace App\Api\V1\Http\Controllers\User;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\User\UserRegisterRequest;
use App\Api\V1\Http\Requests\User\UserUpdateRequest;
use App\Api\V1\Http\Resources\Auth\AuthResource;
use App\Api\V1\Http\Resources\User\{UserConfigurationResource};
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\User\UserServiceInterface;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Api\V1\Support\AuthServiceApi;


/**
 * @group Khách hàng
 */
class UserController extends Controller
{
    use AuthServiceApi, Response, UseLog;

    private static string $GUARD_API = 'api';

    public function __construct(
        UserRepositoryInterface $repository,
        UserServiceInterface $service
    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->middleware('auth:api', ['except' => ['register']]);
    }


    /**
     * Đăng ký người dùng
     *
     * API này cho phép người dùng mới đăng ký bằng cách cung cấp các chi tiết cần thiết như email, tên đầy đủ, mật khẩu và số điện thoại.
     *
     * @bodyParam email string required Địa chỉ email của người dùng. Example: user@example.com
     * @bodyParam fullname string required Tên đầy đủ của người dùng. Example: John Doe
     * @bodyParam password string required Mật khẩu của tài khoản. Example: password123
     * @bodyParam password_confirmation string required Xác nhận mật khẩu. Example: password123
     * @bodyParam phone string required Số điện thoại của người dùng. Example: 0961592551
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "User registered successfully.",
     *     "data": {
     *         "id": 1,
     *         "email": "user@example.com",
     *         "fullname": "John Doe",
     *         "password": "123456",
     *         "password_confirmation": "123456",
     *         "phone": "0961592551"
     *     }
     * }
     *
     * @response 422 {
     *     "status": 422,
     *     "message": "The given data was invalid.",
     *     "errors": {
     *         "email": ["The email has already been taken."],
     *         "phone": ["The phone format is invalid."]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "User creation failed due to a server error."
     * }
     *
     * @return JsonResponse
     */
    public function register(UserRegisterRequest $request): JsonResponse
    {
        try {
            $response = $this->service->store($request);

            return $this->jsonResponseSuccess($response);
        } catch (Throwable $e) {
            $this->logError('User creation failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }


    /**
     * Cập nhật thông tin người dùng
     *
     * API này cho phép người dùng cập nhật thông tin cá nhân.
     *
     * @authenticated
     * @bodyParam fullname string required Tên đầy đủ mới của người dùng. Example: Jane Doe
     * @bodyParam phone string optional Số điện thoại mới của người dùng, phải là số điện thoại hợp lệ. Example: 0977123456
     * @bodyParam email string optional Địa chỉ email mới của người dùng. Example: newuser@example.com
     * @bodyParam avatar string optional Đường dẫn hình ảnh đại diện mới, nếu cập nhật. Example: http://example.com/avatar.jpg
     * @bodyParam gender string optional Giới tính, nếu cập nhật. Example: 1
     * @bodyParam birthday string optional Ngày sinh, nếu cập nhật. Example: 2024-12-12
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thông tin người dùng đã được cập nhật thành công.",
     *     "data": {
     *         "id": 1,
     *         "fullname": "Jane Doe",
     *         "gender": 1,
     *         "birthday": "2024-12-12",
     *         "phone": "0977123456",
     *         "email": "newuser@example.com",
     *         "avatar": "http://example.com/avatar.jpg"
     *     }
     * }
     *
     * @response 422 {
     *     "status": 422,
     *     "message": "Dữ liệu không hợp lệ",
     *     "errors": {
     *         "phone": ["Số điện thoại đã được sử dụng."],
     *         "email": ["Địa chỉ email đã được sử dụng."]
     *     }
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Không thể cập nhật thông tin người dùng do lỗi server."
     * }
     *
     * @param UserUpdateRequest $request
     * @return JsonResponse
     */
    public function update(UserUpdateRequest $request): JsonResponse
    {
        try {
            $response = $this->service->update($request);

            return $this->jsonResponseSuccess(new AuthResource($response));
        } catch (Throwable $e) {
            $this->logError('User creation failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }



}