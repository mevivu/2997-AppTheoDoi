@if ($child)
    <div class="d-flex align-items-center justify-content-center gap-2">
        <span class="avatar avatar-xs rounded-circle bg-blue-subtle text-primary fw-bold">
            {{ mb_substr($child->fullname ?? $child->name ?? 'B', 0, 1) }}
        </span>
        <div class="text-start">
            <span class="fw-bold d-block">{{ $child->fullname ?? $child->name ?? 'N/A' }}</span>
            @if(!empty($child->user))
                <small class="text-muted fs-11">PH: {{ $child->user->fullname ?? $child->user->phone ?? '' }}</small>
            @endif
        </div>
    </div>
@else
    <span class="text-muted">N/A</span>
@endif
