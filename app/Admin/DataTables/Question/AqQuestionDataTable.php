<?php

namespace App\Admin\DataTables\Question;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Question\AgeGroup;
use App\Enums\Question\QuestionType;


class AqQuestionDataTable extends BaseDataTable
{
    protected $nameTable = 'aqQuestionTable';


    public function __construct(
        QuestionRepositoryInterface $repository
    )
    {
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
            'age_group' => 'admin.question.datatable.age_group',
            'code' => 'admin.question.datatable.code',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5, 6];

        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => AgeGroup::asSelectArray()
            ],
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray()
            ]
        ];

        $this->columnSearchDate = [6];
    }

    public function query()
    {
        if (request()->route()->getName() == 'admin.question.eq') {
            return $this->repository->getByQueryBuilder(
                [
                    'question_type' => QuestionType::EQ,
                    ['status', '!=', ActiveStatus::Deleted]
                ]
            );
        } else {
            return $this->repository->getByQueryBuilder(
                [
                    'question_type' => QuestionType::AQ,
                    ['status', '!=', ActiveStatus::Deleted]
                ]
            );
        }
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.aq_questions', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'code' => $this->view['code'],
            'question' => $this->view['question'],
            'question_group_id' => function ($query) {
                return view($this->view['question_group_id'], ['question_group' => $query->group]);
            },
            'created_at' => function ($query) {
                return format_datetime($query->created_at);
            },
            'age_group' => $this->view['age_group'],
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
        $this->customRawColumns = ['question_group_id','code', 'question', 'action', 'status', 'checkbox', 'age_group'];
    }
}
