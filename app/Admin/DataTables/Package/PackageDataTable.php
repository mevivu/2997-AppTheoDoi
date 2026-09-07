<?php

namespace App\Admin\DataTables\Package;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Package\PackageRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Package\PackageStatus;
use App\Enums\Package\PackageType;
use App\Models\Package;
use Illuminate\Database\Eloquent\Builder;

class PackageDataTable extends BaseDataTable
{
    protected $nameTable = 'packageTable';

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
            'max_devices' => 'admin.package.datatable.max_devices',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', PackageStatus::Deleted->value],
            ]
        );
    }

    public function setColumnSearch(): void
    {
        $deviceOptions = Package::query()
            ->whereNotNull('max_devices')
            ->distinct()
            ->orderBy('max_devices')
            ->pluck('max_devices')
            ->mapWithKeys(function ($val) {
                return [$val => $val . ' thiết bị'];
            })
            ->all();

        $this->columnAllSearch = [ 1, 2, 3, 4, 5 ];
        $this->columnSearchDate = [ 5 ];
        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => PackageType::asSelectArray()
            ],
            [
                'column' => 3,
                'data' => $deviceOptions
            ],
            [
                'column' => 4,
                'data' => PackageStatus::asSelectArray()
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
            'max_devices' => $this->view['max_devices'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'max_devices' => function ($query, $keyword) {
                $query->where('max_devices', $keyword);
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox', 'type', 'max_devices'];
    }
}
