@php
    $typeEnum = $type instanceof \App\Enums\Memo\MemoConfigType
        ? $type
        : \App\Enums\Memo\MemoConfigType::tryFrom((string)$type);

    $badgeClass = match ($typeEnum) {
        \App\Enums\Memo\MemoConfigType::IqTest => 'bg-info-lt text-info border border-info border-opacity-25',
        \App\Enums\Memo\MemoConfigType::Competition => 'bg-primary-lt text-primary border border-primary border-opacity-25',
        default => 'bg-secondary-lt text-secondary',
    };

    $icon = match ($typeEnum) {
        \App\Enums\Memo\MemoConfigType::IqTest => 'ti ti-brain',
        \App\Enums\Memo\MemoConfigType::Competition => 'ti ti-trophy',
        default => 'ti ti-category',
    };
@endphp
<span class="badge {{ $badgeClass }} px-2.5 py-1.5 fs-12 fw-semibold rounded-pill d-inline-flex align-items-center">
    <i class="{{ $icon }} me-1 fs-13"></i> {{ $typeEnum?->label() ?? ($type ?: 'Bài kiểm tra IQ') }}
</span>
