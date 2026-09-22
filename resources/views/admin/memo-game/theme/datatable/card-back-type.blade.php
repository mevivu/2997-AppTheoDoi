@php
    $type = $card_back_type ?? 'theme';
    $backImg = (!empty($card_back) && $card_back !== \App\Traits\ImageSystem::DEFAULT_IMAGE) ? $card_back : null;
@endphp

<div class="d-flex justify-content-center align-items-center gap-1">
    @if ($type === 'logo')
        <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1 fs-12" title="{{ __('Mặt úp thẻ trên app sử dụng Logo ứng dụng') }}">
            <i class="ti ti-app-window me-1"></i>{{ __('Ảnh Logo') }}
        </span>
    @else
        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fs-12" title="{{ __('Mặt úp thẻ trên app sử dụng Ảnh mặt sau chủ đề') }}">
            <i class="ti ti-photo me-1"></i>{{ __('Ảnh Chủ đề') }}
        </span>
        @if ($backImg)
            <a href="{{ asset($backImg) }}" target="_blank" title="{{ __('Xem ảnh mặt sau') }}" class="ms-1 d-inline-block">
                <img src="{{ asset($backImg) }}" 
                     alt="Card Back" 
                     class="rounded border shadow-2xs object-fit-contain bg-white" 
                     style="width: 26px; height: 26px; padding: 1px;"
                     onerror="this.style.display='none';">
            </a>
        @endif
    @endif
</div>
