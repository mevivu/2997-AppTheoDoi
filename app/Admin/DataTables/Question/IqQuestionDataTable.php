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

    protected ?array $iqQuestionGroups = null;

    protected function getQuestionGroups(): array
    {
        if ($this->iqQuestionGroups === null) {
            $this->iqQuestionGroups = \App\Models\QuestionGroup::whereIn('type', [
                \App\Enums\Group\GroupType::Linguistic,
                \App\Enums\Group\GroupType::LogicMath,
                \App\Enums\Group\GroupType::Visual,
                \App\Enums\Group\GroupType::Memory,
            ])
            ->where('status', ActiveStatus::Active)
            ->get()
            ->unique(function ($item) {
                return is_object($item->type) ? $item->type->value : $item->type;
            })
            ->pluck('name', 'id')
            ->toArray();
        }
        return $this->iqQuestionGroups;
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.question.datatable.action',
            'status' => 'admin.question.datatable.status',
            'checkbox' => 'admin.common.checkbox',
            'question' => 'admin.question.datatable.question',
            'question_group_id' => 'admin.question.datatable.question_group',
            'answer' => 'admin.question.datatable.answer',
            'code' => 'admin.question.datatable.code',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [1, 2, 3, 4, 5, 6];

        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => $this->getQuestionGroups()
            ],
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray()
            ],
        ];

        $this->columnSearchDate = [6];
    }

    public function query()
    {
        return $this->repository->getByQueryBuilder(
            [
                'question_type' => QuestionType::IQ,
                ['status', '!=', ActiveStatus::Deleted]
            ],
            ['group', 'answers']
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
            'code' => $this->view['code'],
            'checkbox' => $this->view['checkbox'],
            'question' => function ($query) {
                return view($this->view['question'], [
                    'id' => $query->id,
                    'question' => $query->question,
                    'questionModel' => $query,
                ]);
            },
            'question_group_id' => function ($query) {
                return view($this->view['question_group_id'], [
                    'question' => $query,
                    'question_group' => $query->group,
                    'questionGroups' => $this->getQuestionGroups(),
                ]);
            },
            'answer' => $this->view['answer'],
            'created_at' => function ($query) {
                return format_datetime($query->created_at);
            }

        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'question_group_id' => function ($query, $keyword) {
                if (is_numeric($keyword)) {
                    $query->where('question_group_id', $keyword);
                } elseif ($keyword === 'null' || $keyword === 'none') {
                    $query->whereNull('question_group_id');
                } else {
                    $query->whereHas('group', function ($g) use ($keyword) {
                        $g->where('name', 'like', "%{$keyword}%");
                    });
                }
            },
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
        $this->customRawColumns = ['question', 'question_group_id', 'action', 'status', 'checkbox', 'code'];
    }
}
