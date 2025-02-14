<?php

namespace App\Admin\DataTables\Product;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Product\ProductRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Product\ProductStatus;
use Illuminate\Database\Eloquent\Builder;


class ProductDataTable extends BaseDataTable
{
    protected $nameTable = 'productTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        ProductRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.product.datatable.action',
            'name' => 'admin.product.datatable.name',
            'code' => 'admin.product.datatable.code',
            'status' => 'admin.product.datatable.status',
            'brand' => 'admin.product.datatable.brand',
            'product_catalog' => 'admin.product.datatable.product_catalog',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder([
            ['status', '!=', ActiveStatus::Deleted],
        ]);
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [ 1, 2, 3, 4,5,6];
        $this->columnSearchDate = [6];
        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => ProductStatus::asSelectArray()
            ],
        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.product', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'action' => $this->view['action'],
            'name' => $this->view['name'],
            'code' => $this->view['code'],
            'status' => $this->view['status'],
            'brand_id' => function ($product) {
                return view($this->view['brand'], [
                    'brand' => $product->brand,
                ])->render();
            },
            'product_catalog_id' => function ($product) {
                return view($this->view['product_catalog'], [
                    'product_catalogs' => $product->productCatalogs,
                ])->render();
            },
        ];
    }


    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'name', 'created_at', 'status','brand_id','product_catalog_id','checkbox','code'];
    }
    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'brand_id' => function ($query, $keyword) {
                $query->whereHas('brand', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            },
            'product_catalog_id' => function ($query, $keyword) {
                $query->whereHas('productCatalogs', function ($q) use ($keyword) {
                    $q->where('name', 'like', '%' . $keyword . '%');
                });
            }
        ];
    }
}
