<?php

namespace App\Admin\DataTables\Question;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Question\QuestionRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;


class IqQuestionDataTable extends BaseDataTable
{
    protected $nameTable = 'iqQuestionTable';


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
            'answer' => 'admin.question.datatable.answer',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4];

        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => ActiveStatus::asSelectArray()
            ],
        ];

        $this->columnSearchDate = [4];

    }

    public function query()
    {
        return $this->repository->getByQueryBuilder(
            [
                'question_type' => QuestionType::IQ,
                ['status', '!=', ActiveStatus::Deleted]
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.iq_questions', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'checkbox' => $this->view['checkbox'],
            'question' => $this->view['question'],
            'answer' => $this->view['answer'],
            'created_at' => function ($query) {
                return format_datetime($query->created_at);
            }
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
        $this->customRawColumns = ['question', 'action', 'status', 'checkbox'];
    }
}
