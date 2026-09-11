<?php

namespace App\Admin\DataTables\Transaction;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Repositories\Transaction\TransactionRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Enums\Transaction\TransactionStatus;
use App\Enums\Transaction\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

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
            'action' => 'admin.transaction.datatable.action',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2, 3, 4, 5, 6, 7];
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

        // Eager load các quan hệ user và processor (admin duyệt/từ chối)
        $query->with(['user', 'processor']);

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
            'processed_by' => function ($transaction) {
                if (!$transaction->processed_by && !$transaction->processor) {
                    return '<span class="badge bg-secondary-lt text-muted px-2 py-1 fs-11">' . __('Chưa xử lý') . '</span>';
                }

                $processorName = e($transaction->processor?->fullname ?? $transaction->processor?->username ?? 'Admin');
                $html = '<div class="text-start py-1 fs-12" style="line-height: 1.45;">';
                $html .= '<span class="fw-bold text-dark d-flex align-items-center gap-1"><i class="ti ti-user-check text-primary"></i>' . $processorName . '</span>';

                if ($transaction->processed_at) {
                    $html .= '<span class="text-muted fs-11 text-nowrap"><i class="ti ti-clock me-1"></i>' . format_datetime($transaction->processed_at) . '</span>';
                }

                if (!empty($transaction->admin_note)) {
                    $note = e(Str::limit($transaction->admin_note, 30));
                    $fullNote = e($transaction->admin_note);
                    $html .= '<br><span class="text-muted fst-italic fs-11" data-bs-toggle="tooltip" title="' . $fullNote . '"><i class="ti ti-notes me-1"></i>' . $note . '</span>';
                }

                $html .= '</div>';
                return $html;
            },
            'action' => function ($transaction) {
                return view($this->view['action'], [
                    'transaction' => $transaction,
                ])->render();
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
            'processed_by',
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
            'amount' => function ($query, $keyword) {
                $clean = preg_replace('/[^0-9]/', '', $keyword);
                if ($clean !== '') {
                    $query->where('amount', 'like', "%{$clean}%");
                } else {
                    $query->where('amount', 'like', "%{$keyword}%");
                }
            },
            'bank_info' => function ($query, $keyword) {
                $keyword = trim($keyword);
                $query->where(function ($q) use ($keyword) {
                    $q->where('bank_name', 'like', "%$keyword%")
                        ->orWhere('bank_account_number', 'like', "%$keyword%")
                        ->orWhere('bank_account_name', 'like', "%$keyword%");
                });
            },
            'processed_by' => function ($query, $keyword) {
                $keyword = trim($keyword);
                $query->whereHas('processor', function ($subQuery) use ($keyword) {
                    $subQuery->where('fullname', 'like', "%{$keyword}%")
                        ->orWhere('username', 'like', "%{$keyword}%");
                });
            },
        ];
    }
}
