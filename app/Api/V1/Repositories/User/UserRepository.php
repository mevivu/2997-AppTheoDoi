<?php

namespace App\Api\V1\Repositories\User;
use App\Admin\Repositories\EloquentRepository;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Models\User;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return User::class;
    }
    public function findByKey($key, $value)
    {
        $this->instance = $this->model->where($key, $value)->first();
        return $this->instance;
    }
    public function getConfiguration($userId)
    {
        return $this->model->where('id', $userId)->first();
    }

    public function updateObject($user, $data)
    {
        $user->update($data);
        return $user;
    }

    public function emailExists(string $email, int $userId): bool
    {
        return User::where('email', $email)
            ->where('id', '!=', $userId) 
            ->exists();
    }

}
