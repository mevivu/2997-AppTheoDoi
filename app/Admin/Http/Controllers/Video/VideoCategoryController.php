<?php

namespace App\Admin\Http\Controllers\Video;

use App\Admin\DataTables\VideoCategory\VideoCategoryDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\VideoCategory\VideoCategoryRequest;
use App\Admin\Repositories\VideoCategory\VideoCategoryRepositoryInterface;
use App\Admin\Services\VideoCategory\VideoCategoryServiceInterface;
use App\Enums\ActiveStatus;
use App\Models\AgeGroup;
use App\Models\VideoCategory;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VideoCategoryController extends Controller
{
    use ResponseController;

    public function __construct(
        VideoCategoryRepositoryInterface $repository,
        VideoCategoryServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.video_categories.index',
            'create' => 'admin.video_categories.create',
            'edit' => 'admin.video_categories.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.video_category.index',
            'create' => 'admin.video_category.create',
            'edit' => 'admin.video_category.edit',
            'delete' => 'admin.video_category.delete',
        ];
    }

    public function index(VideoCategoryDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'status' => ActiveStatus::asSelectArray(),
            'actionMultiple' => $this->getActionMultiple(),
            'breadcrumbs' => $this->crums->add(__('Danh mục video')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        $ageGroups = AgeGroup::active()->orderBy('sort_order')->get();

        return view($this->view['create'], [
            'ageGroups' => $ageGroups,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Danh mục video'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(VideoCategoryRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);
        $ageGroups = AgeGroup::active()->orderBy('sort_order')->get();

        return view($this->view['edit'], [
            'instance' => $instance,
            'ageGroups' => $ageGroups,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Danh mục video'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(VideoCategoryRequest $request): RedirectResponse
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
