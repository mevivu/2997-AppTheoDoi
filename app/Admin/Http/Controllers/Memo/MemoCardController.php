<?php

namespace App\Admin\Http\Controllers\Memo;

use App\Admin\DataTables\Memo\MemoCardDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Memo\MemoCardBulkRequest;
use App\Admin\Http\Requests\Memo\MemoCardRequest;
use App\Admin\Repositories\MemoCard\MemoCardRepositoryInterface;
use App\Admin\Repositories\MemoTheme\MemoThemeRepositoryInterface;
use App\Admin\Services\MemoCard\MemoCardServiceInterface;
use App\Enums\ActiveStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemoCardController extends Controller
{
    protected $themeRepository;

    public function __construct(
        MemoCardRepositoryInterface $repository,
        MemoCardServiceInterface $service,
        MemoThemeRepositoryInterface $themeRepository
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
        $this->themeRepository = $themeRepository;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.memo-game.card.index',
            'create' => 'admin.memo-game.card.create',
            'bulkCreate' => 'admin.memo-game.card.bulk-create',
            'edit' => 'admin.memo-game.card.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.memo-game.card.index',
            'create' => 'admin.memo-game.card.create',
            'bulkCreate' => 'admin.memo-game.card.bulkCreate',
            'edit' => 'admin.memo-game.card.edit',
            'delete' => 'admin.memo-game.card.delete',
        ];
    }

    public function index(MemoCardDataTable $dataTable)
    {
        $themes = $this->themeRepository->getActiveThemes();

        return $dataTable->render(
            $this->view['index'],
            [
                'themes' => $themes,
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Memo Game: Thư viện thẻ bài'),
            ]
        );
    }

    public function create()
    {
        $themes = $this->themeRepository->getActiveThemes()->pluck('name', 'id');

        return view($this->view['create'], [
            'themes' => $themes,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Memo Game: Thẻ bài', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function bulkCreate()
    {
        $themes = $this->themeRepository->getActiveThemes();

        return view($this->view['bulkCreate'], [
            'themes' => $themes,
            'breadcrumbs' => $this->crums->add('Memo Game: Thẻ bài', route($this->route['index']))->add('Upload hàng loạt'),
        ]);
    }

    public function store(MemoCardRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['edit'], $response->id)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    public function bulkStore(MemoCardBulkRequest $request): RedirectResponse
    {
        $count = $this->service->bulkStore($request);
        return to_route($this->route['index'])->with('success', "Đã tải lên và tạo thành công {$count} thẻ bài!");
    }

    public function edit($id)
    {
        $response = $this->repository->findOrFail($id);
        $themes = $this->themeRepository->getActiveThemes()->pluck('name', 'id');
        $themeCards = $this->repository->getCardsByTheme($response->memo_theme_id);

        return view($this->view['edit'], [
            'response' => $response,
            'themes' => $themes,
            'themeCards' => $themeCards,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Memo Game: Thẻ bài', route($this->route['index']))->add('Cập nhật'),
        ]);
    }

    public function update(MemoCardRequest $request): RedirectResponse
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
                'message' => __('Cập nhật thứ tự các thẻ bài thành công!')
            ]);
        }

        return response()->json([
            'status' => 400,
            'success' => false,
            'message' => __('Cập nhật thứ tự thẻ bài thất bại, vui lòng thử lại.')
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
