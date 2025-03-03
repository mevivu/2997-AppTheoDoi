<?php

namespace App\Admin\Http\Controllers\Product;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Product\ProductRequest;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Admin\Services\Product\ProductServiceInterface;
use App\Admin\DataTables\Product\ProductDataTable;
use App\Enums\ActiveStatus;
use App\Enums\Product\ProductStatus;
use App\Traits\ResponseController;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ResponseController;

    protected ProductRepositoryInterface $Repository;
    protected ProductServiceInterface $Service;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductServiceInterface $productService
    ) {
        parent::__construct();
        $this->Repository = $productRepository;
        $this->Service = $productService;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.product.index',
            'create' => 'admin.product.create',
            'edit' => 'admin.product.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.product.index',
            'create' => 'admin.product.create',
            'edit' => 'admin.product.edit',
            'delete' => 'admin.product.delete',
        ];
    }

    public function index(ProductDataTable $dataTable)
    {
        $actionMultiple = $this->getActionMultiple();
        return $dataTable->render(
            $this->view['index'],
            [
                'status' => ProductStatus::asSelectArray(),
                'actionMultiple' => $actionMultiple,
                'breadcrumbs' => $this->crums->add(__('productList')),
            ]
        );
    }

    public function create(): Factory|View|Application
    {

        $productCatalogs = $this->Repository->getAllProductCatalogs();
        $brands = $this->Repository->getAllBrands();

        return view('admin.product.create', [
            'status' => ProductStatus::asSelectArray(),
            'brands' => $brands,
            'productCatalogs' => $productCatalogs,
            'breadcrumbs' => $this->crums->add(__('productList'), route($this->route['index']))->add(__('add')),
        ]);
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        return $this->handleResponse($request, function ($request) {
            return $this->Service->store($request);
        }, $this->route['index'], $this->route['edit']);
    }

    /**
     * @throws Exception
     */
    public function edit($id): Factory|View|Application
    {
        $product = $this->Repository->findOrFailWithRelations($id, ['productCatalogs']);
        $allProductCatalogs = $this->Repository->getAllProductCatalogs();
        $productCatalogIds = $product->productCatalogs->pluck('id')->toArray();
        $brands = $this->Repository->getAllBrands();
        return view(
            $this->view['edit'],
            [
                'product' => $product,
                'productCatalogs' => $allProductCatalogs,
                'selectedProductCatalogIds' => $productCatalogIds,
                'brands' => $brands,
                'status' => ActiveStatus::asSelectArray(),
                'breadcrumbs' => $this->crums->add(__('productList'),
                    route($this->route['index']))->add(__('edit')),
            ]
        );
    }



    public function update(ProductRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->Service->update($request);
        });
    }

    /**
     * @throws Exception
     */
    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            $product = $this->Repository->findOrFail($id);
            return $product->update(['status' => ProductStatus::Deleted->value]);
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
        $boolean = $this->Service->actionMultipleRecords($request);
        if ($boolean) {
            return back()->with('success', __('notifySuccess'));
        }
        return back()->with('error', __('notifyFail'));
    }
}
