<div class="col-12 col-md-9">
    <div class="card custom-shadow border-0 rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h3 class="card-title fw-bold text-dark mb-0">
                <i class="ti ti-adjustments-alt text-primary me-2"></i>{{ __('Thông số chuẩn hóa độ tuổi') }} {{ $response->age }}
            </h3>
        </div>
        <div class="row card-body p-4">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">{{ __('Độ tuổi áp dụng') }}: <span class="text-danger">*</span></label>
                <div class="input-group">
                    <x-input type="number" min="1" max="20" name="age" :value="$response->age" :required="true" />
                    <span class="input-group-text">tuổi</span>
                </div>
                <small class="form-hint text-muted">{{ __('Độ tuổi của trẻ khi làm bài test IQ') }}</small>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">{{ __('Cỡ mẫu khảo sát (N)') }}:</label>
                <div class="input-group">
                    <x-input type="number" min="0" name="sample_size" :value="$response->sample_size" />
                    <span class="input-group-text">trẻ</span>
                </div>
                <small class="form-hint text-muted">{{ __('Số lượng trẻ trong tập mẫu dùng để chuẩn hóa') }}</small>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-primary">
                    <i class="ti ti-math-function me-1"></i>{{ __('Điểm thô trung bình (Mean μ)') }}: <span class="text-danger">*</span>
                </label>
                <x-input type="number" step="0.01" min="0" name="mean_score" :value="$response->mean_score" :required="true" />
                <small class="form-hint text-muted">{{ __('Giá trị trung bình điểm thô của nhóm tuổi này') }}</small>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold text-azure">
                    <i class="ti ti-chart-arrows me-1"></i>{{ __('Độ lệch chuẩn (StdDev σ)') }}: <span class="text-danger">*</span>
                </label>
                <x-input type="number" step="0.01" min="0.01" name="sd_score" :value="$response->sd_score" :required="true" />
                <small class="form-hint text-muted">{{ __('Độ phân tán chuẩn của nhóm tuổi (bắt buộc > 0)') }}</small>
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">{{ __('Điểm thô thấp nhất (Min)') }}:</label>
                <x-input type="number" step="0.01" name="min_raw_score" :value="$response->min_raw_score" />
            </div>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-semibold">{{ __('Điểm thô cao nhất (Max)') }}:</label>
                <x-input type="number" step="0.01" name="max_raw_score" :value="$response->max_raw_score" />
            </div>

            <div class="col-12 mb-3">
                <label class="form-label fw-semibold">{{ __('Phiên bản bảng chuẩn') }}:</label>
                <x-input type="text" name="version" :value="$response->version" placeholder="vd: baseline_v1" />
            </div>

            <div class="col-12 mb-3">
                <label class="form-label fw-semibold">{{ __('Ghi chú / Thông tin chuyên gia') }}:</label>
                <textarea class="form-control" name="note" rows="3" placeholder="{{ __('Nhập ghi chú hoặc mô tả về tập dữ liệu chuẩn...') }}">{{ $response->note }}</textarea>
            </div>
        </div>
    </div>
</div>
