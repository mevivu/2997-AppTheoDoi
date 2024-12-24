<?php
namespace App\Admin\DataTables\Notification\Common;



use App\Admin\Repositories\Notification\NotificationRepositoryInterface;
use App\Enums\Notification\NotificationStatus;

trait CommonTable {

    public function __construct(
        NotificationRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5, 6];
        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => NotificationStatus::asSelectArray()
            ],

        ];

    }
    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.notifications.datatable.action',
            'title' => 'admin.notifications.datatable.title',
            'status' => 'admin.notifications.datatable.status',
            'admin' => 'admin.notifications.datatable.admin',
            'edit_link_customer' => 'admin.notifications.datatable.edit-link-customer',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'title' => $this->view['title'],
            'status' => $this->view['status'],
            'user_id' => 'admin.notifications.datatable.edit-link-customer',
            'admin_id' => 'admin.notifications.datatable.admin',
            'created_at' => '{{ format_datetime($created_at) }}',
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
        $this->customRawColumns = ['action', 'status', 'checkbox', 'user_id', 'admin_id', 'title'];
    }


}
