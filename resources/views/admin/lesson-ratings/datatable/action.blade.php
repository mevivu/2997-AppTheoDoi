<button type="button" 
        class="btn btn-sm btn-outline-primary btn-rating-detail d-inline-flex align-items-center gap-1 shadow-none"
        data-id="{{ $id }}"
        data-name="{{ $name }}"
        data-total="{{ $total_ratings ?? 0 }}">
    <i class="ti ti-chart-dots fs-16"></i>
    <span>{{ __('Chi tiết') }}</span>
</button>
