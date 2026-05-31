<?php

namespace App\Traits;

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
            // check package
            $package = $user->userPackages->first();
            if (in_array($package->current_type, [PackageType::Normal, PackageType::Trial])) {
                $this->deleteSessionToken($user->id);
                $this->sessionRepository->create([
                    'user_id' => $user->id,
                    'access_token' => $token,
                    'device_token' => $this->login['device_token'],
                    'status' => DeleteStatus::NotDeleted
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
