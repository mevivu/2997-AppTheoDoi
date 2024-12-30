<?php

namespace App\Admin\DataTables\Quiz;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Quiz\QuizRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Question\QuestionType;
use Illuminate\Database\Eloquent\Builder;

class QuizDataTable extends BaseDataTable
{
    protected $nameTable = 'quizTable';

    protected array $actions = ['reset', 'reload'];

    public function __construct(
        QuizRepositoryInterface $repository
    ) {

        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.quiz.datatable.action',
            'name' => 'admin.quiz.datatable.name',
            'status' => 'admin.quiz.datatable.status',
            'type' => 'admin.quiz.datatable.type',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function query(): Builder
    {
        if (request()->routeIs('admin.quiz.iq')) {
            $type = QuestionType::IQ->value;
        } elseif (request()->routeIs('admin.quiz.eq')) {
            $type = QuestionType::EQ->value;
        } elseif (request()->routeIs('admin.quiz.aq')) {
            $type = QuestionType::AQ->value;
        } elseif (request()->routeIs('admin.quiz.pq')) {
            $type = QuestionType::PQ->value;
        } else {
            $type = QuestionType::IQ->value;
        }

        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', ActiveStatus::Deleted],
                ['type', '=', $type]
            ]
        );
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5];
        $this->columnSearchDate = [5];
        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => QuestionType::asSelectArray()
            ],
            [
                'column' => 4,
                'data' => ActiveStatus::asSelectArray()
            ],

        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.quiz', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'name' => $this->view['name'],
            'status' => $this->view['status'],
            'type' => $this->view['type'],
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'name', 'status', 'checkbox', 'type'];
    }
}
