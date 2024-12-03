<?php

namespace App\Admin\DataTables\QuestionGroup;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\QuestionGroup\QuestionGroupRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\User\Gender;


class QuestionGroupDataTable extends BaseDataTable
{
    protected $nameTable = 'questionGroupTable';


    public function __construct(
        QuestionGroupRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.question-group.datatable.action',
            'status' => 'admin.question-group.datatable.status',
            'checkbox' => 'admin.common.checkbox',
            'name' => 'admin.question-group.datatable.name',
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
        return $this->repository->getQueryBuilderOrderBy();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.question_groups', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'name' => $this->view['name'],
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