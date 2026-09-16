@php
    $statusValue = $status instanceof \App\Enums\ActiveStatus ? $status->value : $status;
    $statusEnum = \App\Enums\ActiveStatus::tryFrom($statusValue) ?? \App\Enums\ActiveStatus::Active;
@endphp
<span @class([
    'badge',
    $statusEnum->badge()
])>
    {{ \App\Enums\ActiveStatus::getDescription($statusValue) }}
</span>
