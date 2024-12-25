<?php

namespace App\Admin\DataTables\Classes;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Classes\ClassesRepositoryInterface;

use App\Enums\ActiveStatus;
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
        $this->columnAllSearch = [1, 2];

        $this->columnSearchSelect = [
            [
                'column' => 2,
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
            'name' => $this->view['name'],
            'checkbox' => $this->view['checkbox'],


        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'subject_id' => function ($children) {
                return $children->subjects->first()?->name;
            },
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action','subject_id', 'name', 'status', 'checkbox'];
    }

    public function setCustomFilterColumns(): void
    {

    }

}
