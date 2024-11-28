<?php

namespace App\Admin\DataTables\Customer;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Customer\CustomerRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\User\Gender;
use App\Enums\Customer\CustomerStatus;
use App\Enums\User\UserActive;
use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Builder;

class CustomerDataTable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'customerTable';

    public function __construct(
        CustomerRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'action' => 'admin.customers.datatable.action',
            'fullname' => 'admin.customers.datatable.fullname',
            'status' => 'admin.customers.datatable.status',
            'checkbox' => 'admin.common.checkbox',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [1, 2, 3, 4, 5];

        $this->columnSearchDate = [6];

        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => Gender::asSelectArray()
            ],
            [
                'column' => 5,
                'data' => ActiveStatus::asSelectArray()
            ]
              
        ];
    }

    /**
     * Get query source of dataTable.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->repository->getQueryBuilderOrderBy();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.customer', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'fullname' => $this->view['fullname'],
            'gender' => function ($customer) {
                return $customer->gender;
            },

            'status' => $this->view['status'],

            'created_at' => '{{ format_date($created_at) }}'
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
        $this->customRawColumns = ['fullname', 'action',
            'status', 'checkbox'];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
        ];
    }
}
