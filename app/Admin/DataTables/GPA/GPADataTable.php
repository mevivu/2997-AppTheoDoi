<?php

namespace App\Admin\DataTables\GPA;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\GPA\GPARepositoryInterface;
use App\Enums\ActiveStatus;
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
            'status' => 'admin.gpa.datatable.status',
        ];
    }

    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            [
                ['status', '!=', ActiveStatus::Deleted],
            ]
        )->with(['children', 'class']);
    }


    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 5];
        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray(),
            ],

        ];
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.gpa', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'children.fullname' => function ($children) {
                return view($this->view['children.fullname'], [
                    'children' => $children->children,
                ])->render();
            },

        ];
    }

    // protected function setCustomAddColumns(): void
    // {
    //     $this->customAddColumns = [
    //         'action' => $this->view['action'],
    //     ];
    // }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['children.fullname', 'class.name', 'semester1_grade', 'semester1_grade', 'full_year_grade', 'status'];
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
            switch ($key) {
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
