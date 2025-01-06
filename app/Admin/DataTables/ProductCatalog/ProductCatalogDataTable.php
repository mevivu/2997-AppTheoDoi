<?php

namespace App\Admin\DataTables\ProductCatalog;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\ProductCatalog\ProductCatalogRepositoryInterface;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class ProductCatalogDataTable extends BaseDataTable
{
    protected $nameTable = 'productCatalogTable';

    protected array $actions = ['reset', 'reload'];


    public function __construct(
        ProductCatalogRepositoryInterface $repository,
    )
    {

        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.productCatalog.datatable.action',
            'name' => 'admin.productCatalog.datatable.name',
            'status' => 'admin.productCatalog.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', ActiveStatus::Deleted],
            ]
        );
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [ 1, 2];
        $this->columnSearchDate = [3];
        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => ActiveStatus::asSelectArray()
            ],

        ];


    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.product_catalog', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'name' => $this->view['name'],
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],

        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox'];
    }


}
