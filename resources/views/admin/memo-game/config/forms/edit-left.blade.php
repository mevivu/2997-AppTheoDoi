@push('custom-css')
<style>
/* Phone Mockup Screen Simulation (No Purple, 100% Matching App Screenshot) */
.phone-mockup-wrapper {
    max-width: 310px;
    margin: 0 auto;
}
.phone-mockup-screen {
    background: linear-gradient(180deg, #E0F2FE 0%, #BAE6FD 30%, #F0FDF4 100%);
    border: 4px solid #0284C7;
    border-radius: 28px;
    padding: 10px 12px 14px;
    box-shadow: 0 10px 25px -5px rgba(2, 132, 199, 0.25), 0 8px 10px -6px rgba(2, 132, 199, 0.2);
    position: relative;
    user-select: none;
}
.phone-mockup-speaker {
    width: 42px;
    height: 4px;
    background: #0284C7;
    border-radius: 4px;
    margin: 0 auto 8px;
    opacity: 0.6;
}
.phone-app-header {
    margin-bottom: 6px;
}
.phone-app-title {
    font-size: 13px;
    font-weight: 800;
    color: #0369A1;
    letter-spacing: -0.2px;
    line-height: 1.2;
}
.phone-app-title .sparkle {
    color: #F59E0B;
}
.phone-app-subtitle {
    font-size: 9.5px;
    color: #0284C7;
    font-weight: 500;
    margin-top: 1px;
}
.phone-app-stats {
    display: flex;
    justify-content: center;
    gap: 4px;
    margin-bottom: 8px;
}
.phone-stat-pill {
    font-size: 9.5px;
    font-weight: 600;
    padding: 2px 7px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
    display: inline-flex;
    align-items: center;
    gap: 3px;
}
.phone-game-board {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(4px);
    border-radius: 14px;
    border: 1px solid rgba(186, 230, 253, 0.8);
    padding: 8px;
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.phone-cards-grid {
    display: grid !important;
    width: 100%;
    justify-content: center;
    align-content: center;
}

/* Mini Card Styles */
.phone-card-item {
    aspect-ratio: 3.2 / 4;
    max-height: 50px;
    border-radius: 6px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    box-shadow: 0 2px 4px rgba(2, 132, 199, 0.12);
    user-select: none;
    transition: transform 0.15s ease;
}
/* Face-down cards */
.phone-card-item.card-down {
    background: linear-gradient(135deg, #FFFFFF 0%, #E0F2FE 100%);
    border: 1.5px solid #7DD3FC;
    color: #0284C7;
}
/* Face-up Matched cards (Image 3) */
.phone-card-item.card-matched {
    background: #FFFFFF;
    border: 2px solid #10B981 !important;
    box-shadow: 0 0 0 1px #10B981, 0 3px 6px rgba(16, 185, 129, 0.2);
}
.phone-card-item.card-matched .card-matched-badge {
    position: absolute;
    top: 2px;
    right: 2px;
    width: 12px;
    height: 12px;
    background: #10B981;
    color: #FFFFFF;
    border-radius: 50%;
    font-size: 7.5px;
    font-weight: bold;
    display: flex;
    align-items: center;
    justify-content: center;
    line-height: 1;
}
.phone-card-item.card-matched .card-matched-val {
    font-size: 11px;
    font-weight: 800;
    color: #059669;
    line-height: 1;
}
.phone-card-item.card-matched .card-matched-sub {
    font-size: 7px;
    font-weight: 700;
    color: #059669;
    margin-top: 1px;
    line-height: 1;
}

/* Phone bottom controls */
.phone-app-footer {
    margin-top: 8px;
}
.phone-app-action-btn {
    background: linear-gradient(135deg, #0284C7 0%, #0369A1 100%);
    color: #FFFFFF;
    font-size: 10px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 16px;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.35);
    display: inline-block;
    width: 100%;
}
.phone-app-link {
    font-size: 8.5px;
    color: #059669;
    font-weight: 600;
    margin-top: 3px;
    text-decoration: underline;
}

/* Preset Buttons - Fresh Blue & Emerald (NO PURPLE) */
.memo-preset-btn {
    transition: all 0.15s ease-in-out;
    border: 1.5px solid #CBD5E1 !important;
    background-color: #FFFFFF !important;
    color: #334155 !important;
    font-size: 12px;
    cursor: pointer;
}
.memo-preset-btn:hover {
    border-color: #0284C7 !important;
    color: #0284C7 !important;
    background-color: #F0F9FF !important;
}
.memo-preset-btn.active-preset {
    background-color: #0284C7 !important;
    border-color: #0284C7 !important;
    color: #FFFFFF !important;
    box-shadow: 0 3px 8px rgba(2, 132, 199, 0.35) !important;
}
.memo-preset-btn.active-preset-teal {
    background-color: #0D9488 !important;
    border-color: #0D9488 !important;
    color: #FFFFFF !important;
    box-shadow: 0 3px 8px rgba(13, 148, 136, 0.35) !important;
}
</style>
@endpush

<div class="col-12 col-md-8 col-xl-9">
    <!-- Card 1: Thông tin cơ bản & Nhóm tuổi áp dụng -->
    <div class="card custom-shadow mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0 d-flex align-items-center">
                <i class="ti ti-users text-primary me-2 fs-18"></i>
                {{ __('Thông tin Nhóm tuổi & Tên cấu hình') }}
            </h4>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <div class="mb-2">
                        <label class="form-label fw-bold">{{ __('Tên cấu hình / Nhóm độ tuổi') }}: <span class="text-danger">*</span></label>
                        <x-input type="text" name="name" :value="$response->name" :required="true" placeholder="{{ __('Ví dụ: Khởi động (3 - 5 tuổi) - Lưới 2x3') }}" />
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label fw-bold">{{ __('Độ tuổi tối thiểu (Min Age)') }}: <span class="text-danger">*</span></label>
                        <input type="number" name="min_age" id="inputMinAge" class="form-control" value="{{ $response->min_age }}" min="1" max="25" required>
                        <small class="text-muted fs-12">{{ __('Độ tuổi nhỏ nhất áp dụng cấu hình này (tuổi)') }}</small>
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <div class="mb-2">
                        <label class="form-label fw-bold">{{ __('Độ tuổi tối đa (Max Age)') }}: <span class="text-danger">*</span></label>
                        <input type="number" name="max_age" id="inputMaxAge" class="form-control" value="{{ $response->max_age }}" min="1" max="25" required>
                        <small class="text-muted fs-12">{{ __('Độ tuổi lớn nhất áp dụng cấu hình này (tuổi)') }}</small>
                    </div>
                </div>

                <div class="col-12">
                    <div class="alert alert-info py-2 px-3 mb-0 d-flex align-items-center rounded-3 fs-13">
                        <i class="ti ti-info-circle fs-18 me-2 text-info"></i>
                        <span>{{ __('Phạm vi áp dụng:') }} <strong id="ageRangeBadge" class="text-dark">{{ __('Trẻ từ') }} {{ $response->min_age }} {{ __('đến') }} {{ $response->max_age }} {{ __('tuổi') }}</strong></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Kích thước Lưới thẻ (Hàng x Cột & Mô phỏng App chuẩn Flutter) -->
    <div class="card custom-shadow mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0 d-flex align-items-center">
                <i class="ti ti-grid-dots text-success me-2 fs-18"></i>
                {{ __('Kích thước Lưới thẻ (Hàng x Cột)') }}
            </h4>
        </div>
        <div class="card-body">
            <!-- Preset gợi ý theo lứa tuổi (Clean Blue, Zero Purple) -->
            <div class="mb-3 pb-3 border-bottom">
                <label class="form-label fw-bold fs-12 text-muted text-uppercase mb-2">
                    <i class="ti ti-sparkles text-warning me-1"></i> {{ __('Gợi ý kích thước lưới chuẩn theo lứa tuổi (Bấm để chọn nhanh):') }}
                </label>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-grid-preset px-3 py-1" data-rows="2" data-cols="3">
                        <i class="ti ti-check d-none me-1 check-icon"></i> <strong>2 × 3</strong> (6 thẻ • 3 cặp) - Khởi động (3-5t)
                    </button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-grid-preset px-3 py-1" data-rows="3" data-cols="4">
                        <i class="ti ti-check d-none me-1 check-icon"></i> <strong>3 × 4</strong> (12 thẻ • 6 cặp) - Cơ bản (6-8t)
                    </button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-grid-preset px-3 py-1" data-rows="4" data-cols="4">
                        <i class="ti ti-check d-none me-1 check-icon"></i> <strong>4 × 4</strong> (16 thẻ • 8 cặp) - Nâng cao (9-11t)
                    </button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-grid-preset px-3 py-1" data-rows="4" data-cols="5">
                        <i class="ti ti-check d-none me-1 check-icon"></i> <strong>4 × 5</strong> (20 thẻ • 10 cặp) - Thử thách (12+t)
                    </button>
                </div>
            </div>

            <div class="row g-3 align-items-start">
                <div class="col-12 col-md-5">
                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Số hàng (Rows)') }}: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="ti ti-arrows-vertical fs-14"></i></span>
                            <input type="number" name="rows" id="inputRows" class="form-control fw-bold fs-14" value="{{ $response->rows }}" min="2" max="10" required>
                            <span class="input-group-text bg-light text-muted fs-12">{{ __('hàng') }}</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">{{ __('Số cột (Columns)') }}: <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="ti ti-arrows-horizontal fs-14"></i></span>
                            <input type="number" name="columns" id="inputCols" class="form-control fw-bold fs-14" value="{{ $response->columns }}" min="2" max="10" required>
                            <span class="input-group-text bg-light text-muted fs-12">{{ __('cột') }}</span>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="d-flex align-items-center justify-content-between mb-1.5">
                            <span class="fs-12 text-muted fw-semibold">{{ __('Tổng thẻ bài:') }}</span>
                            <strong class="fs-14 text-primary" id="gridTotalCardsBadge">{{ $response->total_cards }} thẻ</strong>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="fs-12 text-muted fw-semibold">{{ __('Số cặp trùng nhau:') }}</span>
                            <strong class="fs-14 text-success" id="gridPairsBadge">{{ $response->pairs_count }} cặp</strong>
                        </div>
                        <div class="fs-11 text-muted border-top pt-2">
                            <i class="ti ti-info-circle text-azure me-1"></i> {{ __('Màn hình dọc điện thoại (Portrait): Nên chọn số cột ≤ 4 hoặc 5 để các thẻ hiển thị đẹp mắt, vừa vặn.') }}
                        </div>
                    </div>

                    <div id="gridWarning" class="alert alert-danger py-1.5 px-2.5 fs-12 mb-0 d-none text-center">
                        <i class="ti ti-alert-triangle me-1"></i> {{ __('Số thẻ phải là số chẵn (Rows × Cols chẵn) để tạo thành các cặp trùng nhau!') }}
                    </div>
                </div>

                <div class="col-12 col-md-7">
                    <!-- Khung mô phỏng màn hình App điện thoại thực tế (Theo ảnh app Flutter) -->
                    <div class="phone-mockup-wrapper">
                        <div class="phone-mockup-screen">
                            <div class="phone-mockup-speaker"></div>

                            <div class="phone-app-header text-center">
                                <div class="phone-app-title">
                                    <span class="sparkle">✨</span> {{ __('Trò chơi Trí nhớ') }} <span class="sparkle">✨</span>
                                </div>
                                <div class="phone-app-subtitle">
                                    {{ __('Nhớ và tìm đúng cặp hình giống nhau nhé!') }}
                                </div>
                            </div>

                            <div class="phone-app-stats">
                                <span class="phone-stat-pill bg-white text-dark">
                                    <i class="ti ti-device-gamepad text-primary"></i> Ván <span id="phoneStatRound">1/{{ $response->total_rounds }}</span>
                                </span>
                                <span class="phone-stat-pill bg-white text-dark">
                                    <i class="ti ti-clock text-azure"></i> <span id="phoneStatDuration">03:00</span>
                                </span>
                                <span class="phone-stat-pill bg-white text-success">
                                    <i class="ti ti-circle-check text-success"></i> <span id="phoneStatPairs">1/{{ $response->pairs_count }}</span>
                                </span>
                            </div>

                            <div class="phone-game-board">
                                <div id="visualGridContainer" class="phone-cards-grid">
                                    <!-- Rendered dynamically by JS -->
                                </div>
                            </div>

                            <div class="phone-app-footer text-center">
                                <div class="phone-app-action-btn">
                                    <i class="ti ti-cards me-1"></i>
                                    <span id="phoneBtnText">{{ __('Tìm tất cả') }} {{ $response->pairs_count }} {{ __('cặp hình giống nhau!') }}</span>
                                </div>
                                <div class="phone-app-link">
                                    {{ __('Kết thúc sớm & Nộp bài') }}
                                </div>
                            </div>
                        </div>
                        <div class="phone-mockup-label text-center mt-2">
                            <span class="badge bg-blue-lt text-blue px-2.5 py-1 fs-11 rounded-pill">
                                <i class="ti ti-device-mobile me-1"></i> {{ __('Mô phỏng kích thước lưới trên App Flutter') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Quy chuẩn Thời gian & Ván chơi (Rõ ràng 100%, Không màu tím) -->
    <div class="card custom-shadow mb-4">
        <div class="card-header bg-light-subtle">
            <h4 class="card-title mb-0 d-flex align-items-center text-dark">
                <i class="ti ti-clock-play text-warning me-2 fs-18"></i>
                {{ __('Quy chuẩn Thời gian & Số ván chơi (Rounds)') }}
            </h4>
        </div>
        <div class="card-body">
            <!-- 1. Thời lượng 1 ván -->
            <div class="mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <label class="form-label fw-bold mb-0 fs-14">
                        <i class="ti ti-hourglass-low text-primary me-1"></i>
                        {{ __('Thời gian cho MỖI ván chơi (1 game):') }} <span class="text-danger">*</span>
                    </label>
                    <span id="durationConvertedBadge" class="badge bg-azure-lt text-azure px-2 py-1 fs-12 fw-semibold">
                        {{ floor($response->total_duration / 60) }} phút {{ $response->total_duration % 60 > 0 ? ($response->total_duration % 60) . 's' : '' }} / ván
                    </span>
                </div>

                <!-- Presets chọn nhanh thời gian -->
                <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                    <span class="fs-12 text-muted align-self-center me-1">{{ __('Chọn nhanh:') }}</span>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-duration-preset px-2.5 py-0.5" data-seconds="60">1 phút (60s)</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-duration-preset px-2.5 py-0.5" data-seconds="120">2 phút (120s)</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-duration-preset px-2.5 py-0.5" data-seconds="180">⭐ 3 phút (180s - Chuẩn)</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-duration-preset px-2.5 py-0.5" data-seconds="240">4 phút (240s)</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-duration-preset px-2.5 py-0.5" data-seconds="300">5 phút (300s)</button>
                </div>

                <div class="row align-items-center g-2">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fs-12">{{ __('Số giây:') }}</span>
                            <input type="number" name="total_duration" id="inputDuration" class="form-control fw-bold" value="{{ $response->total_duration }}" min="30" max="600" required>
                            <span class="input-group-text bg-light">giây</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-8">
                        <small class="text-muted fs-12 d-block">
                            <i class="ti ti-info-circle me-1"></i> {{ __('Đây là thời gian đếm ngược của 1 ván lật thẻ. Hết giờ hoặc bé ghép xong sẽ chuyển sang ván kế tiếp.') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- 2. Số ván chơi (Xanh Azure thanh lịch - Không tím) -->
            <div class="mb-4 pb-3 border-bottom">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <label class="form-label fw-bold mb-0 fs-14">
                        <i class="ti ti-repeat text-azure me-1"></i>
                        {{ __('Số ván chơi của bài test (Rounds):') }} <span class="text-danger">*</span>
                    </label>
                    <span id="roundsConvertedBadge" class="badge bg-azure-lt text-azure px-2 py-1 fs-12 fw-semibold">
                        {{ $response->total_rounds }} ván liên tiếp
                    </span>
                </div>

                <!-- Presets chọn nhanh số ván -->
                <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                    <span class="fs-12 text-muted align-self-center me-1">{{ __('Chọn nhanh:') }}</span>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-rounds-preset px-2.5 py-0.5" data-rounds="1">1 ván</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-rounds-preset px-2.5 py-0.5" data-rounds="2">2 ván</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-rounds-preset px-2.5 py-0.5" data-rounds="3">⭐ 3 ván (Chuẩn)</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-rounds-preset px-2.5 py-0.5" data-rounds="4">4 ván</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-rounds-preset px-2.5 py-0.5" data-rounds="5">5 ván</button>
                </div>

                <div class="row align-items-center g-2">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fs-12">{{ __('Số ván:') }}</span>
                            <input type="number" name="total_rounds" id="inputRounds" class="form-control fw-bold" value="{{ $response->total_rounds }}" min="1" max="10" required>
                            <span class="input-group-text bg-light">ván</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-8">
                        <small class="text-muted fs-12 d-block">
                            <i class="ti ti-info-circle me-1"></i> {{ __('Mỗi ván là 1 chủ đề độc lập. Hoàn thành mỗi ván bé được tích lũy +1 điểm game.') }}
                        </small>
                    </div>
                </div>
            </div>

            <!-- 3. Thời gian xem trước (Peek time) -->
            <div>
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                    <label class="form-label fw-bold mb-0 fs-14">
                        <i class="ti ti-eye text-teal me-1"></i>
                        {{ __('Thời gian mở thẻ xem trước (Peek Time):') }}
                    </label>
                    <span id="peekConvertedBadge" class="badge bg-teal-lt text-teal px-2 py-1 fs-12 fw-semibold">
                        {{ $response->peek_time }} giây quan sát
                    </span>
                </div>

                <!-- Presets chọn nhanh peek time -->
                <div class="d-flex flex-wrap gap-1.5 mb-2.5">
                    <span class="fs-12 text-muted align-self-center me-1">{{ __('Chọn nhanh:') }}</span>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-peek-preset px-2.5 py-0.5" data-seconds="0">0s (Tắt)</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-peek-preset px-2.5 py-0.5" data-seconds="3">⭐ 3s (Chuẩn)</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-peek-preset px-2.5 py-0.5" data-seconds="5">5s</button>
                    <button type="button" class="btn memo-preset-btn rounded-pill btn-peek-preset px-2.5 py-0.5" data-seconds="10">10s</button>
                </div>

                <div class="row align-items-center g-2">
                    <div class="col-12 col-sm-6 col-md-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted fs-12">{{ __('Số giây:') }}</span>
                            <input type="number" name="peek_time" id="inputPeekTime" class="form-control fw-bold" value="{{ $response->peek_time }}" min="0" max="30">
                            <span class="input-group-text bg-light">giây</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-md-8">
                        <small class="text-muted fs-12 d-block">
                            <i class="ti ti-info-circle me-1"></i> {{ __('Mở ngửa toàn bộ thẻ để bé ghi nhớ trước khi thẻ úp lại và bắt đầu đếm ngược ván chơi.') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('custom-js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Inputs
    const inputMinAge = document.getElementById('inputMinAge');
    const inputMaxAge = document.getElementById('inputMaxAge');
    const inputRows = document.getElementById('inputRows');
    const inputCols = document.getElementById('inputCols');
    const inputDuration = document.getElementById('inputDuration');
    const inputRounds = document.getElementById('inputRounds');
    const inputPeekTime = document.getElementById('inputPeekTime');

    // Badges & Previews
    const ageRangeBadge = document.getElementById('ageRangeBadge');
    const gridTotalCardsBadge = document.getElementById('gridTotalCardsBadge');
    const gridPairsBadge = document.getElementById('gridPairsBadge');
    const gridWarning = document.getElementById('gridWarning');
    const visualGridContainer = document.getElementById('visualGridContainer');
    const durationConvertedBadge = document.getElementById('durationConvertedBadge');
    const roundsConvertedBadge = document.getElementById('roundsConvertedBadge');
    const peekConvertedBadge = document.getElementById('peekConvertedBadge');

    // Phone Mockup Elements
    const phoneStatRound = document.getElementById('phoneStatRound');
    const phoneStatDuration = document.getElementById('phoneStatDuration');
    const phoneStatPairs = document.getElementById('phoneStatPairs');
    const phoneBtnText = document.getElementById('phoneBtnText');

    // Simulation Card (Right column)
    const simAge = document.getElementById('simAge');
    const simGrid = document.getElementById('simGrid');
    const simDuration = document.getElementById('simDuration');
    const simRounds = document.getElementById('simRounds');
    const simTotalGame = document.getElementById('simTotalGame');
    const simGameMinutes = document.getElementById('simGameMinutes');
    const simTotalAssessment = document.getElementById('simTotalAssessment');

    function updateSimulation() {
        const minAge = parseInt(inputMinAge?.value || 1);
        const maxAge = parseInt(inputMaxAge?.value || minAge);
        const rows = parseInt(inputRows?.value || 2);
        const cols = parseInt(inputCols?.value || 2);
        const totalDuration = parseInt(inputDuration?.value || 180);
        const totalRounds = parseInt(inputRounds?.value || 3);
        const peekTime = parseInt(inputPeekTime?.value || 0);

        // 1. Age Range
        const ageText = `Trẻ từ ${minAge} đến ${maxAge} tuổi`;
        if (ageRangeBadge) ageRangeBadge.textContent = ageText;
        if (simAge) simAge.textContent = ageText;

        // 2. Grid & Cards
        const totalCards = rows * cols;
        const pairsCount = Math.floor(totalCards / 2);

        if (gridTotalCardsBadge) gridTotalCardsBadge.textContent = `${totalCards} thẻ`;
        if (gridPairsBadge) gridPairsBadge.textContent = `${pairsCount} cặp`;

        if (simGrid) {
            simGrid.textContent = `${rows} hàng × ${cols} cột (${totalCards} thẻ • ${pairsCount} cặp)`;
        }

        if (totalCards % 2 !== 0) {
            gridWarning?.classList.remove('d-none');
        } else {
            gridWarning?.classList.add('d-none');
        }

        // 3. Duration of 1 round
        const durationMin = Math.floor(totalDuration / 60);
        const durationSec = totalDuration % 60;
        let durationFormatted = '';
        if (durationMin > 0) durationFormatted += `${durationMin} phút`;
        if (durationSec > 0) durationFormatted += ` ${durationSec}s`;
        if (durationFormatted === '') durationFormatted = '0s';

        const clockFormatted = `${String(durationMin).padStart(2, '0')}:${String(durationSec).padStart(2, '0')}`;

        if (durationConvertedBadge) {
            durationConvertedBadge.textContent = `${durationFormatted} / ván`;
        }
        if (simDuration) {
            simDuration.textContent = `${durationFormatted} (${totalDuration}s) / ván`;
        }

        // 4. Rounds
        if (roundsConvertedBadge) {
            roundsConvertedBadge.textContent = `${totalRounds} ván liên tiếp`;
        }
        if (simRounds) {
            simRounds.textContent = `${totalRounds} ván chơi`;
        }

        // 5. Update Phone Mockup Header & Footer
        if (phoneStatRound) phoneStatRound.textContent = `1/${totalRounds}`;
        if (phoneStatDuration) phoneStatDuration.textContent = clockFormatted;
        if (phoneStatPairs) phoneStatPairs.textContent = `1/${pairsCount}`;
        if (phoneBtnText) phoneBtnText.textContent = `Tìm tất cả ${pairsCount} cặp hình giống nhau!`;

        // 6. Draw Realistic Phone Visual Grid (Fix display: grid !important)
        if (visualGridContainer) {
            visualGridContainer.innerHTML = '';
            visualGridContainer.style.setProperty('display', 'grid', 'important');
            visualGridContainer.style.setProperty('grid-template-columns', `repeat(${cols}, 1fr)`, 'important');
            visualGridContainer.style.setProperty('gap', cols >= 5 ? '3px' : '5px', 'important');

            // Pick 2 matched cards to demonstrate matching state (matching app screenshot)
            const matchedIndex1 = totalCards >= 4 ? 2 : 0;
            const matchedIndex2 = totalCards >= 6 ? (cols + 1) : (totalCards >= 4 ? 3 : 1);

            const funIcons = ['🧸', '🚀', '🚗', '🎨', '🧩', '🎈', '⭐', '🦁', '🐱', '🐼', '🍎', '🎁'];

            for (let i = 0; i < totalCards; i++) {
                const card = document.createElement('div');
                card.className = 'phone-card-item';

                if (i === matchedIndex1 || i === matchedIndex2) {
                    // Face-up matched card with green border & check badge (Image 3)
                    card.classList.add('card-matched');
                    card.innerHTML = `
                        <span class="card-matched-badge">✓</span>
                        <div class="card-matched-val">9</div>
                        <div class="card-matched-sub">Số 9</div>
                    `;
                } else {
                    // Face-down card with cheerful toy/symbol
                    card.classList.add('card-down');
                    const icon = funIcons[i % funIcons.length];
                    card.innerHTML = `<span style="font-size: ${cols >= 5 ? '11px' : '13px'};">${icon}</span>`;
                }
                visualGridContainer.appendChild(card);
            }
        }

        // 7. Total Game Duration & Total Assessment Duration
        const totalGameSeconds = totalDuration * totalRounds;
        const totalGameMinutes = Math.round(totalGameSeconds / 60);
        const totalAssessmentMinutes = 15 + totalGameMinutes;

        if (simTotalGame) {
            simTotalGame.textContent = `${totalRounds} ván × ${durationFormatted} = ${totalGameMinutes} PHÚT`;
        }
        if (simGameMinutes) {
            simGameMinutes.textContent = totalGameMinutes;
        }
        if (simTotalAssessment) {
            simTotalAssessment.textContent = `${totalAssessmentMinutes} PHÚT`;
        }

        // 8. Peek Time Badge
        if (peekConvertedBadge) {
            peekConvertedBadge.textContent = peekTime > 0 ? `${peekTime} giây quan sát` : 'Không mở trước';
        }

        // Highlight active presets (100% Ocean Blue & Emerald, NO PURPLE)
        highlightActivePresets(rows, cols, totalDuration, totalRounds, peekTime);
    }

    function highlightActivePresets(rows, cols, duration, rounds, peek) {
        // Grid presets
        document.querySelectorAll('.btn-grid-preset').forEach(btn => {
            const r = parseInt(btn.dataset.rows);
            const c = parseInt(btn.dataset.cols);
            const check = btn.querySelector('.check-icon');
            if (r === rows && c === cols) {
                btn.classList.add('active-preset');
                check?.classList.remove('d-none');
            } else {
                btn.classList.remove('active-preset');
                check?.classList.add('d-none');
            }
        });

        // Duration presets
        document.querySelectorAll('.btn-duration-preset').forEach(btn => {
            const sec = parseInt(btn.dataset.seconds);
            if (sec === duration) {
                btn.classList.add('active-preset');
            } else {
                btn.classList.remove('active-preset');
            }
        });

        // Rounds presets (Clean Ocean Blue, NO PURPLE)
        document.querySelectorAll('.btn-rounds-preset').forEach(btn => {
            const rnd = parseInt(btn.dataset.rounds);
            if (rnd === rounds) {
                btn.classList.add('active-preset');
            } else {
                btn.classList.remove('active-preset');
            }
        });

        // Peek presets (Emerald / Teal)
        document.querySelectorAll('.btn-peek-preset').forEach(btn => {
            const sec = parseInt(btn.dataset.seconds);
            if (sec === peek) {
                btn.classList.add('active-preset-teal');
            } else {
                btn.classList.remove('active-preset-teal');
            }
        });
    }

    // Attach Preset Event Listeners
    document.querySelectorAll('.btn-grid-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputRows) inputRows.value = this.dataset.rows;
            if (inputCols) inputCols.value = this.dataset.cols;
            updateSimulation();
        });
    });

    document.querySelectorAll('.btn-duration-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputDuration) inputDuration.value = this.dataset.seconds;
            updateSimulation();
        });
    });

    document.querySelectorAll('.btn-rounds-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputRounds) inputRounds.value = this.dataset.rounds;
            updateSimulation();
        });
    });

    document.querySelectorAll('.btn-peek-preset').forEach(btn => {
        btn.addEventListener('click', function () {
            if (inputPeekTime) inputPeekTime.value = this.dataset.seconds;
            updateSimulation();
        });
    });

    // Inputs change listeners
    [inputMinAge, inputMaxAge, inputRows, inputCols, inputDuration, inputRounds, inputPeekTime].forEach(input => {
        input?.addEventListener('input', updateSimulation);
        input?.addEventListener('change', updateSimulation);
    });

    // Initial run
    updateSimulation();
});
</script>
@endpush
