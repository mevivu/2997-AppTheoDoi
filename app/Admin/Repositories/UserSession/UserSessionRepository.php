<?php

namespace App\Admin\Repositories\UserSession;

use App\Admin\Repositories\EloquentRepository;
use App\Models\UserSession;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Enums\DeleteStatus;

class UserSessionRepository extends EloquentRepository implements UserSessionRepositoryInterface
{
    public function getModel(): string
    {
        return UserSession::class;
    }

    public function deleteAllSessionTokens(int $userId): void
    {
        $sessions = $this->model->where('user_id', $userId)
            ->where('status', DeleteStatus::NotDeleted)
            ->get();

        foreach ($sessions as $session) {
            try {
                JWTAuth::setToken($session->access_token)->invalidate();
            } catch (JWTException $e) {
                // Ignore if token is already invalid/expired/blacklisted
                report($e);
            }
            $session->update(['status' => DeleteStatus::Deleted]);
        }
    }
}
