<div class="d-flex align-items-center gap-2">
    <span class="avatar avatar-sm rounded-circle bg-light border text-primary fw-bold" style="font-size: 1.1rem; width: 34px; height: 34px;">
        {{ $milestone->icon ?: '🚩' }}
    </span>
    <div>
        <div class="fw-semibold text-dark">{{ $milestone->name }}</div>
        <div class="text-muted small">
            <span class="badge bg-light text-secondary border-0 px-1 py-0">{{ $milestone->age_from_month }}–{{ $milestone->age_to_month }} tháng</span>
        </div>
    </div>
</div>
