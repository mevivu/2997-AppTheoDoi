<?php

namespace App\Api\V1\Repositories\User;


use App\Admin\Repositories\EloquentRepositoryInterface;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{

    public function create(array $data);

    public function getConfiguration($userId);

    public function update($id, array $data);

    public function updateObject($user, $data);

    public function delete($id);

    public function getQueryBuilder();

    public function emailExists(string $email, int $userId): bool;
}
