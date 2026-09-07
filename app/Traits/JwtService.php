<?php

namespace App\Traits;

use App\Admin\Services\Notification\NotificationFirebaseServiceInterface;
use App\AES\AESHelper;
use App\Api\V1\Http\Resources\Package\AuthPackageResource;
use App\Enums\DeleteStatus;
use App\Enums\Package\PackageType;
use App\Models\User;
use App\Enums\User\UserStatus;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;


trait JwtService
{

    private static string $GUARD_API = 'api';
    private static string $GUARD_API_STORE = 'store-api';

    protected function respondWithToken($token, $refreshToken, $user): JsonResponse
    {
        $ttl = config('jwt.ttl');
        return response()->json([
            'access_token' => $token,
            'refresh_token' => $refreshToken,
            'status' => $user->status,
            'role' => $user->roles->pluck('name'),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'expires_in' => $ttl * 60,
            'package' => new AuthPackageResource($user->userPackages->first())
        ]);
    }


    private function createRefreshToken($user)
    {
        $data = [
            'user_id' => $user->id,
            'random' => rand() . time(),
            'is_refresh_token' => true,
            'exp' => time() + config('jwt.refresh_ttl'),
        ];

        return JWTAuth::getJWTProvider()->encode($data);
    }

    /**
     * Đăng nhập phiên bản V1 (Giữ nguyên 100% logic hiện tại của Production)
     *
     * @throws Exception
     */
    public function loginUser(Request $request): JsonResponse
    {
        $this->login = $request->validated();
        $emailEncrypted = AESHelper::encrypt($this->login['email']);

        $user = $this->userRepository->findByField('email', $emailEncrypted);

        if ($user && Hash::check($this->login['password'], $user->password)) {
            if ($user->status === UserStatus::Lock) {
                return response()->json([
                    'status' => 403,
                    'message' => __('Tài khoản của bạn đã bị khóa.')
                ], 403);
            }

            if ($user->status === UserStatus::Inactive) {
                $user->update(['status' => UserStatus::Lock]);
                return response()->json([
                    'status' => 403,
                    'message' => __('Tài khoản của bạn đã bị khóa.')
                ], 403);
            }

            $token = JWTAuth::fromUser($user);
            $refreshToken = $this->createRefreshToken($user);
            
            \Illuminate\Support\Facades\Log::info('Login trace start', [
                'user_id' => $user->id,
                'input_device_token' => $this->login['device_token'] ?? null,
            ]);

            // Lấy token cũ hiện tại của user từ bảng users trước khi cập nhật
            $oldUserDeviceToken = $user->device_token;

            \Illuminate\Support\Facades\Log::info('Login device token compare', [
                'user_id' => $user->id,
                'old_user_device_token' => $oldUserDeviceToken,
                'new_input_device_token' => $this->login['device_token'] ?? null,
            ]);

            // check package
            $package = $user->userPackages->first();
            if ($package && in_array($package->current_type, [PackageType::Normal, PackageType::Trial])) {
                if (!empty($this->login['device_token']) && !empty($oldUserDeviceToken) && $oldUserDeviceToken !== $this->login['device_token']) {
                    \Illuminate\Support\Facades\Log::info('Triggering notifyLoginAnotherDevice using user device_token', [
                        'user_id' => $user->id,
                        'old_device_token' => $oldUserDeviceToken,
                        'new_device_token' => $this->login['device_token'],
                    ]);
                    try {
                        $notificationService = app(NotificationFirebaseServiceInterface::class);
                        $notificationService->notifyLoginAnotherDevice($user, $oldUserDeviceToken);
                    } catch (Exception $e) {
                        $this->logError('Failed to send login another device notification', $e);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::info('Condition notifyLoginAnotherDevice skipped using user device_token', [
                        'input_empty' => empty($this->login['device_token']),
                        'old_device_token_empty' => empty($oldUserDeviceToken),
                        'same_token' => ($oldUserDeviceToken === ($this->login['device_token'] ?? null)),
                    ]);
                }

                $this->deleteSessionToken($user->id);
            }

            // Always create a new session entry regardless of the package type, to allow session invalidation upon downgrade
            $this->sessionRepository->create([
                'user_id' => $user->id,
                'access_token' => $token,
                'device_token' => $this->login['device_token'] ?? null,
                'status' => DeleteStatus::NotDeleted
            ]);

            // Cập nhật device_token trực tiếp cho user nếu có
            if (!empty($this->login['device_token'])) {
                $this->userRepository->update($user->id, [
                    'device_token' => $this->login['device_token']
                ]);
            }

            return $this->respondWithToken($token, $refreshToken, $user);
        }

        return response()->json([
            'status' => 401,
            'message' => __('Thông tin đăng nhập chưa chính xác.')
        ], 401);
    }

    /**
     * Đăng nhập phiên bản V2 (Quản lý đa thiết bị và kiểm tra giới hạn thiết bị)
     *
     * @throws Exception
     */
    public function loginUserV2(Request $request): JsonResponse
    {
        $this->login = $request->validated();
        $emailEncrypted = AESHelper::encrypt($this->login['email']);

        $user = $this->userRepository->findByField('email', $emailEncrypted);

        if ($user && Hash::check($this->login['password'], $user->password)) {
            if ($user->status === UserStatus::Lock) {
                return response()->json([
                    'status' => 403,
                    'message' => __('Tài khoản của bạn đã bị khóa.')
                ], 403);
            }

            if ($user->status === UserStatus::Inactive) {
                $user->update(['status' => UserStatus::Lock]);
                return response()->json([
                    'status' => 403,
                    'message' => __('Tài khoản của bạn đã bị khóa.')
                ], 403);
            }

            // Xác định định danh thiết bị
            $deviceId = $this->login['device_id'] ?? $this->login['device_token'] ?? ('web_' . md5($request->ip() . ($request->userAgent() ?? '')));
            $deviceName = $this->login['device_name'] ?? null;
            $deviceToken = $this->login['device_token'] ?? null;

            // Kiểm tra thiết bị trong user_devices
            $userDevice = \App\Models\UserDevice::where('user_id', $user->id)
                ->where('device_id', $deviceId)
                ->first();

            if ($userDevice && $userDevice->is_active) {
                // Thiết bị đã liên kết và đang hoạt động -> Cập nhật thông tin hoạt động
                $userDevice->update([
                    'device_name' => $deviceName ?? $userDevice->device_name,
                    'device_token' => $deviceToken ?? $userDevice->device_token,
                    'ip_address' => $request->ip(),
                    'last_active_at' => now(),
                ]);
            } else {
                // Thiết bị mới hoặc thiết bị trước đó đã bị giải phóng -> Kiểm tra hạn mức
                $activeCount = \App\Models\UserDevice::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->count();
                $maxAllowed = $user->getMaxDevicesAllowed();

                if ($activeCount >= $maxAllowed) {
                    \Illuminate\Support\Facades\Log::warning('Login blocked: Device limit exceeded', [
                        'user_id' => $user->id,
                        'device_id' => $deviceId,
                        'active_devices' => $activeCount,
                        'max_allowed' => $maxAllowed,
                    ]);

                    return response()->json([
                        'status' => 403,
                        'code' => 'DEVICE_LIMIT_EXCEEDED',
                        'message' => __("Tài khoản của bạn đã đạt giới hạn tối đa :max thiết bị cho gói hiện tại. Vui lòng nâng cấp lên gói VIP để sử dụng trên nhiều thiết bị hoặc liên hệ Quản trị viên để đổi thiết bị.", ['max' => $maxAllowed]),
                        'data' => [
                            'max_devices' => $maxAllowed,
                            'current_devices' => $activeCount,
                            'can_upgrade' => true
                        ]
                    ], 403);
                }

                // Chưa vượt hạn mức -> Cho phép liên kết thiết bị mới
                if ($userDevice) {
                    $userDevice->update([
                        'is_active' => true,
                        'device_name' => $deviceName ?? $userDevice->device_name,
                        'device_token' => $deviceToken ?? $userDevice->device_token,
                        'ip_address' => $request->ip(),
                        'last_active_at' => now(),
                    ]);
                } else {
                    \App\Models\UserDevice::create([
                        'user_id' => $user->id,
                        'device_id' => $deviceId,
                        'device_name' => $deviceName ?? 'Thiết bị di động',
                        'device_token' => $deviceToken,
                        'ip_address' => $request->ip(),
                        'is_active' => true,
                        'last_active_at' => now(),
                    ]);
                }
            }

            $token = JWTAuth::fromUser($user);
            $refreshToken = $this->createRefreshToken($user);

            // Lưu session đăng nhập với định danh thiết bị
            $this->sessionRepository->create([
                'user_id' => $user->id,
                'access_token' => $token,
                'device_token' => $deviceId,
                'status' => DeleteStatus::NotDeleted
            ]);

            // Cập nhật device_token trực tiếp cho user nếu có
            if (!empty($deviceToken)) {
                $this->userRepository->update($user->id, [
                    'device_token' => $deviceToken
                ]);
            }

            return $this->respondWithToken($token, $refreshToken, $user);
        }

        return response()->json([
            'status' => 401,
            'message' => __('Thông tin đăng nhập chưa chính xác.')
        ], 401);
    }

    public function invalidateToken(string $token): bool
    {
        try {
            JWTAuth::setToken($token)->invalidate();
            return true;
        } catch (JWTException $e) {
            report($e);
            return false;
        }
    }

    /**
     * @throws Exception
     */
    public function deleteSessionToken($userId): void
    {
        $sessions = $this->sessionRepository->getBy(
            [
                'user_id' => $userId,
                'status' => DeleteStatus::NotDeleted
            ]
        )->first();
        if($sessions){
            $accessToken = $sessions->access_token;
            if ($this->invalidateToken($accessToken)) {
                $sessions->update(['status' => DeleteStatus::Deleted]);
            }
        }
    }


    /**
     * Create refresh_token.
     */
    private function createRefreshTokenById($user)
    {
        $data = [
            'user_id' => $user->id,
            'random' => rand() . time(),
            'exp' => time() + config('jwt.refresh_ttl')
        ];
        return JWTAuth::getJWTProvider()->encode($data);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return JsonResponse
     */
    public function logoutUser(): JsonResponse
    {
        auth(self::$GUARD_API)->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    public function refreshToken(Request $request): JsonResponse
    {
        $data = $request->validated();
        $refreshToken = $data['refresh_token'];
        try {
            $decoded = JWTAuth::setToken($refreshToken)->getPayload();
            if (!$decoded->get('is_refresh_token', false)) {
                return response()->json(['message' => 'Invalid token type.'], 401);
            }

            if (time() - $decoded->get('token_generated') < config('jwt.refresh_ttl')) {
                return response()->json(['message' => 'Refresh token has already been used.'], 401);
            }
            $user = User::find($decoded->get('user_id'));

            $newToken = JWTAuth::fromUser($user);
            $newRefreshToken = $this->createRefreshToken($user);

            return $this->respondWithToken($newToken, $newRefreshToken, null);

        } catch (Exception $e) {
            $this->logError("Error for refresh token", $e);
            return response()->json(['message' => 'Invalid token.', 'error' => $e->getMessage()], 401);
        }
    }


}
