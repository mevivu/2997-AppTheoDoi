<?php

namespace App\Admin\DataTables\Lesson;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Lesson\LessonRepositoryInterface;
use App\Enums\Lesson\LessonDifficultyRating;
use App\Models\AgeGroup;

class LessonRatingDataTable extends BaseDataTable
{
    protected $nameTable = 'lessonRatingTable';

    public function __construct(LessonRepositoryInterface $repository)
    {
        $this->repository = $repository;
        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.lesson-ratings.datatable.action',
            'thumbnail' => 'admin.lesson-ratings.datatable.thumbnail',
            'rating_bars' => 'admin.lesson-ratings.datatable.rating-bars',
        ];
    }

    public function setColumnSearch(): void
    {
        $ageGroupOptions = AgeGroup::query()
            ->orderBy('sort_order')
            ->pluck('name', 'id')
            ->all();

        $this->columnAllSearch = [1, 2];
        $this->columnSearchSelect = [
            [
                'column' => 2, // age_group
                'data' => $ageGroupOptions,
            ],
        ];
    }

    public function query()
    {
        return $this->repository->getQueryBuilderWithRelations(['ageGroup'])
            ->withCount([
                'difficultyRatings as total_ratings',
                'difficultyRatings as easy_count' => function ($q) {
                    $q->where('difficulty_level', LessonDifficultyRating::Easy->value);
                },
                'difficultyRatings as with_help_count' => function ($q) {
                    $q->where('difficulty_level', LessonDifficultyRating::WithHelp->value);
                },
                'difficultyRatings as hard_count' => function ($q) {
                    $q->where('difficulty_level', LessonDifficultyRating::Hard->value);
                },
            ])
            ->orderBy('total_ratings', 'desc');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.lesson_rating', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'thumbnail' => $this->view['thumbnail'],
            'age_group' => fn($row) => $row->ageGroup?->name ?? '—',
            'total_ratings' => function ($row) {
                $total = (int) ($row->total_ratings ?? 0);
                if ($total === 0) {
                    return '<span class="badge bg-secondary-lt text-muted px-2 py-1">0</span>';
                }
                return '<span class="badge bg-primary-lt fw-bold fs-13 px-2 py-1"><i class="ti ti-users me-1"></i>' . number_format($total) . '</span>';
            },
            'easy_count' => function ($row) {
                $count = (int) ($row->easy_count ?? 0);
                $total = (int) ($row->total_ratings ?? 0);
                if ($total === 0) {
                    return '<span class="text-muted">—</span>';
                }
                $pct = round(($count / $total) * 100);
                return '<span class="badge bg-green-lt text-green fw-semibold px-2 py-1">' . $count . ' <small class="text-muted">(' . $pct . '%)</small></span>';
            },
            'with_help_count' => function ($row) {
                $count = (int) ($row->with_help_count ?? 0);
                $total = (int) ($row->total_ratings ?? 0);
                if ($total === 0) {
                    return '<span class="text-muted">—</span>';
                }
                $pct = round(($count / $total) * 100);
                return '<span class="badge bg-warning-lt text-warning fw-semibold px-2 py-1">' . $count . ' <small class="text-muted">(' . $pct . '%)</small></span>';
            },
            'hard_count' => function ($row) {
                $count = (int) ($row->hard_count ?? 0);
                $total = (int) ($row->total_ratings ?? 0);
                if ($total === 0) {
                    return '<span class="text-muted">—</span>';
                }
                $pct = round(($count / $total) * 100);
                return '<span class="badge bg-danger-lt text-danger fw-semibold px-2 py-1">' . $count . ' <small class="text-muted">(' . $pct . '%)</small></span>';
            },
            'rating_bars' => $this->view['rating_bars'],
        ];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'age_group' => function ($query, $keyword) {
                $query->where('age_group_id', $keyword);
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
        $this->customRawColumns = ['thumbnail', 'total_ratings', 'easy_count', 'with_help_count', 'hard_count', 'rating_bars', 'action'];
    }
}
