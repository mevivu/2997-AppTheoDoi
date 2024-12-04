<?php

namespace App\Admin\DataTables\Package;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Package\PackageRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Package\PackageType;
use Illuminate\Database\Eloquent\Builder;

class PackageDataTable extends BaseDataTable
{
    protected $nameTable = 'packageTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        PackageRepositoryInterface $repository
    )
    {

        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.package.datatable.action',
            'name' => 'admin.package.datatable.name',
            'status' => 'admin.package.datatable.status',
            'type' => 'admin.package.datatable.type',
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
        $this->columnAllSearch = [1, 2, 3,4];
        $this->columnSearchDate = [4];
        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => PackageType::asSelectArray()
            ],
            [
                'column' => 3,
                'data' => ActiveStatus::asSelectArray()
            ],

        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.package', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'name' => $this->view['name'],
            'status' => $this->view['status'],
            'type' => $this->view['type'],
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
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox','type'];
    }
}
