<?php

namespace App\Admin\Http\Controllers\Expected;

use App\Admin\DataTables\Expected\ExpectedDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Expected\ExpectedRequest;
use App\Admin\Repositories\Expected\ExpectedRepositoryInterface;
use App\Admin\Services\Expected\ExpectedServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;


class ExpectedController extends Controller
{
    public function __construct(
        ExpectedRepositoryInterface $repository,
        ExpectedServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.expected.index',
            'create' => 'admin.expected.create',
            'edit' => 'admin.expected.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.expected.index',
            'create' => 'admin.expected.create',
            'edit' => 'admin.expected.edit',
            'delete' => 'admin.expected.delete'
        ];
    }

    public function index(ExpectedDataTable $dataTable)
    {
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $this->getActionMultiple(),
                'breadcrumbs' => $this->crums->add('Danh sách chiều cao cân nặng dự kiến'),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Danh sách chiều cao cân nặng dự kiến', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function store(ExpectedRequest $request): RedirectResponse
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['edit'], $response)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'response' => $response,
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách chiều cao cân nặng dự kiến', route($this->route['index']))->add('Cập nhật'),
            ]
        );

    }

    public function update(ExpectedRequest $request): RedirectResponse
    {
        $this->service->update($request);
        return back()->with('success', __('notifySuccess'));

    }

    public function delete($id): RedirectResponse
    {

        $this->repository->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));

    }

    protected function getActionMultiple(): array
    {
        return [
            ActiveStatus::Active->value => ActiveStatus::Active->description(),
            ActiveStatus::Draft->value => ActiveStatus::Draft->description(),
            ActiveStatus::Deleted->value => ActiveStatus::Deleted->description(),
        ];
    }

    public function actionMultipleRecords(Request $request)
    {
        $boolean = $this->service->actionMultipleRecords($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}