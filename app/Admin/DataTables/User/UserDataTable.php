<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\AES\AESHelper;
use App\Enums\Package\PackageType;
use App\Enums\User\UserStatus;
use Illuminate\Database\Eloquent\Builder;
use Throwable;

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
                        'phone' => $item->phone ? AESHelper::decrypt($item->phone) : null
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
                $type = optional($item->userPackages->first()?->package)->type;

                return view(
                    $this->view['package_type'],
                    [
                        'package_type' => $type ?? null
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
        $this->customFilterColumns = [

            'package_type' => function ($query, $keyword) {
                $query->whereHas('userPackages', function ($subQuery) use ($keyword) {
                    $subQuery->where('current_type', 'like', '%' . $keyword . '%');
                });

            },
            'email' => function ($query, $keyword) {
                try {
                    $encrypted = AESHelper::encrypt($keyword);
                    $query->where('email', $encrypted);
                } catch (Throwable $e) {
                    $query->whereRaw('0 = 1');
                }
            },
            'phone' => function ($query, $keyword) {
                try {
                    $encrypted = AESHelper::encrypt($keyword);
                    $query->where('phone', $encrypted);
                } catch (Throwable $e) {
                    $query->whereRaw('0 = 1');
                }
            },
        ];
    }
}
