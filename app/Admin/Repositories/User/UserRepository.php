<?php

namespace App\Admin\Repositories\User;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\BaseAuthCMS;
use App\Admin\Traits\Roles;
use App\Models\User;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    use Roles;
    use BaseAuthCMS;

    protected $select = [];

    public function getModel(): string
    {
        return User::class;
    }
    public function getAll()
    {
        return $this->model->all();
    }
    public function getUserByRole($role)
    {
        return $this->model->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->get();
    }

    public function searchAllLimit($keySearch = '', $meta = [], $select = ['id', 'fullname', 'phone'], $limit = 10, $role = 0)
    {
        $this->instance = $this->model->select($select);
        $this->getQueryBuilderFindByKey($keySearch);

        foreach ($meta as $key => $value) {
            $this->instance = $this->instance->where($key, $value);
        }

        return $this->instance->limit($limit)->get();
    }

    protected function getQueryBuilderFindByKey($key): void
    {
        $this->instance = $this->instance->where(function ($query) use ($key) {
            return $query->where('username', 'LIKE', '%' . $key . '%')
                ->orWhere('phone', 'LIKE', '%' . $key . '%')
                ->orWhere('email', 'LIKE', '%' . $key . '%')
                ->orWhere('fullname', 'LIKE', '%' . $key . '%');
        });
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }

    /**
     * Tìm nhiều bản ghi theo ID
     *
     * @param array $ids
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findMany(array $ids)
    {
        return $this->model->whereIn('id', $ids)->get();
    }

}