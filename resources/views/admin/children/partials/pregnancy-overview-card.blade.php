{{-- Partial Thẻ Theo Dõi Thai Kỳ trên Admin CMS khớp 100% với thiết kế mẫu --}}
<div class="pregnancy-admin-card position-relative overflow-hidden">
    {{-- Tim hồng trang trí nền --}}
    <div class="position-absolute" style="top: 10px; right: 18px; opacity: 0.35; transform: rotate(18deg); color: #fb7185; pointer-events: none;">
        <i class="ti ti-heart-filled fs-3"></i>
    </div>
    <div class="position-absolute" style="top: 55px; left: 34%; opacity: 0.3; transform: rotate(-15deg); color: #fb7185; pointer-events: none;">
        <i class="ti ti-heart-filled fs-4"></i>
    </div>
    <div class="position-absolute" style="bottom: 40px; left: 38%; opacity: 0.35; transform: rotate(12deg); color: #fb7185; pointer-events: none;">
        <i class="ti ti-heart-filled fs-5"></i>
    </div>

    <div class="row g-2 align-items-end h-100 position-relative" style="z-index: 2;">
        {{-- Bên trái: Hình ảnh mẹ bầu trong váy mint --}}
        <div class="col-4 col-sm-5 col-md-4 text-start d-flex align-items-end justify-content-center">
            <img src="{{ asset('images/pregnancy/pregnancy_header_mother.png') }}" 
                 alt="Mẹ bầu mang thai" 
                 class="img-fluid"
                 style="max-height: 185px; width: auto; object-fit: contain; filter: drop-shadow(0 4px 8px rgba(15, 118, 110, 0.12));">
        </div>

        {{-- Bên phải: Khối thông tin tuần thai & tiêu chuẩn --}}
        <div class="col-8 col-sm-7 col-md-8 ps-2 pe-3 pb-2">
            {{-- Ngày dự sinh --}}
            <div class="d-flex align-items-center mb-1 text-muted fs-12 fw-semibold">
                <i class="ti ti-calendar-event me-1 text-teal fs-14"></i>
                <span>{{ __('Dự sinh') }}: <strong class="text-dark">{{ $pregnancyOverview['dueDate'] }}</strong></span>
            </div>

            {{-- Tuần thai nổi bật --}}
            <div class="pregnancy-week-title mb-1">
                {{ $pregnancyOverview['weekDisplay'] }}
            </div>

            {{-- Huy hiệu đếm ngược ngày chào đời --}}
            <div class="d-inline-flex align-items-center pregnancy-countdown-badge px-2 py-1 mb-2">
                <i class="ti ti-heart-filled me-1 fs-12 text-pink"></i>
                <span class="fs-11 fw-bold text-teal">
                    @if($pregnancyOverview['daysRemaining'] > 0)
                        {{ __('Còn') }} {{ $pregnancyOverview['daysRemaining'] }} {{ __('ngày chào đời') }}
                    @elseif($pregnancyOverview['daysRemaining'] === 0)
                        {{ __('Hôm nay là ngày dự sinh') }}
                    @else
                        {{ __('Đến ngày dự sinh') }}
                    @endif
                </span>
            </div>

            {{-- Thẻ Tiêu Chuẩn --}}
            <div class="pregnancy-standard-box p-2">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="fw-bolder text-dark fs-12 mb-1">
                            {{ __('Tiêu chuẩn:') }}
                        </div>
                        <div class="d-flex align-items-center mb-1 fs-11 text-muted">
                            <i class="ti ti-ruler-2 me-1 text-teal"></i>
                            <span>{{ __('Dài') }}: <strong class="text-dark">{{ $pregnancyOverview['standard']['length'] !== null ? $pregnancyOverview['standard']['length'] . ' cm' : '-- cm' }}</strong></span>
                        </div>
                        <div class="d-flex align-items-center fs-11 text-muted">
                            <i class="ti ti-scale me-1 text-teal"></i>
                            <span>{{ __('Nặng') }}: <strong class="text-dark">{{ $pregnancyOverview['standard']['weight'] !== null ? $pregnancyOverview['standard']['weight'] . ' g' : '-- g' }}</strong></span>
                        </div>
                    </div>

                    {{-- Ảnh 3D thai nhi --}}
                    <div class="text-center ps-2">
                        <img src="{{ asset('images/pregnancy/pregnancy_standard_fetus.png') }}" 
                             alt="Thai nhi theo tuần" 
                             style="width: 48px; height: 48px; object-fit: contain; filter: drop-shadow(0 2px 6px rgba(15, 118, 110, 0.15));">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.pregnancy-admin-card {
    background: linear-gradient(135deg, #dcf7f4 0%, #e8faf7 50%, #f4fbfb 100%);
    border-radius: 18px;
    border: 1px solid rgba(13, 148, 136, 0.18);
    box-shadow: 0 4px 20px rgba(15, 118, 110, 0.08);
    padding: 8px 6px 4px 6px;
    min-height: 175px;
}
.pregnancy-week-title {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    font-size: 20px;
    font-weight: 800;
    color: #0f766e;
    letter-spacing: -0.3px;
    line-height: 1.2;
}
.pregnancy-countdown-badge {
    background: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(255, 255, 255, 1);
    border-radius: 20px;
    box-shadow: 0 2px 6px rgba(15, 118, 110, 0.06);
}
.pregnancy-standard-box {
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid rgba(13, 148, 136, 0.08);
    box-shadow: 0 3px 10px rgba(15, 118, 110, 0.06);
}
.text-teal {
    color: #0d9488 !important;
}
.text-pink {
    color: #fb7185 !important;
}
</style>
