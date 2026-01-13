<?php

namespace App\Admin\DataTables\Notification;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\DataTables\Notification\Common\CommonTable;

class NotificationUserDataTable extends BaseDataTable
{
    use CommonTable;

    protected $nameTable = 'notificationUserTable';

    protected array $actions = ['reset', 'reload', 'pageLength'];


    public function query()
    {
        return $this->repository->getByQueryBuilder(
            [
                ['user_id', '!=', null]
            ]
        );
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.notifications', []);
    }


}
