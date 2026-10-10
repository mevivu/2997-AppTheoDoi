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
                {{ __('Khi được bật, hệ thống sẽ tự động tổng hợp thời gian dùng app, tần suất mở app, số bài đánh giá và điểm số các chỉ số của con để tính điểm và phân hạng phụ huynh định kỳ 3 tháng (theo quý).') }}
            </p>
        </div>
    </div>

    {{-- Card 2: Trọng số 5 tiêu chí (%) --}}
    <div class="card rank-tier-card shadow-sm">
        <div class="card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-chart-pie text-indigo fs-3"></i>
                <div>
                    <h3 class="card-title mb-0">{{ __('Trọng số 5 Tiêu chí Đánh giá') }}</h3>
                    <div class="small text-muted">{{ __('Tổng trọng số của 5 tiêu chí phải bằng đúng 100%') }}</div>
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
                {{ __('Cảnh báo: Tổng 5 trọng số hiện đang khác 100%. Vui lòng điều chỉnh lại để công thức tính điểm chính xác.') }}
            </div>

            <div class="row g-3">
                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="input_parent_rank_weight_usage">
                        <i class="ti ti-clock text-primary me-1"></i>
                        {{ __('1. Thời gian dùng app (%)') }}
                    </label>
                    <input type="number" step="any" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_usage" name="parent_rank_weight_usage"
                           value="{{ $settingsByKey->get('parent_rank_weight_usage')?->plain_value ?? 25 }}">
                    <div class="form-hint">{{ __('Đóng góp của tổng phút dùng app trong kỳ 3 tháng (mặc định: 25%)') }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="input_parent_rank_weight_frequency">
                        <i class="ti ti-calendar-event text-success me-1"></i>
                        {{ __('2. Tần suất mở app (%)') }}
                    </label>
                    <input type="number" step="any" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_frequency" name="parent_rank_weight_frequency"
                           value="{{ $settingsByKey->get('parent_rank_weight_frequency')?->plain_value ?? 20 }}">
                    <div class="form-hint">{{ __('Đóng góp của số ngày vào app trong kỳ 3 tháng (mặc định: 20%)') }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="input_parent_rank_weight_assessment">
                        <i class="ti ti-checklist text-warning me-1"></i>
                        {{ __('3. Số bài đánh giá cho con (%)') }}
                    </label>
                    <input type="number" step="any" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_assessment" name="parent_rank_weight_assessment"
                           value="{{ $settingsByKey->get('parent_rank_weight_assessment')?->plain_value ?? 20 }}">
                    <div class="form-hint">{{ __('Đóng góp của số bài trắc nghiệm/học bạ cho con trong kỳ 3 tháng (mặc định: 20%)') }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="input_parent_rank_weight_lesson_video">
                        <i class="ti ti-video text-purple me-1"></i>
                        {{ __('4. Lượt xem bài học & video (%)') }}
                    </label>
                    <input type="number" step="any" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_lesson_video" name="parent_rank_weight_lesson_video"
                           value="{{ $settingsByKey->get('parent_rank_weight_lesson_video')?->plain_value ?? 15 }}">
                    <div class="form-hint">{{ __('Đóng góp của số lượt xem bài học & video trong kỳ 3 tháng (mặc định: 15%)') }}</div>
                </div>

                <div class="col-md-6 col-lg-4">
                    <label class="form-label" for="input_parent_rank_weight_child_score">
                        <i class="ti ti-heart-rate-monitor text-danger me-1"></i>
                        {{ __('5. Chỉ số phát triển của con (%)') }}
                    </label>
                    <input type="number" step="any" min="0" max="100" class="form-control weight-input"
                           id="input_parent_rank_weight_child_score" name="parent_rank_weight_child_score"
                           value="{{ $settingsByKey->get('parent_rank_weight_child_score')?->plain_value ?? 20 }}">
                    <div class="form-hint">{{ __('Đóng góp của điểm trung bình IQ, EQ, AQ, PQ, GPA (mặc định: 20%)') }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 3: Mục tiêu tiêu chuẩn 3 tháng (Quý) --}}
    <div class="card rank-tier-card shadow-sm">
        <div class="card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-target-arrow text-info fs-3"></i>
                <div>
                    <h3 class="card-title mb-0">{{ __('Mục Tiêu Tiêu Chuẩn Trong 3 Tháng (Quý)') }}</h3>
                    <div class="small text-muted">{{ __('Mốc hoàn thành để đạt 100 điểm thành phần của từng tiêu chí & phục vụ cảnh báo đồng hành trong kỳ 3 tháng') }}</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="input_parent_rank_target_usage_minutes">
                        {{ __('Thời gian dùng app chuẩn / 3 tháng (Phút)') }}
                    </label>
                    <input type="number" step="1" min="0" class="form-control"
                           id="input_parent_rank_target_usage_minutes" name="parent_rank_target_usage_minutes"
                           value="{{ $settingsByKey->get('parent_rank_target_usage_minutes')?->plain_value ?? 1800 }}">
                    <div class="form-hint">{{ __('Ví dụ: 1800 phút = 30 giờ trong 3 tháng') }}</div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="input_parent_rank_target_active_days">
                        {{ __('Số ngày vào app chuẩn / 3 tháng (Ngày)') }}
                    </label>
                    <input type="number" step="1" min="0" max="92" class="form-control"
                           id="input_parent_rank_target_active_days" name="parent_rank_target_active_days"
                           value="{{ $settingsByKey->get('parent_rank_target_active_days')?->plain_value ?? 60 }}">
                    <div class="form-hint">{{ __('Ví dụ: 60 ngày / 3 tháng') }}</div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="input_parent_rank_target_assessments">
                        {{ __('Số bài đánh giá chuẩn / 3 tháng (Bài)') }}
                    </label>
                    <input type="number" step="1" min="0" class="form-control"
                           id="input_parent_rank_target_assessments" name="parent_rank_target_assessments"
                           value="{{ $settingsByKey->get('parent_rank_target_assessments')?->plain_value ?? 15 }}">
                    <div class="form-hint">{{ __('Ví dụ: 15 bài / 3 tháng') }}</div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <label class="form-label" for="input_parent_rank_target_lesson_video_views">
                        {{ __('Lượt xem bài học / video chuẩn / 3 tháng (Lượt)') }}
                    </label>
                    <input type="number" step="1" min="0" class="form-control"
                           id="input_parent_rank_target_lesson_video_views" name="parent_rank_target_lesson_video_views"
                           value="{{ $settingsByKey->get('parent_rank_target_lesson_video_views')?->plain_value ?? 60 }}">
                    <div class="form-hint">{{ __('Ví dụ: 60 lượt / 3 tháng') }}</div>
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
                    <h3 class="card-title mb-0">{{ __('Ngưỡng Điểm Phân Hạng Thành Viên Chăm Con 360 (0 - 100)') }}</h3>
                    <div class="small text-muted">{{ __('Mức điểm tối thiểu để được nâng lên từng cấp bậc thành viên') }}</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 border rounded text-center bg-muted-lt h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="ti ti-mood-smile fs-1 text-muted mb-1"></i>
                            <h4 class="fw-bold mb-1">{{ __('Hạng D') }}</h4>
                            <div class="small text-muted mb-2">{{ __('Bố mẹ cần hỗ trợ thêm') }}</div>
                        </div>
                        <div>
                            <div class="badge bg-secondary fs-6 py-2 px-3">&lt; 40 điểm</div>
                            <div class="small text-muted mt-2">{{ __('Mặc định ban đầu') }}</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded text-center bg-orange-lt h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="ti ti-shield fs-1 text-orange mb-1"></i>
                            <h4 class="fw-bold mb-1">{{ __('Hạng C') }}</h4>
                            <div class="small text-muted mb-2">{{ __('Bố mẹ đang cố gắng') }}</div>
                        </div>
                        <div>
                            <label class="form-label small" for="input_parent_rank_points_c">{{ __('Điểm sàn tối thiểu') }}</label>
                            <input type="number" step="any" min="0" max="100" class="form-control text-center fw-bold"
                                   id="input_parent_rank_points_c" name="parent_rank_points_c"
                                   value="{{ $settingsByKey->get('parent_rank_points_c')?->plain_value ?? 40 }}">
                            <div class="small text-muted mt-2">&ge; 40 điểm</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded text-center bg-cyan-lt h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="ti ti-medal fs-1 text-cyan mb-1"></i>
                            <h4 class="fw-bold mb-1">{{ __('Hạng B') }}</h4>
                            <div class="small text-muted mb-2">{{ __('Bố mẹ tích cực') }}</div>
                        </div>
                        <div>
                            <label class="form-label small" for="input_parent_rank_points_b">{{ __('Điểm sàn tối thiểu') }}</label>
                            <input type="number" step="any" min="0" max="100" class="form-control text-center fw-bold"
                                   id="input_parent_rank_points_b" name="parent_rank_points_b"
                                   value="{{ $settingsByKey->get('parent_rank_points_b')?->plain_value ?? 60 }}">
                            <div class="small text-muted mt-2">&ge; 60 điểm</div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border rounded text-center bg-yellow-lt h-100 d-flex flex-column justify-content-between">
                        <div>
                            <i class="ti ti-crown fs-1 text-warning mb-1"></i>
                            <h4 class="fw-bold mb-1">{{ __('Hạng A') }}</h4>
                            <div class="small text-muted mb-2">{{ __('Bố mẹ gương mẫu') }}</div>
                        </div>
                        <div>
                            <label class="form-label small" for="input_parent_rank_points_a">{{ __('Điểm sàn tối thiểu') }}</label>
                            <input type="number" step="any" min="0" max="100" class="form-control text-center fw-bold"
                                   id="input_parent_rank_points_a" name="parent_rank_points_a"
                                   value="{{ $settingsByKey->get('parent_rank_points_a')?->plain_value ?? 80 }}">
                            <div class="small text-muted mt-2">&ge; 80 điểm</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center text-muted small mt-3">
                {{ __('Ghi chú: Người dùng có tổng điểm dưới điểm sàn Hạng C sẽ được gắn cấp "Hạng D – Bố mẹ cần hỗ trợ thêm".') }}
            </div>
        </div>
    </div>

    {{-- Card 5: Cảnh báo & Gợi ý đồng hành --}}
    @php
        $suggestDefaults = \App\Services\ParentRank\ParentRankService::DEFAULT_SUGGESTION_TEXTS;
        $suggestSwitches = [
            'usage' => ['icon' => 'ti-clock', 'label' => __('Thời gian dùng app / số ngày vào app')],
            'assessment' => ['icon' => 'ti-checklist', 'label' => __('Số bài đánh giá cho con')],
            'content' => ['icon' => 'ti-video', 'label' => __('Lượt xem bài học & video')],
            'competency' => ['icon' => 'ti-heart-rate-monitor', 'label' => __('Chỉ số năng lực của con thấp')],
        ];
    @endphp
    <div class="card rank-tier-card shadow-sm">
        <div class="card-header">
            <div class="d-flex align-items-center gap-2">
                <i class="ti ti-bulb text-success fs-3"></i>
                <div>
                    <h3 class="card-title mb-0">{{ __('Cảnh báo & Gợi ý "Gợi ý từ Chăm Con 360"') }}</h3>
                    <div class="small text-muted">{{ __('Cấu hình ngưỡng, bật/tắt và nội dung các câu gợi ý hiển thị cho phụ huynh trên app') }}</div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label" for="input_parent_rank_warning_ratio">{{ __('Ngưỡng cảnh báo so với mục tiêu 3 tháng (%)') }}</label>
                    <input type="number" step="any" min="0" max="100" class="form-control"
                           id="input_parent_rank_warning_ratio" name="parent_rank_warning_ratio"
                           value="{{ $settingsByKey->get('parent_rank_warning_ratio')?->plain_value ?? 50 }}">
                    <div class="form-hint">{{ __('Dưới mức % này so với mục tiêu 3 tháng thì hiện gợi ý (mặc định: 50%). Áp dụng cho 3 cảnh báo đầu.') }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="input_parent_rank_competency_threshold">{{ __('Ngưỡng điểm chỉ số năng lực yếu (0 - 10)') }}</label>
                    <input type="number" step="any" min="0" max="10" class="form-control"
                           id="input_parent_rank_competency_threshold" name="parent_rank_competency_threshold"
                           value="{{ $settingsByKey->get('parent_rank_competency_threshold')?->plain_value ?? 5 }}">
                    <div class="form-hint">{{ __('IQ/EQ/AQ/PQ/GPA thấp hơn mức này sẽ được nhắc cải thiện (mặc định: 5).') }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label" for="input_parent_rank_competency_max_display">{{ __('Số lĩnh vực yếu tối đa hiển thị') }}</label>
                    <input type="number" step="1" min="1" max="5" class="form-control"
                           id="input_parent_rank_competency_max_display" name="parent_rank_competency_max_display"
                           value="{{ $settingsByKey->get('parent_rank_competency_max_display')?->plain_value ?? 2 }}">
                    <div class="form-hint">{{ __('Lấy các lĩnh vực có điểm thấp nhất trước (mặc định: 2).') }}</div>
                </div>
            </div>

            <div class="d-flex flex-column gap-4">
                @foreach ($suggestSwitches as $key => $meta)
                    @php
                        $enableSetting = $settingsByKey->get("parent_rank_suggest_enable_{$key}");
                        $textValue = $settingsByKey->get("parent_rank_suggest_text_{$key}")?->plain_value;
                        $textValue = trim((string) $textValue) !== '' ? $textValue : $suggestDefaults[$key];
                    @endphp
                    <div class="border rounded p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="fw-bold">
                                <i class="ti {{ $meta['icon'] }} me-1"></i>
                                {{ __('Cảnh báo') }}: {{ $meta['label'] }}
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input type="hidden" name="parent_rank_suggest_enable_{{ $key }}" value="0">
                                <input class="form-check-input" type="checkbox"
                                       id="parent_rank_suggest_enable_{{ $key }}"
                                       name="parent_rank_suggest_enable_{{ $key }}" value="1"
                                       {{ ($enableSetting?->plain_value ?? '1') === '0' ? '' : 'checked' }}>
                            </div>
                        </div>
                        <textarea class="form-control suggest-text" rows="2" maxlength="300"
                                  id="input_parent_rank_suggest_text_{{ $key }}"
                                  name="parent_rank_suggest_text_{{ $key }}"
                                  data-key="{{ $key }}"
                                  data-default="{{ $suggestDefaults[$key] }}">{{ $textValue }}</textarea>
                        <div class="d-flex align-items-start justify-content-between gap-2 mt-1">
                            <div class="form-hint mb-0">
                                @if ($key === 'competency')
                                    {{ __('Biến có thể dùng:') }}
                                    <code>{child}</code> {{ __('(Bé An / Con)') }},
                                    <code>{label}</code> {{ __('(chỉ số / các chỉ số)') }},
                                    <code>{list}</code> {{ __('(VD: EQ = 2, GPA = 3)') }}.
                                @else
                                    {{ __('Để trống sẽ dùng câu mặc định.') }}
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-secondary btn-reset-suggest flex-shrink-0"
                                    data-target="#input_parent_rank_suggest_text_{{ $key }}">
                                {{ __('Khôi phục mặc định') }}
                            </button>
                        </div>
                        @if ($key === 'competency')
                            <div class="mt-2 p-2 rounded small" style="background:#f0fdf4;color:#166534;">
                                <strong>{{ __('Xem trước:') }}</strong>
                                <span id="competencyPreview"></span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
