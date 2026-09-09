@php
    $type = $service_type instanceof \App\Enums\User\UserServiceType ? $service_type : \App\Enums\User\UserServiceType::tryFrom((int)$service_type);
@endphp
@if($type)
    <span @class(['badge', $type->badge()])>
        <i class="{{ $type->icon() }} me-1"></i>{{ $type->name() }}
    </span>
@else
    <span class="badge bg-secondary-lt">
        <i class="ti ti-mail me-1"></i>Email
    </span>
@endif
