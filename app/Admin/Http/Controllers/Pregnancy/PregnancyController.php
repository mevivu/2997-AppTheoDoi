<?php

namespace App\Admin\Http\Controllers\Pregnancy;

use App\Admin\DataTables\Pregnancy\PregnancyDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Pregnancy\PregnancyRequest;
use App\Admin\Repositories\Pregnancy\PregnancyRepositoryInterface;

use App\Admin\Services\Pregnancy\PregnancyServiceInterface;
use App\Enums\ActiveStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PregnancyController extends Controller
{
    public function __construct(
        PregnancyRepositoryInterface $repository,
        PregnancyServiceInterface    $service
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.pregnancy.index',
            'create' => 'admin.pregnancy.create',
            'edit' => 'admin.pregnancy.edit'
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.pregnancy.index',
            'create' => 'admin.pregnancy.create',
            'edit' => 'admin.pregnancy.edit',
            'delete' => 'admin.pregnancy.delete'
        ];
    }

    public function index(PregnancyDataTable $datatable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $datatable->render(
            $this->view['index'],
            [
                'actionMultiple' => $actionMultiple,
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add('Danh sách Thai kì'),
            ]
        );
    }

    public function update(PregnancyRequest $request)
    {
        $this->service->update($request);
        return back()->with('success', __('notifySuccess'));
    }

    public function delete($id): RedirectResponse
    {

        $this->repository->delete($id);
        return redirect()->back()->with('success', __('notifySuccess'));

    }

    public function edit($id): Factory|View|Application
    {
        $response = $this->repository->findOrFail($id);
        return view($this->view['edit'], [
            'response' => $response,
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('DS thai kì')->add('Cập nhật'),
        ]);
    }

    public function create(): Factory|View|Application
    {

        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Danh sách Thai kì', route($this->route['index']))->add('Thêm mới'),
        ]);
    }

    public function store(PregnancyRequest $request)
    {
        $response = $this->service->store($request);
        if ($response) {
            return to_route($this->route['create'], $response)->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => ActiveStatus::Active->description(),
            'draft' => ActiveStatus::Draft->description(),
            'deleted' => ActiveStatus::Deleted->description()
        ];
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
