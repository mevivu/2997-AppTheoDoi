<?php

namespace App\Admin\DataTables\GPA;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\GPA\GPARepositoryInterface;
use App\Enums\ActiveStatus;
use App\Enums\ClassGrade\ClassGradeStatus;
use Illuminate\Database\Eloquent\Builder;

class GPADataTable extends BaseDataTable
{
    protected $nameTable = 'GPATable';

    protected array $actions = ['reset', 'reload'];


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
            'status' => 'admin.gpa.datatable.status',
        ];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', ActiveStatus::Deleted],
            ]
        )->with(['children', 'classes']);
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 5];
        $this->columnSearchSelect = [
            [

                'column' => 5,
                'data' => ActiveStatus::asSelectArray()
            ],

        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.gpa', []);
    }

    // protected function setCustomEditColumns(): void
    // {
    //     $this->customEditColumns = [
    //         'status' => $this->view['status'],
    //         'name' => $this->view['name'],
    //         'checkbox' => $this->view['checkbox'],
    //     ];
    // }

    // protected function setCustomAddColumns(): void
    // {
    //     $this->customAddColumns = [
    //         'action' => $this->view['action'],
    //     ];
    // }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['children.fullname', 'classes.name', 'semester1_grade', 'semester1_grade', 'full_year_grade', 'status'];
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
            'classes.name' => function ($query, $keyword) {
                // Sử dụng whereHas để filter cột name trong mối quan hệ classes
                $query->whereHas('classes', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            },
        ];
    }
}
