<div class="col-12 col-xl-9 col-lg-8">
    {{-- Card 1: Core Parameters --}}
    <div class="iq-card">
        <div class="iq-card-header">
            <h3 class="iq-card-title">
                <i class="ti ti-plus text-primary fs-3"></i>
                <span>{{ __('Thông số chuẩn hóa mốc tuổi mới') }}</span>
            </h3>
            <span class="badge bg-blue-lt border border-blue-subtle d-none d-sm-inline-flex align-items-center gap-1">
                <i class="ti ti-chart-dots"></i>
                <span>{{ __('Mô hình phân phối chuẩn Wechsler (Gaussian)') }}</span>
            </span>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                {{-- Group 1: Age & Sample Size --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-calendar-user text-primary"></i>
                        <span>{{ __('Độ tuổi áp dụng') }}:</span>
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <x-input type="number" min="1" max="20" name="age" :value="old('age')" :required="true" placeholder="1 - 20" class="fw-bold fs-15 text-dark" />
                        <span class="input-group-text bg-light text-muted fw-semibold">tuổi</span>
                    </div>
                    <small class="form-hint text-muted mt-1">
                        <i class="ti ti-info-circle me-1"></i>{{ __('Độ tuổi của trẻ khi làm bài test IQ (1 - 20 tuổi)') }}
                    </small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-users text-purple"></i>
                        <span>{{ __('Cỡ mẫu khảo sát (N)') }}:</span>
                    </label>
                    <div class="input-group">
                        <x-input type="number" min="0" name="sample_size" :value="old('sample_size', 0)" class="fs-15" />
                        <span class="input-group-text bg-light text-muted fw-semibold">trẻ</span>
                    </div>
                    <small class="form-hint text-muted mt-1">
                        <i class="ti ti-info-circle me-1"></i>{{ __('Số lượng trẻ trong tập mẫu dùng để chuẩn hóa mốc tuổi này') }}
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
                                    <x-input type="number" step="0.01" min="0" name="mean_score" :value="old('mean_score')" :required="true" placeholder="vd: 10.00" class="fw-bold fs-15 text-primary border-primary-subtle" />
                                </div>
                                <small class="form-hint text-muted mt-1">
                                    {{ __('Kỳ vọng điểm thô thực tế của nhóm tuổi này') }}
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
                                    <x-input type="number" step="0.01" min="0.01" name="sd_score" :value="old('sd_score', 3.0)" :required="true" placeholder="vd: 3.50" class="fw-bold fs-15 text-azure border-info-subtle" />
                                </div>
                                <small class="form-hint text-muted mt-1">
                                    {{ __('Độ phân tán chuẩn của nhóm tuổi (bắt buộc > 0)') }}
                                </small>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top border-blue-subtle text-muted fs-12">
                            <i class="ti ti-bulb text-warning fs-4"></i>
                            <span>{{ __('Quy chuẩn Wechsler: 1 độ lệch chuẩn thực tế (σ) sẽ tương đương 15 điểm trên thang IQ chuẩn (Mean 100).') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Group 3: Min & Max Raw Score Range --}}
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-arrow-bar-down text-muted"></i>
                        <span>{{ __('Điểm thô thấp nhất (Min)') }}:</span>
                    </label>
                    <x-input type="number" step="0.01" name="min_raw_score" :value="old('min_raw_score', 0)" placeholder="vd: 0" />
                    <small class="form-hint text-muted">{{ __('Mức điểm thô nhỏ nhất từng ghi nhận') }}</small>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-arrow-bar-up text-muted"></i>
                        <span>{{ __('Điểm thô cao nhất (Max)') }}:</span>
                    </label>
                    <x-input type="number" step="0.01" name="max_raw_score" :value="old('max_raw_score', 30)" placeholder="vd: 30" />
                    <small class="form-hint text-muted">{{ __('Mức điểm thô lớn nhất từng ghi nhận') }}</small>
                </div>

                {{-- Group 4: Version & Clinical Notes --}}
                <div class="col-12">
                    <label class="form-label fw-semibold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-git-branch text-muted"></i>
                        <span>{{ __('Phiên bản bảng chuẩn') }}:</span>
                    </label>
                    <x-input type="text" name="version" :value="old('version', 'baseline_v1')" placeholder="vd: baseline_v1, wechsler_vietnam_2026" />
                    <small class="form-hint text-muted">{{ __('Mã định danh phiên bản dữ liệu chuẩn hóa') }}</small>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold text-dark d-flex align-items-center gap-1">
                        <i class="ti ti-notes text-muted"></i>
                        <span>{{ __('Ghi chú / Thông tin chuyên gia') }}:</span>
                    </label>
                    <textarea class="form-control" name="note" rows="3" placeholder="{{ __('Nhập ghi chú nguồn mẫu khảo sát, phương pháp thống kê hoặc lưu ý chuyên môn...') }}">{{ old('note') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
