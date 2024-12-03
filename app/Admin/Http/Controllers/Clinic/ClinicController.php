<?php

namespace App\Admin\Http\Controllers\Clinic;

use App\Admin\DataTables\Clinic\ClinicDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Clinic\ClinicRequest;
use App\Admin\Repositories\Clinic\ClinicRepositoryInterface;
use App\Admin\Services\Clinic\ClinicServiceInterface;
use App\Traits\ResponseController;
use Exception;
use App\Enums\Child\ChildStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClinicController extends Controller
{
    use ResponseController;



    public function __construct(
        ClinicRepositoryInterface   $repository,
        ClinicServiceInterface      $service
    )
    {

        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.clinic.index',
            'create' => 'admin.clinic.create',
            'edit' => 'admin.clinic.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.clinic.index',
            'create' => 'admin.clinic.create',
            'edit' => 'admin.clinic.edit',
            'delete' => 'admin.clinic.delete',
        ];
    }

    public function index(ClinicDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ChildStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('clinic')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ChildStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('clinic'),
                route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(ClinicRequest $request): RedirectResponse
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
                'status' => ChildStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('childrenList'), route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    public function update(ClinicRequest $request): RedirectResponse
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
            return $response->update(['status' => ChildStatus::Deleted->value]);
        });
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => ChildStatus::Active->description(),
            'draft' => ChildStatus::Draft->description(),
            'deleted' => ChildStatus::Deleted->description()
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
