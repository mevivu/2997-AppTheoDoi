<?php

namespace App\Admin\Repositories\UserSession;

use App\Admin\Repositories\EloquentRepository;
use App\Models\UserSession;

class UserSessionRepository extends EloquentRepository implements UserSessionRepositoryInterface
{
    public function getModel(): string
    {
        return UserSession::class;
    }
}
