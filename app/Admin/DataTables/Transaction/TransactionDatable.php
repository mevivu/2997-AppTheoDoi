<?php

namespace App\Admin\DataTables\Transaction;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Transaction\TransactionEnumService;
use App\Enums\Transaction\TransactionStatus;
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
            'user' => 'admin.transaction.datatable.user',
            'package' => 'admin.transaction.datatable.package',
            'code' => 'admin.transaction.datatable.code',
            'service' => 'admin.transaction.datatable.service',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 3, 4, 5, 6];
        $this->columnSearchDate = [6];
        $this->columnSearchSelect = [
            [
                'column' => 4,
                'data' => TransactionEnumService::asSelectArray()
            ],
            [
                'column' => 5,
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
            'created_at' => '<span class="text-muted fs-12 text-nowrap"><i class="ti ti-clock me-1"></i>{{ $created_at ? format_datetime($created_at) : "" }}</span>',
            'amount' => '<span class="fw-bold text-success font-monospace fs-13 text-nowrap">{{ $amount ? number_format($amount, 0, ",", ".") . " đ" : "0 đ" }}</span>',
            'status' => $this->view['status'],
            'service' => $this->view['service'],
            'code' => $this->view['code'],
            'user_id' => function ($transaction) {
                return view($this->view['user'], [
                    'user' => $transaction->user,
                ])->render();
            },
            'package_id' => function ($transaction) {
                return view($this->view['package'], [
                    'package' => $transaction->package,
                ])->render();
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
            'package_id',
            'service',
            'created_at',
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
