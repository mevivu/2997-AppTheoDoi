<?php

namespace App\Admin\DataTables\WeightHeightWho;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\WeightHeightWho\WeightHeightWhoRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;


class WeightHeightWhoDatatable extends BaseDataTable
{
    protected $nameTable = 'WeightHeightWhoTable';


    public function __construct(
        WeightHeightWhoRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.WeightHeightWho.datatable.action',
            'age' => 'admin.WeightHeightWho.datatable.age',
            'checkbox' => 'admin.common.checkbox',
            'month' => 'admin.WeightHeightWho.datatable.month',
            'status' => 'admin.WeightHeightWho.datatable.status',
            'gender' => 'admin.WeightHeightWho.datatable.gender',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5,6];

        $this->columnSearchSelect = [
            [
                'column' => 6,
                'data' => ActiveStatus::asSelectArray()
            ],
            [
                'column' => 1,
                'data' => Gender::asSelectArray()
            ]
        ];

    }

    public function query()
    {
        return $this->repository->getQueryBuilderOrderBy();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.WeightHeightWho', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'gender' => $this->view['gender'],
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
        $this->customRawColumns = ['gender', 'action', 'status', 'checkbox'];
    }
}
