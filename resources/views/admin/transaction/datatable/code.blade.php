@if ($code)
    <span class="d-inline-flex align-items-center gap-1.5 font-monospace fw-bold text-dark bg-light px-2 py-1 rounded border">
        <span>{{ $code }}</span>
        <i class="ti ti-copy copy-btn text-muted hover-primary" style="font-size: 15px; cursor: pointer;" title="Sao chép mã" data-value="{{ $code }}"></i>
        <i class="ti ti-check check-icon text-success" style="display:none; font-size: 15px"></i>
    </span>
@else
    <span class="text-muted fst-italic">N/A</span>
@endif

