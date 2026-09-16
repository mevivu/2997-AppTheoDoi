<?php

namespace App\Admin\Http\Controllers\Memo;

use App\Admin\DataTables\Memo\MemoThemeDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Memo\MemoThemeRequest;
use App\Admin\Repositories\MemoTheme\MemoThemeRepositoryInterface;
use App\Admin\Services\MemoTheme\MemoThemeServiceInterface;
use App\Enums\ActiveStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemoThemeController extends Controller
{
    public function __construct(
        MemoThemeRepositoryInterface $repository,
        MemoThemeServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.memo-game.theme.index',
            'create' => 'admin.memo-game.theme.create',
            'edit' => 'admin.memo-game.theme.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.memo-game.theme.index',
            'create' => 'admin.memo-game.theme.create',
            'edit' => 'admin.memo-game.theme.edit',
            'delete' => 'admin.memo-game.theme.delete',
        ];
    }

    public function index(MemoThemeDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Memo Game: Chủ đề'),
            ]
        );
    }

    public function create()
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Memo Game: Chủ đề', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function store(MemoThemeRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['edit'], $response->id)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    public function edit($id)
    {
        $response = $this->repository->findOrFail($id);
        $allThemes = $this->repository->getAllByPosition();

        return view($this->view['edit'], [
            'response' => $response,
            'allThemes' => $allThemes,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Memo Game: Chủ đề', route($this->route['index']))->add('Cập nhật'),
        ]);
    }

    public function update(MemoThemeRequest $request): RedirectResponse
    {
        $this->service->update($request);
        return back()->with('success', __('notifySuccess'));
    }

    public function updatePosition(Request $request): \Illuminate\Http\JsonResponse
    {
        $boolean = $this->service->updatePosition($request);
        if ($boolean) {
            return response()->json([
                'status' => 200,
                'success' => true,
                'message' => __('Cập nhật thứ tự các chủ đề thành công!')
            ]);
        }

        return response()->json([
            'status' => 400,
            'success' => false,
            'message' => __('Cập nhật thứ tự chủ đề thất bại, vui lòng thử lại.')
        ], 400);
    }

    public function delete($id): RedirectResponse
    {
        $this->repository->delete($id);
        return back()->with('success', __('notifySuccess'));
    }

    public function actionMultipleRecords(Request $request): RedirectResponse
    {
        $boolean = $this->service->actionMultipleRecords($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    protected function getActionMultiple(): array
    {
        return [
            ActiveStatus::Active->value => ActiveStatus::Active->description(),
            ActiveStatus::Draft->value => ActiveStatus::Draft->description(),
            ActiveStatus::Deleted->value => ActiveStatus::Deleted->description(),
        ];
    }
}
