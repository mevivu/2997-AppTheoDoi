@php
    $accessEnum = is_string($access_type) ? \App\Enums\Lesson\LessonAccessType::tryFrom($access_type) : $access_type;
@endphp
@if(($accessEnum?->value ?? $access_type) === 'vip')
    <span class="badge bg-yellow text-dark fw-bold px-2 py-1">
        <i class="ti ti-crown me-1"></i>VIP
    </span>
@else
    <span class="badge bg-green-lt fw-semibold px-2 py-1">
        <i class="ti ti-gift me-1"></i>FREE
    </span>
@endif
