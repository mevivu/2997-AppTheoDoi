<?php

namespace App\Api\V1\Http\Controllers\User;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Http\Requests\User\AcceptAffiliateTermsRequest;
use App\Api\V1\Http\Requests\User\ApplyReferralCodeRequest;
use App\Api\V1\Http\Requests\User\UserRegisterRequest;
use App\Api\V1\Http\Requests\User\UserUpdateRequest;
use App\Api\V1\Http\Resources\Auth\AuthResource;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\User\UserServiceInterface;
use App\Api\V1\Support\Response;
use App\Api\V1\Support\UseLog;
use App\Traits\MessageSystem;
use Illuminate\Http\JsonResponse;
use Throwable;
use App\Api\V1\Support\AuthServiceApi;


use App\Models\User;
use Illuminate\Http\Request;

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
        $this->middleware('auth:api', ['except' => ['register', 'checkReferralCode']]);
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
            $this->logError(MessageSystem::SERVER_ERROR, $e);
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
     * @bodyParam father_name string optional Tên của bố. Example: John Doe
     * @bodyParam father_height integer optional Chiều cao của bố (cm). Example: 180
     * @bodyParam father_birthday string optional Ngày sinh của bố, nếu cập nhật. Example: 1960-05-15
     * @bodyParam mother_name string optional Tên của mẹ. Example: Jane Smith
     * @bodyParam mother_height integer optional Chiều cao của mẹ (cm). Example: 165
     * @bodyParam mother_birthday string optional Ngày sinh của mẹ, nếu cập nhật. Example: 1962-08-30
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
     *         "avatar": "http://example.com/avatar.jpg",
     *         "father_name": "John Doe",
     *         "father_height": 180,
     *         "father_birthday": "1960-05-15",
     *         "mother_name": "Jane Smith",
     *         "mother_height": 165,
     *         "mother_birthday": "1962-08-30"
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
            $this->logError(MessageSystem::SERVER_ERROR, $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Kiểm tra tính hợp lệ của mã giới thiệu
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkReferralCode(Request $request): JsonResponse
    {
        try {
            $code = trim((string) $request->input('code', ''));
            if (empty($code)) {
                return $this->jsonResponseError('Vui lòng nhập mã giới thiệu.', 400);
            }

            $referrer = User::where('affiliate_code', $code)->first();
            if (!$referrer) {
                return $this->jsonResponseError('Mã giới thiệu không tồn tại trong hệ thống.', 404);
            }

            return $this->jsonResponseSuccess([
                'valid' => true,
                'affiliate_code' => $referrer->affiliate_code,
                'referrer_name' => $referrer->fullname,
            ], 'Mã giới thiệu hợp lệ.');
        } catch (Throwable $e) {
            $this->logError('Check referral code failed', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Áp dụng mã giới thiệu sau khi đăng nhập / đăng ký Social Login
     *
     * @authenticated
     * @param ApplyReferralCodeRequest $request
     * @return JsonResponse
     */
    public function applyReferralCode(ApplyReferralCodeRequest $request): JsonResponse
    {
        try {
            $data = $this->service->applyReferralCode($request);
            return $this->jsonResponseSuccess($data, 'Áp dụng mã giới thiệu thành công!');
        } catch (BadRequestException $e) {
            return $this->jsonResponseError($e->getMessage(), 400);
        } catch (Throwable $e) {
            $this->logError('Lỗi khi áp dụng mã giới thiệu', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }

    /**
     * Chấp nhận Điều kiện & Điều khoản Affiliate
     *
     * API này cho phép người dùng xác nhận đồng ý Điều kiện & Điều khoản
     * trước khi có thể sử dụng mã giới thiệu và tham gia chương trình Affiliate.
     *
     * @authenticated
     * @bodyParam accepted boolean required Xác nhận đồng ý điều khoản (bắt buộc = true). Example: true
     *
     * @response 200 {
     *     "status": 200,
     *     "message": "Đã chấp nhận Điều kiện & Điều khoản Affiliate.",
     *     "data": {
     *         "affiliate_terms_accepted": true,
     *         "affiliate_terms_accepted_at": "2026-09-12T10:00:00.000000Z"
     *     }
     * }
     *
     * @param AcceptAffiliateTermsRequest $request
     * @return JsonResponse
     */
    public function acceptAffiliateTerms(AcceptAffiliateTermsRequest $request): JsonResponse
    {
        try {
            $user = auth('api')->user();
            if (!$user) {
                return $this->jsonResponseError('Phiên đăng nhập không hợp lệ.', 401);
            }

            // Nếu đã đồng ý trước đó → trả success luôn
            if ($user->hasAcceptedAffiliateTerms()) {
                return $this->jsonResponseSuccess([
                    'affiliate_terms_accepted' => true,
                    'affiliate_terms_accepted_at' => $user->affiliate_terms_accepted_at->toISOString(),
                ], 'Bạn đã đồng ý Điều kiện & Điều khoản trước đó.');
            }

            // Cập nhật thời điểm đồng ý
            $user->affiliate_terms_accepted_at = now();
            $user->save();

            return $this->jsonResponseSuccess([
                'affiliate_terms_accepted' => true,
                'affiliate_terms_accepted_at' => $user->affiliate_terms_accepted_at->toISOString(),
            ], 'Đã chấp nhận Điều kiện & Điều khoản Affiliate.');
        } catch (Throwable $e) {
            $this->logError('Lỗi khi chấp nhận điều khoản Affiliate', $e);
            return $this->jsonResponseError($e->getMessage(), 500);
        }
    }
}
