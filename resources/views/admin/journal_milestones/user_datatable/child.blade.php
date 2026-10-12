@php
    use App\Traits\RouteAdminSystem;
@endphp
@if ($child)
    <div class="d-flex align-items-center gap-2">
        <span class="avatar avatar-sm rounded-circle bg-azure-lt">
            @if ($child->avatar)
                <img src="{{ asset($child->avatar) }}" alt="{{ $child->fullname }}" class="rounded-circle" style="width: 32px; height: 32px; object-fit: cover;">
            @else
                <i class="ti ti-baby-carriage"></i>
            @endif
        </span>
        <div>
            <x-link target="_blank" :href="RouteAdminSystem::childDetailUrl($child)" :title="$child->fullname" class="fw-semibold text-primary" />
            <div class="text-muted small">
                {{ $child->birthday ? 'Sinh: ' . format_date($child->birthday) : 'Chưa có ngày sinh' }}
            </div>
        </div>
    </div>
@else
    <span class="text-muted">N/A</span>
@endif
