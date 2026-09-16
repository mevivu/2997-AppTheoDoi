@if ($theme)
    <span class="badge bg-light text-dark border px-2 py-1 fs-12">
        <i class="ti ti-palette me-1 text-primary"></i> {{ $theme->name }}
    </span>
@else
    <span class="text-muted">N/A</span>
@endif
