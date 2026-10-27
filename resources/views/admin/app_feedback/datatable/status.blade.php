@php
    use App\Enums\Feedback\AppFeedbackStatus;
    $status = $feedback->status ?? AppFeedbackStatus::PENDING;
@endphp

<div class="text-center py-1">
    @if ($status === AppFeedbackStatus::PENDING)
        <span class="badge bg-warning text-white px-2.5 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
            <span class="spinner-grow spinner-grow-sm" style="width: 6px; height: 6px;" role="status"></span>
            {{ __('Chờ duyệt') }}
        </span>
    @elseif ($status === AppFeedbackStatus::APPROVED)
        <span class="badge bg-info text-white px-2.5 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
            <i class="ti ti-circle-check fs-13"></i>
            {{ __('Đã duyệt') }}
        </span>
    @elseif ($status === AppFeedbackStatus::VIP_GRANTED)
        <span class="badge bg-success text-white px-2.5 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
            <i class="ti ti-crown fs-13"></i>
            {{ __('Đã cấp VIP') }}
        </span>
        @if ($feedback->grantedPackage)
            <div class="text-success fw-semibold fs-11 mt-1">
                {{ $feedback->grantedPackage->name }} ({{ number_format((float) $feedback->grantedPackage->price, 0, ',', '.') }}đ)
            </div>
        @endif
        @if ($feedback->vip_granted_at)
            <div class="text-muted fs-10 mt-0.5 text-nowrap">
                <i class="ti ti-calendar-check me-0.5"></i>{{ format_datetime($feedback->vip_granted_at) }}
            </div>
        @endif
    @elseif ($status === AppFeedbackStatus::REJECTED)
        <span class="badge bg-danger text-white px-2.5 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1 shadow-2xs">
            <i class="ti ti-circle-x fs-13"></i>
            {{ __('Từ chối') }}
        </span>
    @endif
</div>
