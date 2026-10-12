@if($milestone->is_active)
    <span class="badge bg-success-lt text-success fw-bold">
        <i class="ti ti-check me-1"></i>Đang bật
    </span>
@else
    <span class="badge bg-secondary-lt text-muted fw-bold">
        <i class="ti ti-x me-1"></i>Đang tắt
    </span>
@endif
