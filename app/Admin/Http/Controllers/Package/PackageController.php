<?php

namespace App\Admin\Http\Controllers\Package;

use App\Admin\DataTables\Package\PackageDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Package\PackageRequest;
use App\Admin\Repositories\Package\PackageRepositoryInterface;
use App\Admin\Services\Package\PackageServiceInterface;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    use ResponseController;

    public function __construct(
        PackageRepositoryInterface $repository,
        PackageServiceInterface    $service
    )
    {

        parent::__construct();

        $this->repository = $repository;

        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.package.index',
            'create' => 'admin.package.create',
            'edit' => 'admin.package.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.package.index',
            'create' => 'admin.package.create',
            'edit' => 'admin.package.edit',
            'delete' => 'admin.package.delete',
        ];
    }

    public function index(PackageDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => PackageStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('package')),
            ]

        );
    }


    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => PackageStatus::asSelectArray(),
            'type' => PackageType::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('package'),
                route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(PackageRequest $request): RedirectResponse
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
                'type' => PackageType::asSelectArray(),
                'status' => PackageStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('package'),
                    route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    public function update(PackageRequest $request): RedirectResponse
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
            return $response->update(['status' => PackageStatus::Deleted->value]);
        });
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => PackageStatus::Active->description(),
            'draft' => PackageStatus::Draft->description(),
            'deleted' => PackageStatus::Deleted->description()
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
