<?php

namespace App\Api\V1\Services\User;

use App\Admin\Repositories\Otp\OtpRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\OTPEmail;
use App\Api\V1\Support\UseLog;
use App\Enums\User\Gender;
use App\Enums\User\UserServiceType;
use App\Enums\User\UserStatus;
use App\Models\User;
use App\Services\Affiliate\AffiliateServiceInterface;
use App\Api\V1\Services\Notification\NotificationServiceInterface;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;


class UserService implements UserServiceInterface
{
    use Setup, Roles, AuthServiceApi, UseLog, OTPEmail;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected UserRepositoryInterface $repository;
    protected OtpRepositoryInterface $otpRepository;
    protected FileService $fileService;
    protected AffiliateServiceInterface $affiliateService;
    protected NotificationServiceInterface $notificationService;

    public function __construct(
        UserRepositoryInterface $repository,
        OtpRepositoryInterface  $otpRepository,
        FileService             $fileService,
        AffiliateServiceInterface $affiliateService,
        NotificationServiceInterface $notificationService
    )
    {
        $this->repository = $repository;
        $this->fileService = $fileService;
        $this->otpRepository = $otpRepository;
        $this->affiliateService = $affiliateService;
        $this->notificationService = $notificationService;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $data['password'] = bcrypt($data['password']);
            $data['code'] = $this->createCodeUser();
            $data['email'] = AESHelper::encrypt($data['email']);
            $data['username'] = $data['email'];
            if (!empty($data['phone'])) {
                $data['phone'] = AESHelper::encrypt($data['phone']);
            } else {
                $data['phone'] = null;
            }
            $data['status'] = UserStatus::Active;
            $data['service_type'] = UserServiceType::Email;

            // Xử lý kiểm tra và liên kết mã giới thiệu khi đăng ký
            if (!empty($data['referral_code'])) {
                $referrer = User::where('affiliate_code', trim($data['referral_code']))->first();
                if ($referrer) {
                    $data['referrer_id'] = $referrer->id;
                }
            }

            $user = $this->repository->create($data);

            // Xử lý cộng tiền thưởng hoa hồng Affiliate:
            // Nghiệp vụ này chỉ thực hiện trong phương thức đăng ký khi tài khoản có nhập mã giới thiệu hợp lệ
            if ($user && !empty($user->referrer_id)) {
                $this->affiliateService->processRegistrationReward($user);
            }

            // Gửi thông báo chào mừng thành viên mới (In-app notification)
            if ($user) {
                $this->notificationService->sendWelcomeNotification($user);
            }

            DB::commit();
            return $user;
        } catch (Throwable $e) {
            DB::rollback();
            $this->logError('Failed to process register user API', $e);
            return false;
        }
    }

    public function update(Request $request): bool|object
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $user = $this->getCurrentUser();
            $fullname = $data['fullname'] ?? null;
            $fatherName = $data['father_name'] ?? null;
            $fatherHeight = $data['father_height'] ?? null;
            $motherName = $data['mother_name'] ?? null;
            $motherHeight = $data['mother_height'] ?? null;
            $motherBirthday = $data['mother_birthday'] ?? null;
            $avatar = $data['avatar'] ?? null;

            if ($fullname) {
                $data['fullname'] = $fullname;
            }

            if ($fatherName) {
                $data['father_name'] = $fatherName;
            }

            if ($fatherHeight) {
                $data['father_height'] = $fatherHeight;
            }

            if ($motherName) {
                $data['mother_name'] = $motherName;
            }

            if ($motherHeight) {
                $data['mother_height'] = $motherHeight;
            }

            if ($motherBirthday) {
                $data['mother_birthday'] = $motherBirthday;
            }

            if ($avatar) {
                $data['avatar'] = $this->fileService->uploadAvatar('images/users', $avatar, $user->avatar);
            }

            if (!empty($data['email'])) {
                $data['email'] = AESHelper::encrypt($data['email']);
                $data['username'] = $data['email'];
            }

            if (!empty($data['phone'])) {
                $data['phone'] = AESHelper::encrypt($data['phone']);
            }

            $response = $this->repository->update($user->id, $data);
            DB::commit();
            return $response;
        } catch (Exception $e) {
            DB::rollback();
            $this->logError('Failed to process update user API', $e);
            return false;
        }
    }

    public function updateEmail(Request $request): bool|object
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            Log::info($data);
            $user = $this->getCurrentUser();

            if ($data['email'] === $user->email) {
                return false;
            }
            if ($this->repository->emailExists($data['email'], $user->id)) {
                throw new Exception("Email already exists.");
            }

            $response = $this->repository->update($user->id, $data);

            DB::commit();;
            return $response;
        } catch (Exception $e) {
            DB::rollback();
            $this->logError('Failed to process update user API', $e);
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }

    /**
     * @throws Exception
     */
    public function validateOtp(Request $request): bool
    {
        $data = $request->validated();
        $email = $data['email'];
        $otpCode = $data['otp'];

        $this->validateOtpCode($email, $otpCode);

        $email = AESHelper::encrypt($email);
        $user = $this->repository->findByField('email', $email);
        $userData = [
            'status' => UserStatus::Active,
            'email_verified_at' => now()
        ];
        $this->repository->update($user->id, $userData);
        $this->deleteOtpWithEmail($email);

        return true;
    }

    /**
     * @throws Exception
     */
    public function resendOtp(Request $request): bool
    {
        $data = $request->validated();
        $email = $data['email'];

        $this->deleteOtpWithEmail($email);

        if ($this->generateAndSendOtp($email, 15)) {
            return true;
        } else {
            throw new BadRequestException('Failed to generate OTP.');
        }
    }

    public function forgotPassword($request): bool|object
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $email = AESHelper::encrypt($data['email']);
            $user = $this->repository->findByField('email', value: $email);

            $data['password'] = bcrypt($data['password']);
            $data['email'] = $email;

            $response = $this->repository->update($user->id, $data);
            DB::commit();
            return $response;
        } catch (Throwable $e) {
            DB::rollback();
            $this->logError('Failed to reset password', $e);
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function updatePassword(Request $request)
    {
        $data = $request->validated();
        $user = $this->getCurrentUser();

        $data['password'] = bcrypt($data['password']);

        return $this->repository->update($user->id, $data);
    }

    /**
     * Áp dụng mã người giới thiệu sau khi đã tạo tài khoản (dành cho Social Login hoặc bổ sung sau)
     *
     * @param Request $request
     * @return array
     * @throws BadRequestException|Throwable
     */
    public function applyReferralCode(Request $request): array
    {
        $data = $request->validated();
        $user = $this->getCurrentUser();
        if (!$user) {
            throw new BadRequestException('Không tìm thấy thông tin tài khoản.');
        }

        $referralCode = trim($data['referral_code']);
        $referrer = User::where('affiliate_code', $referralCode)->first();
        if (!$referrer) {
            throw new BadRequestException('Mã giới thiệu không tồn tại hoặc không hợp lệ.');
        }

        DB::beginTransaction();
        try {
            // Cập nhật người giới thiệu cho tài khoản
            $user->referrer_id = $referrer->id;
            $user->save();

            // Kích hoạt tính hoa hồng cho người giới thiệu và thưởng chào mừng nếu có
            $this->affiliateService->processRegistrationReward($user);

            DB::commit();

            return [
                'referrer' => [
                    'id' => $referrer->id,
                    'fullname' => $referrer->fullname,
                    'affiliate_code' => $referrer->affiliate_code,
                ]
            ];
        } catch (Throwable $e) {
            DB::rollback();
            $this->logError('Lỗi khi áp dụng mã giới thiệu: ' . $e->getMessage(), $e);
            throw new BadRequestException('Đã xảy ra lỗi khi áp dụng mã giới thiệu. Vui lòng thử lại sau.');
        }
    }
}
