<?php

namespace App\Admin\Http\Controllers\ProductCatalog;

use App\Admin\DataTables\ProductCatalog\ProductCatalogDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\ProductCatalog\ProductCatalogRequest;
use App\Admin\Repositories\ProductCatalog\ProductCatalogRepositoryInterface;
use App\Admin\Services\ProductCatalog\ProductCatalogServiceInterface;
use App\Enums\ActiveStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    use ResponseController;



    public function __construct(
        ProductCatalogRepositoryInterface   $repository,
        ProductCatalogServiceInterface      $service
    )
    {

        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;

    }

    public function getView(): array
    {
        return [
            'index' => 'admin.productCatalog.index',
            'create' => 'admin.productCatalog.create',
            'edit' => 'admin.productCatalog.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.category.index',
            'create' => 'admin.category.create',
            'edit' => 'admin.category.edit',
            'delete' => 'admin.category.delete',
        ];
    }

    public function index(ProductCatalogDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ActiveStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('category')),
            ]

        );
    }

    public function create(): Factory|View|Application
    {
        return view($this->view['create'], [
            'status' => ActiveStatus::asSelectArray(),
            'breadcrumbs' => $this->crums->add(__('category'),
                route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(ProductCatalogRequest $request): RedirectResponse
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
                'breadcrumbs' => $this->crums->add(__('list'), route($this->route['index']))->add(__('edit')),
            ],
        );

    }

    public function update(ProductCatalogRequest $request): RedirectResponse
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
