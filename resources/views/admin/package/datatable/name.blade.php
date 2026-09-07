@php
    $discType = $discount_type ?? null;
    if ($discType instanceof \BackedEnum) {
        $discType = $discType->value;
    }
    $discVal = (float)($discount_value ?? 0);
    $origPrice = (float)($price ?? 0);
    $hasDisc = !empty($discType) && $discType !== 'none' && $discVal > 0;

    $calcFinalPrice = $final_price ?? $origPrice;
    if ($hasDisc && !isset($final_price)) {
        if ($discType === 'percent') {
            $calcFinalPrice = max(0, round($origPrice - ($origPrice * $discVal / 100)));
        } elseif ($discType === 'fixed') {
            $calcFinalPrice = max(0, round($origPrice - $discVal));
        }
    }

    $calcDiscountDisplay = $discount_display ?? '';
    if ($hasDisc && empty($calcDiscountDisplay)) {
        if ($discType === 'percent') {
            $calcDiscountDisplay = '-' . (int)$discVal . '%';
        } elseif ($discType === 'fixed') {
            $calcDiscountDisplay = '-' . number_format($discVal, 0, ',', '.') . ' đ';
        }
    }
@endphp
<div class="d-flex flex-column">
    <x-link target="_blank" :href="route('admin.package.edit', $id)" :title="$name" class="fw-bold text-dark"/>
    <div class="mt-1 d-flex align-items-center gap-1 flex-wrap">
        @if($hasDisc)
            <span class="text-decoration-line-through text-muted small">{{ format_price($origPrice) }}</span>
            <span class="text-success fw-bold small">{{ format_price($calcFinalPrice) }}</span>
            <span class="badge bg-danger-lt px-1 py-0 small">{{ $calcDiscountDisplay }}</span>
            @if(!empty($discount_code))
                <span class="badge bg-purple-lt px-1 py-0 small" title="Mã ưu đãi"><i class="ti ti-ticket"></i> {{ $discount_code }}</span>
            @endif
        @else
            <span class="text-muted small">{{ format_price($origPrice) }}</span>
        @endif
        <span class="badge bg-azure-lt px-1 py-0 small" title="Hạn mức thiết bị đăng nhập">
            <i class="ti ti-devices"></i> {{ $max_devices ?? 1 }} máy
        </span>
    </div>
</div>
