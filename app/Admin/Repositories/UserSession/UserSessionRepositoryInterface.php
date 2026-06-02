<?php

namespace App\Admin\Repositories\UserSession;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface UserSessionRepositoryInterface extends EloquentRepositoryInterface
{
    public function deleteAllSessionTokens(int $userId): void;
}
