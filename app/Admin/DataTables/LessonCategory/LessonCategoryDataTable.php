<?php

namespace App\Admin\DataTables\LessonCategory;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\LessonCategory\LessonCategoryRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Models\AgeGroup;

class LessonCategoryDataTable extends BaseDataTable
{
    protected $nameTable = 'lessonCategoryTable';

    public function __construct(LessonCategoryRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.lesson_categories.datatable.action',
            'status' => 'admin.lesson_categories.datatable.status',
            'icon' => 'admin.lesson_categories.datatable.icon',
            'pillar' => 'admin.lesson_categories.datatable.pillar',
            'key' => 'admin.lesson_categories.datatable.key',
            'lessons_count' => 'admin.lesson_categories.datatable.lessons-count',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $ageGroupOptions = AgeGroup::query()
            ->orderBy('sort_order')
            ->pluck('name', 'id')
            ->all();

        $pillarOptions = EducationPillar::asSelectArray();

        $this->columnAllSearch = [2, 3, 4, 5, 8];
        $this->columnSearchSelect = [
            [
                'column' => 3, // Pillar
                'data' => $pillarOptions,
            ],
            [
                'column' => 5, // Age Group
                'data' => $ageGroupOptions,
            ],
            [
                'column' => 8, // Status
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderWithRelations(['ageGroup'])
            ->withCount('lessons')
            ->orderBy('sort_order', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.lesson_category', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'icon' => $this->view['icon'],
            'pillar' => $this->view['pillar'],
            'key' => $this->view['key'],
            'age_group' => fn($row) => $row->ageGroup?->name ?? '—',
            'lessons_count' => $this->view['lessons_count'],
            'status' => $this->view['status'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'pillar' => function ($query, $keyword) {
                $query->where('pillar', $keyword);
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
        $this->customRawColumns = ['checkbox', 'icon', 'pillar', 'key', 'lessons_count', 'status', 'action'];
    }
}
