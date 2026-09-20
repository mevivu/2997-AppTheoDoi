@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.action')
    @include('admin.memo-game.play.css.game')
@endpush

@section('content')
    <!-- Canvas pháo hoa mừng chiến thắng -->
    <canvas id="memoConfettiCanvas" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; pointer-events: none; z-index: 9999; display: none;"></canvas>

    <div class="page-body">
        <div class="container-fluid memo-play-container">

            <!-- Header chuẩn CMS -->
            <x-admin.page-header
                :title="__('Memo Game: Chơi thử & Trải nghiệm')"
                :subtitle="__('Trải nghiệm cơ chế lật thẻ 3D, đếm ngược xem trước (Peek Time), tính điểm và xếp loại bài test')"
                icon="device-gamepad-2"
                :back-route="route(RouteAdminSystem::MEMO_THEME_INDEX)"
            >
                <x-slot:actions>
                    <a id="headerBtnCardIndex" href="{{ route(RouteAdminSystem::MEMO_CARD_INDEX) }}" class="btn btn-memo-library-header">
                        <i class="ti ti-cards me-1"></i>{{ __('Thư viện thẻ bài') }}
                    </a>
                    <a id="headerBtnBulkAdd" href="{{ route(RouteAdminSystem::MEMO_CARD_BULK_CREATE) }}" class="btn btn-memo-bulk-header">
                        <i class="ti ti-cloud-upload me-1"></i>{{ __('Upload hàng loạt ảnh') }}
                    </a>
                </x-slot:actions>
            </x-admin.page-header>

            <!-- Khung Điều Khiển & Thiết Lập Game -->
            <div class="memo-control-card">
                <div class="row g-3">
                    <!-- 1. Chọn Chủ đề -->
                    <div class="col-12 col-lg-6">
                        <div class="memo-section-title">
                            <i class="ti ti-palette text-primary"></i> {{ __('1. Chọn Chủ đề Thẻ bài') }}
                        </div>
                        <div class="memo-choice-group">
                            @foreach ($themes as $idx => $theme)
                                @php
                                    $code = strtolower($theme->code);
                                    $themeClass = 'theme-default';
                                    $icon = 'ti ti-cards';
                                    if (str_contains($code, 'vehic') || str_contains($code, 'xe')) {
                                        $themeClass = 'theme-vehicles';
                                        $icon = 'ti ti-car';
                                    } elseif (str_contains($code, 'flow') || str_contains($code, 'hoa')) {
                                        $themeClass = 'theme-flowers';
                                        $icon = 'ti ti-flower';
                                    } elseif (str_contains($code, 'numb') || str_contains($code, 'so')) {
                                        $themeClass = 'theme-numbers';
                                        $icon = 'ti ti-numbers';
                                    } elseif (str_contains($code, 'flag') || str_contains($code, 'co')) {
                                        $themeClass = 'theme-flags';
                                        $icon = 'ti ti-flag';
                                    }
                                @endphp
                                <button type="button"
                                        class="memo-choice-btn memo-theme-btn {{ $themeClass }} {{ $idx === 0 ? 'active' : '' }}"
                                        data-theme-id="{{ $theme->id }}"
                                        data-theme-code="{{ $theme->code }}">
                                    <i class="{{ $icon }}"></i>
                                    <span>{{ $theme->name }}</span>
                                    <span class="memo-choice-badge" title="{{ __('Số thẻ đã upload') }}">
                                        {{ $theme->cards_count }} thẻ
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. Chọn Cấu hình Độ tuổi & Lưới thẻ -->
                    <div class="col-12 col-lg-6">
                        <div class="memo-section-title">
                            <i class="ti ti-adjustments text-success"></i> {{ __('2. Chọn Mức độ & Kích thước Lưới') }}
                        </div>
                        <div class="memo-choice-group">
                            @foreach ($ageConfigs as $idx => $cfg)
                                <button type="button"
                                        class="memo-choice-btn memo-age-btn {{ $idx === 0 ? 'active' : '' }}"
                                        data-age-id="{{ $cfg->id }}"
                                        data-rows="{{ $cfg->rows }}"
                                        data-cols="{{ $cfg->columns }}"
                                        data-pairs="{{ $cfg->pairs_count }}">
                                    <i class="ti ti-grid-dots"></i>
                                    <span>{{ $cfg->min_age }}-{{ $cfg->max_age }} tuổi ({{ $cfg->rows }}x{{ $cfg->columns }})</span>
                                    <span class="memo-choice-badge">
                                        {{ $cfg->pairs_count }} cặp
                                    </span>
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 pt-3 mt-3 border-top">
                    <!-- Chế độ test -->
                    <div class="d-flex align-items-center gap-2">
                        <span class="fs-13 fw-bold text-muted">{{ __('Chế độ:') }}</span>
                        <div class="btn-group btn-group-sm" role="group">
                            <button type="button" class="btn btn-outline-primary memo-mode-btn active" data-mode="single">
                                <i class="ti ti-player-play me-1"></i>{{ __('Chơi thử 1 lượt (Nhanh)') }}
                            </button>
                            <button type="button" class="btn btn-outline-primary memo-mode-btn" data-mode="multi">
                                <i class="ti ti-repeat me-1"></i>{{ __('Bài test chuẩn (3 lượt)') }}
                            </button>
                        </div>
                    </div>

                    <!-- Nút thao tác chính -->
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" id="btnToggleSound" class="btn btn-outline-success btn-sm">
                            <i id="soundIcon" class="ti ti-volume me-1"></i>
                            <span id="soundText">{{ __('Bật') }}</span>
                        </button>
                        <button type="button" id="btnStartGame" class="btn-memo-start">
                            <i class="ti ti-player-play fs-16"></i>
                            <span>{{ __('Bắt đầu Chơi thử') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Thông báo tự động bổ sung thẻ ảo nếu CSDL chưa upload đủ ảnh -->
            <div id="memoFallbackNotice" class="memo-fallback-notice" style="display: none;">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-info-circle fs-18"></i>
                    <div>
                        <strong>{{ __('Chế độ mô phỏng:') }}</strong>
                        Chủ đề này hiện có <span id="availableCardsCount" class="fw-bold text-dark">0</span> ảnh thật trên CSDL. Cần tối thiểu <span id="neededPairsCount" class="fw-bold text-dark">0</span> thẻ cho lưới này. Game đang tự động bổ sung thẻ bài thử nghiệm sinh động để bạn chơi thử ngay!
                    </div>
                </div>
                <a id="linkUploadThemeCards" href="{{ route(RouteAdminSystem::MEMO_CARD_BULK_CREATE) }}" class="btn btn-warning btn-sm ms-3 text-nowrap">
                    <i class="ti ti-upload me-1"></i>{{ __('Nạp ảnh thật ngay') }}
                </a>
            </div>

            <!-- Bảng HUD thông số trong lúc chơi -->
            <div class="memo-hud">
                <div class="row text-center g-2">
                    <div class="col-3">
                        <div class="memo-hud-item">
                            <span class="memo-hud-label">{{ __('Lượt chơi') }}</span>
                            <span id="hudRound" class="memo-hud-value text-info">1/1</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="memo-hud-item">
                            <span class="memo-hud-label">{{ __('Thời gian còn lại') }}</span>
                            <span id="hudTimer" class="memo-hud-value text-warning">03:00</span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="memo-hud-item">
                            <span class="memo-hud-label">{{ __('Đã ghép đúng') }}</span>
                            <span class="memo-hud-value text-success">
                                <span id="hudPairsMatched">0</span>/<span id="hudPairsTotal">0</span>
                            </span>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="memo-hud-item">
                            <span class="memo-hud-label">{{ __('Lượt mở thẻ') }}</span>
                            <span id="hudMoves" class="memo-hud-value text-danger">0</span>
                        </div>
                    </div>
                </div>
                <div class="memo-timer-bar-wrap">
                    <div id="memoTimerBar" class="memo-timer-bar" style="width: 100%;"></div>
                </div>
            </div>

            <!-- Khung Bàn cờ Lưới thẻ bài -->
            <div class="memo-board-wrapper mb-4">
                <!-- 1. Loading State (Full width, Centered, Clean - Không vỡ giao diện) -->
                <div id="memoLoadingState" class="memo-loading-state">
                    <div class="memo-spinner-ring"></div>
                    <div class="memo-loading-title">{{ __('Đang tải cấu hình thẻ bài...') }}</div>
                    <div class="memo-loading-sub">{{ __('Hệ thống đang chuẩn bị bàn cờ theo chủ đề & độ tuổi') }}</div>
                </div>

                <!-- 2. Error State (Kèm nút thử lại) -->
                <div id="memoErrorState" class="memo-error-state" style="display: none;">
                    <div class="memo-error-icon">
                        <i class="ti ti-alert-triangle"></i>
                    </div>
                    <div id="memoErrorMessage" class="memo-error-title mb-2">
                        {{ __('Không thể tải dữ liệu bàn cờ.') }}
                    </div>
                    <button type="button" id="btnRetryLoad" class="btn btn-outline-primary btn-sm mt-2">
                        <i class="ti ti-rotate-clockwise me-1"></i>{{ __('Thử lại') }}
                    </button>
                </div>

                <!-- 3. Overlay Đếm ngược Xem trước (Peek Time) -->
                <div id="memoPeekOverlay" class="memo-peek-overlay" style="display: none;">
                    <div id="memoPeekCounter" class="memo-peek-counter">3</div>
                    <div class="memo-peek-text">
                        <i class="ti ti-eye me-1"></i> {{ __('Hãy ghi nhớ vị trí các thẻ bài!') }}
                    </div>
                </div>

                <!-- 4. Lưới thẻ bài động (Chỉ hiển thị khi đã nạp thẻ thành công) -->
                <div id="memoGameBoard" class="memo-grid memo-grid-2x3" style="display: none;">
                    <!-- Cards generated via JS -->
                </div>
            </div>

        </div>
    </div>

    <!-- Modal Tổng kết Kết quả Bài test -->
    <div class="modal fade" id="memoResultModal" tabindex="-1" aria-labelledby="memoResultModalLabel" aria-hidden="true" data-bs-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden;">
                <div class="modal-header border-0 bg-light pb-0 pt-4 px-4 justify-content-center">
                    <h4 class="modal-title fw-bolder text-center" id="memoResultModalLabel">
                        🎉 {{ __('Kết quả Bài test Trí nhớ') }}
                    </h4>
                </div>
                <div class="modal-body p-4 text-center">
                    <!-- Vòng tròn Điểm số -->
                    <div id="modalScoreCircle" class="memo-score-circle">
                        <span id="modalScoreNumber" class="memo-score-number">95</span>
                        <span class="memo-score-max">/ 100 điểm</span>
                    </div>

                    <!-- Xếp loại Badge -->
                    <div class="mb-4">
                        <span id="modalEvaluationBadge" class="badge bg-success px-3 py-2 fs-14">
                            {{ __('Xuất sắc') }}
                        </span>
                    </div>

                    <!-- 3 Khối chỉ số chính -->
                    <div class="row g-2 mb-4">
                        <div class="col-4">
                            <div class="p-2 bg-light rounded-3">
                                <div class="text-muted fs-11 text-uppercase fw-bold">{{ __('Thời gian') }}</div>
                                <div id="modalTotalDuration" class="fs-18 fw-bolder text-primary">45s</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded-3">
                                <div class="text-muted fs-11 text-uppercase fw-bold">{{ __('Cặp đúng') }}</div>
                                <div id="modalTotalPairs" class="fs-18 fw-bolder text-success">6</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2 bg-light rounded-3">
                                <div class="text-muted fs-11 text-uppercase fw-bold">{{ __('Lần sai') }}</div>
                                <div id="modalTotalMistakes" class="fs-18 fw-bolder text-danger">2</div>
                            </div>
                        </div>
                    </div>

                    <!-- Bảng chi tiết các lượt -->
                    <div class="table-responsive mb-3 border rounded-3 text-start">
                        <table class="table table-sm table-striped mb-0 fs-13">
                            <thead class="table-light">
                                <tr>
                                    <th>{{ __('Lượt') }}</th>
                                    <th>{{ __('Thời gian') }}</th>
                                    <th>{{ __('Cặp đúng') }}</th>
                                    <th>{{ __('Số lỗi') }}</th>
                                    <th>{{ __('Điểm') }}</th>
                                </tr>
                            </thead>
                            <tbody id="modalRoundsTableBody">
                                <!-- Generated by JS -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Nhận xét đánh giá chuyên môn -->
                    <div class="alert alert-info border-0 text-start fs-13 p-3 mb-0" style="border-radius: 12px;">
                        <div class="fw-bold mb-1 text-primary">
                            <i class="ti ti-bulb me-1"></i>{{ __('Nhận xét chuyên môn:') }}
                        </div>
                        <div id="modalFeedbackText">
                            {{ __('Bé có khả năng định vị hình ảnh và trí nhớ thị giác rất tốt.') }}
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 bg-light p-3 justify-content-center gap-2">
                    <button type="button" id="btnRestartGame" class="btn btn-outline-secondary px-3">
                        <i class="ti ti-rotate-clockwise me-1"></i>{{ __('Chơi lại') }}
                    </button>
                    <button type="button" id="btnSaveResult" class="btn btn-success px-4">
                        <i class="ti ti-device-floppy me-1"></i>{{ __('Lưu vào Lịch sử bài test') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('admin.memo-game.play.js.game')
@endpush
