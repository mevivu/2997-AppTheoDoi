<?php

namespace App\Admin\DataTables\Children;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Children\ChildrenRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Child\ChildStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Builder;

class ChildrenDataTable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'childrenTable';

    public function __construct(
        ChildrenRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.children.datatable.action',
            'fullname' => 'admin.children.datatable.fullname',
            'status' => 'admin.children.datatable.status',
            'gender' => 'admin.children.datatable.gender',
            'user' => 'admin.children.datatable.user',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5];

        $this->columnSearchDate = [3];

        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => Gender::asSelectArray()
            ],
            [
                'column' => 5,
                'data' => ChildStatus::asSelectArray()
            ],
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getByQueryBuilder(
            ['status' => ChildStatus::Active]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.children', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'fullname' => $this->view['fullname'],
            'user_id' => function ($children) {
                return view($this->view['user'], [
                    'user' => $children->user
                ])->render();
            },
            'gender' => $this->view['gender'],
            'birthday' => '{{ date("d-m-Y", strtotime($birthday)) }}',
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
        $this->customRawColumns = [
            'action',
            'status',
            'gender',
            'user_id',
            'fullname',
            'checkbox',

        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user_id' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%$keyword%");
                });
            },
            'fullname' => function ($query, $keyword) {
                $query->where('fullname', 'like', "%$keyword%");
            },

        ];
    }
}
