<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Enums\Package\PackageType;
use App\Enums\User\UserActive;
use App\Enums\User\UserStatus;
use Illuminate\Database\Eloquent\Builder;

class UserDataTable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'userTable';

    public function __construct(
        UserRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.users.datatable.action',
            'editlink' => 'admin.users.datatable.editlink',
            'status' => 'admin.users.datatable.status',
            'email' => 'admin.users.datatable.email',
            'phone' => 'admin.users.datatable.phone',
            'package_type' => 'admin.users.datatable.package_type',
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
                'data' => PackageType::asSelectArray()
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
            'email' => function ($item) {
                return view(
                    $this->view['email'],
                    [
                        'email' => AESHelper::decrypt($item->email)
                    ]
                )->render();
            },
            'phone' => function ($item) {
                return view(
                    $this->view['phone'],
                    [
                        'phone' => AESHelper::decrypt($item->phone)
                    ]
                )->render();
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'action' => $this->view['action'],
            'package_type' => function ($item) {
                return view(
                    $this->view['package_type'],
                    [
                        'package_type' => $item->userPackages->first()->type
                    ]
                )->render();
            },
            'checkbox' => $this->view['checkbox'],
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'action',
            'status',
            'checkbox',
            'code',
            'email',
            'phone',
            'package_type'
        ];
    }

    public function setCustomFilterColumns(): void
    {
        //
    }
}
