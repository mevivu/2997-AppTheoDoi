@php
    $type = $card_back_type ?? 'theme';
@endphp

<div class="d-flex justify-content-center align-items-center">
    @if ($type === 'logo')
        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fs-12 text-nowrap" title="{{ __('Mặt úp thẻ trên app sử dụng Logo ứng dụng') }}">
            <i class="ti ti-app-window me-1"></i>{{ __('Ảnh Logo') }}
        </span>
    @else
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12 text-nowrap" title="{{ __('Mặt úp thẻ trên app sử dụng Ảnh chủ đề') }}">
            <i class="ti ti-photo me-1"></i>{{ __('Ảnh chủ đề') }}
        </span>
    @endif
</div>
