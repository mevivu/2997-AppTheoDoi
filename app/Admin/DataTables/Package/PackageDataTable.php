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
            'is_auto_renew' => 'admin.package.datatable.is_auto_renew',
            'is_sale' => 'admin.package.datatable.is_sale',
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

        $this->columnAllSearch = [ 1, 2, 3, 4, 5, 6, 7 ];
        $this->columnSearchDate = [ 7 ];
        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => PackageType::asSelectArray()
            ],
            [
                'column' => 3,
                'data' => [
                    '1' => 'Tự động gia hạn',
                    '0' => 'Một lần (Không gia hạn)',
                ]
            ],
            [
                'column' => 4,
                'data' => [
                    '0' => 'Gói thường',
                    '1' => 'Gói sale',
                ]
            ],
            [
                'column' => 5,
                'data' => $deviceOptions
            ],
            [
                'column' => 6,
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
            'is_auto_renew' => $this->view['is_auto_renew'],
            'is_sale' => $this->view['is_sale'],
            'max_devices' => $this->view['max_devices'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'is_auto_renew' => function ($query, $keyword) {
                $query->where('is_auto_renew', (int) $keyword);
            },
            'is_sale' => function ($query, $keyword) {
                $query->where('is_sale', (int) $keyword);
            },
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
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox', 'type', 'is_auto_renew', 'is_sale', 'max_devices'];
    }
}
