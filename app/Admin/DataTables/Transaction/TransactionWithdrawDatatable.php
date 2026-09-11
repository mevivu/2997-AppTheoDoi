<?php

namespace App\Admin\DataTables\Transaction;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use Illuminate\Database\Eloquent\Builder;

class TransactionWithdrawDatatable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'TransactionWithdrawTable';

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
            'code' => 'admin.transaction.datatable.code',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2, 4, 5, 6];
        $this->columnSearchDate = [4, 6];
        $this->columnSearchSelect = [
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
        $query = $this->repository->getQueryBuilderOrderBy();

        // Chỉ lấy các giao dịch RÚT TIỀN HOA HỒNG
        $query->where('type', TransactionType::Withdraw);

        $userId = request()->route('id') ?: request('user_id');
        if ($userId) {
            $query->where('user_id', $userId);
        }

        return $query;
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.transaction_withdraw', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'created_at' => '<span class="text-muted fs-12 text-nowrap"><i class="ti ti-clock me-1"></i>{{ $created_at ? format_datetime($created_at) : "" }}</span>',
            'amount' => '<span class="fw-bold text-success font-monospace fs-13 text-nowrap">{{ $amount ? number_format($amount, 0, ",", ".") . " đ" : "0 đ" }}</span>',
            'status' => $this->view['status'],
            'code' => $this->view['code'],
            'user_id' => function ($transaction) {
                return view($this->view['user'], [
                    'user' => $transaction->user,
                ])->render();
            },
            'bank_info' => function ($transaction) {
                $bankName = e($transaction->bank_name ?? 'Chưa rõ NH');
                $accountNumber = e($transaction->bank_account_number ?? '-');
                $accountName = e($transaction->bank_account_name ?? '-');
                return '<div class="text-start py-1 fs-12" style="line-height: 1.45;">' .
                    '<span class="fw-bold text-primary"><i class="ti ti-building-bank me-1"></i>' . $bankName . '</span><br>' .
                    '<span class="font-monospace fw-bold text-dark fs-13">' . $accountNumber . '</span><br>' .
                    '<span class="text-muted text-uppercase fs-11">' . $accountName . '</span>' .
                    '</div>';
            },
            'scheduled_payout_date' => function ($transaction) {
                if (!$transaction->scheduled_payout_date) {
                    return '<span class="text-muted fs-12">-</span>';
                }
                return '<span class="badge bg-success-lt text-success px-2 py-1 fs-11 fw-semibold text-nowrap"><i class="ti ti-calendar-event me-1"></i>Thứ 5 (' . format_date($transaction->scheduled_payout_date) . ')</span>';
            },
            'action' => function ($transaction) {
                if ($transaction->status === TransactionStatus::Pending) {
                    $amountFmt = number_format((float) $transaction->amount, 0, ',', '.') . 'đ';
                    $userFullname = e($transaction->user?->fullname ?? 'Đối tác #' . $transaction->user_id);
                    $bankInfo = e(($transaction->bank_name ?? '') . ' - ' . ($transaction->bank_account_number ?? '') . ' (' . ($transaction->bank_account_name ?? '') . ')');
                    
                    return '<div class="d-flex align-items-center justify-content-center gap-1.5">' .
                        '<button type="button" class="btn btn-sm btn-success px-2 py-1 btn-approve-withdraw" ' .
                            'data-id="' . $transaction->id . '" ' .
                            'data-code="' . e($transaction->code) . '" ' .
                            'data-amount="' . $amountFmt . '" ' .
                            'data-user="' . $userFullname . '" ' .
                            'data-bank="' . $bankInfo . '" ' .
                            'title="Duyệt chi trả chuyển khoản">' .
                            '<i class="ti ti-check me-1"></i>Duyệt chi' .
                        '</button>' .
                        '<button type="button" class="btn btn-sm btn-outline-danger px-2 py-1 btn-reject-withdraw" ' .
                            'data-id="' . $transaction->id . '" ' .
                            'data-code="' . e($transaction->code) . '" ' .
                            'data-amount="' . $amountFmt . '" ' .
                            'data-user="' . $userFullname . '" ' .
                            'title="Từ chối lệnh rút và hoàn lại ví">' .
                            '<i class="ti ti-x me-1"></i>Từ chối' .
                        '</button>' .
                    '</div>';
                }
                if ($transaction->status === TransactionStatus::Confirmed) {
                    $note = e($transaction->admin_note ?? 'Đã chi trả thành công');
                    return '<span class="badge bg-success-lt text-success px-2 py-1" title="' . $note . '"><i class="ti ti-check-double me-1"></i>Đã chi trả</span>';
                }
                if ($transaction->status === TransactionStatus::Refunded) {
                    $reason = e($transaction->admin_note ?? 'Đã hoàn ví');
                    return '<span class="badge bg-danger-lt text-danger px-2 py-1" title="' . $reason . '"><i class="ti ti-arrow-back-up me-1"></i>Đã hoàn ví</span>';
                }
                return '<span class="text-muted fs-12">-</span>';
            },
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
            'bank_info',
            'scheduled_payout_date',
            'created_at',
            'action',
        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user_id' => function ($query, $keyword) {
                $query->whereHas('user', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%");
                });
            },
            'bank_info' => function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('bank_name', 'like', "%$keyword%")
                        ->orWhere('bank_account_number', 'like', "%$keyword%")
                        ->orWhere('bank_account_name', 'like', "%$keyword%");
                });
            },
        ];
    }
}
