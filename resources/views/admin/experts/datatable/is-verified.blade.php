@if(!empty($is_verified))
    <span class="badge bg-blue-lt"><i class="ti ti-discount-check text-blue"></i> {{ __('Đã xác thực') }}</span>
@else
    <span class="badge bg-secondary-lt">{{ __('Chưa') }}</span>
@endif
