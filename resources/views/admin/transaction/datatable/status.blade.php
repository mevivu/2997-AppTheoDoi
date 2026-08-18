@php
    $statusEnum = is_string($status) ? \App\Enums\Transaction\TransactionStatus::tryFrom($status) : $status;
    $badgeClass = match ($statusEnum) {
        \App\Enums\Transaction\TransactionStatus::Pending => 'bg-warning-subtle text-warning border-warning-subtle',
        \App\Enums\Transaction\TransactionStatus::Confirmed => 'bg-success-subtle text-success border-success-subtle',
        \App\Enums\Transaction\TransactionStatus::Refunded => 'bg-danger-subtle text-danger border-danger-subtle',
        default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
    };
    $label = $statusEnum?->label() ?? \App\Enums\Transaction\TransactionStatus::getDescription($status);
@endphp

<span class="badge {{ $badgeClass }} border rounded-pill px-2.5 py-1 fs-12 fw-semibold d-inline-flex align-items-center gap-1.5">
    <span class="status-dot rounded-circle" style="width: 6px; height: 6px; background-color: currentColor;"></span>
    {{ $label }}
</span>
