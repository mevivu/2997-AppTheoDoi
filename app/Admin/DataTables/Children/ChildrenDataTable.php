<?php

namespace App\Admin\DataTables\Children;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Children\ChildrenRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Child\BornStatus;
use App\Enums\Child\ChildStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Builder;

class ChildrenDataTable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'childrenTable';

    protected array $actions = ['reset', 'reload', 'excel'];

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
            'is_born' => 'admin.children.datatable.born_status',
            'gender' => 'admin.children.datatable.gender',
            'user' => 'admin.children.datatable.user',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];

        $this->columnSearchDate = [6, 7];

        $this->columnSearchSelect = [
            [
                'column' => 8,
                'data' => Gender::asSelectArray()
            ],
            [
                'column' => 9,
                'data' => BornStatus::asSelectArray()
            ],
            [
                'column' => 10,
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
        )->with(['user']);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.children', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'is_born' => $this->view['is_born'],
            'fullname' => $this->view['fullname'],
            'user_fullname' => function ($children) {
                return view($this->view['user'], [
                    'user' => $children->user
                ])->render();
            },
            'user_code' => function ($children) {
                if (!$children->user) {
                    return '';
                }
                return view('admin.users.datatable.editlink', [
                    'id' => $children->user->id,
                    'code' => $children->user->code
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
            'user_fullname',
            'user_code',
            'fullname',
            'checkbox',
            'is_born'
        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user_fullname' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%$keyword%");
                });
            },
            'user_code' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('code', 'like', "%$keyword%");
                });
            },
            'fullname' => function ($query, $keyword) {
                $query->where('fullname', 'like', "%$keyword%");
            },
            'is_born' => function ($query, $keyword) {
                $query->where('is_born', $keyword);
            },

        ];
    }

    protected function getExportValue($key, $row)
    {
        try {
            switch ($key) {
                case 'id':
                    return 'TE' . $row->id;
                case 'fullname':
                    return $row->fullname;
                case 'user_id':
                     return  $row->user ? 'CM' . $row->user->id : '';
                case 'user_fullname':
                    return $row->user ? $row->user->fullname : '';
                case 'user_code':
                    return $row->user ? $row->user->code : '';
                case 'birthday':
                    return $row->birthday ? date('d/m/Y', strtotime($row->birthday)) : '';
                case 'due_date':
                    return $row->due_date ? date('d/m/Y', strtotime($row->due_date)) : '';
                case 'is_born':
                    // Handle Native Enum description if available
                    if ($row->is_born instanceof \BackedEnum && method_exists($row->is_born, 'description')) {
                        return $row->is_born->description();
                    }
                    return $row->is_born ?? '';
                case 'status':
                    // Handle Native Enum description if available
                    if ($row->status instanceof \BackedEnum && method_exists($row->status, 'description')) {
                        return $row->status->description();
                    }
                    // Fallback for raw integer value
                    if (is_numeric($row->status)) {
                        return \App\Enums\Child\ChildStatus::tryFrom($row->status)?->description() ?? $row->status;
                    }
                    return $row->status ?? '';
            }
        } catch (\Throwable $e) {
            return '';
        }

        return parent::getExportValue($key, $row);
    }
}
