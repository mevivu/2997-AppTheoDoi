<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Order\OrderRepositoryInterface;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\OrderType;
use App\Enums\Payment\PaymentMethod;


class UserOrderDataTable extends BaseDataTable
{


    protected $nameTable = 'userOrderTable';


    public function __construct(
        OrderRepositoryInterface $repository
    ) {
        $this->repository = $repository;

        parent::__construct();

    }

    public function setView(): void
    {
        $this->view = [
            'status' => 'admin.users.order.datatable.status',
            'user' => 'admin.users.order.datatable.user',
            'driver' => 'admin.users.order.datatable.driver',
            'payment-method' => 'admin.users.order.datatable.payment',
            'code' => 'admin.users.order.datatable.code',
            'order_type' => 'admin.users.order.datatable.order_type',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0, 1, 2, 3, 4, 5, 6];

        $this->columnSearchDate = [5];

        $this->columnSearchSelect = [
            [
                'column' => 2,
                'data' => PaymentMethod::asSelectArray()
            ],
            [
                'column' => 4,
                'data' => OrderStatus::asSelectArray()
            ],
            [
                'column' => 3,
                'data' => OrderType::asSelectArray()
            ],

        ];
    }

    public function query()
    {
        return $this->repository->getByQueryBuilder([
            'user_id' => request()->route('id')
        ], ['vehicle', 'driver.user']);
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.user_order', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'code' => $this->view['code'],
            'created_at' => '{{ $created_at ? format_datetime($created_at) : "" }}',
            'total' => '{{ format_price($total) }}',
            'status' => $this->view['status'],
            'driver_id' => $this->view['driver'],
            'payment_method' => $this->view['payment-method'],
            'order_type' => $this->view['order_type'],
        ];
    }

    protected function setCustomAddColumns(): void
    {
       //
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [ 'status', 'driver_id', 'payment_method', 'code', 'order_type'];
    }

    protected function setCustomFilterColumns()
    {

        $this->customFilterColumns = [
            'driver_id' => function ($query, $keyword) {
                $query->whereHas('driver.user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', '%' . $keyword . '%');
                });
            },
        ];
    }
}