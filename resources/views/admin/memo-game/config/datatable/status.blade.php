@php
    $statusEnum = $status instanceof \App\Enums\ActiveStatus
        ? $status
        : \App\Enums\ActiveStatus::tryFrom($status);

    $badgeClass = match ($statusEnum) {
        \App\Enums\ActiveStatus::Active => 'bg-green-lt text-green',
        \App\Enums\ActiveStatus::Deleted => 'bg-red-lt text-red',
        \App\Enums\ActiveStatus::Draft => 'bg-yellow-lt text-yellow',
        default => 'bg-secondary-lt text-secondary',
    };

    $icon = match ($statusEnum) {
        \App\Enums\ActiveStatus::Active => 'ti ti-circle-check',
        \App\Enums\ActiveStatus::Deleted => 'ti ti-circle-x',
        \App\Enums\ActiveStatus::Draft => 'ti ti-clock-pause',
        default => 'ti ti-info-circle',
    };
@endphp
<span class="badge {{ $badgeClass }} px-2.5 py-1.5 fs-12 fw-semibold rounded-pill d-inline-flex align-items-center">
    <i class="{{ $icon }} me-1 fs-13"></i> {{ $statusEnum?->description() ?? $status }}
</span>
