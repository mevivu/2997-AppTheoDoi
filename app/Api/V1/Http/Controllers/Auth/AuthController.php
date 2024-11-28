<?php

namespace App\Api\V1\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Http\Requests\Auth\LoginRequest;
use App\Api\V1\Http\Requests\Auth\ResetPasswordRequest;
use App\Api\V1\Http\Requests\Auth\UpdatePasswordRequest;
use App\Api\V1\Http\Requests\Auth\UpdateEmailRequest;
use App\Api\V1\Http\Requests\User\ResendOtpRequest;
use App\Api\V1\Http\Requests\User\VerificationOtp;
use App\Api\V1\Http\Resources\Auth\AuthResource;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\User\UserServiceInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\Response;
use App\Traits\JwtService;
use App\Traits\UseLog;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Throwable;

/**
 * @group Người dùng
 */
class AuthController extends Controller
{
    use JwtService, Response, AuthServiceApi, UseLog;

    private static string $GUARD_API = 'api';

    private $login;

    protected UserRepositoryInterface $userRepository;


    public function __construct(
        UserRepositoryInterface $userRepository,
        UserServiceInterface $service
    ) {
        $this->userRepository = $userRepository;
        $this->service = $service;
        $this->middleware('auth:api', [
            'except' => [
                'login',
                'register',
                'verificationOtp',
                'resendOtp',
                'updatePassword',
                'forgotPassword'
            ]
        ]);
    }

    protected function resolve(): bool
    {
        return Auth::attempt($this->login);
    }

    /**
     * Đăng nhập
     *
     * API này dùng để đăng nhập
     *
     * @bodyParam email string required
     * Email của người dùng. Example: minhhuy1220011@gmail.com
     * @bodyParam password string
     *  Mật khẩu. Example: 123456
     *
     * @response 200 {
     *     "access_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbG9jYWxob3N0OjgwODAvMjc0MS1BcHBEaWNodnV0aHVvbmdtYWkvYXBpL3YxL2RyaXZlcnMvbG9naW4iLCJpYXQiOjE3MjEzODM2ODQsImV4cCI6MTcyNjU2NzY4NCwibmJmIjoxNzIxMzgzNjg0LCJqdGkiOiJwWnNJclVrSms2UHFzT0xrIiwic3ViIjoiOSIsInBydiI6IjIzYmQ1Yzg5NDlmNjAwYWRiMzllNzAxYzQwMDg3MmRiN2E1OTc2ZjcifQ.En3WpOwOpKMTMHk4PmG799dZZ0DwfrH9HraimUqSU24",
     *     "refresh_token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjo5LCJyYW5kb20iOiIxMzcwNzU5NTQxMTcyMTM4MzY4NCIsImlzX3JlZnJlc2hfdG9rZW4iOnRydWUsImV4cCI6MTcyMTkwOTI4NH0.abTovSfJmNOB_8ZqGpbNBFxwhGpue7OSEQgnbdPiVak",
     *     "status": 1,
     *     "expires_in": 5184000
     * }
     *
     * @response 401 {
     *     "status": 401,
     *     "message": "Thông tin đăng nhập chưa chính xác.",
     * }
     *
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            return $this->loginUser($request);
        } catch (Exception $e) {
            $this->logError("Login failed", $e);
            return $this->jsonResponseError($e->getMessage());
        }
    }

    /**
     * Cập nhật email
     *
     * API này cho phép cập nhật email cá nhân của họ.
     *
     * Điều kiện:
     * + Email phải khác với email hiện tại của người dùng
     * + Email không được giống với email của tất cả người dùng trước đó.
     *
     * @authenticated
     * @bodyParam email string optional Địa chỉ email mới của người dùng. Example: newuser@example.com
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thông tin email người dùng đã được cập nhật thành công.",
     *     "data": {
     *         "id": 1,
     *         "fullname": "Jane Doe",
     *         "bank_account_number": "123456789",
     *         "bank_id": 1,
     *         "phone": "0977123456",
     *         "email": "newuser@example.com",
     *         "avatar": "http://example.com/avatar.jpg"
     *     }
     * }
     *
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Không thể cập nhật thông tin người dùng do lỗi server."
     * }
     *
     * @response 422 {
     *     "status": 422,
     *     "message": ["Email đã được sử dụng. "]
     * }
     *
     * @param UpdateEmailRequest $request
     * @return JsonResponse
     */
    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        try {
            $response = $this->service->updateEmail($request);
            if ($response === false) {
                return $this->jsonResponseError('Email đã được sử dụng', 422);
            }
            return $this->jsonResponseSuccess(new AuthResource($response));
        } catch (Throwable $e) {
            $this->logError('User creation failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }


    /**
     * Hiển thị thông tin người dùng hiện tại
     *
     * API này dùng để lấy thông tin chi tiết về người dùng hiện tại. Nếu người dùng là tài xế, thông tin tài xế sẽ được trả về;
     * nếu không, thông tin người dùng sẽ được trả về.
     *
     * @authenticated
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Thực hiện thành công.",
     *     "data": {
     *         "id": 1,
     *         "user": {
     *             "id": 1,
     *             "fullname": "NGUYỄN MINH HUY",
     *             "slug": "nguyen-minh-huy",
     *             "email": "minhhuy122001@gmail.com",
     *             "phone": "0383476965",
     *             "address": null,
     *             "gender": 1,
     *             "active": true,
     *             "longitude": null,
     *             "latitude": null,
     *             "area_id": null,
     *             "birthday": "2024-08-07",
     *             "avatar": "public/uploads/images/drivers//PSOB6B3wVnItL945mAi3Zww6hUqYaUNkDfW9jm05.jpg",
     *             "notification_preference": 1,
     *             "status": 3,
     *             "created_at": "2024-08-22",
     *             "roles": [
     *                 "driver"
     *             ]
     *         },
     *         "id_card": "123456789012",
     *         "license_plate": null,
     *         "vehicle_company": null,
     *         "bank_name": null,
     *         "bank_account_name": null,
     *         "bank_account_number": null,
     *         "auto_accept": 1,
     *         "current_lat": 10.815832,
     *         "current_lng": 106.664132,
     *         "current_address": "Sân bay quốc tế Tân Sơn Nhất, Đường Trường Sơn, Tân Bình, Hồ Chí Minh, Việt Nam",
     *         "order_accepted": null,
     *         "is_locked": null,
     *         "is_on": null,
     *         "images": {
     *             "id_card_front": "public/uploads/images/drivers//k3Jj4AUngXlR4w9atw8kh0AYlyn7C04ziGaT5CdT.jpg",
     *             "avatar": "public/uploads/images/drivers//PSOB6B3wVnItL945mAi3Zww6hUqYaUNkDfW9jm05.jpg",
     *             "id_card_back": "public/uploads/images/drivers//emM1OLK6GkpOfjf9glK2GnvgkWTp8wL6XDfuvLWB.jpg",
     *             "license_plate_image": null,
     *             "vehicle_registration_front": null,
     *             "vehicle_registration_back": null,
     *             "driver_license_front": "public/uploads/images/drivers//cc32Sqpx0DEM1BZo5wmUrEgGR2FjoCPmAqHy0vjd.jpg",
     *             "driver_license_back": "public/uploads/images/drivers//Qy8zyAoQyEHI2RyKbwv8S8ImBOgiyVbf4VsfAgoB.jpg",
     *             "vehicle_front_image": null,
     *             "vehicle_back_image": null,
     *             "vehicle_side_image": null,
     *             "vehicle_interior_image": null,
     *             "insurance_front_image": null,
     *             "insurance_back_image": null
     *         }
     *     }
     * }
     *
     * @response 404 {
     *     "status": 404,
     *     "message": "Người dùng không tìm thấy."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi xử lý server không xác định."
     * }
     *
     * @return JsonResponse
     * @throws Exception
     */
    public function show(): JsonResponse
    {
        try {
            $user = $this->getCurrentUser();
            return $this->jsonResponseSuccess(new AuthResource($user));
        } catch (Exception $e) {
            $this->logError("Fetch user information failed", $e);
            return $this->jsonResponseError("Fetch user information failed: " . $e->getMessage());
        }
    }

    /**
     * Xác nhận OTP
     *
     * API này dùng để xác nhận mã OTP được gửi qua email để kích hoạt tài khoản hoặc xác nhận thay đổi quan trọng.
     *
     * @authenticated
     *
     * @bodyParam email string required Địa chỉ email của người dùng cần xác thực. Example: user@example.com
     * @bodyParam otp string required Mã OTP mà người dùng nhận được trong email. Example: 123456
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "OTP xác nhận thành công."
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "OTP không hợp lệ hoặc đã hết hạn."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi xử lý server không xác định."
     * }
     *
     * @param VerificationOtp $request
     * @return JsonResponse
     */
    public function verificationOtp(VerificationOtp $request): JsonResponse
    {
        try {
            $this->service->validateOtp($request);
            return $this->jsonResponseSuccessNoData();
        } catch (BadRequestException $e) {
            $this->logError('OTP validation failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 400);
        } catch (Throwable $e) {
            $this->logError('OTP validation failed:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Gửi lại OTP
     *
     * API này dùng để gửi lại mã OTP đến email của người dùng trong trường hợp họ không nhận được hoặc yêu cầu nhận lại.
     *
     * @authenticated
     *
     * @bodyParam email string required Địa chỉ email của người dùng cần gửi lại OTP. Example: user@example.com
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "OTP đã được gửi lại thành công."
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Không thể gửi OTP do thông tin email không hợp lệ hoặc không tồn tại trong hệ thống."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi xử lý server không xác định."
     * }
     *
     * @return JsonResponse
     */
    public function resendOtp(ResendOtpRequest $request): JsonResponse
    {
        try {
            $response = $this->service->resendOtp($request);
            return $this->jsonResponseSuccess($response, 'OTP resent successfully.');
        } catch (BadRequestException $e) {
            $this->logError('Failed to resend OTP:', $e);
            return $this->jsonResponseError($e->getMessage(), 400);
        } catch (Throwable $e) {
            $this->logError('Failed to resend OTP:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Quên mật khẩu
     *
     * API này dùng để cập nhật mật khẩu người dùng
     *
     * @bodyParam email string required Địa chỉ email của người dùng. Example: user@example.com
     * @bodyParam password string required Mật khẩu mới của người dùng. Example: 123456
     * @bodyParam password_confirmation string required Xác nhận mật khẩu mới của người dùng. Example: 123456
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Cập nhật mật khẩu thành công."
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Mật khẩu hiện tại không đúng."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi xử lý server không xác định."
     * }
     *
     * @param ResetPasswordRequest $request
     * @return JsonResponse
     */
    public function forgotPassword(ResetPasswordRequest $request): JsonResponse
    {
        try {
            $response = $this->service->forgotPassword($request);
            return $this->jsonResponseSuccess($response, 'Updated New Password Successfully');
        } catch (Throwable $e) {
            $this->logError('Failed to update Password:', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Cập nhật lại mật khẩu
     *
     * API này cho phép người dùng cập nhật mật khẩu hiện tại của họ sau khi xác thực mật khẩu cũ.
     * Người dùng phải cung cấp mật khẩu hiện tại và mật khẩu mới.
     *
     * @authenticated
     *
     * @bodyParam old_password string required Mật khẩu hiện tại của người dùng. Example: currentPassword123!
     * @bodyParam password string required Mật khẩu mới của người dùng. Example: newPassword123!
     * @bodyParam password_confirmation string required Xác nhận mật khẩu mới của người dùng. Example: newPassword123!
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Mật khẩu đã được cập nhật thành công."
     * }
     *
     * @response 400 {
     *     "status": 400,
     *     "message": "Mật khẩu hiện tại không chính xác hoặc mật khẩu mới không khớp."
     * }
     *
     * @response 500 {
     *     "status": 500,
     *     "message": "Lỗi server không xác định."
     * }
     *
     * @param UpdatePasswordRequest $request
     * @return JsonResponse
     */

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        try {
            $this->service->updatePassword($request);
            return $this->jsonResponseSuccess([]);
        } catch (Exception $e) {
            $this->logError('Update password failed', $e);
            return $this->jsonResponseError('Update password failed.', 500);
        }
    }
}