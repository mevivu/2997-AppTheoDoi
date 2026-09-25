<span class="text-muted fs-12 fw-medium">
    {{ $completed_at ? format_datetime($completed_at) : ($started_at ? format_datetime($started_at) : '--') }}
</span>
