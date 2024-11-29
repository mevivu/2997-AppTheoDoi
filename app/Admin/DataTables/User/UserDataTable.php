<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\User\UserActive;
use App\Enums\User\UserStatus;
use Illuminate\Database\Eloquent\Builder;

class UserDataTable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'userTable';

    public function __construct(
        UserRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.users.datatable.action',
            'editlink' => 'admin.users.datatable.editlink',
            'status' => 'admin.users.datatable.status',
            'active' => 'admin.users.datatable.active',
            'email' => 'admin.users.datatable.email',
            'phone' => 'admin.users.datatable.phone',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5, 6];

        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => UserStatus::asSelectArray()
            ],
            [
                'column' => 6,
                'data' => UserActive::asSelectArray()
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
        return $this->repository->getQueryBuilder()->with('roles');
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.user', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'code' => $this->view['editlink'],
            'status' => $this->view['status'],
            'active' => $this->view['active'],
            'email' => function($item){
                return view($this->view['email'],
                [
                    'email' => $item->email
                ])->render();
            },
            'phone' => function($item){
                return view($this->view['phone'],
                [
                    'phone' => $item->phone
                ])->render();
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
        $this->customRawColumns = [
            'action',
            'status',
            'active',
            'checkbox',
            'code',
            'email',
            'phone',
        ];
    }

    public function setCustomFilterColumns(): void
    {
        //
    }
}
