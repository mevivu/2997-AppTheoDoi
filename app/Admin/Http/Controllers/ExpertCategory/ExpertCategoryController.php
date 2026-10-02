<?php

namespace App\Admin\Http\Controllers\ExpertCategory;

use App\Admin\DataTables\ExpertCategory\ExpertCategoryDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\ExpertCategory\ExpertCategoryRequest;
use App\Admin\Repositories\ExpertCategory\ExpertCategoryRepositoryInterface;
use App\Admin\Services\ExpertCategory\ExpertCategoryServiceInterface;
use App\Enums\DefaultStatus;
use App\Traits\ResponseController;
use App\Traits\RouteAdminSystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpertCategoryController extends Controller
{
    use ResponseController;

    public function __construct(
        ExpertCategoryRepositoryInterface $repository,
        ExpertCategoryServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.expert_categories.index',
            'create' => 'admin.expert_categories.create',
            'edit' => 'admin.expert_categories.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::EXPERT_CATEGORY_INDEX,
            'create' => RouteAdminSystem::EXPERT_CATEGORY_CREATE,
            'edit' => RouteAdminSystem::EXPERT_CATEGORY_EDIT,
            'delete' => RouteAdminSystem::EXPERT_CATEGORY_DELETE,
        ];
    }

    public function index(ExpertCategoryDataTable $dataTable)
    {
        $actionMultiple = [
            1 => 'Hoạt động',
            2 => 'Tạm ẩn',
        ];

        return $dataTable->render($this->view['index'], [
            'actionMultiple' => $actionMultiple,
            'breadcrumbs' => $this->crums->add(__('Danh mục Chuyên đề')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => [
                DefaultStatus::Published->value => 'Hoạt động',
                DefaultStatus::Draft->value => 'Tạm ẩn',
            ],
            'breadcrumbs' => $this->crums->add(__('Danh mục Chuyên đề'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(ExpertCategoryRequest $request): RedirectResponse
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
            'breadcrumbs' => $this->crums->add(__('Danh mục Chuyên đề'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(ExpertCategoryRequest $request): RedirectResponse
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
