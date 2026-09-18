@php
    $latestPq = $children->ratingPQs()->latest()->first();

    $predHeight = $heightPrediction['predicting_adult_height'] ?? 0;
    $speedChange = $heightPrediction['speed_change'] ?? 0;
    $whoDiff = $heightPrediction['height_comparison']['height_who_current'] ?? 0;
    $isTaller = $heightPrediction['height_comparison']['is_taller_than_who'] ?? ($whoDiff > 0);
    $whoMessage = $heightPrediction['height_comparison']['message'] ?? 'Chăm con 360 sẽ dự đoán chiều cao cho con bạn';
    $advice = $heightPrediction['advice_message'] ?? '';

    $fatherHeight = (float)($children->user?->father_height ?? 0);
    $motherHeight = (float)($children->user?->mother_height ?? 0);
    $isMissingParentHeight = ($fatherHeight <= 0 || $motherHeight <= 0);

    $currentAge = $heightChart['current_age'] ?? (round($children->birthday ? \Carbon\Carbon::now()->diffInDays($children->birthday) / 365.3 : 5, 1));
    $currentHeight = $heightChart['current_height'] ?? ($latestPq?->height ?? 0);
    $targetHeight = $heightChart['target_height'] ?? null;
    $regimenAdvice = $heightChart['regimen_advice'] ?? null;
    $predictionLine = $heightChart['prediction_line'] ?? [];
    $whoLine = $heightChart['who_line'] ?? [];
    $targetLine = $heightChart['target_line'] ?? [];
@endphp

<div class="phac-do-v2-container" id="phac-do-print-area">
    @if(!$latestPq)
        {{-- Trạng thái chưa có đánh giá PQ --}}
        <div class="empty-pq-state-box text-center py-5">
            <div class="state-icon mb-3">
                <span class="avatar avatar-xl bg-orange-lt text-orange rounded-circle">
                    <i class="ti ti-run fs-1"></i>
                </span>
            </div>
            <h4 class="fw-bold text-dark mb-2">{{ __('Chưa có đánh giá thể chất (PQ)') }}</h4>
            <p class="text-muted fs-14 mb-4 mx-auto" style="max-width: 500px;">
                {{ __('Bé chưa có đợt đánh giá thể chất nào. Vui lòng cập nhật chiều cao và cân nặng trong phần đánh giá thể chất (PQ) để hệ thống có dữ liệu tính toán dự báo V2 và phác đồ tăng trưởng.') }}
            </p>
            <a href="{{ route('admin.ratingPQ.index', ['child_id' => $children->id]) }}" class="btn btn-primary px-4 py-2 rounded-3" target="_blank">
                <i class="ti ti-plus me-1"></i> {{ __('Xem & Thêm đánh giá PQ cho bé') }}
            </a>
        </div>
    @else
        {{-- Header: Nguồn dữ liệu & Nút Xuất/In --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom no-print">
            <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-sm bg-blue-lt text-primary rounded-circle">
                    <i class="ti ti-chart-arrows fs-3"></i>
                </span>
                <div>
                    <h4 class="mb-0 fw-bold text-dark fs-16">{{ __('Trung Tâm Phác Đồ Phát Triển Chiều Cao V2') }}</h4>
                    <span class="text-muted fs-12">
                        {{ __('Chuẩn đối chiếu: Tổ chức Y tế Thế giới') }} 
                        (<a href="https://www.who.int/tools/child-growth-standards/standards/length-height-for-age" target="_blank" class="text-primary text-decoration-underline">WHO Growth Standards</a>)
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3 shadow-sm" id="btn-print-phac-do">
                    <i class="ti ti-printer me-1"></i> {{ __('In / Xuất Phác Đồ') }}
                </button>
            </div>
        </div>

        {{-- Cảnh báo nếu thiếu chiều cao cha mẹ --}}
        @if($isMissingParentHeight)
            <div class="alert alert-warning d-flex align-items-center rounded-3 mb-4 p-3 shadow-sm no-print" role="alert">
                <i class="ti ti-alert-triangle fs-2 text-warning me-3 flex-shrink-0"></i>
                <div class="flex-grow-1">
                    <h5 class="alert-heading mb-1 fw-bold">{{ __('Chưa cập nhật đầy đủ chiều cao của Bố / Mẹ') }}</h5>
                    <p class="mb-0 fs-13 text-muted">
                        {{ __('Hồ sơ phụ huynh hiện thiếu chiều cao bố hoặc mẹ. Để thuật toán V2 tính toán yếu tố di truyền chuẩn xác nhất (+/-5cm), vui lòng cập nhật hồ sơ phụ huynh.') }}
                    </p>
                </div>
                @if($children->user)
                    <a href="{{ route('admin.user.edit', $children->user->id) }}" class="btn btn-warning btn-sm ms-3 text-nowrap rounded-pill" target="_blank">
                        <i class="ti ti-user-edit me-1"></i> {{ __('Cập nhật hồ sơ') }}
                    </a>
                @endif
            </div>
        @endif

        {{-- BƯỚC 1: NHẬP SỐ THÁNG DẬY THÌ --}}
        <div class="card mb-4 border-0 shadow-sm rounded-3 overflow-hidden no-print">
            <div class="card-header bg-gradient-blue text-white py-2 px-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-primary fw-bold px-2 py-1">BƯỚC 1</span>
                    <h5 class="mb-0 fw-bold fs-14 text-white">{{ __('Tình trạng dậy thì của bé (V2 Algorithm)') }}</h5>
                </div>
                <span class="badge bg-white-lt text-white fs-11">
                    <i class="ti ti-sparkles me-1"></i>{{ __('Chuẩn xác từng giai đoạn') }}
                </span>
            </div>
            <div class="card-body p-3 bg-light-subtle">
                <p class="text-muted fs-13 mb-3">
                    {{ __('Số tháng bé đã có dấu hiệu dậy thì (ngực nhú/vỡ giọng, tăng vọt chiều cao...). Thuật toán V2 sẽ tính toán độ rút ngắn giai đoạn tăng trưởng để dự đoán chính xác chiều cao mốc 19 tuổi.') }}
                </p>

                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-7">
                        <label class="form-label fs-12 fw-bold text-muted mb-2">{{ __('Chọn nhanh số tháng dậy thì:') }}</label>
                        <div class="d-flex flex-wrap gap-2 puberty-chips-group">
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill puberty-chip active" data-months="0">
                                <i class="ti ti-check me-1 d-none chip-check"></i>0 tháng (Chưa dậy thì)
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill puberty-chip" data-months="3">
                                3 tháng
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill puberty-chip" data-months="6">
                                6 tháng (~0.5 năm)
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill puberty-chip" data-months="12">
                                12 tháng (1 năm)
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill puberty-chip" data-months="18">
                                18 tháng (~1.5 năm)
                            </button>
                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill puberty-chip" data-months="24">
                                24 tháng (2 năm)
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-md-5">
                        <label class="form-label fs-12 fw-bold text-muted mb-2">{{ __('Hoặc nhập số tháng tùy chọn:') }}</label>
                        <div class="input-group">
                            <input type="number" 
                                   id="input_puberty_months" 
                                   class="form-control form-control-sm text-center fw-bold fs-14" 
                                   value="0" 
                                   min="0" 
                                   max="96" 
                                   placeholder="0">
                            <span class="input-group-text fs-13">{{ __('tháng') }}</span>
                            <button type="button" class="btn btn-primary btn-sm px-3" id="btn-predict-v2">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="predict-spinner"></span>
                                <i class="ti ti-calculator me-1" id="predict-icon"></i> {{ __('Dự đoán CC V2') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BƯỚC 2: KẾT QUẢ DỰ BÁO V2 (TỔNG QUAN) --}}
        <div class="mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge bg-primary text-white fw-bold px-2 py-1">BƯỚC 2</span>
                <h5 class="mb-0 fw-bold fs-15 text-dark">{{ __('Kết quả dự báo V2 & Thống kê tăng trưởng') }}</h5>
            </div>

            <div class="row g-3">
                <!-- Thẻ 1: Dự đoán chiều cao mốc 19 tuổi -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-gradient-azure-lt border-start border-azure border-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fs-11 fw-bold text-azure">{{ __('Dự đoán tuổi 19') }}</span>
                            <span class="badge bg-azure text-white rounded-pill px-2 fs-10">±5 cm</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h2 class="mb-0 fw-bolder text-azure fs-28" id="display-pred-height">{{ number_format($predHeight, 0) }}</h2>
                            <span class="fs-14 fw-bold text-muted">cm</span>
                        </div>
                        <div class="text-muted fs-11 mt-2">
                            <i class="ti ti-target me-1"></i>{{ __('Tiềm năng trưởng thành tự nhiên') }}
                        </div>
                    </div>
                </div>

                <!-- Thẻ 2: So sánh chuẩn WHO -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-gradient-green-lt border-start border-success border-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fs-11 fw-bold text-success">{{ __('So với chuẩn WHO') }}</span>
                            <i class="ti ti-world text-success fs-4"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h2 class="mb-0 fw-bolder text-success fs-28" id="display-who-diff">
                                {{ ($whoDiff > 0 ? '+' : '') . number_format($whoDiff, 1) }}
                            </h2>
                            <span class="fs-14 fw-bold text-muted">cm</span>
                        </div>
                        <div class="text-muted fs-11 mt-2" id="display-who-msg">
                            {{ $isTaller ? __('Đang cao hơn chuẩn WHO cùng độ tuổi') : __('Đang thấp hơn chuẩn WHO cùng độ tuổi') }}
                        </div>
                    </div>
                </div>

                <!-- Thẻ 3: Tốc độ tăng trưởng trong năm -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-gradient-orange-lt border-start border-orange border-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fs-11 fw-bold text-orange">{{ __('Tốc độ tăng trưởng') }}</span>
                            <span class="badge bg-orange text-white rounded-pill px-2 fs-10">Max 6.5 cm</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h2 class="mb-0 fw-bolder text-orange fs-28" id="display-speed-change">{{ number_format($speedChange, 1) }}</h2>
                            <span class="fs-14 fw-bold text-muted">cm/năm</span>
                        </div>
                        <div class="text-muted fs-11 mt-2">
                            <i class="ti ti-activity me-1"></i>{{ __('Đánh giá qua lịch sử PQ') }}
                        </div>
                    </div>
                </div>

                <!-- Thẻ 4: Thể trạng BMI hiện tại -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-gradient-purple-lt border-start border-purple border-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-uppercase fs-11 fw-bold text-purple">{{ __('Thể trạng BMI') }}</span>
                            <i class="ti ti-scale text-purple fs-4"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <h2 class="mb-0 fw-bolder text-purple fs-28" id="display-bmi-val">
                                {{ $regimenAdvice['bmi'] ?? '--' }}
                            </h2>
                            <span class="fs-14 fw-bold text-muted">kg/m²</span>
                        </div>
                        <div class="text-purple fw-bold fs-11 mt-2" id="display-bmi-status">
                            {{ $regimenAdvice['bmi_assessment'] ?? 'Đạt chuẩn' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BƯỚC 3: THIẾT LẬP MỤC TIÊU & CHẠY PHÁC ĐỒ --}}
        <div class="card mb-4 border-0 shadow-sm rounded-3 overflow-hidden no-print" style="border: 1px solid #fecdd3 !important;">
            <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%);">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-white text-danger fw-bold px-2 py-1">BƯỚC 3</span>
                    <h5 class="mb-0 fw-bold fs-14 text-white">{{ __('Thiết lập Mục Tiêu Chiều Cao & Chạy Phác Đồ Tăng Trưởng') }}</h5>
                </div>
                <span class="badge bg-white-lt text-white fs-11">
                    <i class="ti ti-flame me-1"></i>{{ __('Lộ trình 3 việc: Ăn - Tập - Ngủ') }}
                </span>
            </div>
            <div class="card-body p-3 bg-white">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-7">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <label class="form-label fs-12 fw-bold text-muted mb-0">{{ __('Chọn mục tiêu tăng thêm so với dự đoán:') }}</label>
                            <span class="badge bg-light text-dark fs-11">
                                {{ __('Dự đoán hiện tại:') }} <strong class="text-primary" id="ref-pred-height">{{ number_format($predHeight, 0) }}</strong> cm
                            </span>
                        </div>
                        <div class="d-flex flex-wrap gap-2 target-chips-group">
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill target-chip" data-delta="2">
                                +2 cm
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill target-chip active" data-delta="5">
                                +5 cm (Khuyến nghị)
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill target-chip" data-delta="10">
                                +10 cm (Nỗ lực)
                            </button>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill target-chip" data-delta="15">
                                +15 cm (Bứt phá)
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <label class="form-label fs-12 fw-bold text-muted mb-2">{{ __('Chiều cao mục tiêu tuổi 19 (cm):') }}</label>
                        <div class="input-group">
                            <input type="number" 
                                   id="input_target_height" 
                                   class="form-control form-control-sm text-center fw-bolder fs-15 text-danger" 
                                   value="{{ $targetHeight ? round($targetHeight, 0) : ($predHeight > 0 ? round($predHeight + 5, 0) : 170) }}" 
                                   min="100" 
                                   max="220" 
                                   step="1"
                                   placeholder="VD: 175">
                            <span class="input-group-text fs-13 fw-bold text-danger">cm</span>
                            <button type="button" class="btn btn-danger btn-sm px-3 fw-bold" id="btn-run-chart-v2">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="chart-spinner"></span>
                                <i class="ti ti-chart-line me-1" id="chart-icon"></i> {{ __('Chạy Phác Đồ') }}
                            </button>
                        </div>
                        <div class="fs-11 text-muted mt-1" id="target-validation-hint">
                            <i class="ti ti-info-circle text-danger me-1"></i>{{ __('Mục tiêu phác đồ tự động phân bổ đều đặn đến năm 19 tuổi.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BƯỚC 4: BIỂU ĐỒ & BẢNG LỘ TRÌNH CHI TIẾT --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4" id="chart-card-wrapper">
            <div class="card-header bg-white py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-danger text-white fw-bold px-2 py-1">BƯỚC 4</span>
                    <h5 class="mb-0 fw-bold fs-15 text-dark">{{ __('Biểu đồ Phác đồ Tăng trưởng Chiều cao (Hiện tại → 19 tuổi)') }}</h5>
                </div>

                {{-- Legend tùy chỉnh --}}
                <div class="d-flex flex-wrap align-items-center gap-3 fs-12">
                    <span class="d-inline-flex align-items-center">
                        <span class="legend-indicator" style="background: #ea580c; width: 14px; height: 4px; border-radius: 2px; display: inline-block; margin-right: 6px;"></span>
                        <strong class="text-dark">{{ __('Dự đoán tự nhiên') }}</strong>
                    </span>
                    <span class="d-inline-flex align-items-center">
                        <span class="legend-indicator" style="background: #0284c7; width: 14px; height: 3px; border-top: 2px dashed #0284c7; display: inline-block; margin-right: 6px;"></span>
                        <strong class="text-muted">{{ __('Chuẩn WHO') }}</strong>
                    </span>
                    <span class="d-inline-flex align-items-center">
                        <span class="legend-indicator" style="background: #dc2626; width: 14px; height: 4px; border-radius: 2px; display: inline-block; margin-right: 6px;"></span>
                        <strong class="text-danger">{{ __('Mục tiêu phác đồ') }}</strong>
                    </span>
                </div>
            </div>

            <div class="card-body p-4">
                {{-- Container biểu đồ ApexCharts --}}
                <div id="phac-do-apexchart" style="min-height: 400px; width: 100%;">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border text-primary mb-2" role="status"></div>
                        <div>{{ __('Đang tải biểu đồ phác đồ chiều cao...') }}</div>
                    </div>
                </div>

                {{-- BẢNG CỘT MỐC TĂNG TRƯỞNG CHI TIẾT TỪNG NĂM --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0 fw-bold fs-14 text-dark">
                            <i class="ti ti-table me-1 text-primary"></i> {{ __('Bảng Cột Mốc Tăng Trưởng Chiều Cao Theo Năm (Mốc 19 tuổi)') }}
                        </h5>
                        <span class="badge bg-light text-muted fs-11">{{ __('Đơn vị: cm') }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-vcenter table-sm table-bordered table-striped table-hover text-center align-middle" id="table-milestones">
                            <thead class="table-light fs-12">
                                <tr>
                                    <th style="width: 100px;">{{ __('Độ tuổi') }}</th>
                                    <th class="text-orange" style="width: 160px;">{{ __('Chiều cao Dự đoán') }}</th>
                                    <th style="width: 140px;">{{ __('Mức tăng/năm') }}</th>
                                    <th class="text-info" style="width: 160px;">{{ __('Chuẩn WHO') }}</th>
                                    <th class="text-danger" style="width: 180px;">{{ __('Mục tiêu Phác đồ') }}</th>
                                    <th>{{ __('Chênh lệch / Đánh giá') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-milestones" class="fs-12 fw-medium">
                                {{-- Rendered dynamically via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- LỜI KHUYÊN PHÁC ĐỒ & LỘ TRÌNH 3 VIỆC CẦN LÀM --}}
        <div class="card border-0 shadow-sm rounded-3 mb-4 overflow-hidden" id="advice-card-wrapper">
            <div class="card-header bg-white py-3 px-4 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success text-white fw-bold px-2 py-1">{{ __('HÀNH ĐỘNG') }}</span>
                    <h5 class="mb-0 fw-bold fs-15 text-dark" id="advice-header-title">
                        {{ $regimenAdvice['target_header'] ?? __('Để phát huy tối đa tiềm năng chiều cao, bé cần tập trung vào 3 việc: ăn đủ – vận động đều – sinh hoạt đúng:') }}
                    </h5>
                </div>
            </div>

            <div class="card-body p-4 bg-light-subtle">
                <div class="row g-3" id="advice-actions-container">
                    @php
                        $actions = $regimenAdvice['target_actions'] ?? [
                            [
                                'title' => '1. Ăn đủ và đa dạng',
                                'content' => 'Cho con ăn đa dạng, đủ đạm từ thịt, cá, trứng, đậu; bổ sung thêm các thực phẩm giàu canxi như sữa. Kết hợp các loại rau xanh cung cấp đầy đủ các loại Vitamin (D3, K2) và khoáng chất (kẽm, Magie). Không dùng nước ngọt, bánh kẹo và đồ ăn quá nhiều đường.'
                            ],
                            [
                                'title' => '2. Vận động đều đặn',
                                'content' => 'Khuyến khích bé vận động khoảng 60 phút mỗi ngày, phù hợp với độ tuổi và thể trạng. Có thể lựa chọn nhảy dây, bóng rổ, cầu lông, bơi lội và các bài tập bổ trợ chiều cao trong CHĂM CON 360. Duy trì đều đặn quan trọng hơn tập một cách quá sức.'
                            ],
                            [
                                'title' => '3. Ngủ đủ và sinh hoạt lành mạnh',
                                'content' => 'Khuyến khích bé ngủ sớm trước 22 giờ, ngủ đủ theo độ tuổi 8-10 tiếng/ngày, vui chơi ngoài trời và duy trì tinh thần vui vẻ, thoải mái.'
                            ],
                        ];
                        $icons = ['ti-tools-kitchen-2 text-orange', 'ti-stretching text-primary', 'ti-moon-stars text-purple'];
                        $bgColors = ['bg-orange-lt', 'bg-blue-lt', 'bg-purple-lt'];
                    @endphp

                    @foreach($actions as $idx => $act)
                        <div class="col-12 col-md-4">
                            <div class="card border-0 shadow-sm rounded-3 h-100 p-3 bg-white">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="avatar avatar-sm {{ $bgColors[$idx % 3] }} rounded-circle">
                                        <i class="ti {{ $icons[$idx % 3] }} fs-4"></i>
                                    </span>
                                    <h5 class="mb-0 fw-bold fs-14 text-dark action-item-title">{{ $act['title'] }}</h5>
                                </div>
                                <p class="text-muted fs-12 mb-0 action-item-content" style="line-height: 1.6;">
                                    {{ $act['content'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Khối đánh giá BMI & Lời khuyên thể trạng --}}
                <div class="card border-0 shadow-sm rounded-3 mt-3 p-3 bg-white border-start border-purple border-4">
                    <div class="d-flex align-items-start gap-3">
                        <span class="avatar avatar-md bg-purple-lt text-purple rounded-circle flex-shrink-0">
                            <i class="ti ti-heart-rate-monitor fs-3"></i>
                        </span>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                <h5 class="mb-0 fw-bold text-dark fs-14">{{ __('Đánh giá thể trạng & BMI Chuẩn WHO') }}</h5>
                                <span class="badge bg-purple-lt text-purple fw-bold fs-11" id="advice-bmi-badge">
                                    {{ $regimenAdvice['bmi_assessment'] ?? 'Đạt chuẩn' }}
                                </span>
                            </div>
                            <p class="text-muted fs-12 mb-2" id="advice-bmi-text">
                                {{ $regimenAdvice['bmi_advice'] ?? 'BMI ở mức trung bình so với bé cùng tuổi và giới tính. Tiếp tục duy trì chế độ ăn đa dạng, vận động phù hợp và thói quen sinh hoạt lành mạnh.' }}
                            </p>
                            <div class="text-muted fs-11 fst-italic">
                                <i class="ti ti-info-circle me-1"></i>
                                {{ $regimenAdvice['bmi_general_note'] ?? 'Trẻ béo hay gầy không chỉ dựa vào cân nặng để đánh giá mà phải dùng chỉ số BMI để đánh giá trẻ đang gầy, đạt chuẩn hay có xu hướng thừa cân. Bé cần duy trì chỉ số BMI hợp lý để giúp chiều cao phát triển tốt hơn.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Print & Custom CSS --}}
<style>
    /* CSS cho Chips & Nút tương tác */
    .puberty-chip, .target-chip {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        font-size: 12px;
        padding: 5px 14px;
        font-weight: 600;
    }
    .puberty-chip.active {
        background-color: #2563eb !important;
        color: #ffffff !important;
        border-color: #2563eb !important;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
    }
    .target-chip.active {
        background-color: #dc2626 !important;
        color: #ffffff !important;
        border-color: #dc2626 !important;
        box-shadow: 0 2px 6px rgba(220, 38, 38, 0.3);
    }

    /* Bảng Cột Mốc */
    #table-milestones th {
        vertical-align: middle;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        font-size: 11px;
    }
    #table-milestones tr.is-current-row {
        background-color: #f0fdf4 !important;
        font-weight: 700;
    }
    #table-milestones tr.is-puberty-end-row {
        background-color: #eff6ff !important;
    }

    /* Print Stylesheet */
    @media print {
        body * {
            visibility: hidden;
        }
        #phac-do-print-area, #phac-do-print-area * {
            visibility: visible;
        }
        #phac-do-print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100% !important;
            padding: 10px !important;
            background: #ffffff !important;
        }
        .no-print, .btn, .nav, .page-header-custom, .child-hero-banner {
            display: none !important;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
            margin-bottom: 20px !important;
            page-break-inside: avoid;
        }
        #phac-do-apexchart {
            min-height: 350px !important;
        }
    }
</style>
