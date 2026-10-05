<?php

namespace App\Admin\Http\Controllers\Admin;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Admin\AdminRequest;
use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Services\Admin\AdminServiceInterface;
use App\Admin\DataTables\Admin\AdminDataTable;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    use ResponseController;

    public function __construct(
        AdminRepositoryInterface $repository,
        AdminServiceInterface $service
    ){

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.admins.index',
            'create' => 'admin.admins.create',
            'edit' => 'admin.admins.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.admin.index',
            'create' => 'admin.admin.create',
            'edit' => 'admin.admin.edit',
            'delete' => 'admin.admin.delete'
        ];
    }
    public function index(AdminDataTable $dataTable){
        return $dataTable->render($this->view['index']);
    }

    public function create(): Factory|View|Application
    {
		$roles = $this->repository->getAllRolesByGuardName('admin');
        return view($this->view['create'], [
			'roles' => $roles
		]);
    }


    public function store(AdminRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    /**
     * @throws \Exception
     */
    public function edit($id): Factory|View|Application
    {

        $instance = $this->repository->findOrFail($id);
		$roles = $this->repository->getAllRolesByGuardName('admin');
        return view(
            $this->view['edit'],
			[
                'admin' => $instance,
				'roles' => $roles,
            ],
        );

    }

    public function update(AdminRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse(function () use ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
