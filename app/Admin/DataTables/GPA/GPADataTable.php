<?php

namespace App\Admin\DataTables\GPA;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\GPA\GPARepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\Semester\SemesterStatus;
use App\Enums\Class\LevelGroup;
use Illuminate\Database\Eloquent\Builder;

class GPADataTable extends BaseDataTable
{
    protected $nameTable = 'GPATable';

    protected array $actions = ['reset', 'reload', 'excel'];


    public function __construct(
        GPARepositoryInterface $repository,
    ) {

        parent::__construct();
        $this->repository = $repository;
    }

    public function setView(): void
    {
        $this->view = [
            'index' => 'admin.gpa.index',
            'children.fullname' => 'admin.gpa.datatable.name',
        ];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', ActiveStatus::Deleted],
            ]
        )->where(function ($query) {
            $query->where('semester1_grade', '>', 0)
                  ->orWhere('semester2_grade', '>', 0)
                  ->orWhereHas('evaluations', function($q) {
                      $q->whereIn('semester', [SemesterStatus::Semester1, SemesterStatus::Semester2])
                        ->where('average_score', '>', 0);
                  });
        })->with(['children.user', 'class', 'evaluations']);
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2, 3];
        $this->columnSearchSelect = [];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.gpa', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'child_code' => function ($row) {
                if ($row->children) {
                    return view('admin.children.datatable.editlink', [
                        'id' => $row->children->id,
                        'code' => 'TE' . $row->children->id
                    ])->render();
                }
                return '';
            },
            'parent_code' => function ($row) {
                if ($row->children && $row->children->user) {
                    return view('admin.users.datatable.editlink', [
                        'id' => $row->children->user->id,
                        'code' => 'CM' . $row->children->user->id
                    ])->render();
                }
                return '';
            },
            'children.fullname' => function ($row) {
                return view($this->view['children.fullname'], [
                    'children' => $row->children,
                ])->render();
            },
            'semester1_grade' => function ($row) {
                $grade = $row->semester1_grade ?: $this->calculateSemesterGrade($row, SemesterStatus::Semester1);
                return $grade > 0 ? number_format($grade, 2) : '';
            },
            'semester2_grade' => function ($row) {
                $grade = $row->semester2_grade ?: $this->calculateSemesterGrade($row, SemesterStatus::Semester2);
                return $grade > 0 ? number_format($grade, 2) : '';
            },
            'full_year_grade' => function ($row) {
                $levelGroup = $row->class?->level_group;
                $s1 = $row->semester1_grade ?: $this->calculateSemesterGrade($row, SemesterStatus::Semester1);
                $s2 = $row->semester2_grade ?: $this->calculateSemesterGrade($row, SemesterStatus::Semester2);

                if ($levelGroup === LevelGroup::Junior) {
                    return $s2 > 0 ? number_format($s2, 2) : ($s1 > 0 ? number_format($s1, 2) : '');
                } else {
                    if ($s1 > 0 && $s2 > 0) {
                        return number_format(($s1 + 2 * $s2) / 3, 2);
                    }
                    return $s2 > 0 ? number_format($s2, 2) : ($s1 > 0 ? number_format($s1, 2) : '');
                }
            }
        ];
    }

    protected function calculateSemesterGrade($row, SemesterStatus $semester)
    {
        $evaluation = $row->evaluations->first(function ($eval) use ($semester) {
            $evalSemester = $eval->semester instanceof SemesterStatus ? $eval->semester->value : $eval->semester;
            return $evalSemester === $semester->value;
        });
        return $evaluation ? $evaluation->average_score : 0;
    }

    // protected function setCustomAddColumns(): void
    // {
    //     $this->customAddColumns = [
    //         'action' => $this->view['action'],
    //     ];
    // }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['children.fullname', 'class.name', 'semester1_grade', 'semester2_grade', 'full_year_grade', 'child_code', 'parent_code'];
    }

    protected function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'children.fullname' => function ($query, $keyword) {
                // Sử dụng whereHas để filter cột fullname trong mối quan hệ children
                $query->whereHas('children', function ($q) use ($keyword) {
                    $q->where('fullname', 'LIKE', "%{$keyword}%");
                });
            },
            'class.name' => function ($query, $keyword) {
                // Sử dụng whereHas để filter cột name trong mối quan hệ classes
                $query->whereHas('class', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            },
        ];
    }

    protected function getExportValue($key, $row)
    {
        try {
            if ($key === 'semester1_grade' && (is_null($row->semester1_grade) || $row->semester1_grade == 0)) {
                return $this->calculateSemesterGrade($row, SemesterStatus::Semester1);
            }
            if ($key === 'semester2_grade' && (is_null($row->semester2_grade) || $row->semester2_grade == 0)) {
                return $this->calculateSemesterGrade($row, SemesterStatus::Semester2);
            }
            if ($key === 'full_year_grade') {
                $levelGroup = $row->class?->level_group;
                $s1 = $row->semester1_grade ?: $this->calculateSemesterGrade($row, SemesterStatus::Semester1);
                $s2 = $row->semester2_grade ?: $this->calculateSemesterGrade($row, SemesterStatus::Semester2);

                if ($levelGroup === LevelGroup::Junior) {
                    return $s2 ?: $s1;
                } else {
                    if ($s1 > 0 && $s2 > 0) {
                        return round(($s1 + 2 * $s2) / 3, 2);
                    }
                    return $s2 ?: $s1;
                }
            }

            switch ($key) {
                case 'child_code':
                    return $row->children ? 'TE' . $row->children->id : '';
                case 'parent_code':
                    return ($row->children && $row->children->user) ? 'CM' . $row->children->user->id : '';
                case 'children.fullname':
                    return $row->children?->fullname ?? '';
                case 'class.name':
                    return $row->class?->name ?? '';
                case 'status':
                     // Handle Native Enum description if available
                     if ($row->status instanceof \BackedEnum && method_exists($row->status, 'description')) {
                        return $row->status->description();
                    }
                    return $row->status ?? '';
            }
        } catch (\Throwable $e) {
            return '';
        }

        return parent::getExportValue($key, $row);
    }
}
