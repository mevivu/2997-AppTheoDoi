<?php

namespace App\Admin\DataTables\AgeGroup;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\AgeGroup\AgeGroupRepositoryInterface;
use App\Enums\ActiveStatus;

class AgeGroupDataTable extends BaseDataTable
{
    protected $nameTable = 'ageGroupTable';

    public function __construct(AgeGroupRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.age_groups.datatable.action',
            'status' => 'admin.age_groups.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5, 6];
        $this->columnSearchDate = [6];
        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy('sort_order', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.age_group', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'min_months' => fn($row) => $row->min_months !== null ? $row->min_months . ' tháng' : '—',
            'max_months' => fn($row) => $row->max_months !== null ? $row->max_months . ' tháng' : '—',
            'status' => $this->view['status'],
            'created_at' => fn($row) => $row->created_at ? date('d-m-Y H:i', strtotime($row->created_at)) : '',
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
        $this->customRawColumns = ['action', 'status', 'checkbox'];
    }
}
