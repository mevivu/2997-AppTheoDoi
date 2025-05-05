<?php

namespace App\Admin\DataTables\Notification;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\DataTables\Notification\Common\CommonTable;
use App\Admin\Traits\AuthService;

class NotificationAdminDataTable extends BaseDataTable
{
    use CommonTable, AuthService;

    protected $nameTable = 'notificationAdminTable';



    public function query()
    {
        return $this->repository->getByQueryBuilder(
            [
                ['admin_id', '!=', null],
                'package_id' => null,
                ['admin_id', '=', $this->getCurrentAdminId()]
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.notifications', []);
    }



    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = ['action', 'status', 'checkbox', 'user_id', 'admin_id', 'title'];
    }


}
