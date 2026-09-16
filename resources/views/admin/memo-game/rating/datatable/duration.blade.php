<span class="badge bg-light text-dark border px-2 py-1 fs-12">
    <i class="ti ti-clock me-1 text-primary"></i>
    {{ floor($total_duration_spent / 60) > 0 ? floor($total_duration_spent / 60) . 'm ' : '' }}
    {{ ($total_duration_spent % 60) . 's' }}
</span>
