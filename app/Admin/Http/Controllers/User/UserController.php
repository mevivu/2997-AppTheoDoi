<?php

namespace App\Admin\Http\Controllers\User;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\User\UserRequest;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Services\User\UserServiceInterface;
use App\Admin\DataTables\User\UserDataTable;
use App\Traits\ResponseController;
use Exception;
use App\Enums\User\{Gender, UserStatus};
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ResponseController;

    public function __construct(
        UserRepositoryInterface $repository,
        UserServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.users.index',
            'create' => 'admin.users.create',
            'edit' => 'admin.users.edit',
            'history' => 'admin.users.order.index',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.user.index',
            'create' => 'admin.user.create',
            'edit' => 'admin.user.edit',
            'delete' => 'admin.user.delete',
            'history' => 'admin.users.history',
        ];
    }

    public function index(UserDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'gender' => Gender::asSelectArray(),
                'status' => UserStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
            ]

        );
    }


    public function create(): Factory|View|Application
    {
        $roles = $this->repository->getAllRolesByGuardName('web');

        return view($this->view['create'], [
            'gender' => Gender::asSelectArray(),
            'roles' => $roles,
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {

        $instance = $this->repository->findOrFail($id);
        $roles = $this->repository->getAllRolesByGuardName('web');
        return view(
            $this->view['edit'],
            [
                'user' => $instance,
                'gender' => Gender::asSelectArray(),
                'status' => UserStatus::asSelectArray(),
                'roles' => $roles,
            ],
        );

    }

    public function update(UserRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    /**
     * @throws Exception
     */
    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            $response = $this->repository->findOrFail($id);
            return $response->update(['status' => UserStatus::Inactive->value]);
        });
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => UserStatus::Active->description(),
            'inactive' => UserStatus::Inactive->description(),
            'lock' => UserStatus::Lock->description()
        ];
    }

    public function actionMultipleRecode(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecode($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
