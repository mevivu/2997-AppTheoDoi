<?php

namespace App\Admin\DataTables\Transaction;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Child\ChildStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\User\Gender;
use Illuminate\Database\Eloquent\Builder;

class TransactionDatable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'TransactionTable';

    public function __construct(
        TransactionRepositoryInterface $repository
    )
    {
        $this->repository = $repository;

        parent::__construct();
    }

    public function setView(): void
    {
        $this->view = [
            'status' => 'admin.transaction.datatable.status',
        ];
    }

    public function setColumnSearch(): void
    {

        $this->columnAllSearch = [0,1, 3, 4];
        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => TransactionStatus::asSelectArray()
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
        return $this->repository->getQueryBuilderOrderBy();
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.transaction', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'status' => $this->view['status'],
            'user_id' => function ($children) {
                return $children->user->fullname;
            },
            'package_id' => function ($children) {
                return $children->package->name;
            }
        ];
    }

    protected function setCustomAddColumns(): void
    {
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'code',
            'status',
            'user_id',
            'amount',
            'package_id'

        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user_id' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%$keyword%");
                });
            },

            'package_id' => function ($query, $keyword) {
                $query->whereHas('package', function ($subQuery) use ($keyword) {
                    $subQuery->where('name', 'like', "%$keyword%");
                });
            },
        ];
    }
}
