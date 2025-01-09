<?php

namespace App\Admin\DataTables\Brand;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Brand\BrandRepositoryInterface;
use App\Enums\Brand\BrandStatus;
use Illuminate\Database\Eloquent\Builder;

class BrandDataTable extends BaseDataTable
{
    protected $nameTable = 'brandTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        BrandRepositoryInterface $repository
    ) {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.brand.datatable.action',
            'name' => 'admin.brand.datatable.name',
            'status' => 'admin.brand.datatable.status',
            'country' => 'admin.brand.datatable.country',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', BrandStatus::Deleted],
            ]
        );
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4];
        $this->columnSearchDate = [4];
        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => BrandStatus::asSelectArray()
            ],
        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.brand', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'name' => $this->view['name'],
            'status' => $this->view['status'],
            'country' => $this->view['country'],
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox', 'country'];
    }
}
