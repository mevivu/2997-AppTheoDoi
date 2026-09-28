<?php

namespace App\Admin\DataTables\Lesson;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Lesson\LessonRepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Lesson\EducationPillar;
use App\Enums\Lesson\LessonAccessType;
use App\Enums\Lesson\LessonDifficulty;
use App\Models\AgeGroup;
use App\Models\LessonCategory;

class LessonDataTable extends BaseDataTable
{
    protected $nameTable = 'lessonTable';

    public function __construct(LessonRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.lessons.datatable.action',
            'status' => 'admin.lessons.datatable.status',
            'thumbnail' => 'admin.lessons.datatable.thumbnail',
            'category' => 'admin.lessons.datatable.category',
            'pillar' => 'admin.lessons.datatable.pillar',
            'videos_count' => 'admin.lessons.datatable.videos-count',
            'difficulty' => 'admin.lessons.datatable.difficulty',
            'access_type' => 'admin.lessons.datatable.access-type',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {
        $categoryOptions = LessonCategory::query()
            ->orderBy('sort_order')
            ->pluck('name', 'id')
            ->all();

        $ageGroupOptions = AgeGroup::query()
            ->orderBy('sort_order')
            ->pluck('name', 'id')
            ->all();

        $this->columnAllSearch = [2, 3, 4, 5, 7, 8, 10];
        $this->columnSearchSelect = [
            [
                'column' => 3, // Category
                'data' => $categoryOptions,
            ],
            [
                'column' => 4, // Pillar
                'data' => EducationPillar::asSelectArray(),
            ],
            [
                'column' => 5, // Age Group
                'data' => $ageGroupOptions,
            ],
            [
                'column' => 7, // Difficulty
                'data' => LessonDifficulty::asSelectArray(),
            ],
            [
                'column' => 8, // Access Type
                'data' => LessonAccessType::asSelectArray(),
            ],
            [
                'column' => 10, // Status
                'data' => ActiveStatus::asSelectArray(),
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderWithRelations(['category', 'ageGroup', 'videos'])
            ->withCount('videos')
            ->orderBy('sort_order', 'asc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.lesson', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'thumbnail' => $this->view['thumbnail'],
            'category' => $this->view['category'],
            'pillar' => $this->view['pillar'],
            'age_group' => fn($row) => $row->ageGroup?->name ?? '—',
            'videos_count' => $this->view['videos_count'],
            'difficulty' => $this->view['difficulty'],
            'access_type' => $this->view['access_type'],
            'status' => $this->view['status'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'category' => function ($query, $keyword) {
                $query->where('lesson_category_id', $keyword);
            },
            'pillar' => function ($query, $keyword) {
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('pillar', $keyword);
                });
            },
            'age_group' => function ($query, $keyword) {
                $query->where('age_group_id', $keyword);
            },
            'difficulty' => function ($query, $keyword) {
                $query->where('difficulty', $keyword);
            },
            'access_type' => function ($query, $keyword) {
                $query->where('access_type', $keyword);
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
        $this->customRawColumns = ['checkbox', 'thumbnail', 'category', 'pillar', 'videos_count', 'difficulty', 'access_type', 'status', 'action'];
    }
}
