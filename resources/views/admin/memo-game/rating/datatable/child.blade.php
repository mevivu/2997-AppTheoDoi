@php
    $childData = $child ?? null;
    $fullname = is_array($childData) ? ($childData['fullname'] ?? $childData['name'] ?? null) : ($childData->fullname ?? $childData->name ?? null);
    $user = is_array($childData) ? ($childData['user'] ?? null) : ($childData->user ?? null);
    $parentName = is_array($user) ? ($user['fullname'] ?? $user['phone'] ?? null) : ($user->fullname ?? $user->phone ?? null);
@endphp

@if (!empty($childData) && !empty($fullname))
    <div class="d-flex align-items-center justify-content-center gap-2">
        <span class="avatar avatar-xs rounded-circle bg-blue-subtle text-primary fw-bold">
            {{ mb_substr($fullname, 0, 1) }}
        </span>
        <div class="text-start">
            <span class="fw-bold d-block">{{ $fullname }}</span>
            @if(!empty($parentName))
                <small class="text-muted fs-11">PH: {{ $parentName }}</small>
            @endif
        </div>
    </div>
@else
    <span class="text-muted">N/A</span>
@endif
