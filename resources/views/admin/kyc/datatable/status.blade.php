@php
    use App\Enums\User\KycStatus;
    $status = $user->kyc_status ?? KycStatus::NOT_SUBMITTED;
@endphp

<div class="text-center py-1">
    @if ($status === KycStatus::PENDING)
        <span class="badge bg-warning text-white px-2 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
            <span class="spinner-grow spinner-grow-sm" style="width: 6px; height: 6px;" role="status"></span>
            {{ __('Chờ duyệt') }}
        </span>
    @elseif ($status === KycStatus::APPROVED)
        <span class="badge bg-success text-white px-2 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
            <i class="ti ti-circle-check fs-13"></i>
            {{ __('Đã duyệt') }}
        </span>
        @if ($user->kyc_verified_at)
            <div class="text-muted fs-10 mt-0.5 text-nowrap">
                {{ format_date($user->kyc_verified_at) }}
            </div>
        @endif
    @elseif ($status === KycStatus::REJECTED)
        <span class="badge bg-danger text-white px-2 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
            <i class="ti ti-circle-x fs-13"></i>
            {{ __('Từ chối') }}
        </span>
        @if (!empty($user->kyc_rejection_reason))
            <div class="text-danger fs-11 mt-1 fst-italic text-start px-1" 
                 style="max-width: 150px; margin: 0 auto; line-height: 1.3;"
                 data-bs-toggle="tooltip" 
                 title="{{ $user->kyc_rejection_reason }}">
                <i class="ti ti-info-circle me-0.5"></i>{{ \Illuminate\Support\Str::limit($user->kyc_rejection_reason, 35) }}
            </div>
        @endif
    @else
        <span class="badge bg-secondary-lt text-secondary px-2 py-1 fs-11">
            {{ __('Chưa gửi') }}
        </span>
    @endif
</div>
