<div class="d-flex justify-content-center align-items-center">
    @if (!empty($image) && file_exists(public_path($image)))
        <a href="{{ asset($image) }}" target="_blank" title="{{ __('Xem ảnh gốc') }}" class="memo-card-thumb-link">
            <img src="{{ asset($image) }}" alt="{{ $name ?? 'Card' }}" class="memo-card-thumb shadow-xs">
        </a>
    @elseif(!empty($image))
        <a href="{{ asset($image) }}" target="_blank" title="{{ __('Xem ảnh gốc') }}" class="memo-card-thumb-link">
            <img src="{{ asset($image) }}" alt="{{ $name ?? 'Card' }}" class="memo-card-thumb shadow-xs" onerror="this.onerror=null;this.parentElement.innerHTML='<div class=\'memo-card-thumb-placeholder\'><i class=\'ti ti-photo-off\'></i></div>'">
        </a>
    @else
        <div class="memo-card-thumb-placeholder" title="{{ __('Chưa có ảnh') }}">
            <i class="ti ti-photo-off"></i>
        </div>
    @endif
</div>
