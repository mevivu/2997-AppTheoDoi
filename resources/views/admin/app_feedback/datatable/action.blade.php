@php
    use App\Enums\Feedback\AppFeedbackStatus;
    $status = $feedback->status ?? AppFeedbackStatus::PENDING;
    $fullname = $feedback->user?->fullname ?? ('User #' . $feedback->user_id);
    $email = $feedback->email ?: ($feedback->user?->decrypted_email ?? '-');
@endphp

<div class="dt-action-feedback-group">
    @if ($status !== AppFeedbackStatus::VIP_GRANTED)
        {{-- Nút Duyệt & Cấp VIP --}}
        <button type="button"
                class="btn-action-fb btn-action-vip"
                data-id="{{ $feedback->id }}"
                data-userid="{{ $feedback->user_id }}"
                data-fullname="{{ $fullname }}"
                data-email="{{ $email }}"
                data-rating="{{ $feedback->rating }}"
                data-feature="{{ $feedback->favorite_feature }}"
                onclick="openGrantVipModal(this)"
                title="{{ __('Duyệt & Cấp gói VIP cho User ID này') }}">
            <i class="ti ti-crown"></i>
            <span>{{ __('Cấp VIP') }}</span>
        </button>

        @if ($status !== AppFeedbackStatus::APPROVED)
            {{-- Nút Đã duyệt --}}
            <button type="button"
                    class="btn-action-fb btn-action-approve"
                    onclick="updateFeedbackStatus({{ $feedback->id }}, 'approved', {{ $feedback->user_id }})"
                    title="{{ __('Chuyển trạng thái sang Đã duyệt') }}">
                <i class="ti ti-check"></i>
                <span>{{ __('Duyệt') }}</span>
            </button>
        @endif

        @if ($status !== AppFeedbackStatus::REJECTED)
            {{-- Nút Từ chối --}}
            <button type="button"
                    class="btn-action-fb btn-action-reject"
                    onclick="updateFeedbackStatus({{ $feedback->id }}, 'rejected', {{ $feedback->user_id }})"
                    title="{{ __('Từ chối đánh giá này') }}">
                <i class="ti ti-x"></i>
                <span>{{ __('Từ chối') }}</span>
            </button>
        @endif
    @else
        <span class="badge bg-success-lt text-success px-2.5 py-1 fs-11 fw-semibold d-inline-flex align-items-center gap-1">
            <i class="ti ti-circle-check"></i> {{ __('Hoàn tất cấp VIP') }}
        </span>
    @endif
</div>
