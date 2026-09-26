<?php

namespace App\Admin\Http\Controllers\AgeGroup;

use App\Admin\DataTables\AgeGroup\AgeGroupDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\AgeGroup\AgeGroupRequest;
use App\Admin\Repositories\AgeGroup\AgeGroupRepositoryInterface;
use App\Admin\Services\AgeGroup\AgeGroupServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AgeGroupController extends Controller
{
    use ResponseController;

    public function __construct(
        AgeGroupRepositoryInterface $repository,
        AgeGroupServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.age_groups.index',
            'create' => 'admin.age_groups.create',
            'edit' => 'admin.age_groups.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.age_group.index',
            'create' => 'admin.age_group.create',
            'edit' => 'admin.age_group.edit',
            'delete' => 'admin.age_group.delete',
        ];
    }

    public function index(AgeGroupDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'status' => ActiveStatus::asSelectArray(),
            'actionMultiple' => $this->getActionMultiple(),
            'breadcrumbs' => $this->crums->add(__('Nhóm tuổi')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Nhóm tuổi'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(AgeGroupRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);

        return view($this->view['edit'], [
            'instance' => $instance,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Nhóm tuổi'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(AgeGroupRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function () use ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        });
    }

    protected function getActionMultiple(): array
    {
        return ActiveStatus::asSelectArray();
    }

    public function actionMultipleRecords(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecords($request);

        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }

        return back()->with('error', __('notifyFail'));
    }
}
