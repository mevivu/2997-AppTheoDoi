<?php

namespace App\Admin\Http\Controllers\ExpertPost;

use App\Admin\DataTables\ExpertPost\ExpertPostDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\ExpertPost\ExpertPostRequest;
use App\Admin\Repositories\ExpertPost\ExpertPostRepositoryInterface;
use App\Admin\Services\ExpertPost\ExpertPostServiceInterface;
use App\Enums\DefaultStatus;
use App\Models\AgeGroup;
use App\Models\Expert;
use App\Models\ExpertCategory;
use App\Traits\ResponseController;
use App\Traits\RouteAdminSystem;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExpertPostController extends Controller
{
    use ResponseController;

    public function __construct(
        ExpertPostRepositoryInterface $repository,
        ExpertPostServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.expert_posts.index',
            'create' => 'admin.expert_posts.create',
            'edit' => 'admin.expert_posts.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => RouteAdminSystem::EXPERT_POST_INDEX,
            'create' => RouteAdminSystem::EXPERT_POST_CREATE,
            'edit' => RouteAdminSystem::EXPERT_POST_EDIT,
            'delete' => RouteAdminSystem::EXPERT_POST_DELETE,
        ];
    }

    public function index(ExpertPostDataTable $dataTable)
    {
        $actionMultiple = [
            1 => 'Xuất bản',
            2 => 'Bản nháp',
        ];

        return $dataTable->render($this->view['index'], [
            'actionMultiple' => $actionMultiple,
            'breadcrumbs' => $this->crums->add(__('Bài viết & Lời khuyên Chuyên gia')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'experts' => Expert::query()->published()->orderBy('name')->pluck('name', 'id')->all(),
            'categories' => ExpertCategory::query()->published()->orderBy('name')->pluck('name', 'id')->all(),
            'age_groups' => AgeGroup::query()->active()->orderBy('sort_order')->pluck('name', 'id')->all(),
            'status' => [
                DefaultStatus::Published->value => 'Xuất bản',
                DefaultStatus::Draft->value => 'Bản nháp',
            ],
            'breadcrumbs' => $this->crums->add(__('Bài viết Chuyên gia'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(ExpertPostRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($req) {
            return $this->service->store($req);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFailWithRelations($id);

        return view($this->view['edit'], [
            'instance' => $instance,
            'experts' => Expert::query()->published()->orderBy('name')->pluck('name', 'id')->all(),
            'categories' => ExpertCategory::query()->published()->orderBy('name')->pluck('name', 'id')->all(),
            'age_groups' => AgeGroup::query()->active()->orderBy('sort_order')->pluck('name', 'id')->all(),
            'status' => [
                DefaultStatus::Published->value => 'Xuất bản',
                DefaultStatus::Draft->value => 'Bản nháp',
            ],
            'breadcrumbs' => $this->crums->add(__('Bài viết Chuyên gia'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(ExpertPostRequest $request): RedirectResponse
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
