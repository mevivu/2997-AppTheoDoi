@php
    $enumStatus = \App\Enums\ActiveStatus::tryFrom($status);
    $isActive = $enumStatus === \App\Enums\ActiveStatus::Active || (int)$status === 1;
@endphp
@if($isActive)
    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 fw-bold fs-12">
        <span class="badge-dot bg-success me-1 d-inline-block" style="width:6px;height:6px;border-radius:50%;"></span>{{ \App\Enums\ActiveStatus::getDescription($status) }}
    </span>
@else
    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2 py-1 fw-bold fs-12">
        <span class="badge-dot bg-danger me-1 d-inline-block" style="width:6px;height:6px;border-radius:50%;"></span>{{ \App\Enums\ActiveStatus::getDescription($status) }}
    </span>
@endif
