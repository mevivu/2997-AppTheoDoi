<?php

namespace App\Admin\Http\Controllers\Guide;

use App\Admin\DataTables\ClinicType\ClinicTypeDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\ClinicType\ClinicTypeRequest;
use App\Admin\Repositories\Guide\GuideRepositoryInterface;
use App\Admin\Services\Guide\GuideServiceInterface;
use App\Traits\ResponseController;
use Exception;
use App\Enums\Child\ChildStatus;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GuideController extends Controller
{
    use ResponseController;

    public function __construct(
        GuideRepositoryInterface $repository,
        GuideServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.guide.index',
            'create' => 'admin.guide.create',
            'edit' => 'admin.guide.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.guide.index',
            'create' => 'admin.guide.create',
            'edit' => 'admin.guide.edit',
            'delete' => 'admin.guide.delete',
        ];
    }

    public function index(ClinicTypeDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ChildStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('clinic_type')),
            ]

        );
    }


    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ChildStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('clinic_type'),
                route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(ClinicTypeRequest $request): RedirectResponse
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
                'breadcrumbs' => $this->crums->add(__('clinic_type'), route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    public function update(ClinicTypeRequest $request): RedirectResponse
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
