<div class="text-center">
    @if(!empty($is_sale))
        <span class="badge bg-danger-lt py-1 px-2 fs-6" title="Gói khuyến mãi / Sale đặc biệt">
            <i class="ti ti-flame me-1"></i> Gói sale
        </span>
    @else
        <span class="badge bg-blue-lt py-1 px-2 fs-6" title="Gói tiêu chuẩn thường">
            <i class="ti ti-circle-check me-1"></i> Gói thường
        </span>
    @endif
</div>
