<div class="text-start py-1 fs-12" style="line-height: 1.45;">
    {{-- Mã số thuế (MST) --}}
    <div class="mb-1 d-flex align-items-center gap-1">
        <span class="text-muted fs-11 text-nowrap"><i class="ti ti-receipt-tax me-0.5 text-danger"></i>MST:</span>
        @if (!empty($user->tax_code))
            <span class="fw-bold font-monospace text-dark bg-light px-1.5 py-0.5 rounded border fs-12">
                {{ $user->tax_code }}
            </span>
        @else
            <span class="badge bg-secondary-lt fs-10 text-muted">{{ __('Chưa có') }}</span>
        @endif
    </div>

    {{-- Tài khoản ngân hàng --}}
    @if (!empty($user->bank_account_number) || !empty($user->bank_name))
        <div class="pt-1 border-top border-light-subtle">
            <span class="fw-semibold text-primary d-inline-flex align-items-center gap-1">
                <i class="ti ti-building-bank fs-13"></i>
                <span class="text-truncate" style="max-width: 170px;" title="{{ $user->bank_name }}">{{ $user->bank_name ?? 'Ngân hàng' }}</span>
            </span>
            <div class="font-monospace fw-bold text-dark fs-12">
                {{ $user->bank_account_number ?? '-' }}
            </div>
            <div class="text-uppercase fw-bold text-dark fs-11 d-flex align-items-center gap-1 mt-0.5">
                <i class="ti ti-user-check text-success fs-13"></i>
                <span>{{ $user->bank_account_name ?? '-' }}</span>
            </div>
        </div>
    @else
        <div class="text-muted fst-italic fs-11 pt-1 border-top border-light-subtle">
            <i class="ti ti-alert-circle me-0.5 text-secondary"></i>{{ __('Chưa thêm thông tin ngân hàng') }}
        </div>
    @endif
</div>
