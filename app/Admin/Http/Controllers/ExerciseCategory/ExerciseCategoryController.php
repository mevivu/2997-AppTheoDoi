<?php

namespace App\Admin\Http\Controllers\ExerciseCategory;

use App\Admin\DataTables\ExerciseCategory\ExerciseCategoryDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\ExerciseCategory\ExerciseCategoryRequest;
use App\Admin\Repositories\ExerciseCategory\ExerciseCategoryRepositoryInterface;
use App\Admin\Services\ExerciseCategory\ExerciseCategoryServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseTopic;
use App\Models\AgeGroup;
use App\Models\ExerciseCategory;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExerciseCategoryController extends Controller
{
    use ResponseController;

    public function __construct(
        ExerciseCategoryRepositoryInterface $repository,
        ExerciseCategoryServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.exercise_categories.index',
            'create' => 'admin.exercise_categories.create',
            'edit' => 'admin.exercise_categories.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.exercise_category.index',
            'create' => 'admin.exercise_category.create',
            'edit' => 'admin.exercise_category.edit',
            'delete' => 'admin.exercise_category.delete',
        ];
    }

    public function index(ExerciseCategoryDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'status' => ActiveStatus::asSelectArray(),
            'topics' => ExerciseTopic::asSelectArray(),
            'actionMultiple' => $this->getActionMultiple(),
            'breadcrumbs' => $this->crums->add(__('Danh mục bài tập')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        $ageGroups = AgeGroup::active()->orderBy('sort_order')->get();

        return view($this->view['create'], [
            'ageGroups' => $ageGroups,
            'topics' => ExerciseTopic::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Danh mục bài tập'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(ExerciseCategoryRequest $request): RedirectResponse
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
            'topics' => ExerciseTopic::asSelectArray(),
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Danh mục bài tập'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(ExerciseCategoryRequest $request): RedirectResponse
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
