@php
    $latestPq = $children->ratingPQs()->latest()->first();

    $predHeight = $heightPrediction['predicting_adult_height'] ?? 0;
    $speedChange = $heightPrediction['speed_change'] ?? 0;
    $whoDiff = $heightPrediction['height_comparison']['height_who_current'] ?? 0;
    $isTaller = $heightPrediction['height_comparison']['is_taller_than_who'] ?? ($whoDiff > 0);
    $whoMessage = $heightPrediction['height_comparison']['message'] ?? 'Chăm con 360 sẽ dự đoán chiều cao cho con bạn';
    $advice = $heightPrediction['advice_message'] ?? 'Bạn nhập chiều cao trong phần đánh giá thể chất';

    $fatherHeight = (float)($children->user?->father_height ?? 0);
    $motherHeight = (float)($children->user?->mother_height ?? 0);
    $isMissingParentHeight = ($fatherHeight <= 0 || $motherHeight <= 0);
@endphp

<div class="predict-height-app-wrapper">
    @if(!$latestPq)
        {{-- Trạng thái chưa có đánh giá PQ (Đồng bộ UI khi chưa có dữ liệu trên App) --}}
        <div class="empty-pq-state-box">
            <div class="state-icon">
                <i class="ti ti-run"></i>
            </div>
            <h5 class="fw-bold text-dark mb-2">{{ __('Chưa có đánh giá thể chất (PQ)') }}</h5>
            <p class="text-muted fs-13 mb-3 mx-auto" style="max-width: 480px;">
                {{ __('Bé chưa có đợt đánh giá thể chất nào. Vui lòng thực hiện đánh giá thể chất (PQ) trên ứng dụng để hệ thống có đủ dữ liệu dự đoán chiều cao.') }}
            </p>
            <a href="{{ route('admin.ratingPQ.index', ['child_id' => $children->id]) }}" class="btn btn-primary btn-sm px-3 rounded-3" target="_blank">
                <i class="ti ti-list-details me-1"></i> {{ __('Xem danh sách đánh giá PQ') }}
            </a>
        </div>
    @else
        {{-- 1. Link Nguồn Dữ Liệu WHO (Đúng 100% như trên App) --}}
        <div class="text-center mb-3">
            <a href="https://www.who.int/tools/child-growth-standards/standards/length-height-for-age"
               target="_blank"
               class="who-source-link">
                {{ __('Nguồn dữ liệu: Tổ chức Y tế Thế giới (World Health Organization - WHO)') }}
            </a>
        </div>

        {{-- 2. Thông điệp & Lời khuyên (Chỉ hiển thị khi có dữ liệu cha mẹ, đồng bộ như trên App) --}}
        @if(!$isMissingParentHeight)
            @if($advice)
                <div class="prediction-banner-message">
                    <div class="inner-content">
                        {{ $advice }}
                    </div>
                </div>
            @endif

            @if($whoMessage)
                <div class="prediction-banner-message">
                    <div class="inner-content">
                        {{ $whoMessage }}
                    </div>
                </div>
            @endif
        @endif

        {{-- 3. Khối "Tổng quan" (GeneralPredictionWidget - Đồng bộ 100% thông số của App) --}}
        <div class="general-prediction-header">
            <div class="header-chip-icon">
                <i class="ti ti-layout-dashboard"></i>
            </div>
            <h5 class="header-title">{{ __('Tổng quan') }}</h5>
        </div>

        <div class="row g-3">
            <!-- Mục 1: So sánh chuẩn WHO -->
            <div class="col-12">
                <div class="general-prediction-item-card">
                    <img src="{{ asset('assets/images/predict_height/who.png') }}"
                         alt="WHO Standard"
                         class="item-thumb"
                         onerror="this.src='{{ asset('assets/images/default.png') }}'">
                    <div class="item-content">
                        <div class="item-title">
                            {{ $isTaller ? __('Đang cao hơn chuẩn WHO') : __('Đang thấp hơn chuẩn WHO') }}
                        </div>
                        <div class="item-val">
                            {{ ($whoDiff > 0 ? '+' : '') . number_format($whoDiff, 1) }} cm
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mục 2: Dự đoán chiều cao khi trưởng thành (+/- 5cm) -->
            <div class="col-12">
                <div class="general-prediction-item-card">
                    <img src="{{ asset('assets/images/predict_height/predict_adult_height.png') }}"
                         alt="Predict Adult Height"
                         class="item-thumb"
                         onerror="this.src='{{ asset('assets/images/default.png') }}'">
                    <div class="item-content">
                        <div class="item-title">
                            {{ __('Dự đoán chiều cao khi trưởng thành (+/- 5cm)') }}
                        </div>
                        <div class="item-val">
                            {{ number_format($predHeight, 1) }} cm
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mục 3: Tốc độ tăng trưởng trong năm qua (Chỉ hiển thị khi có đủ dữ liệu bố mẹ) -->
            @if(!$isMissingParentHeight)
                <div class="col-12">
                    <div class="general-prediction-item-card">
                        <img src="{{ asset('assets/images/predict_height/growth_speed.png') }}"
                             alt="Growth Speed"
                             class="item-thumb"
                             onerror="this.src='{{ asset('assets/images/default.png') }}'">
                        <div class="item-content">
                            <div class="item-title">
                                {{ __('Tốc độ tăng trưởng trong năm qua') }}
                            </div>
                            <div class="item-val">
                                {{ number_format($speedChange, 1) }} cm
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- 4. Cảnh báo thiếu dữ liệu cha mẹ (UpdateDataWidget - Đồng bộ như trên App) --}}
        @if($isMissingParentHeight)
            <div class="missing-parent-warning-box">
                <div class="warning-title">
                    <i class="ti ti-mood-sad fs-4"></i>
                    {{ __('Không đủ dữ liệu để hiển thị đầy đủ kết quả') }}
                </div>
                <p class="warning-desc">
                    {{ __('Phụ huynh chưa cập nhật chiều cao của bố/mẹ trong hồ sơ tài khoản. Cần cập nhật để dự đoán tốc độ tăng trưởng chính xác nhất.') }}
                </p>
                @if($children->user)
                    <div class="mt-2">
                        <a href="{{ route('admin.user.edit', $children->user->id) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 mt-1" target="_blank">
                            <i class="ti ti-user-edit me-1"></i> {{ __('Xem hồ sơ phụ huynh: ') . ($children->user->fullname ?? '') }}
                        </a>
                    </div>
                @endif
            </div>
        @endif
    @endif
</div>
