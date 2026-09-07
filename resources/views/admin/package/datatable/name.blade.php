<div class="d-flex flex-column">
    <x-link target="_blank" :href="route('admin.package.edit', $id)" :title="$name" class="fw-bold text-dark"/>
    <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
        @if(isset($discount_type) && $discount_type !== \App\Enums\Package\PackageDiscountType::None && $discount_value > 0)
            <span class="text-decoration-line-through text-muted small">{{ format_price($price) }}</span>
            <span class="text-success fw-bold small">{{ format_price($final_price) }}</span>
            <span class="badge bg-danger-lt px-1 py-0 small">{{ $discount_display }}</span>
            @if(!empty($discount_code))
                <span class="badge bg-purple-lt px-1 py-0 small" title="Mã ưu đãi"><i class="ti ti-ticket"></i> {{ $discount_code }}</span>
            @endif
        @else
            <span class="text-muted small">{{ format_price($price) }}</span>
        @endif
    </div>
</div>
