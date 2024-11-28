<?php

namespace App\Admin\Services\Permission;

use App\Admin\Services\Permission\PermissionServiceInterface;
use App\Admin\Repositories\Permission\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;


class PermissionService implements PermissionServiceInterface
{
    /**
     * Current Object instance
     *
     * @var array
     */
    protected $data;

    protected $repository;

    public function __construct(PermissionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        if (empty($request['module_id'])) {
            $request['module_id'] = null;
        }
        $this->data = $request->validated();
        return Permission::create($this->data);
    }

    public function update(Request $request)
    {
        if (empty($request['module_id'])) {
            $request['module_id'] = null;
        }
        $this->data = $request->validated();

        return $this->repository->update($this->data['id'], $this->data);

    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function actionMultipleRecord(Request $request)
    {
        $data = $request->all();

        if ($data['action'] == 'delete') {
            foreach ($data['id'] as $id) {
                $this->delete($id);
            }
            return true;
        }

        return false;
    }
}