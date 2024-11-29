<?php

namespace App\Admin\DataTables\Exercise;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Exercise\ExerciseRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseType;


class ExercisePowerDataTable extends BaseDataTable
{
    protected $nameTable = 'exercisePhysicalTable';


    public function __construct(
        ExerciseRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.exercise.datatable.action',
            'name' => 'admin.exercise.datatable.name',
            'status' => 'admin.exercise.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => ActiveStatus::asSelectArray()
            ],

        ];

    }

    public function query()
    {
        return $this->repository->getByQueryBuilder(['exercise_type' => ExerciseType::POWER->value]);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.exercise', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
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
        $this->customRawColumns = ['name', 'action', 'status', 'checkbox'];
    }



}