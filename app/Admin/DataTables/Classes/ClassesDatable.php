<?php

namespace App\Admin\DataTables\Classes;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Classes\ClassesRepositoryInterface;

use App\Enums\ActiveStatus;
use App\Enums\Class\LevelGroup;
use Illuminate\Database\Eloquent\Builder;

class ClassesDatable extends BaseDataTable
{
    protected $nameTable = 'classesTable';

    protected array $actions = ['reset', 'reload'];


    public function __construct(
        ClassesRepositoryInterface $repository,
    )
    {

        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.classes.datatable.action',
            'name' => 'admin.classes.datatable.name',
            'status' => 'admin.classes.datatable.status',
            'level_group' => 'admin.classes.datatable.level_group',
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
        $this->columnAllSearch = [1, 2, 3];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => LevelGroup::asSelectArray()
            ],

            [
                'column' => 3,
                'data' => ActiveStatus::asSelectArray()
            ],

        ];


    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.classes', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'level_group' => $this->view['level_group'],
            'name' => $this->view['name'],
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
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox',
            'level_group'];
    }

    public function setCustomFilterColumns(): void
    {

    }

}
