@php
    $imagePath = null;
    $candidates = [
        $icon ?? null,
        $card_back ?? null,
    ];
    foreach ($candidates as $cand) {
        if (!empty($cand) && $cand !== \App\Traits\ImageSystem::DEFAULT_IMAGE) {
            $imagePath = $cand;
            break;
        }
    }
@endphp

<div class="d-flex justify-content-center align-items-center">
    @if ($imagePath)
        <a href="{{ asset($imagePath) }}" target="_blank" title="{{ __('Xem ảnh') }}" class="d-inline-block">
            <img src="{{ asset($imagePath) }}" 
                 alt="{{ $name ?? 'Theme' }}" 
                 class="rounded border shadow-2xs object-fit-contain bg-white" 
                 style="width: 42px; height: 42px; padding: 2px;"
                 onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'rounded bg-light border d-flex align-items-center justify-content-center text-primary\' style=\'width: 42px; height: 42px;\'><i class=\'ti ti-palette fs-4\'></i></div>';">
        </a>
    @else
        <div class="rounded bg-light border d-flex align-items-center justify-content-center text-primary" style="width: 42px; height: 42px;">
            @if (($code ?? '') === 'vehicles')
                <i class="ti ti-car fs-3"></i>
            @elseif (($code ?? '') === 'flowers')
                <i class="ti ti-flower fs-3"></i>
            @elseif (($code ?? '') === 'numbers')
                <i class="ti ti-numbers fs-3"></i>
            @elseif (($code ?? '') === 'flags')
                <i class="ti ti-flag fs-3"></i>
            @else
                <i class="ti ti-palette fs-3"></i>
            @endif
        </div>
    @endif
</div>
