<?php

namespace App\Admin\DataTables\Notification;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\AES\AESHelper;
use App\Enums\ApprovalStatus;
use App\Enums\Notification\NotificationStatus;
use App\Enums\Package\PackageType;
use App\Models\User;

class NotificationPackageDataTable extends BaseDataTable
{
    use AuthService;

    protected $nameTable = 'notificationTable';


    public function __construct(
        NotificationRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.notifications.datatable.action',
            'title' => 'admin.notifications.datatable.title',
            'approval_status' => 'admin.notifications.datatable.approval_status',
            'admin' => 'admin.notifications.datatable.admin',
            'package' => 'admin.notifications.datatable.package',
            'edit_link_customer' => 'admin.notifications.datatable.edit-link-customer',
            'checkbox' => 'admin.common.checkbox',
            'user_id_attribute' => 'admin.notifications.datatable.user',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5, 6];
        $this->columnSearchSelect = [
            [
                'column' => 6,
                'data' => ApprovalStatus::asSelectArray()
            ],

        ];

    }

    public function query()
    {
        return $this->repository->getByQueryBuilder(
            [
                ['admin_id', '!=', null],
                ['package_id', '!=', null],
                ['user_id', '=', null],
                ['admin_id', '=', $this->getCurrentAdminId()]
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.notifications_approval', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'title' => $this->view['title'],
            'approval_status' => $this->view['approval_status'],
            'admin_id' => 'admin.notifications.datatable.admin',
            'created_at' => '{{ format_datetime($created_at) }}',
            'package_id' => function ($item) {
                return view(
                    $this->view['package'],
                    [
                        'name' => $item->package->name,
                        'package_id' => $item->package_id
                    ]
                )->render();
            },
            'user_id_attribute' => $this->view['user_id_attribute'],
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
        $this->customRawColumns = ['action', 'approval_status', 'checkbox',
            'user_id_attribute', 'admin_id', 'title', 'package_id'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'package_id' => function ($query, $keyword) {
                $query->whereHas('package', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', '%' . $keyword . '%');
                });
            },

            'user_id_attribute' => function ($query, $keyword) {
                $query->whereIn('user_id_attribute', function ($subQuery) use ($keyword) {
                    $subQuery->select('id')
                        ->from('users')
                        ->where('fullname', 'like', '%' . $keyword . '%');
                });
            },
        ];
    }

}
