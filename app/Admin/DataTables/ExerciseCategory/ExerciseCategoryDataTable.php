<?php

namespace App\Admin\DataTables\ExerciseCategory;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\ExerciseCategory\ExerciseCategoryRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Exercise\ExerciseTopic;
use App\Models\AgeGroup;

class ExerciseCategoryDataTable extends BaseDataTable
{
    protected $nameTable = 'exerciseCategoryTable';

    public function __construct(ExerciseCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.exercise_categories.datatable.action',
            'status' => 'admin.exercise_categories.datatable.status',
            'icon' => 'admin.exercise_categories.datatable.icon',
            'topic' => 'admin.exercise_categories.datatable.topic',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $ageGroupOptions = AgeGroup::query()
            ->orderBy('sort_order')
            ->pluck('name', 'id')
            ->all();

        $this->columnAllSearch = [2, 3, 4, 5, 6];
        $this->columnSearchSelect = [
            [
                'column' => 3,
                'data' => ExerciseTopic::asSelectArray(),
            ],
            [
                'column' => 4,
                'data' => $ageGroupOptions,
            ],
            [
                'column' => 6,
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderWithRelations(['ageGroup'])
            ->orderBy('sort_order', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.exercise_category', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'icon' => $this->view['icon'],
            'topic' => $this->view['topic'],
            'age_group' => fn($row) => $row->ageGroup?->name ?? '—',
            'status' => $this->view['status'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'topic' => function ($query, $keyword) {
                $query->where('topic', $keyword);
            },
            'age_group' => function ($query, $keyword) {
                $query->where('age_group_id', $keyword);
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['icon', 'topic', 'action', 'status', 'checkbox'];
    }
}
