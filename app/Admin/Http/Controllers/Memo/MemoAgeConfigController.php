<?php

namespace App\Admin\Http\Controllers\Memo;

use App\Admin\DataTables\Memo\MemoAgeConfigDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Memo\MemoAgeConfigRequest;
use App\Admin\Repositories\MemoAgeConfig\MemoAgeConfigRepositoryInterface;
use App\Admin\Services\MemoAgeConfig\MemoAgeConfigServiceInterface;
use App\Enums\ActiveStatus;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemoAgeConfigController extends Controller
{
    public function __construct(
        MemoAgeConfigRepositoryInterface $repository,
        MemoAgeConfigServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.memo-game.config.index',
            'create' => 'admin.memo-game.config.create',
            'edit' => 'admin.memo-game.config.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.memo-game.config.index',
            'create' => 'admin.memo-game.config.create',
            'edit' => 'admin.memo-game.config.edit',
            'delete' => 'admin.memo-game.config.delete',
        ];
    }

    public function index(MemoAgeConfigDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Memo Game: Cấu hình độ tuổi & Lưới thẻ'),
            ]
        );
    }

    public function create()
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Memo Game: Cấu hình', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function store(MemoAgeConfigRequest $request): RedirectResponse
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
        return view($this->view['edit'], [
            'response' => $response,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Memo Game: Cấu hình', route($this->route['index']))->add('Cập nhật'),
        ]);
    }

    public function update(MemoAgeConfigRequest $request): RedirectResponse
    {
        $this->service->update($request);
        return back()->with('success', __('notifySuccess'));
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
