<?php

namespace App\Admin\Http\Controllers\Expert;

use App\Admin\DataTables\Expert\ExpertDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Expert\ExpertRequest;
use App\Admin\Repositories\Expert\ExpertRepositoryInterface;
use App\Admin\Services\Expert\ExpertServiceInterface;
use App\Enums\DefaultStatus;
use App\Traits\ResponseController;
use App\Traits\RouteAdminSystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpertController extends Controller
{
    use ResponseController;

    public function __construct(
        ExpertRepositoryInterface $repository,
        ExpertServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.experts.index',
            'create' => 'admin.experts.create',
            'edit' => 'admin.experts.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::EXPERT_INDEX,
            'create' => RouteAdminSystem::EXPERT_CREATE,
            'edit' => RouteAdminSystem::EXPERT_EDIT,
            'delete' => RouteAdminSystem::EXPERT_DELETE,
        ];
    }

    public function index(ExpertDataTable $dataTable)
    {
        $actionMultiple = [
            1 => 'Hoạt động',
            2 => 'Tạm ẩn',
        ];

        return $dataTable->render($this->view['index'], [
            'actionMultiple' => $actionMultiple,
            'breadcrumbs' => $this->crums->add(__('Hồ sơ Chuyên gia')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => [
                DefaultStatus::Published->value => 'Hoạt động',
                DefaultStatus::Draft->value => 'Tạm ẩn',
            ],
            'breadcrumbs' => $this->crums->add(__('Hồ sơ Chuyên gia'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(ExpertRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($req) {
            return $this->service->store($req);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);

        return view($this->view['edit'], [
            'instance' => $instance,
            'status' => [
                DefaultStatus::Published->value => 'Hoạt động',
                DefaultStatus::Draft->value => 'Tạm ẩn',
            ],
            'breadcrumbs' => $this->crums->add(__('Hồ sơ Chuyên gia'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(ExpertRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($req) {
            return $this->service->update($req);
        });
    }

    public function delete($id): RedirectResponse
    {
        $this->service->delete($id);
        return to_route($this->route['index'])->with('success', __('notifySuccess'));
    }

    public function actionMultipleRecode(Request $request): RedirectResponse
    {
        $success = $this->service->actionMultipleRecode($request);
        if ($success) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
