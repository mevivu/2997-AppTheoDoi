<?php

namespace App\Admin\Services\User;

use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Api\V1\Support\UseLog;
use App\Enums\User\UserStatus;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    use Setup, Roles, UseLog;

    /**
     * Current Object instance
     *
     * @var array
     */
    protected array $data;

    protected UserRepositoryInterface $repository;



    public function __construct(
        UserRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    public function store(Request $request): object|false
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['username'] = $data['email'];
            $data['code'] = $this->createCodeUser();
            $data['longitude'] = $request['lng'];
            $data['latitude'] = $request['lat'];
            $data['password'] = bcrypt($data['password']);
            $user = $this->repository->create($data);

            $data['user_id'] = $user->id;

            $roles = $data['roles'];
            //create role
            $this->repository->assignRoles($user, [$roles]);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            $this->logError('Failed to process create user', $e);
            return false;
        }
    }

    public function update(Request $request): object|bool
    {

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['longitude'] = $request['lng'];
            $data['latitude'] = $request['lat'];
            if (isset($data['password']) && $data['password']) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }

            $user = $this->repository->update($data['id'], $data);

            $roles = $data['roles'];
            //create role
            $this->repository->assignRoles($user, [$roles]);


            DB::commit();
            return $user;

        } catch (Exception $e) {
            DB::rollback();
            $this->logError('Failed to process update user', $e);
            return false;
        }
    }

    public function delete($id): object
    {
        return $this->repository->delete($id);

    }

    public function actionMultipleRecode(Request $request): bool
    {
        $this->data = $request->all();
        switch ($this->data['action']) {
            case 'active':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', UserStatus::Active);
                }
                return true;
            case 'inactive':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', UserStatus::Inactive);
                }
                return true;
            case 'lock':
                foreach ($this->data['id'] as $value) {
                    $this->repository->updateAttribute($value, 'status', UserStatus::Lock);
                }
                return true;

            default:
                return false;
        }
    }

}
