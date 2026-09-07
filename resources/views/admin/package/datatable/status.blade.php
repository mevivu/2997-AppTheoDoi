@php
    $statusEnum = $status instanceof \App\Enums\Package\PackageStatus 
        ? $status 
        : (\App\Enums\Package\PackageStatus::tryFrom($status) ?? \App\Enums\ActiveStatus::tryFrom($status));
@endphp
@if($statusEnum)
    <span @class(['badge', $statusEnum->badge()])>{{ $statusEnum->description() }}</span>
@else
    <span class="badge bg-secondary">{{ $status }}</span>
@endif

