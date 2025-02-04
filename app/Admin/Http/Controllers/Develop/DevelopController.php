<?php

namespace App\Admin\Http\Controllers\Develop;

use App\Admin\DataTables\Develop\DevelopDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Guide\GuideRequest;
use App\Admin\Repositories\Guide\GuideRepositoryInterface;
use App\Admin\Services\Guide\GuideServiceInterface;
use App\Enums\ActiveStatus;
use App\Enums\Guide\GuideType;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DevelopController extends Controller
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
            'index' => 'admin.develop.index',
            'create' => 'admin.develop.create',
            'edit' => 'admin.develop.edit',
        ];
    }

    public function getRoute(): array
    {
        return array(
            'index' => 'admin.develop.index',
            'create' => 'admin.develop.create',
            'edit' => 'admin.develop.edit',
            'delete' => 'admin.develop.delete',
        );
    }

    public function index(DevelopDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Develop Guide')),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('Develop Guide'),
                route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(GuideRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            $request->merge(['type' => GuideType::Develop->value]);
            $guide = $this->service->store($request);
            $this->service->storeSteps($guide, $request->input('steps', []));
            return $guide;
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
                'steps' => $instance->steps,
                'type' => GuideType::asSelectArray(),
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('Develop Guide'), route($this->route['index']))->add(__('edit')),
            ],
        );
    }

    public function update(GuideRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            $guide = $this->service->update($request);
            $this->service->updateSteps($guide, $request->input('steps', []));
            return $guide;
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
