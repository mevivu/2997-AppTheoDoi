@if(!empty($is_featured))
    <span class="badge bg-yellow-lt"><i class="ti ti-star-filled text-warning"></i> {{ __('Nổi bật') }}</span>
@else
    <span class="badge bg-secondary-lt">{{ __('Thường') }}</span>
@endif
