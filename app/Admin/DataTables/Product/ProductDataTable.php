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
        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.product.datatable.action',
            'name' => 'admin.product.datatable.name',
            'status' => 'admin.product.datatable.status',
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
        $this->columnAllSearch = [1, 2, 3];
        $this->columnSearchSelect = [
            [
                'column' => 2,
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
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
        ];
    }


    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'name', 'checkbox', 'created_at', 'status'];
    }
}
