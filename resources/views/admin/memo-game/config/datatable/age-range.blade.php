<span class="badge bg-blue-lt px-2.5 py-1.5 fs-12 fw-semibold rounded-pill d-inline-flex align-items-center">
    <i class="ti ti-calendar me-1 fs-13"></i>
    @if(!empty($max_age))
        {{ $min_age }} - {{ $max_age }} {{ __('tuổi') }}
    @else
        {{ __('Từ') }} {{ $min_age }} {{ __('tuổi trở lên') }}
    @endif
</span>
