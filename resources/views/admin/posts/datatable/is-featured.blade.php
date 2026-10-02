@php
    $isFeatured = false;
    if ($is_featured instanceof \App\Enums\FeaturedStatus) {
        $isFeatured = ($is_featured === \App\Enums\FeaturedStatus::Featured);
    } elseif (!empty($is_featured)) {
        $isFeatured = ((int) $is_featured === \App\Enums\FeaturedStatus::Featured->value || $is_featured === 1 || $is_featured === '1');
    }
@endphp

@if($isFeatured)
    <span class="badge bg-yellow-lt"><i class="ti ti-star-filled text-warning"></i> {{ __('Nổi bật') }}</span>
@else
    <span class="badge bg-secondary-lt">{{ __('Thường') }}</span>
@endif