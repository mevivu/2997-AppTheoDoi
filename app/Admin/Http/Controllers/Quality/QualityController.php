<?php

namespace App\Admin\Http\Controllers\Quality;

use App\Admin\DataTables\Quality\QualityDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Quality\QualityRequest;
use App\Admin\Repositories\Quality\QualityRepositoryInterface;
use App\Admin\Services\Quality\QualityServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    use ResponseController;

    public function __construct(
        QualityRepositoryInterface $repository,
        QualityServiceInterface $service
    ) {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.quality.index',
            'create' => 'admin.quality.create',
            'edit' => 'admin.quality.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.quality.index',
            'create' => 'admin.quality.create',
            'edit' => 'admin.quality.edit',
            'delete' => 'admin.quality.delete',
        ];
    }

    public function index(QualityDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('Phẩm chất')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(
                __('Phẩm chất'),
                route($this->route['index'])
            )->add(__('add')),
        ]);
    }

    public function store(QualityRequest $request): RedirectResponse
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
                    __('Phẩm chất'),
                    route($this->route['index'])
                )->add(__('edit')),
            ],
        );

    }

    public function update(QualityRequest $request): RedirectResponse
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
