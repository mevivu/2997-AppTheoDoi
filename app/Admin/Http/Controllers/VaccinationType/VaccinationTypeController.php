<?php

namespace App\Admin\Http\Controllers\VaccinationType;

use App\Admin\DataTables\VaccinationType\VaccinationTypeDataTable;
use App\Admin\Http\Controllers\Controller;

use App\Admin\Http\Requests\VaccinationType\VaccinationType;
use App\Admin\Repositories\VaccinationType\VaccinationTypeRepositoryInterface;
use App\Admin\Services\VaccinationType\VaccinationTypeServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VaccinationTypeController extends Controller
{
    use ResponseController;


    public function __construct(
        VaccinationTypeRepositoryInterface $repository,
        VaccinationTypeServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.vaccinationType.index',
            'create' => 'admin.vaccinationType.create',
            'edit' => 'admin.vaccinationType.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.vaccinationType.index',
            'create' => 'admin.vaccinationType.create',
            'edit' => 'admin.vaccinationType.edit',
            'delete' => 'admin.vaccinationType.delete',
        ];
    }

    public function index(VaccinationTypeDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Danh sách Loại tiêm chủng')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('Danh sách Loại tiêm chủng', route($this->route['create']))->add('Thêm mới'),

        ]);
    }

    public function store(VaccinationType $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {

        $instance = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'instance' => $instance,
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('Danh sách Loại tiêm chủng'),
                    route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    public function update(VaccinationType $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    /**
     * @throws Exception
     */
    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            $response = $this->repository->findOrFail($id);
            return $response->update(['status' => ActiveStatus::Deleted->value]);
        });
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
