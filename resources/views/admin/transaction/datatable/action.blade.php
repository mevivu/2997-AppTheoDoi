@php
    use App\Enums\Transaction\TransactionStatus;
@endphp

@if ($transaction->status === TransactionStatus::Pending)
    @php
        $amountFmt = number_format((float) $transaction->amount, 0, ',', '.') . 'đ';
        $userFullname = $transaction->user?->fullname ?? 'Đối tác #' . $transaction->user_id;
        $bankInfo = ($transaction->bank_name ?? '') . ' - ' . ($transaction->bank_account_number ?? '') . ' (' . ($transaction->bank_account_name ?? '') . ')';
    @endphp

    <div class="dt-action-withdraw-group">
        {{-- Nút Duyệt chi trả --}}
        <button type="button"
                class="btn-action-withdraw btn-action-approve btn-approve-withdraw"
                data-id="{{ $transaction->id }}"
                data-code="{{ $transaction->code }}"
                data-amount="{{ $amountFmt }}"
                data-user="{{ $userFullname }}"
                data-bank="{{ $bankInfo }}"
                title="{{ __('Duyệt chuyển khoản chi trả') }}"
                data-bs-toggle="tooltip"
                data-bs-placement="top">
            <i class="ti ti-check"></i>
            <span>{{ __('Duyệt chi') }}</span>
        </button>

        {{-- Nút Từ chối rút tiền --}}
        <button type="button"
                class="btn-action-withdraw btn-action-reject btn-reject-withdraw"
                data-id="{{ $transaction->id }}"
                data-code="{{ $transaction->code }}"
                data-amount="{{ $amountFmt }}"
                data-user="{{ $userFullname }}"
                title="{{ __('Từ chối và hoàn tiền vào ví') }}"
                data-bs-toggle="tooltip"
                data-bs-placement="top">
            <i class="ti ti-x"></i>
            <span>{{ __('Từ chối') }}</span>
        </button>
    </div>
@elseif ($transaction->status === TransactionStatus::Confirmed)
    @php
        $note = $transaction->admin_note ?? __('Đã chi trả thành công');
    @endphp
    <span class="badge bg-success-lt text-success px-2.5 py-1.5 rounded-2 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs"
          title="{{ $note }}"
          data-bs-toggle="tooltip">
        <i class="ti ti-check-double fs-13"></i>
        <span>{{ __('Đã chi trả') }}</span>
    </span>
@elseif ($transaction->status === TransactionStatus::Refunded)
    @php
        $reason = $transaction->admin_note ?? __('Đã hoàn tiền');
    @endphp
    <span class="badge bg-danger-lt text-danger px-2.5 py-1.5 rounded-2 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-xs"
          title="{{ $reason }}"
          data-bs-toggle="tooltip">
        <i class="ti ti-arrow-back-up fs-13"></i>
        <span>{{ __('Đã từ chối') }}</span>
    </span>
@else
    <span class="text-muted fs-12">-</span>
@endif
