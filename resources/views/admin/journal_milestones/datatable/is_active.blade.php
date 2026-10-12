@if($milestone->is_active)
    <span class="badge bg-success-lt text-success fw-bold px-2.5 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1">
        <span class="status-dot status-dot-animated bg-success me-1"></span>
        <span>Đang bật</span>
    </span>
@else
    <span class="badge bg-secondary-lt text-muted fw-bold px-2.5 py-1.5 rounded-pill shadow-xs d-inline-flex align-items-center gap-1">
        <i class="ti ti-eye-off me-1"></i>
        <span>Đang ẩn</span>
    </span>
@endif
