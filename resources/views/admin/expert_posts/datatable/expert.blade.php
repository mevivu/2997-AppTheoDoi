@php
    $expertName = is_array($expert ?? null) ? ($expert['name'] ?? null) : ($expert->name ?? null);
    $expertAvatar = is_array($expert ?? null) ? ($expert['avatar'] ?? null) : ($expert->avatar ?? null);
@endphp

@if(!empty($expertName))
    <div class="d-flex align-items-center gap-2">
        @if(!empty($expertAvatar))
            <img src="{{ asset($expertAvatar) }}" class="rounded-circle" style="width: 28px; height: 28px; object-fit: cover;">
        @endif
        <span class="fw-medium text-dark">{{ $expertName }}</span>
    </div>
@else
    <span class="text-muted fs-12">—</span>
@endif
