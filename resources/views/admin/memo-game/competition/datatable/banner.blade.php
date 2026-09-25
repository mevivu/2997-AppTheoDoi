@if (!empty($banner_image))
    <div class="d-inline-block position-relative comp-dt-banner-wrap" style="cursor: pointer;">
        <a href="{{ asset($banner_image) }}" target="_blank" class="d-block text-decoration-none" title="{{ __('Nhấp để xem ảnh banner gốc') }}">
            <div class="comp-dt-banner-thumb rounded-2 border overflow-hidden position-relative shadow-xs" 
                 style="width: 72px; aspect-ratio: 16/9; background: #f1f5f9;">
                <img src="{{ asset($banner_image) }}" 
                     alt="{{ $name }}" 
                     class="w-100 h-100" 
                     style="object-fit: cover; transition: transform 0.25s ease;"
                     onerror="this.onerror=null; this.parentElement.innerHTML='<div class=\'w-100 h-100 d-flex align-items-center justify-content-center bg-light text-muted\'><i class=\'ti ti-photo-off\'></i></div>';">
                
                {{-- Mini Badge 16:9 --}}
                <span class="position-absolute bottom-0 end-0 bg-dark text-white px-1 py-0" 
                      style="font-size: 8px; opacity: 0.85; border-top-left-radius: 4px; line-height: 1.2;">
                    16:9
                </span>

                {{-- Hover zoom icon --}}
                <div class="comp-dt-banner-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center text-white"
                     style="background: rgba(15, 23, 42, 0.4); opacity: 0; transition: opacity 0.2s ease;">
                    <i class="ti ti-zoom-in fs-12"></i>
                </div>
            </div>
        </a>
    </div>
@else
    <div class="comp-dt-banner-empty rounded-2 border d-inline-flex flex-column align-items-center justify-content-center text-muted shadow-xs" 
         style="width: 72px; aspect-ratio: 16/9; background: linear-gradient(135deg, #fefce8 0%, #fef3c7 100%); border-color: #fde68a !important;"
         title="{{ __('Chưa có ảnh banner (Sử dụng đồ họa mặc định)') }}">
        <i class="ti ti-trophy text-warning" style="font-size: 15px; line-height: 1;"></i>
        <span class="text-secondary fw-semibold mt-0.5" style="font-size: 8px; letter-spacing: 0.3px;">MEMO</span>
    </div>
@endif
