<?php

namespace App\Admin\Http\Controllers\Memo;

use App\Admin\DataTables\Memo\MemoRatingDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\MemoRating\MemoRatingRepositoryInterface;
use App\Admin\Services\MemoRating\MemoRatingServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemoRatingController extends Controller
{
    public function __construct(
        MemoRatingRepositoryInterface $repository,
        MemoRatingServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.memo-game.rating.index',
            'show' => 'admin.memo-game.rating.show',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.memo-game.rating.index',
            'show' => 'admin.memo-game.rating.show',
            'delete' => 'admin.memo-game.rating.delete',
        ];
    }

    public function index(MemoRatingDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Memo Game: Lịch sử bài test trí nhớ'),
            ]
        );
    }

    public function show($id)
    {
        $response = $this->repository->getQueryBuilder()
            ->with(['child.user', 'theme', 'ageConfig', 'rounds'])
            ->findOrFail($id);

        return view($this->view['show'], [
            'response' => $response,
            'breadcrumbs' => $this->crums->add('Memo Game: Lịch sử bài test', route($this->route['index']))->add('Chi tiết bài test #' . $response->id),
        ]);
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
            'delete' => 'Xóa bản ghi đã chọn',
        ];
    }
}
