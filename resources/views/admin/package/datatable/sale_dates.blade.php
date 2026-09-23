<div class="text-center">
    @if(empty($is_sale))
        <span class="text-muted fst-italic fs-6">—</span>
    @else
        @php
            $start = !empty($sale_start_at) ? \Carbon\Carbon::parse($sale_start_at) : null;
            $end = !empty($sale_end_at) ? \Carbon\Carbon::parse($sale_end_at) : null;
            $now = now();
            
            if ($start && $now->lt($start)) {
                $saleState = 'upcoming';
                $badgeClass = 'bg-warning-lt';
                $badgeIcon = 'ti-clock';
                $badgeText = 'Sắp diễn ra';
            } elseif ($end && $now->gt($end)) {
                $saleState = 'expired';
                $badgeClass = 'bg-secondary-lt';
                $badgeIcon = 'ti-clock-off';
                $badgeText = 'Đã hết hạn';
            } else {
                $saleState = 'active';
                $badgeClass = 'bg-success-lt';
                $badgeIcon = 'ti-check';
                $badgeText = 'Đang sale';
            }
        @endphp

        <div class="d-inline-flex flex-column align-items-center">
            <span class="badge {{ $badgeClass }} py-1 px-2 mb-1" style="font-size: 0.75rem;">
                <i class="ti {{ $badgeIcon }} me-1"></i> {{ $badgeText }}
            </span>
            <div class="text-muted small" style="font-size: 0.8rem; line-height: 1.3;">
                @if($start && $end)
                    <div><span class="fw-semibold">Từ:</span> {{ $start->format('d/m/Y H:i') }}</div>
                    <div><span class="fw-semibold">Đến:</span> {{ $end->format('d/m/Y H:i') }}</div>
                @elseif($start)
                    <div><span class="fw-semibold">Từ:</span> {{ $start->format('d/m/Y H:i') }}</div>
                @elseif($end)
                    <div><span class="fw-semibold">Đến:</span> {{ $end->format('d/m/Y H:i') }}</div>
                @else
                    <span>Không giới hạn</span>
                @endif
            </div>
        </div>
    @endif
</div>
