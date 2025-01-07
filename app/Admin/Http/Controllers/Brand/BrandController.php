<?php

namespace App\Admin\Http\Controllers\Brand;

use App\Admin\DataTables\Brand\BrandDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Brand\BrandRequest;
use App\Admin\Repositories\Brand\BrandRepositoryInterface;
use App\Admin\Services\Brand\BrandServiceInterface;
use App\Enums\Brand\BrandStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use ResponseController;

    public function __construct(
        BrandRepositoryInterface $repository,
        BrandServiceInterface    $service
    ) {
        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.brand.index',
            'create' => 'admin.brand.create',
            'edit' => 'admin.brand.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.brand.index',
            'create' => 'admin.brand.create',
            'edit' => 'admin.brand.edit',
            'delete' => 'admin.brand.delete',
        ];
    }

    public function index(BrandDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => BrandStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('brand')),
            ]
        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => BrandStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(
                __('brand'),
                route($this->route['index'])
            )->add(__('add')),
        ]);
    }

    public function store(BrandRequest $request): RedirectResponse
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
                'status' => \App\Enums\ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(
                    __('brand'),
                    route($this->route['index'])
                )->add(__('edit')),
            ],
        );
    }


    public function update(BrandRequest $request): RedirectResponse
    {

        return $this->handleUpdateResponse($request, function () use ($request) {
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
            return $response->update(['status' => BrandStatus::Deleted->value]);
        });
    }

    protected function getActionMultiple(): array
    {
        return [
            'active' => BrandStatus::Active->label(),
            'draft' => BrandStatus::Draft->label(),
            'deleted' => BrandStatus::Deleted->label(),
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
