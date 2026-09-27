<?php

namespace App\Admin\Http\Controllers\LessonCategory;

use App\Admin\DataTables\LessonCategory\LessonCategoryDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\LessonCategory\LessonCategoryRequest;
use App\Admin\Repositories\LessonCategory\LessonCategoryRepositoryInterface;
use App\Admin\Services\LessonCategory\LessonCategoryServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonCategoryKey;
use App\Models\AgeGroup;
use App\Models\LessonCategory;
use App\Traits\ResponseController;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LessonCategoryController extends Controller
{
    use ResponseController;

    public function __construct(
        LessonCategoryRepositoryInterface $repository,
        LessonCategoryServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.lesson_categories.index',
            'create' => 'admin.lesson_categories.create',
            'edit' => 'admin.lesson_categories.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.lesson_category.index',
            'create' => 'admin.lesson_category.create',
            'edit' => 'admin.lesson_category.edit',
            'delete' => 'admin.lesson_category.delete',
        ];
    }

    public function index(LessonCategoryDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'status' => ActiveStatus::asSelectArray(),
            'pillars' => EducationPillar::asSelectArray(),
            'actionMultiple' => $this->getActionMultiple(),
            'breadcrumbs' => $this->crums->add(__('Danh mục bài học')),
        ]);
    }

    public function create(): Factory|View|Application
    {
        $ageGroups = AgeGroup::active()->orderBy('sort_order')->get();

        return view($this->view['create'], [
            'ageGroups' => $ageGroups,
            'pillars' => EducationPillar::cases(),
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Danh mục bài học'), route($this->route['index']))->add(__('Thêm mới')),
        ]);
    }

    public function store(LessonCategoryRequest $request): RedirectResponse
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
            'pillars' => EducationPillar::cases(),
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Danh mục bài học'), route($this->route['index']))->add(__('Chỉnh sửa')),
        ]);
    }

    public function update(LessonCategoryRequest $request): RedirectResponse
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

    /**
     * API AJAX lấy danh sách Keys theo Pillar được chọn (phục vụ tự động hóa form)
     */
    public function getKeysByPillar(Request $request): JsonResponse
    {
        $pillar = $request->query('pillar');
        if (empty($pillar)) {
            return response()->json(['success' => false, 'data' => []]);
        }

        $keys = LessonCategoryKey::getByPillar($pillar);

        return response()->json([
            'success' => true,
            'data' => $keys,
        ]);
    }

    /**
     * API AJAX lấy danh sách danh mục lọc theo Độ tuổi và Pillar (phục vụ cascading select trên form bài học)
     */
    public function getCategoriesByFilter(Request $request): JsonResponse
    {
        $ageGroupId = $request->query('age_group_id');
        $pillar = $request->query('pillar');

        $query = LessonCategory::active()->orderBy('sort_order', 'asc');

        if (!empty($ageGroupId)) {
            $query->where('age_group_id', $ageGroupId);
        }

        if (!empty($pillar)) {
            $query->where('pillar', $pillar);
        }

        $categories = $query->get(['id', 'name', 'pillar', 'key', 'age_group_id']);

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
