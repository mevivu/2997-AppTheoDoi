<?php

namespace App\Api\V1\Services\User;

use App\Admin\Repositories\Otp\OtpRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\Roles;
use App\Api\V1\Exception\BadRequestException;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Support\AuthServiceApi;
use App\Api\V1\Support\OTPEmail;
use App\Api\V1\Support\UseLog;
use App\Enums\User\Gender;
use App\Enums\User\UserStatus;
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


    public function __construct(
        UserRepositoryInterface $repository,
        OtpRepositoryInterface $otpRepository,
        FileService $fileService,
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
        $this->otpRepository = $otpRepository;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();

            $data['username'] = $data['phone'];
            $data['password'] = bcrypt($data['password']);
            $data['code'] = $this->createCodeUser();
            $data['active'] = false;
            $user = $this->repository->create($data);

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
            $avatar = $data['avatar'] ?? null;
            if ($avatar) {
                $data['avatar'] = $this->fileService->uploadAvatar('images/users', $avatar, $user->avatar);
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
                throw new \Exception("Email already exists.");
            }

            $response = $this->repository->update($user->id, $data);

            DB::commit();
            ;
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

        if ($this->generateAndSendOtp($email, 1)) {
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
            $user = $this->repository->findByField('email', $data['email']);
            $data['password'] = bcrypt($data['password']);
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
}