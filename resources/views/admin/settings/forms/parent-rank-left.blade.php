@php
    $settingsByKey = $settings->keyBy('setting_key');
@endphp

<div class="d-flex flex-column gap-3">
    {{-- Card 1: Kích hoạt hệ thống --}}
    <div class="card rank-tier-card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-power text-primary fs-3"></i>
                <h3 class="card-title mb-0">{{ __('Trạng thái Hệ thống Phân hạng Bố mẹ') }}</h3>
            </div>
            @php $activeSetting = $settingsByKey->get('parent_rank_active'); @endphp
            @if ($activeSetting)
                <div class="form-check form-switch mb-0">
                    <input type="hidden" name="parent_rank_active" value="0">
                    <input class="form-check-input" type="checkbox" id="parent_rank_active" name="parent_rank_active" value="1"
                        {{ $activeSetting->plain_value == '1' ? 'checked' : '' }}>
                </div>
            @endif
        </div>
        <div class="card-body">
            <p class="text-muted mb-0 small">
                {{ __('Khi được bật, hệ thống sẽ tự động tổng hợp thời gian dùng app, tần suất mở app, số bài đánh giá và điểm số các chỉ số của con để tính điểm và phân hạng phụ huynh hàng tháng.') }}
            </p>
        </div>
    </div>

    {{-- Card 2: Trọng số 4 tiêu chí (%) --}}
    <div class="card rank-tier-card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-chart-pie text-indigo fs-3"></i>
                <div>
                    <h3 class="card-title mb-0">{{ __('Trọng số 4 Tiêu chí Đánh giá') }}</h3>
                    <div class="small text-muted">{{ __('Tổng trọng số của 4 tiêu chí phải bằng đúng 100%') }}</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="small fw-bold">{{ __('Tổng:') }}</span>
                <span id="totalWeightDisplay" class="badge bg-success fs-5 px-3 py-1">100%</span>
            </div>
        </div>
        <div class="card-body">
            <div id="weightWarning" class="alert alert-danger d-none mb-3 py-2 px-3 small">
                <i class="ti ti-alert-triangle me-1"></i>
                {{ __('Cảnh báo: Tổng 4 trọng số hiện đang khác 100%. Vui lòng điều chỉnh lại để công thức tính điểm chính xác.') }}
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label" for="input_parent_rank_weight_usage">
                        <i class="ti ti-clock text-primary me-1"></i>
                        {{ __('1. Thời gian sử dụng app (%)') }}
                    </label>
                    <input type="number" step="1" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_usage" name="parent_rank_weight_usage"
                           value="{{ $settingsByKey->get('parent_rank_weight_usage')?->plain_value ?? 30 }}">
                    <div class="form-hint">{{ __('Đóng góp của tổng phút dùng app trong tháng (mặc định: 30%)') }}</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="input_parent_rank_weight_frequency">
                        <i class="ti ti-calendar-event text-success me-1"></i>
                        {{ __('2. Tần suất mở app (%)') }}
                    </label>
                    <input type="number" step="1" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_frequency" name="parent_rank_weight_frequency"
                           value="{{ $settingsByKey->get('parent_rank_weight_frequency')?->plain_value ?? 25 }}">
                    <div class="form-hint">{{ __('Đóng góp của số ngày vào app trong tháng (mặc định: 25%)') }}</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="input_parent_rank_weight_assessment">
                        <i class="ti ti-checklist text-warning me-1"></i>
                        {{ __('3. Số bài đánh giá cho con (%)') }}
                    </label>
                    <input type="number" step="1" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_assessment" name="parent_rank_weight_assessment"
                           value="{{ $settingsByKey->get('parent_rank_weight_assessment')?->plain_value ?? 25 }}">
                    <div class="form-hint">{{ __('Đóng góp của số bài trắc nghiệm/học bạ cho con (mặc định: 25%)') }}</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label" for="input_parent_rank_weight_child_score">
                        <i class="ti ti-heart-rate-monitor text-danger me-1"></i>
                        {{ __('4. Chỉ số phát triển của con (%)') }}
                    </label>
                    <input type="number" step="1" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_child_score" name="parent_rank_weight_child_score"
                           value="{{ $settingsByKey->get('parent_rank_weight_child_score')?->plain_value ?? 20 }}">
                    <div class="form-hint">{{ __('Đóng góp của điểm trung bình IQ, EQ, AQ, PQ, GPA (mặc định: 20%)') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Mục tiêu tiêu chuẩn tháng --}}
    <div class="card rank-tier-card shadow-sm">
        <div class="card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-target-arrow text-info fs-3"></i>
                <div>
                    <h3 class="card-title mb-0">{{ __('Mục Tiêu Tiêu Chuẩn Trong Tháng') }}</h3>
                    <div class="small text-muted">{{ __('Mốc hoàn thành để đạt 100 điểm thành phần của từng tiêu chí') }}</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label" for="input_parent_rank_target_usage_minutes">
                        {{ __('Thời gian dùng app chuẩn/tháng (Phút)') }}
                    </label>
                    <input type="number" step="10" min="1" class="form-control"
                           id="input_parent_rank_target_usage_minutes" name="parent_rank_target_usage_minutes"
                           value="{{ $settingsByKey->get('parent_rank_target_usage_minutes')?->plain_value ?? 600 }}">
                    <div class="form-hint">{{ __('Ví dụ: 600 phút = 10 giờ trong tháng') }}</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="input_parent_rank_target_active_days">
                        {{ __('Số ngày vào app chuẩn/tháng (Ngày)') }}
                    </label>
                    <input type="number" step="1" min="1" max="31" class="form-control"
                           id="input_parent_rank_target_active_days" name="parent_rank_target_active_days"
                           value="{{ $settingsByKey->get('parent_rank_target_active_days')?->plain_value ?? 20 }}">
                    <div class="form-hint">{{ __('Ví dụ: 20 ngày/tháng') }}</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label" for="input_parent_rank_target_assessments">
                        {{ __('Số bài đánh giá chuẩn/tháng (Bài)') }}
                    </label>
                    <input type="number" step="1" min="1" class="form-control"
                           id="input_parent_rank_target_assessments" name="parent_rank_target_assessments"
                           value="{{ $settingsByKey->get('parent_rank_target_assessments')?->plain_value ?? 5 }}">
                    <div class="form-hint">{{ __('Ví dụ: 5 bài/tháng') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 4: Ngưỡng điểm 4 cấp bậc --}}
    <div class="card rank-tier-card shadow-sm">
        <div class="card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-trophy text-warning fs-3"></i>
                <div>
                    <h3 class="card-title mb-0">{{ __('Ngưỡng Điểm Phân Hạng Thành Viên (0 - 100)') }}</h3>
                    <div class="small text-muted">{{ __('Mức điểm tối thiểu để được nâng lên từng cấp bậc thành viên') }}</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 border rounded text-center bg-orange-lt">
                        <i class="ti ti-shield fs-1 text-orange mb-1"></i>
                        <h4 class="fw-bold mb-2">{{ __('Hạng Đồng') }}</h4>
                        <label class="form-label small" for="input_parent_rank_points_bronze">{{ __('Điểm sàn tối thiểu') }}</label>
                        <input type="number" step="1" min="0" max="100" class="form-control text-center fw-bold"
                               id="input_parent_rank_points_bronze" name="parent_rank_points_bronze"
                               value="{{ $settingsByKey->get('parent_rank_points_bronze')?->plain_value ?? 20 }}">
                        <div class="small text-muted mt-2">&ge; 20 điểm</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded text-center bg-secondary-lt">
                        <i class="ti ti-medal fs-1 text-secondary mb-1"></i>
                        <h4 class="fw-bold mb-2">{{ __('Hạng Bạc') }}</h4>
                        <label class="form-label small" for="input_parent_rank_points_silver">{{ __('Điểm sàn tối thiểu') }}</label>
                        <input type="number" step="1" min="0" max="100" class="form-control text-center fw-bold"
                               id="input_parent_rank_points_silver" name="parent_rank_points_silver"
                               value="{{ $settingsByKey->get('parent_rank_points_silver')?->plain_value ?? 40 }}">
                        <div class="small text-muted mt-2">&ge; 40 điểm</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded text-center bg-yellow-lt">
                        <i class="ti ti-crown fs-1 text-warning mb-1"></i>
                        <h4 class="fw-bold mb-2">{{ __('Hạng Vàng') }}</h4>
                        <label class="form-label small" for="input_parent_rank_points_gold">{{ __('Điểm sàn tối thiểu') }}</label>
                        <input type="number" step="1" min="0" max="100" class="form-control text-center fw-bold"
                               id="input_parent_rank_points_gold" name="parent_rank_points_gold"
                               value="{{ $settingsByKey->get('parent_rank_points_gold')?->plain_value ?? 60 }}">
                        <div class="small text-muted mt-2">&ge; 60 điểm</div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded text-center bg-cyan-lt">
                        <i class="ti ti-diamond fs-1 text-cyan mb-1"></i>
                        <h4 class="fw-bold mb-2">{{ __('Hạng Kim Cương') }}</h4>
                        <label class="form-label small" for="input_parent_rank_points_diamond">{{ __('Điểm sàn tối thiểu') }}</label>
                        <input type="number" step="1" min="0" max="100" class="form-control text-center fw-bold"
                               id="input_parent_rank_points_diamond" name="parent_rank_points_diamond"
                               value="{{ $settingsByKey->get('parent_rank_points_diamond')?->plain_value ?? 80 }}">
                        <div class="small text-muted mt-2">&ge; 80 điểm</div>
                    </div>
                </div>
            </div>
            <div class="text-center text-muted small mt-3">
                {{ __('Ghi chú: Người dùng có tổng điểm dưới điểm sàn Hạng Đồng sẽ được gắn cấp "Thành viên mới".') }}
            </div>
        </div>
    </div>
</div>
