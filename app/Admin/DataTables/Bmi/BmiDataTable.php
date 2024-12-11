<?php

namespace App\Admin\DataTables\Bmi;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Bmi\BmiRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseType;
use App\Enums\User\Gender;


class BmiDataTable extends BaseDataTable
{
    protected $nameTable = 'bmiTable';


    public function __construct(
        BmiRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.bmi.datatable.action',
            'status' => 'admin.bmi.datatable.status',
            'checkbox' => 'admin.common.checkbox',
            'gender' => 'admin.bmi.datatable.gender',
            'id' => 'admin.bmi.datatable.id',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5];

        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray()
            ],
            [
                'column' => 2,
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
        $this->customColumns = config('datatables_columns.bmi', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'gender' => $this->view['gender'],
            'id' => $this->view['id'],
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
        $this->customRawColumns = ['id', 'gender', 'action', 'status', 'checkbox'];
    }
}
