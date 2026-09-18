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

<div class="phac-do-slim-container" id="phac-do-print-area">
    @if(!$latestPq)
        {{-- Trạng thái chưa có đánh giá PQ --}}
        <div class="empty-pq-state-card text-center py-5">
            <div class="empty-icon-circle mb-3">
                <i class="ti ti-run fs-1 text-muted"></i>
            </div>
            <h5 class="fw-semibold text-dark mb-2">{{ __('Chưa có đánh giá thể chất (PQ)') }}</h5>
            <p class="text-muted fs-13 mb-4 mx-auto" style="max-width: 460px; line-height: 1.6;">
                {{ __('Bé chưa có đợt đánh giá thể chất nào. Vui lòng cập nhật chiều cao và cân nặng trong phần đánh giá thể chất (PQ) để hệ thống có đủ dữ liệu tính toán dự báo V2 và lộ trình phác đồ.') }}
            </p>
            <a href="{{ route('admin.ratingPQ.index', ['child_id' => $children->id]) }}" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-2" target="_blank">
                <i class="ti ti-plus me-1"></i> {{ __('Xem & Thêm đánh giá PQ cho bé') }}
            </a>
        </div>
    @else
        {{-- Top Header: Tiêu đề thanh lịch & Nút In/Xuất --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 pb-3 border-bottom border-light-subtle no-print">
            <div class="d-flex align-items-center gap-2">
                <div class="header-icon-box">
                    <i class="ti ti-chart-line fs-3 text-indigo"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-semibold text-slate fs-15">{{ __('Trung Tâm Phác Đồ Tăng Trưởng Chiều Cao V2') }}</h5>
                    <span class="text-muted fs-12">
                        {{ __('Chuẩn đối chiếu:') }} 
                        <a href="https://www.who.int/tools/child-growth-standards/standards/length-height-for-age" target="_blank" class="text-muted text-decoration-underline hover-primary">
                            WHO Growth Standards
                        </a>
                    </span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 px-3 fw-normal" id="btn-print-phac-do">
                    <i class="ti ti-printer me-1 text-muted"></i> {{ __('In / Xuất Phác Đồ') }}
                </button>
            </div>
        </div>

        {{-- Cảnh báo nhẹ nếu thiếu chiều cao cha mẹ --}}
        @if($isMissingParentHeight)
            <div class="alert-slim-warning d-flex align-items-center rounded-2 mb-4 p-3 no-print">
                <i class="ti ti-info-circle fs-3 text-amber me-2 flex-shrink-0"></i>
                <div class="flex-grow-1 fs-12 text-slate">
                    <strong>{{ __('Lưu ý dữ liệu:') }}</strong> {{ __('Hồ sơ phụ huynh hiện thiếu chiều cao bố hoặc mẹ. Cập nhật chiều cao cha mẹ sẽ giúp thuật toán V2 tính toán yếu tố di truyền chuẩn xác nhất (±5 cm).') }}
                </div>
                @if($children->user)
                    <a href="{{ route('admin.user.edit', $children->user->id) }}" class="btn btn-link btn-sm text-primary text-decoration-none p-0 ms-3 text-nowrap fs-12 fw-medium" target="_blank">
                        {{ __('Cập nhật ngay') }} →
                    </a>
                @endif
            </div>
        @endif

        {{-- BƯỚC 1: TÌNH TRẠNG DẬY THÌ --}}
        <div class="slim-step-card mb-3 no-print">
            <div class="step-card-header d-flex align-items-center justify-content-between p-3 border-bottom border-light-subtle">
                <div class="d-flex align-items-center gap-2">
                    <span class="step-badge">{{ __('Bước 1') }}</span>
                    <span class="step-title">{{ __('Tình trạng dậy thì của bé (V2 Algorithm)') }}</span>
                </div>
                <span class="text-muted fs-11">
                    {{ __('Mỗi 12 tháng dậy thì rút ngắn ~1 năm tăng trưởng') }}
                </span>
            </div>
            <div class="step-card-body p-3">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-md-7">
                        <label class="form-label fs-11 text-muted text-uppercase fw-semibold mb-2">{{ __('Chọn nhanh thời gian đã dậy thì:') }}</label>
                        <div class="d-flex flex-wrap gap-2 puberty-chips-group">
                            <button type="button" class="btn btn-slim-chip puberty-chip active" data-months="0">
                                0 tháng (Chưa dậy thì)
                            </button>
                            <button type="button" class="btn btn-slim-chip puberty-chip" data-months="3">
                                3 tháng
                            </button>
                            <button type="button" class="btn btn-slim-chip puberty-chip" data-months="6">
                                6 tháng (~0.5 năm)
                            </button>
                            <button type="button" class="btn btn-slim-chip puberty-chip" data-months="12">
                                12 tháng (1 năm)
                            </button>
                            <button type="button" class="btn btn-slim-chip puberty-chip" data-months="18">
                                18 tháng (~1.5 năm)
                            </button>
                            <button type="button" class="btn btn-slim-chip puberty-chip" data-months="24">
                                24 tháng (2 năm)
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-md-5">
                        <label class="form-label fs-11 text-muted text-uppercase fw-semibold mb-2">{{ __('Hoặc nhập số tháng cụ thể:') }}</label>
                        <div class="input-group input-group-sm">
                            <input type="number" 
                                   id="input_puberty_months" 
                                   class="form-control text-center fw-semibold fs-13" 
                                   value="0" 
                                   min="0" 
                                   max="96" 
                                   placeholder="0">
                            <span class="input-group-text fs-12 text-muted">{{ __('tháng') }}</span>
                            <button type="button" class="btn btn-slim-action" id="btn-predict-v2">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="predict-spinner"></span>
                                <i class="ti ti-calculator me-1" id="predict-icon"></i> {{ __('Dự đoán CC V2') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BƯỚC 2: KẾT QUẢ DỰ BÁO V2 (STAT CARDS) --}}
        <div class="mb-3">
            <div class="d-flex align-items-center gap-2 mb-2">
                <span class="step-badge">{{ __('Bước 2') }}</span>
                <span class="step-title">{{ __('Kết quả dự báo V2 & Thống kê tăng trưởng') }}</span>
            </div>

            <div class="row g-2">
                <!-- Card 1: Dự đoán tuổi 19 -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-slim-card">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="stat-slim-label">{{ __('Dự đoán tuổi 19') }}</span>
                            <span class="badge-soft-indigo">±5 cm</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="stat-slim-val text-indigo" id="display-pred-height">{{ number_format($predHeight, 0) }}</span>
                            <span class="stat-slim-unit">cm</span>
                        </div>
                        <div class="stat-slim-sub">
                            <i class="ti ti-target me-1 text-muted"></i>{{ __('Tiềm năng tự nhiên') }}
                        </div>
                    </div>
                </div>

                <!-- Card 2: So với chuẩn WHO -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-slim-card">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="stat-slim-label">{{ __('So với chuẩn WHO') }}</span>
                            <i class="ti ti-world text-muted fs-4"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="stat-slim-val {{ $isTaller ? 'text-emerald' : 'text-slate' }}" id="display-who-diff">
                                {{ ($whoDiff > 0 ? '+' : '') . number_format($whoDiff, 1) }}
                            </span>
                            <span class="stat-slim-unit">cm</span>
                        </div>
                        <div class="stat-slim-sub" id="display-who-msg">
                            {{ $isTaller ? __('Đang cao hơn chuẩn WHO') : __('Đang thấp hơn chuẩn WHO') }}
                        </div>
                    </div>
                </div>

                <!-- Card 3: Tốc độ tăng trưởng -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-slim-card">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="stat-slim-label">{{ __('Tốc độ tăng trưởng') }}</span>
                            <span class="badge-soft-amber">Max 6.5 cm</span>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="stat-slim-val text-amber" id="display-speed-change">{{ number_format($speedChange, 1) }}</span>
                            <span class="stat-slim-unit">cm/năm</span>
                        </div>
                        <div class="stat-slim-sub">
                            <i class="ti ti-activity me-1 text-muted"></i>{{ __('Tăng trưởng 1 năm qua') }}
                        </div>
                    </div>
                </div>

                <!-- Card 4: Thể trạng BMI -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-slim-card">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="stat-slim-label">{{ __('Thể trạng BMI') }}</span>
                            <i class="ti ti-scale text-muted fs-4"></i>
                        </div>
                        <div class="d-flex align-items-baseline gap-1">
                            <span class="stat-slim-val text-slate" id="display-bmi-val">
                                {{ $regimenAdvice['bmi'] ?? '--' }}
                            </span>
                            <span class="stat-slim-unit">kg/m²</span>
                        </div>
                        <div class="stat-slim-sub fw-medium text-purple" id="display-bmi-status">
                            {{ $regimenAdvice['bmi_assessment'] ?? 'Đạt chuẩn' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BƯỚC 3: THIẾT LẬP MỤC TIÊU & CHẠY PHÁC ĐỒ --}}
        <div class="slim-step-card mb-3 no-print">
            <div class="step-card-header d-flex align-items-center justify-content-between p-3 border-bottom border-light-subtle">
                <div class="d-flex align-items-center gap-2">
                    <span class="step-badge">{{ __('Bước 3') }}</span>
                    <span class="step-title">{{ __('Thiết lập Mục Tiêu Chiều Cao & Chạy Phác Đồ') }}</span>
                </div>
                <div class="fs-12 text-muted">
                    {{ __('Dự đoán gốc:') }} <strong class="text-indigo" id="ref-pred-height">{{ number_format($predHeight, 0) }}</strong> cm
                </div>
            </div>
            <div class="step-card-body p-3">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-lg-7">
                        <label class="form-label fs-11 text-muted text-uppercase fw-semibold mb-2">{{ __('Chọn mức tăng thêm mục tiêu:') }}</label>
                        <div class="d-flex flex-wrap gap-2 target-chips-group">
                            <button type="button" class="btn btn-slim-chip target-chip" data-delta="2">
                                +2 cm
                            </button>
                            <button type="button" class="btn btn-slim-chip target-chip active" data-delta="5">
                                +5 cm (Khuyến nghị)
                            </button>
                            <button type="button" class="btn btn-slim-chip target-chip" data-delta="10">
                                +10 cm (Nỗ lực)
                            </button>
                            <button type="button" class="btn btn-slim-chip target-chip" data-delta="15">
                                +15 cm (Bứt phá)
                            </button>
                        </div>
                    </div>

                    <div class="col-12 col-lg-5">
                        <label class="form-label fs-11 text-muted text-uppercase fw-semibold mb-2">{{ __('Mục tiêu tuổi 19 (cm):') }}</label>
                        <div class="input-group input-group-sm">
                            <input type="number" 
                                   id="input_target_height" 
                                   class="form-control text-center fw-semibold fs-14 text-rose" 
                                   value="{{ $targetHeight ? round($targetHeight, 0) : ($predHeight > 0 ? round($predHeight + 5, 0) : 170) }}" 
                                   min="100" 
                                   max="220" 
                                   step="1"
                                   placeholder="175">
                            <span class="input-group-text fs-12 text-muted">cm</span>
                            <button type="button" class="btn btn-slim-action-rose" id="btn-run-chart-v2">
                                <span class="spinner-border spinner-border-sm me-1 d-none" id="chart-spinner"></span>
                                <i class="ti ti-chart-arrows me-1" id="chart-icon"></i> {{ __('Chạy Phác Đồ') }}
                            </button>
                        </div>
                        <div class="fs-11 text-muted mt-1" id="target-validation-hint">
                            <i class="ti ti-info-circle me-1 text-muted"></i>{{ __('Phác đồ sẽ phân bổ cột mốc phát triển theo từng năm đến tuổi 19.') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- BƯỚC 4: BIỂU ĐỒ & BẢNG LỘ TRÌNH CHI TIẾT --}}
        <div class="slim-step-card mb-3" id="chart-card-wrapper">
            <div class="step-card-header d-flex flex-wrap align-items-center justify-content-between gap-2 p-3 border-bottom border-light-subtle">
                <div class="d-flex align-items-center gap-2">
                    <span class="step-badge">{{ __('Bước 4') }}</span>
                    <span class="step-title">{{ __('Lộ trình Tăng trưởng Chiều cao (Hiện tại → 19 tuổi)') }}</span>
                </div>

                {{-- Legend thanh mảnh, tinh tế --}}
                <div class="d-flex flex-wrap align-items-center gap-3 fs-12">
                    <span class="d-inline-flex align-items-center">
                        <span class="legend-line" style="background: #d97706;"></span>
                        <span class="text-slate fs-11 fw-medium ms-1">{{ __('Dự đoán tự nhiên') }}</span>
                    </span>
                    <span class="d-inline-flex align-items-center">
                        <span class="legend-line-dashed" style="border-top-color: #64748b;"></span>
                        <span class="text-muted fs-11 fw-medium ms-1">{{ __('Chuẩn WHO') }}</span>
                    </span>
                    <span class="d-inline-flex align-items-center">
                        <span class="legend-line" style="background: #e05263;"></span>
                        <span class="text-rose fs-11 fw-medium ms-1">{{ __('Mục tiêu phác đồ') }}</span>
                    </span>
                </div>
            </div>

            <div class="step-card-body p-3">
                {{-- Container biểu đồ ApexCharts --}}
                <div id="phac-do-apexchart" style="min-height: 380px; width: 100%;">
                    <div class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-indigo mb-2" role="status"></div>
                        <div class="fs-12">{{ __('Đang tạo biểu đồ phác đồ...') }}</div>
                    </div>
                </div>

                {{-- BẢNG CỘT MỐC TĂNG TRƯỞNG CHI TIẾT TỪNG NĂM --}}
                <div class="mt-4 pt-3 border-top border-light-subtle">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="fs-12 fw-semibold text-slate">
                            <i class="ti ti-table me-1 text-muted"></i> {{ __('Bảng Cột Mốc Tăng Trưởng Chiều Cao Theo Năm') }}
                        </div>
                        <span class="badge-soft-neutral">{{ __('Đơn vị: cm') }}</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-slim text-center align-middle mb-0" id="table-milestones">
                            <thead>
                                <tr>
                                    <th style="width: 110px;">{{ __('Độ tuổi') }}</th>
                                    <th style="width: 150px; color: #d97706;">{{ __('Dự đoán') }}</th>
                                    <th style="width: 130px;">{{ __('Mức tăng/năm') }}</th>
                                    <th style="width: 150px; color: #64748b;">{{ __('Chuẩn WHO') }}</th>
                                    <th style="width: 160px; color: #e05263;">{{ __('Mục tiêu') }}</th>
                                    <th>{{ __('So sánh / Đánh giá') }}</th>
                                </tr>
                            </thead>
                            <tbody id="tbody-milestones">
                                {{-- Rendered dynamically via JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- LỜI KHUYÊN PHÁC ĐỒ & LỘ TRÌNH 3 VIỆC CẦN LÀM --}}
        <div class="slim-step-card mb-3" id="advice-card-wrapper">
            <div class="step-card-header p-3 border-bottom border-light-subtle">
                <div class="d-flex align-items-center gap-2">
                    <span class="step-badge">{{ __('Lộ trình') }}</span>
                    <span class="step-title" id="advice-header-title">
                        {{ $regimenAdvice['target_header'] ?? __('Để phát huy tối đa tiềm năng chiều cao, bé cần tập trung vào 3 việc: ăn đủ – vận động đều – sinh hoạt đúng:') }}
                    </span>
                </div>
            </div>

            <div class="step-card-body p-3">
                <div class="row g-2" id="advice-actions-container">
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
                        $icons = ['ti-tools-kitchen-2 text-amber', 'ti-stretching text-indigo', 'ti-moon-stars text-purple'];
                        $bgColors = ['bg-amber-soft', 'bg-indigo-soft', 'bg-purple-soft'];
                    @endphp

                    @foreach($actions as $idx => $act)
                        <div class="col-12 col-md-4">
                            <div class="action-slim-card h-100 p-3">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <div class="action-icon-box {{ $bgColors[$idx % 3] }}">
                                        <i class="ti {{ $icons[$idx % 3] }} fs-4"></i>
                                    </div>
                                    <h6 class="mb-0 fw-semibold text-slate fs-13">{{ $act['title'] }}</h6>
                                </div>
                                <p class="text-muted fs-12 mb-0" style="line-height: 1.6;">
                                    {{ $act['content'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Khối đánh giá BMI & Lời khuyên thể trạng --}}
                <div class="bmi-slim-card mt-3 p-3">
                    <div class="d-flex align-items-start gap-2">
                        <div class="action-icon-box bg-purple-soft flex-shrink-0 mt-1">
                            <i class="ti ti-heart-rate-monitor fs-4 text-purple"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                                <span class="fw-semibold text-slate fs-13">{{ __('Đánh giá thể trạng & BMI Chuẩn WHO:') }}</span>
                                <span class="badge-soft-purple" id="advice-bmi-badge">
                                    {{ $regimenAdvice['bmi_assessment'] ?? 'Đạt chuẩn' }}
                                </span>
                            </div>
                            <p class="text-muted fs-12 mb-1" id="advice-bmi-text">
                                {{ $regimenAdvice['bmi_advice'] ?? 'BMI ở mức trung bình so với bé cùng tuổi và giới tính. Tiếp tục duy trì chế độ ăn đa dạng, vận động phù hợp và thói quen sinh hoạt lành mạnh.' }}
                            </p>
                            <div class="text-muted fs-11 fst-italic">
                                {{ $regimenAdvice['bmi_general_note'] ?? 'Trẻ béo hay gầy không chỉ dựa vào cân nặng để đánh giá mà phải dùng chỉ số BMI để đánh giá trẻ đang gầy, đạt chuẩn hay có xu hướng thừa cân. Bé cần duy trì chỉ số BMI hợp lý để giúp chiều cao phát triển tốt hơn.' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- Giao diện Thanh Lịch, Đẹp Mảnh, Màu Sắc Êm Dịu --}}
<style>
    /* Scope: Container tổng */
    .phac-do-slim-container {
        font-family: inherit;
        color: #334155;
    }

    /* Cards thanh mảnh */
    .slim-step-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: border-color 0.2s ease;
    }
    .slim-step-card:hover {
        border-color: #cbd5e1;
    }
    .step-card-header {
        background: #ffffff;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
    .step-badge {
        font-size: 11px;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        padding: 2px 8px;
        border-radius: 6px;
        letter-spacing: 0.2px;
    }
    .step-title {
        font-size: 13.5px;
        font-weight: 600;
        color: #1e293b;
    }

    /* Header Icons */
    .header-icon-box {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #eef2ff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* Action Icons */
    .action-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .bg-indigo-soft { background: #eef2ff; }
    .bg-amber-soft { background: #fffbeb; }
    .bg-purple-soft { background: #faf5ff; }

    /* Text Colors thanh lịch */
    .text-slate { color: #1e293b !important; }
    .text-indigo { color: #4338ca !important; }
    .text-amber { color: #b45309 !important; }
    .text-emerald { color: #059669 !important; }
    .text-purple { color: #6d28d9 !important; }
    .text-rose { color: #be123c !important; }

    /* Alert thanh mảnh */
    .alert-slim-warning {
        background: #fffbeb;
        border: 1px solid #fef3c7;
    }

    /* Stat Cards kiểu Minimalist */
    .stat-slim-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
        height: 100%;
        transition: all 0.2s ease;
    }
    .stat-slim-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    }
    .stat-slim-label {
        font-size: 11px;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .stat-slim-val {
        font-size: 24px;
        font-weight: 700;
        line-height: 1.1;
    }
    .stat-slim-unit {
        font-size: 12px;
        font-weight: 500;
        color: #94a3b8;
    }
    .stat-slim-sub {
        font-size: 11.5px;
        color: #64748b;
        margin-top: 6px;
        line-height: 1.3;
    }

    /* Soft Badges */
    .badge-soft-indigo {
        background: #eef2ff;
        color: #4338ca;
        font-size: 10.5px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 6px;
    }
    .badge-soft-amber {
        background: #fffbeb;
        color: #b45309;
        font-size: 10.5px;
        font-weight: 600;
        padding: 2px 7px;
        border-radius: 6px;
    }
    .badge-soft-purple {
        background: #faf5ff;
        color: #6d28d9;
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .badge-soft-neutral {
        background: #f1f5f9;
        color: #64748b;
        font-size: 10.5px;
        font-weight: 500;
        padding: 2px 8px;
        border-radius: 6px;
    }

    /* Slim Chips */
    .btn-slim-chip {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        padding: 4px 13px;
        transition: all 0.15s ease;
    }
    .btn-slim-chip:hover {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #cbd5e1;
    }
    .puberty-chip.active {
        background: #4f46e5 !important;
        color: #ffffff !important;
        border-color: #4f46e5 !important;
        box-shadow: 0 2px 6px rgba(79, 70, 229, 0.25);
    }
    .target-chip.active {
        background: #be123c !important;
        color: #ffffff !important;
        border-color: #be123c !important;
        box-shadow: 0 2px 6px rgba(190, 18, 60, 0.25);
    }

    /* Slim Action Buttons */
    .btn-slim-action {
        background: #4f46e5;
        color: #ffffff;
        border: 1px solid #4338ca;
        font-weight: 500;
        font-size: 12px;
        padding: 0 14px;
        transition: all 0.15s ease;
    }
    .btn-slim-action:hover {
        background: #4338ca;
        color: #ffffff;
    }
    .btn-slim-action-rose {
        background: #be123c;
        color: #ffffff;
        border: 1px solid #9f1239;
        font-weight: 500;
        font-size: 12px;
        padding: 0 14px;
        transition: all 0.15s ease;
    }
    .btn-slim-action-rose:hover {
        background: #9f1239;
        color: #ffffff;
    }

    /* Legend Lines */
    .legend-line {
        width: 14px;
        height: 3px;
        border-radius: 2px;
        display: inline-block;
    }
    .legend-line-dashed {
        width: 14px;
        height: 0;
        border-top: 2px dashed #64748b;
        display: inline-block;
    }

    /* Table Slim */
    .table-slim {
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table-slim thead th {
        background: #f8fafc;
        border-top: 1px solid #e2e8f0;
        border-bottom: 1px solid #e2e8f0;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        padding: 8px 10px;
    }
    .table-slim tbody td {
        padding: 8px 10px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 12px;
        color: #334155;
    }
    .table-slim tbody tr:hover td {
        background: #f8fafc;
    }
    .table-slim tr.is-current-row td {
        background: #f0fdf4 !important;
        font-weight: 600;
    }

    /* Action & BMI Cards */
    .action-slim-card {
        background: #ffffff;
        border: 1px solid #f1f5f9;
        border-radius: 10px;
    }
    .bmi-slim-card {
        background: #faf5ff;
        border: 1px solid #ede9fe;
        border-radius: 10px;
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
        .slim-step-card {
            border: 1px solid #e2e8f0 !important;
            box-shadow: none !important;
            margin-bottom: 14px !important;
            page-break-inside: avoid;
        }
        #phac-do-apexchart {
            min-height: 320px !important;
        }
    }
</style>
