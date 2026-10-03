<?php

namespace App\Admin\DataTables\FetalGrowthStandard;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\FetalGrowthStandard\FetalGrowthStandardRepositoryInterface;
use App\Enums\ActiveStatus;

class FetalGrowthStandardDatatable extends BaseDataTable
{
    protected $nameTable = 'fetalGrowthStandardTable';

    public function __construct(FetalGrowthStandardRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.fetal_growth_standards.datatable.action',
            'status' => 'admin.fetal_growth_standards.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5];
        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy('week', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.fetal_growth_standards', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'week' => fn($row) => 'Tuần ' . $row->week,
            'length' => fn($row) => $row->length !== null ? number_format($row->length, 1) . ' cm' : '—',
            'weight' => fn($row) => $row->weight !== null ? number_format($row->weight, 0) . ' g' : '—',
            'head_circumference' => fn($row) => $row->head_circumference !== null ? number_format($row->head_circumference, 1) . ' cm' : '—',
            'status' => $this->view['status'],
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
