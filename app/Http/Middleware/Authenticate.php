<?php

namespace App\Http\Middleware;

use App\Enums\DeleteStatus;
use App\Models\UserDevice;
use App\Models\UserSession;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Determine if the user is logged in to any of the given guards.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $guards
     * @return void
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    protected function authenticate($request, array $guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if ($this->auth->guard($guard)->check()) {
                if ($guard === 'api') {
                    $token = $request->bearerToken();
                    $user = $this->auth->guard($guard)->user();
                    if ($token && $user) {
                        $session = UserSession::where('user_id', $user->id)
                            ->where('access_token', $token)
                            ->first();

                        if ($session) {
                            if ($session->status === DeleteStatus::Deleted || (is_string($session->status) && $session->status === 'deleted')) {
                                throw new AuthenticationException(__('Phiên đăng nhập đã hết hạn hoặc thiết bị đã bị giải phóng.'));
                            }

                            if (!empty($session->device_token)) {
                                $device = UserDevice::where('user_id', $user->id)
                                    ->where(function ($q) use ($session) {
                                        $q->where('device_id', $session->device_token)
                                          ->orWhere('device_token', $session->device_token);
                                    })
                                    ->first();

                                if ($device && !$device->is_active) {
                                    $session->update(['status' => DeleteStatus::Deleted]);
                                    throw new AuthenticationException(__('Thiết bị này đã bị giải phóng.'));
                                }
                            }
                        }
                    }
                }

                return $this->auth->shouldUse($guard);
            }
        }

        $this->unauthenticated($request, $guards);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            if($request->routeIs('api.*')){
                return response()->json([
                    'status' => 401,
                    'message' => __('Xác thực không thành công.')
                ], 401);
            }
            return route('login');
        }
    }
}
