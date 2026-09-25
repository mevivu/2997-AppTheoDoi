@php
    $statusValue = $status instanceof \App\Enums\Memo\MemoCompetitionStatus ? $status->value : (string) $status;
    $now = \Carbon\Carbon::now();
    $endAt = !empty($end_at) ? \Carbon\Carbon::parse($end_at) : null;
    $startAt = !empty($start_at) ? \Carbon\Carbon::parse($start_at) : null;
    $isTimeEnded = $endAt && $endAt->isPast();
    $isTimeUpcoming = $startAt && $startAt->isFuture();
@endphp

@if ($statusValue === 'active' && !$isTimeEnded && !$isTimeUpcoming)
    <span class="badge bg-success text-white px-2 py-1 fs-11">
        <i class="ti ti-bolt me-1"></i>{{ __('Đang mở') }}
    </span>
@elseif ($statusValue === 'upcoming' || ($statusValue === 'active' && $isTimeUpcoming))
    <span class="badge bg-warning text-dark px-2 py-1 fs-11">
        <i class="ti ti-clock me-1"></i>{{ __('Sắp diễn ra') }}
    </span>
@elseif ($statusValue === 'ended' || $isTimeEnded)
    <span class="badge bg-secondary text-white px-2 py-1 fs-11">
        <i class="ti ti-flag-filled me-1"></i>{{ __('Đã kết thúc') }}
    </span>
    @if (!empty($ranking_calculated_at))
        <div class="text-success fs-10 mt-1">
            <i class="ti ti-check"></i> {{ __('Đã chốt BXH') }}
        </div>
    @endif
@elseif ($statusValue === 'cancelled')
    <span class="badge bg-danger text-white px-2 py-1 fs-11">
        <i class="ti ti-ban me-1"></i>{{ __('Đã hủy') }}
    </span>
@else
    <span class="badge bg-light text-muted border px-2 py-1 fs-11">
        <i class="ti ti-pencil me-1"></i>{{ __('Bản nháp') }}
    </span>
@endif
