<?php

namespace App\Admin\Http\Controllers\Step;

use App\Admin\DataTables\Step\StepDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Step\StepRequest;
use App\Admin\Repositories\Step\StepRepositoryInterface;
use App\Admin\Services\Step\StepServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StepController extends Controller
{
    use ResponseController;

    public function __construct(
        StepRepositoryInterface $repository,
        StepServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.step.index',
            'create' => 'admin.step.create',
            'edit' => 'admin.step.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.step.index',
            'create' => 'admin.step.create',
            'edit' => 'admin.step.edit',
            'delete' => 'admin.step.delete',
        ];
    }

    public function index(StepDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Step')),
            ]

        );
    }


    public function create($guideId): Factory|View|Application
    {
        $step = $this->repository->getMaxOrder($guideId) + 1;
        return view($this->view['create'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách các bước'),
                route('admin.develop.steps', $guideId))->add(__('add')),
            'step' => $step,
        ]);
    }

    public function store(StepRequest $request): RedirectResponse
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
                'breadcrumbs' => $this->crums->add(__('Danh sách các bước'), route('admin.develop.steps', $instance->guide_id))->add(__('edit')),
            ],
        );
    }

    public function update(StepRequest $request): RedirectResponse
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
            $model = $this->repository->findOrFail($id);
            return $model->delete();
        });
    }


    protected function getActionMultiple(): array
    {
        return [
            'active' => ActiveStatus::Active->description(),
            'draft' => ActiveStatus::Draft->description(),
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
