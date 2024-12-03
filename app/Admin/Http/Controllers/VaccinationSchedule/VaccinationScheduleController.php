<?php

namespace App\Admin\Http\Controllers\VaccinationSchedule;

use App\Admin\DataTables\VaccinationSchedule\VaccinationScheduleDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\VaccinationSchedule\VaccinationScheduleRequest;
use App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Admin\Services\VaccinationSchedule\VaccinationScheduleServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VaccinationScheduleController extends Controller
{
    use ResponseController;



    public function __construct(
        VaccinationScheduleRepositoryInterface   $repository,
        VaccinationScheduleServiceInterface      $service
    )
    {

        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.vaccinationSchedule.index',
            'create' => 'admin.vaccinationSchedule.create',
            'edit' => 'admin.vaccinationSchedule.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.vaccination.index',
            'create' => 'admin.vaccination.create',
            'edit' => 'admin.vaccination.edit',
            'delete' => 'admin.vaccination.delete',
        ];
    }

    public function index(VaccinationScheduleDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('vaccination_schedule')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('vaccination_schedule'),
                route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(VaccinationScheduleRequest $request): RedirectResponse
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
                'breadcrumbs' => $this->crums->add(__('vaccination_schedule'),
                    route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    public function update(VaccinationScheduleRequest $request): RedirectResponse
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
