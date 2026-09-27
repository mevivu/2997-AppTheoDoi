@php
    $statusEnum = $status instanceof \App\Enums\ActiveStatus ? $status : \App\Enums\ActiveStatus::tryFrom((string) $status);
@endphp
<span class="badge {{ $statusEnum?->badge() ?? 'bg-secondary' }}">
    {{ $statusEnum?->description() ?? $status }}
</span>
