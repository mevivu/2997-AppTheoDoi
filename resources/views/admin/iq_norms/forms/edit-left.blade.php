<div class="col-12 col-xl-9 col-lg-8">
    {{-- Card 1: Core Parameters --}}
    <div class="iq-card">
        <div class="iq-card-header">
            <h3 class="iq-card-title">
                <i class="ti ti-adjustments-alt text-primary fs-3"></i>
                <span>{{ __('Thông số chuẩn hóa độ tuổi') }} {{ $response->age }}</span>
            </h3>
            <span class="badge bg-blue-lt border border-blue-subtle d-none d-sm-inline-flex align-items-center gap-1">
                <i class="ti ti-chart-dots"></i>
                <span>{{ __('Mô hình phân phối chuẩn Wechsler (Gaussian)') }}</span>
            </span>
        </div>
        <div class="card-body p-4">
            {{-- Quick Context Alert --}}
            <div class="alert alert-info border border-info-subtle d-flex align-items-start gap-2 p-3 mb-3 rounded-3 bg-info-subtle">
                <i class="ti ti-info-circle text-azure fs-3 flex-shrink-0 mt-1"></i>
                <div class="fs-12 text-dark">
                    <strong>{{ __('Hướng dẫn điều chỉnh thông số chuẩn:') }}</strong>
                    <div class="text-muted mt-1">
                        {{ __('Hệ thống sử dụng các thông số này để quy đổi điểm thô của bài test thành chỉ số IQ Wechsler theo chuẩn phân phối đối xứng. Nếu vừa được tái chuẩn hóa từ dữ liệu bài test thực tế, bạn vẫn có thể tinh chỉnh thủ công Mean và StdDev theo đánh giá của chuyên gia tâm lý.') }}
                    </div>
                </div>
            </div>

            <div class="row g-3">
                {{-- Group 1: Age & Sample Size --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-calendar-user text-primary"></i>
                        <span>{{ __('Độ tuổi áp dụng') }}:</span>
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <x-input type="number" min="1" max="20" name="age" :value="$response->age" :required="true" class="fw-bold fs-15 text-dark" />
                        <span class="input-group-text bg-light text-muted fw-semibold">tuổi</span>
                    </div>
                    <small class="form-hint text-muted mt-1">
                        <i class="ti ti-info-circle me-1"></i>{{ __('Độ tuổi của trẻ khi làm bài test IQ (hệ thống sẽ lấy bảng chuẩn theo tuổi này để tính IQ)') }}
                    </small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-users text-purple"></i>
                        <span>{{ __('Cỡ mẫu khảo sát (N)') }}:</span>
                    </label>
                    <div class="input-group">
                        <x-input type="number" min="0" name="sample_size" :value="$response->sample_size" class="fs-15" />
                        <span class="input-group-text bg-light text-muted fw-semibold">trẻ</span>
                    </div>
                    <small class="form-hint text-muted mt-1">
                        <i class="ti ti-info-circle me-1"></i>{{ __('Số lượng trẻ trong mẫu thống kê (cỡ mẫu N càng lớn, bảng chuẩn càng có ý nghĩa thống kê cao)') }}
                    </small>
                </div>

                {{-- Group 2: Core Math Well (Mean & StdDev) --}}
                <div class="col-12">
                    <div class="iq-params-well">
                        <span class="iq-params-badge">
                            <i class="ti ti-math-symbols me-1"></i>{{ __('Bộ tham số cốt lõi') }}
                        </span>

                        <div class="row g-3">
                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold text-primary d-flex align-items-center gap-1">
                                    <i class="ti ti-math-function fs-4"></i>
                                    <span>{{ __('Điểm thô trung bình (Mean μ)') }}:</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group shadow-xs">
                                    <span class="input-group-text bg-primary-subtle text-primary border-primary-subtle fw-bold">μ</span>
                                    <x-input type="number" step="0.01" min="0" name="mean_score" :value="$response->mean_score" :required="true" class="fw-bold fs-15 text-primary border-primary-subtle" />
                                </div>
                                <small class="form-hint text-muted mt-1">
                                    <i class="ti ti-help me-1 text-primary"></i>
                                    {{ __('Điểm số trung bình các trẻ ở tuổi này thường đạt được. Khi một bé đạt đúng điểm thô này, điểm IQ sẽ tương đương mốc 100.') }}
                                </small>
                            </div>

                            <div class="col-12 col-md-6">
                                <label class="form-label fw-bold text-azure d-flex align-items-center gap-1">
                                    <i class="ti ti-chart-arrows fs-4"></i>
                                    <span>{{ __('Độ lệch chuẩn (StdDev σ)') }}:</span>
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group shadow-xs">
                                    <span class="input-group-text bg-info-subtle text-azure border-info-subtle fw-bold">σ</span>
                                    <x-input type="number" step="0.01" min="0.01" name="sd_score" :value="$response->sd_score" :required="true" class="fw-bold fs-15 text-azure border-info-subtle" />
                                </div>
                                <small class="form-hint text-muted mt-1">
                                    <i class="ti ti-help me-1 text-azure"></i>
                                    {{ __('Độ phân tán của bài test xung quanh điểm trung bình. Trong thang Wechsler, 1 độ lệch chuẩn thực tế (σ) sẽ tương đương với 15 điểm IQ.') }}
                                </small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top border-blue-subtle text-muted fs-12">
                            <i class="ti ti-bulb text-warning fs-4 flex-shrink-0"></i>
                            <span>{{ __('Công thức Wechsler: Điểm IQ = 100 + 15 × (Điểm thô của trẻ - Mean μ) / StdDev σ. Kết quả được làm tròn thành số nguyên.') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Group 3: Min & Max Raw Score Range --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-arrow-bar-down text-muted"></i>
                        <span>{{ __('Điểm thô thấp nhất (Min)') }}:</span>
                    </label>
                    <x-input type="number" step="0.01" name="min_raw_score" :value="$response->min_raw_score" placeholder="vd: 0" />
                    <small class="form-hint text-muted">{{ __('Mức điểm thô nhỏ nhất từng ghi nhận ở độ tuổi này') }}</small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-arrow-bar-up text-muted"></i>
                        <span>{{ __('Điểm thô cao nhất (Max)') }}:</span>
                    </label>
                    <x-input type="number" step="0.01" name="max_raw_score" :value="$response->max_raw_score" placeholder="vd: 30" />
                    <small class="form-hint text-muted">{{ __('Mức điểm thô lớn nhất từng ghi nhận ở độ tuổi này') }}</small>
                </div>

                {{-- Group 4: Version & Clinical Notes --}}
                <div class="col-12">
                    <label class="form-label fw-semibold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-git-branch text-muted"></i>
                        <span>{{ __('Phiên bản bảng chuẩn') }}:</span>
                    </label>
                    <x-input type="text" name="version" :value="$response->version" placeholder="vd: baseline_v1, wechsler_vietnam_2026" />
                    <small class="form-hint text-muted">{{ __('Mã định danh phiên bản dữ liệu chuẩn hóa (giúp theo dõi lịch sử cập nhật bảng chuẩn)') }}</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-notes text-muted"></i>
                        <span>{{ __('Ghi chú / Thông tin chuyên gia') }}:</span>
                    </label>
                    <textarea class="form-control" name="note" rows="3" placeholder="{{ __('Nhập ghi chú nguồn mẫu khảo sát, phương pháp thống kê hoặc lưu ý chuyên môn...') }}">{{ $response->note }}</textarea>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Interactive Real-Time Wechsler IQ Simulator --}}
    <div class="iq-sim-card">
        <div class="iq-sim-header d-flex align-items-center justify-content-between flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-sm bg-white text-indigo rounded-circle">
                    <i class="ti ti-calculator fs-3"></i>
                </span>
                <div>
                    <h4 class="mb-0 fw-bold text-white fs-15">{{ __('Trình giả lập & Kiểm tra điểm IQ trực quan') }}</h4>
                    <span class="fs-12 text-white-50">{{ __('Thử nghiệm điểm thô để kiểm tra kết quả quy đổi thời gian thực') }}</span>
                </div>
            </div>
            <span class="badge bg-white text-indigo fw-bold px-3 py-1 rounded-pill">
                <i class="ti ti-activity me-1"></i>{{ __('Live Simulator') }}
            </span>
        </div>

        <div class="card-body p-4">
            {{-- Interactive Test Score Input --}}
            <div class="row align-items-center g-3 mb-4">
                <div class="col-12 col-md-7">
                    <label class="form-label fw-bold text-dark d-flex align-items-center justify-content-between mb-2">
                        <span>
                            <i class="ti ti-target me-1 text-primary"></i>
                            {{ __('Điểm thô bài test giả định (X):') }}
                        </span>
                        <span class="text-muted fs-12">{{ __('Kéo thanh trượt để thử nghiệm') }}</span>
                    </label>
                    <input type="range" class="iq-slider" id="sim_raw_score_slider" min="0" max="30" step="1" value="{{ round($response->mean_score) }}">
                    <div class="d-flex justify-content-between text-muted fs-11 mt-1">
                        <span>0 điểm</span>
                        <span>15 điểm</span>
                        <span>30 điểm</span>
                    </div>
                </div>

                <div class="col-12 col-md-5">
                    <label class="form-label fw-bold text-dark mb-2">{{ __('Hoặc nhập điểm thô:') }}</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white fw-bold text-primary">X =</span>
                        <input type="number" class="form-control fw-bold fs-16 text-dark" id="sim_raw_score_input" min="0" max="150" step="1" value="{{ round($response->mean_score) }}">
                        <span class="input-group-text bg-light text-muted">điểm</span>
                    </div>
                </div>
            </div>

            {{-- Live Dynamic Calculation Results --}}
            <div class="row g-3 mb-4">
                {{-- Score Card --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 bg-light-subtle rounded-3 p-3 h-100 text-center shadow-xs">
                        <div class="text-muted fs-11 fw-bold text-uppercase mb-1">{{ __('Điểm IQ Wechsler') }}</div>
                        <div class="iq-score-display my-1" id="sim-iq-score">100</div>
                        <div>
                            <span class="badge bg-blue-lt" id="sim-percentile">{{ __('Hạng phân vị: ~50.0%') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Classification Card --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 bg-light-subtle rounded-3 p-3 h-100 text-center d-flex flex-column justify-content-center shadow-xs">
                        <div class="text-muted fs-11 fw-bold text-uppercase mb-2">{{ __('Mức Phân Loại Trí Tuệ') }}</div>
                        <div>
                            <span class="badge bg-success text-white fs-13 py-1 px-3" id="sim-tier-badge">
                                {{ __('Trung bình') }}
                            </span>
                        </div>
                        <div class="text-muted fs-12 mt-2" id="sim-tier-desc">
                            {{ __('50.0% dân số chuẩn (Average)') }}
                        </div>
                    </div>
                </div>

                {{-- Z-Score Card --}}
                <div class="col-12 col-md-4">
                    <div class="card border-0 bg-light-subtle rounded-3 p-3 h-100 text-center d-flex flex-column justify-content-center shadow-xs">
                        <div class="text-muted fs-11 fw-bold text-uppercase mb-2">{{ __('Độ Lệch Chuẩn (Z-Score)') }}</div>
                        <div class="fs-20 fw-bold text-indigo" id="sim-z-score">Z = +0.00 σ</div>
                        <div class="text-muted fs-12 mt-1">
                            {{ __('Độ lệch so với trung bình nhóm') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Live Formula Preview --}}
            <div class="bg-white border rounded-3 p-2 text-center text-muted fs-12 font-monospace mb-3" id="sim-formula-preview">
                IQ = 100 + 15 × (X - {{ number_format($response->mean_score, 2) }}) / {{ number_format($response->sd_score, 2) }} = <strong>100</strong>
            </div>

            {{-- Visual 7-Tier Spectrum Bar --}}
            <div>
                <label class="form-label fw-bold text-dark fs-12 mb-2 d-flex align-items-center justify-content-between">
                    <span>
                        <i class="ti ti-chart-infographic me-1 text-primary"></i>
                        {{ __('Vị trí điểm số trên phổ phân phối Wechsler:') }}
                    </span>
                    <span class="text-muted fs-11">{{ __('Mũi tên chỉ phân vùng điểm số hiện tại') }}</span>
                </label>

                <div class="iq-spectrum">
                    <div class="iq-spectrum-item" id="tier-0" style="background: #dc2626;" title="Dưới 70: Rất kém (2.2%)">
                        &lt; 70
                    </div>
                    <div class="iq-spectrum-item" id="tier-70" style="background: #ea580c;" title="70 - 79: Giới hạn chậm (6.7%)">
                        70 - 79
                    </div>
                    <div class="iq-spectrum-item" id="tier-80" style="background: #d97706;" title="80 - 89: Dưới trung bình (16.1%)">
                        80 - 89
                    </div>
                    <div class="iq-spectrum-item active" id="tier-90" style="background: #16a34a;" title="90 - 109: Trung bình (50.0%)">
                        90 - 109
                    </div>
                    <div class="iq-spectrum-item" id="tier-110" style="background: #06b6d4;" title="110 - 119: Trên trung bình (16.1%)">
                        110 - 119
                    </div>
                    <div class="iq-spectrum-item" id="tier-120" style="background: #2563eb;" title="120 - 129: Ưu tú (6.7%)">
                        120 - 129
                    </div>
                    <div class="iq-spectrum-item" id="tier-130" style="background: #7c3aed;" title="130+: Rất ưu tú (2.2%)">
                        130+
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
