<?php

namespace App\Admin\DataTables\Question;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;


class AqEqQuestionDataTable extends BaseDataTable
{
    protected $nameTable = 'aqEqQuestionTable';


    public function __construct(
        QuestionRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.question.datatable.action',
            'status' => 'admin.question.datatable.status',
            'checkbox' => 'admin.common.checkbox',
            'question' => 'admin.question.datatable.question',
            'question_group_id' => 'admin.question.datatable.question_group',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4];

        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => ActiveStatus::asSelectArray()
            ]
        ];

        $this->columnSearchDate = [4];
    }

    public function query()
    {
        if (request()->route()->getName() == 'admin.question.eq') {
            return $this->repository->getByQueryBuilder(['question_type' => QuestionType::EQ->value]);
        } else {
            return $this->repository->getByQueryBuilder(['question_type' => QuestionType::AQ->value]);
        }
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.eq_aq_questions', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'question' => $this->view['question'],
            'question_group_id' => function ($query) {
                return view($this->view['question_group_id'], ['question_group' => $query->group]);
            },
            'created_at' => function ($query) {
                return format_datetime($query->created_at);
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
        ];
    }

    protected function setCustomFilterColumns()
    {
        $this->customFilterColumns = [
            'question_group_id' => function ($query, $keyword) {
                $query->whereHas('group', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
            },
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['question_group_id', 'question', 'action', 'status', 'checkbox'];
    }
}