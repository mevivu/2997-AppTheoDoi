@php
    $serviceEnum = is_string($service) ? \App\Enums\Transaction\TransactionEnumService::tryFrom($service) : $service;
    $serviceVal = $serviceEnum?->value ?? $service;
    $serviceName = $serviceEnum?->label() ?? \App\Enums\Transaction\TransactionEnumService::getDescription($service);
    $icon = match($serviceVal) {
        'GOOGLE_PLAY', 'google_play' => 'ti-brand-google-play text-success',
        'APPLE', 'apple_store' => 'ti-brand-apple text-dark',
        default => 'ti-wallet text-primary',
    };
@endphp

<span class="badge bg-light text-dark border px-2.5 py-1 fs-12 fw-medium d-inline-flex align-items-center gap-1.5 shadow-sm">
    <i class="ti {{ $icon }} fs-14"></i>
    <span>{{ $serviceName }}</span>
</span>
