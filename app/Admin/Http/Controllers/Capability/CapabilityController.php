<?php

namespace App\Admin\Http\Controllers\Capability;

use App\Admin\DataTables\Capability\CapabilityDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Capability\CapabilityRequest;
use App\Admin\Repositories\Capability\CapabilityRepositoryInterface;
use App\Admin\Services\Capability\CapabilityService;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CapabilityController extends Controller
{
    use ResponseController;

    public function __construct(
        CapabilityRepositoryInterface $repository,
        CapabilityService $service
    ) {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.capability.index',
            'create' => 'admin.capability.create',
            'edit' => 'admin.capability.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.capability.index',
            'create' => 'admin.capability.create',
            'edit' => 'admin.capability.edit',
            'delete' => 'admin.capability.delete',
        ];
    }

    public function index(CapabilityDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Năng lực')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(
                __('Năng lực'),
                route($this->route['index'])
            )->add(__('add')),
        ]);
    }

    public function store(CapabilityRequest $request): RedirectResponse
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
                'response' => $instance,
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(
                    __('Năng lực'),
                    route($this->route['index'])
                )->add(__('edit')),
            ],
        );

    }

    public function update(CapabilityRequest $request): RedirectResponse
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
        try {
            $this->repository->delete($id);
            return redirect()->back()->with('success', __('notifySuccess'));
        } catch (Exception $exception) {
            return redirect()->back()->with('error', __('notifyFail'));
        }
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