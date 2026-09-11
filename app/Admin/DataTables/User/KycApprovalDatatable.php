<?php

namespace App\Admin\DataTables\User;

use App\Admin\DataTables\BaseDataTable;
use App\Admin\Traits\Roles;
use App\Enums\User\KycStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class KycApprovalDatatable extends BaseDataTable
{
    use Roles;

    protected $nameTable = 'KycApprovalTable';

    public function setView(): void
    {
        $this->view = [
            'user' => 'admin.kyc.datatable.user',
            'id_cards' => 'admin.kyc.datatable.id_cards',
            'tax_and_bank' => 'admin.kyc.datatable.tax_and_bank',
            'status' => 'admin.kyc.datatable.status',
            'action' => 'admin.kyc.datatable.action',
        ];
    }

    public function setColumnSearch(): void
    {
        $this->columnAllSearch = [0, 1, 2, 3, 4, 5, 6];
        $this->columnSearchDate = [4];
        $this->columnSearchSelect = [
            [
                'column' => 5,
                'data' => KycStatus::asSelectArray(),
            ],
        ];
    }

    /**
     * Get query source of dataTable.
     */
    public function query(): Builder
    {
        $query = User::query();

        // Chỉ lấy những user đã từng gửi thông tin xác minh (hoặc có trạng thái KYC rõ ràng)
        $query->where(function ($q) {
            $q->where('kyc_status', '!=', KycStatus::NOT_SUBMITTED->value)
                ->orWhereNotNull('id_card_front')
                ->orWhereNotNull('id_card_back')
                ->orWhereNotNull('tax_code');
        });

        // Lọc theo tab trạng thái nếu có
        $status = request('kyc_status') ?: request('status');
        if ($status && $status !== 'all') {
            $query->where('kyc_status', $status);
        }

        // Sắp xếp ưu tiên: Hồ sơ Chờ duyệt (Pending) lên đầu, tiếp theo là thời gian gửi mới nhất
        $query->orderByRaw("
            CASE 
                WHEN kyc_status = '" . KycStatus::PENDING->value . "' THEN 0 
                WHEN kyc_status = '" . KycStatus::REJECTED->value . "' THEN 1 
                WHEN kyc_status = '" . KycStatus::APPROVED->value . "' THEN 2 
                ELSE 3 
            END ASC
        ")->orderByDesc('kyc_submitted_at')
          ->orderByDesc('updated_at');

        return $query;
    }

    protected function setCustomColumns(): void
    {
        $this->customColumns = config('datatables_columns.kyc_approval', []);
    }

    protected function setCustomEditColumns(): void
    {
        $this->customEditColumns = [
            'wallet_balance' => function ($user) {
                $balance = (float) ($user->wallet_balance ?? 0);
                $html = '<div class="text-center py-1">';
                $html .= '<span class="fw-bold font-monospace fs-13 text-success d-inline-flex align-items-center gap-1">';
                $html .= '<i class="ti ti-wallet fs-14"></i> ' . number_format($balance, 0, ',', '.') . 'đ';
                $html .= '</span>';
                $html .= '</div>';
                return $html;
            },
            'kyc_submitted_at' => function ($user) {
                $time = $user->kyc_submitted_at ?: $user->updated_at;
                if (!$time) {
                    return '<span class="text-muted fs-12">-</span>';
                }
                return '<div class="text-center text-nowrap fs-12 text-muted" style="line-height: 1.4;">' .
                    '<i class="ti ti-clock me-1"></i>' . format_datetime($time) .
                    '</div>';
            },
            'status' => function ($user) {
                return view($this->view['status'], [
                    'user' => $user,
                ])->render();
            },
        ];
    }

    protected function setCustomAddColumns(): void
    {
        $this->customAddColumns = [
            'user' => function ($user) {
                return view($this->view['user'], [
                    'user' => $user,
                ])->render();
            },
            'id_cards' => function ($user) {
                return view($this->view['id_cards'], [
                    'user' => $user,
                ])->render();
            },
            'tax_and_bank' => function ($user) {
                return view($this->view['tax_and_bank'], [
                    'user' => $user,
                ])->render();
            },
            'action' => function ($user) {
                return view($this->view['action'], [
                    'user' => $user,
                ])->render();
            },
        ];
    }

    protected function setCustomRawColumns(): void
    {
        $this->customRawColumns = [
            'user',
            'wallet_balance',
            'id_cards',
            'tax_and_bank',
            'kyc_submitted_at',
            'status',
            'action',
        ];
    }

    public function setCustomFilterColumns(): void
    {
        $this->customFilterColumns = [
            'user' => function ($query, $keyword) {
                $encrypted = null;
                try {
                    $encrypted = \App\AES\AESHelper::encrypt($keyword);
                } catch (\Throwable $e) {
                }

                $query->where(function ($sub) use ($keyword, $encrypted) {
                    $sub->where('fullname', 'like', "%$keyword%")
                        ->orWhere('phone', 'like', "%$keyword%")
                        ->orWhere('email', 'like', "%$keyword%");
                    if ($encrypted) {
                        $sub->orWhere('phone', $encrypted)
                            ->orWhere('email', $encrypted);
                    }
                });
            },
            'tax_and_bank' => function ($query, $keyword) {
                $query->where(function ($sub) use ($keyword) {
                    $sub->where('tax_code', 'like', "%$keyword%")
                        ->orWhere('bank_account_number', 'like', "%$keyword%")
                        ->orWhere('bank_account_name', 'like', "%$keyword%")
                        ->orWhere('bank_name', 'like', "%$keyword%");
                });
            },
        ];
    }
}
