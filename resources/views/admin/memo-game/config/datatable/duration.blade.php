<span class="badge bg-orange-lt px-2.5 py-1.5 fs-12 fw-semibold rounded-pill d-inline-flex align-items-center">
    <i class="ti ti-clock me-1 fs-13"></i>
    {{ floor($total_duration / 60) > 0 ? floor($total_duration / 60) . ' phút' : '' }}{{ $total_duration % 60 > 0 ? ' ' . ($total_duration % 60) . 's' : '' }}/ván
    <span class="opacity-75 ms-1 fw-normal">({{ $total_duration }}s)</span>
</span>
