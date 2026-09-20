@php
    $themeName = is_array($theme) ? ($theme['name'] ?? null) : ($theme->name ?? null);
@endphp

@if (!empty($themeName))
    <span class="badge bg-light text-dark border px-2 py-1 fs-12">
        <i class="ti ti-palette me-1 text-primary"></i> {{ $themeName }}
    </span>
@else
    <span class="text-muted">N/A</span>
@endif
