@php
    $entryStatusVal = $status instanceof \App\Enums\Memo\MemoCompetitionEntryStatus ? $status->value : (string) $status;
@endphp
@if ($is_valid)
    <span class="badge bg-success-lt text-success border border-success-subtle fw-bold px-2 py-1"><i class="ti ti-check me-1"></i>{{ __('Đủ điều kiện') }}</span>
@elseif ($entryStatusVal === 'completed')
    <span class="badge bg-danger-lt text-danger border border-danger-subtle fw-bold px-2 py-1"><i class="ti ti-x me-1"></i>{{ __('Chưa đạt (4/4)') }}</span>
@elseif ($entryStatusVal === 'abandoned')
    <span class="badge bg-secondary-lt text-secondary border border-secondary-subtle fw-bold px-2 py-1"><i class="ti ti-door-exit me-1"></i>{{ __('Bỏ cuộc') }}</span>
@else
    <span class="badge bg-warning-lt text-warning border border-warning-subtle fw-bold px-2 py-1"><i class="ti ti-clock me-1"></i>{{ __('Đang thi') }}</span>
@endif
