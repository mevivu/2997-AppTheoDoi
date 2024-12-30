<?php

namespace App\Admin\Http\Controllers\VaccinationSchedule;

use App\Admin\DataTables\VaccinationSchedule\AdminVaccinationScheduleDataTable;
use App\Admin\DataTables\VaccinationSchedule\UserVaccinationScheduleDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\VaccinationSchedule\VaccinationScheduleRequest;
use App\Admin\Repositories\VaccinationSchedule\VaccinationScheduleRepositoryInterface;
use App\Admin\Services\VaccinationSchedule\VaccinationScheduleServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Permission\PermissionType;
use App\Enums\Vaccination\VaccinationStatus;
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
        VaccinationScheduleRepositoryInterface $repository,
        VaccinationScheduleServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'admin' => 'admin.vaccinationSchedule.admin',
            'create' => 'admin.vaccinationSchedule.create',
            'edit' => 'admin.vaccinationSchedule.edit',
            'user' => 'admin.vaccinationSchedule.user',
        ];
    }

    public function getRoute(): array
    {
        return [
            'admin' => 'admin.vaccination.admin',
            'create' => 'admin.vaccination.create',
            'edit' => 'admin.vaccination.edit',
            'delete' => 'admin.vaccination.delete',
            'user' => 'admin.vaccination.user',
        ];
    }

    public function user(UserVaccinationScheduleDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['user'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('vaccination_schedule')),
            ]

        );
    }

    public function admin(AdminVaccinationScheduleDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['admin'],
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
            'type' => PermissionType::asSelectArray(),
            'vaccinationStatus' => VaccinationStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add('DS tiêm chủng')->add('Thêm'),
        ]);
    }

    public function store(VaccinationScheduleRequest $request): RedirectResponse
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

        $instance = $this->repository->findOrFail($id);
        return view(
            $this->view['edit'],
            [
                'instance' => $instance,
                'type' => PermissionType::asSelectArray(),
                'status' => ActiveStatus::asSelectArray(),
                'vaccinationStatus' => VaccinationStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add('vaccination_schedule')->add('Cập nhật'),

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
