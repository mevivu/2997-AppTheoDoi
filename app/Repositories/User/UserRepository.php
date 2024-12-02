<?php

namespace App\Repositories\User;
use App\Admin\Repositories\EloquentRepository;
use App\Repositories\User\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return User::class;
    }



}
