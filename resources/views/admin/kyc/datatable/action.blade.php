@php
    use App\Enums\User\KycStatus;
    $status = $user->kyc_status ?? KycStatus::NOT_SUBMITTED;
    $hasCards = !empty($user->id_card_front) || !empty($user->id_card_back);
@endphp

<div class="dt-action-kyc-group">
    {{-- Nút Duyệt CCCD --}}
    @if ($status !== KycStatus::APPROVED && $hasCards)
        <button type="button" 
                class="btn-action-kyc btn-action-approve"
                onclick="openApproveKycModal('{{ $user->id }}', '{{ addslashes($user->fullname ?? '#' . $user->id) }}', '{{ $user->tax_code ?? '-' }}', '{{ addslashes($user->bank_account_name ?? '-') }}')"
                title="{{ __('Phê duyệt xác minh CCCD') }}">
            <i class="ti ti-check"></i>
            <span>{{ __('Duyệt') }}</span>
        </button>
    @elseif ($status === KycStatus::APPROVED)
        <span class="badge bg-success-lt text-success px-2 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1">
            <i class="ti ti-check"></i> {{ __('Đã duyệt') }}
        </span>
    @endif

    {{-- Nút Từ chối CCCD --}}
    @if ($status !== KycStatus::REJECTED && $hasCards)
        <button type="button" 
                class="btn-action-kyc btn-action-reject"
                onclick="openRejectKycModal('{{ $user->id }}', '{{ addslashes($user->fullname ?? '#' . $user->id) }}')"
                title="{{ __('Từ chối hồ sơ CCCD') }}">
            <i class="ti ti-x"></i>
            <span>{{ __('Từ chối') }}</span>
        </button>
    @endif

    {{-- Nút Lịch sử giao dịch rút tiền --}}
    <a href="{{ route('admin.transaction.withdraw') }}?user_id={{ $user->id }}" 
       target="_blank" 
       class="btn-action-kyc btn-action-history text-muted"
       title="{{ __('Xem các yêu cầu rút tiền của đối tác này') }}">
        <i class="ti ti-history"></i>
    </a>
</div>
