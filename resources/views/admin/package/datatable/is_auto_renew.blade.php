<div class="text-center">
    @if(!empty($is_auto_renew))
        <span class="badge bg-green-lt py-1 px-2 fs-6" title="Gói tự động gia hạn định kỳ (Subscription)">
            <i class="ti ti-refresh me-1"></i> Tự động gia hạn
        </span>
    @else
        <span class="badge bg-secondary-lt py-1 px-2 fs-6" title="Gói mua một lần (Không tự động gia hạn)">
            <i class="ti ti-clock-pause me-1"></i> Một lần
        </span>
    @endif
</div>
