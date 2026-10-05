@php
    $rankService = app(\App\Services\ParentRank\ParentRankServiceInterface::class);
    $rankConfig = $rankService->getConfig();
    $weights = $rankConfig['weights'] ?? ['usage' => 30, 'frequency' => 25, 'assessment' => 25, 'child_score' => 20];
    $targets = $rankConfig['targets'] ?? ['usage_minutes' => 600, 'active_days' => 20, 'assessments' => 5];
    $thresholds = $rankConfig['thresholds'] ?? ['bronze' => 20, 'silver' => 40, 'gold' => 60, 'diamond' => 80];
@endphp

<div class="card border border-primary-subtle bg-primary-lt rounded-3 mb-3 shadow-none">
    <div class="card-header bg-transparent border-bottom border-primary-subtle py-2 px-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-primary text-white p-1 rounded">
                <i class="ti ti-crown fs-3"></i>
            </span>
            <div>
                <strong class="text-primary fs-4">Quy tắc & Cách tính phân hạng Bố mẹ (Thành viên)</strong>
                <span class="text-muted fs-6 ms-2 d-none d-md-inline">
                    (Kỳ đánh giá: <strong>Hàng tháng</strong> • Tự động tính: <strong>01:00 hàng ngày</strong>)
                </span>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('admin.setting.parent_rank') }}" class="btn btn-sm btn-white text-primary border-primary-subtle shadow-sm">
                <i class="ti ti-settings me-1"></i>{{ __('Cấu hình tiêu chí') }}
            </a>
            <button class="btn btn-sm btn-ghost-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseParentRankInfo" aria-expanded="false" aria-controls="collapseParentRankInfo" id="btnToggleRankInfo">
                <i class="ti ti-chevron-down" id="iconToggleRankInfo"></i> <span class="d-none d-sm-inline">{{ __('Chi tiết') }}</span>
            </button>
        </div>
    </div>

    <!-- Tóm tắt nhanh hiển thị luôn khi chưa bấm mở rộng -->
    <div class="px-3 py-2 bg-white bg-opacity-75 border-bottom border-primary-subtle d-flex align-items-center justify-content-between flex-wrap gap-2 fs-6">
        <div class="d-flex align-items-center flex-wrap gap-3">
            <span><i class="ti ti-calculator text-primary me-1"></i><strong>Công thức:</strong> Tổng điểm = (Thời gian × <strong>{{ $weights['usage'] }}%</strong>) + (Tần suất × <strong>{{ $weights['frequency'] }}%</strong>) + (Đánh giá × <strong>{{ $weights['assessment'] }}%</strong>) + (Chỉ số con × <strong>{{ $weights['child_score'] }}%</strong>)</span>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-1">
            <span class="badge bg-muted-lt">Hạng D (&lt;{{ $thresholds['c'] ?? 40 }}đ)</span>
            <i class="ti ti-arrow-right text-muted fs-7"></i>
            <span class="badge bg-orange-lt">Hạng C (≥{{ $thresholds['c'] ?? 40 }}đ)</span>
            <i class="ti ti-arrow-right text-muted fs-7"></i>
            <span class="badge bg-cyan-lt">Hạng B (≥{{ $thresholds['b'] ?? 60 }}đ)</span>
            <i class="ti ti-arrow-right text-muted fs-7"></i>
            <span class="badge bg-yellow-lt">Hạng A (≥{{ $thresholds['a'] ?? 80 }}đ)</span>
        </div>
    </div>

    <!-- Chi tiết mở rộng khi bấm xem -->
    <div class="collapse" id="collapseParentRankInfo">
        <div class="card-body bg-white p-3">
            <div class="row g-3">
                <!-- Tiêu chí 1: Thời gian -->
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 rounded-3 border border-1 h-100 bg-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-blue-lt fw-bold">Tiêu chí 1</span>
                            <span class="badge bg-primary text-white">{{ $weights['usage'] }}% trọng số</span>
                        </div>
                        <h4 class="m-0 mb-1 d-flex align-items-center gap-1 text-blue">
                            <i class="ti ti-clock-play fs-2"></i> Thời gian dùng app
                        </h4>
                        <div class="fs-6 text-muted mb-2">Mục tiêu: <strong>{{ number_format($targets['usage_minutes']) }} phút/tháng</strong> (~{{ round($targets['usage_minutes']/60, 1) }}h)</div>
                        <div class="bg-light p-2 rounded font-monospace fs-7 text-dark mb-1">
                            min(100, (phút thực tế / {{ $targets['usage_minutes'] }}) × 100)
                        </div>
                        <small class="text-secondary d-block fs-8">• Ghi nhận tự động khi mở app (tối đa 16h/ngày, ngắt phiên khi nghỉ &gt; 5 phút).</small>
                    </div>
                </div>

                <!-- Tiêu chí 2: Tần suất -->
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 rounded-3 border border-1 h-100 bg-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-green-lt fw-bold">Tiêu chí 2</span>
                            <span class="badge bg-primary text-white">{{ $weights['frequency'] }}% trọng số</span>
                        </div>
                        <h4 class="m-0 mb-1 d-flex align-items-center gap-1 text-green">
                            <i class="ti ti-calendar-check fs-2"></i> Tần suất mở app
                        </h4>
                        <div class="fs-6 text-muted mb-2">Mục tiêu: <strong>{{ $targets['active_days'] }} ngày/tháng</strong></div>
                        <div class="bg-light p-2 rounded font-monospace fs-7 text-dark mb-1">
                            min(100, (số ngày mở / {{ $targets['active_days'] }}) × 100)
                        </div>
                        <small class="text-secondary d-block fs-8">• Đếm số ngày phân biệt trong tháng mà phụ huynh có mở app.</small>
                    </div>
                </div>

                <!-- Tiêu chí 3: Số bài đánh giá -->
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 rounded-3 border border-1 h-100 bg-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-purple-lt fw-bold">Tiêu chí 3</span>
                            <span class="badge bg-primary text-white">{{ $weights['assessment'] }}% trọng số</span>
                        </div>
                        <h4 class="m-0 mb-1 d-flex align-items-center gap-1 text-purple">
                            <i class="ti ti-clipboard-check fs-2"></i> Bài đánh giá cho con
                        </h4>
                        <div class="fs-6 text-muted mb-2">Mục tiêu: <strong>{{ $targets['assessments'] }} bài/tháng</strong></div>
                        <div class="bg-light p-2 rounded font-monospace fs-7 text-dark mb-1">
                            min(100, (số bài làm / {{ $targets['assessments'] }}) × 100)
                        </div>
                        <small class="text-secondary d-block fs-8">• Tổng bài IQ + EQ + AQ + Thể chất (PQ) + Học bạ (GPA). Bỏ qua bài IQ rỗng.</small>
                    </div>
                </div>

                <!-- Tiêu chí 4: Chỉ số con -->
                <div class="col-md-6 col-xl-3">
                    <div class="p-3 rounded-3 border border-1 h-100 bg-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge bg-yellow-lt fw-bold">Tiêu chí 4</span>
                            <span class="badge bg-primary text-white">{{ $weights['child_score'] }}% trọng số</span>
                        </div>
                        <h4 class="m-0 mb-1 d-flex align-items-center gap-1 text-warning">
                            <i class="ti ti-mood-smile fs-2"></i> Chỉ số con trung bình
                        </h4>
                        <div class="fs-6 text-muted mb-2">Thang chuẩn hóa: <strong>0 – 100 điểm</strong></div>
                        <div class="bg-light p-2 rounded font-monospace fs-7 text-dark mb-1">
                            Trung bình (IQ + EQ + AQ + PQ + GPA)
                        </div>
                        <small class="text-secondary d-block fs-8">• Quy đổi các thang đo về 100. Nếu gia đình có nhiều con sẽ tính trung bình các con.</small>
                    </div>
                </div>
            </div>

            <!-- Lưu ý chốt sổ & đồng bộ -->
            <div class="alert alert-info py-2 px-3 mt-3 mb-0 d-flex align-items-center gap-2 fs-6 rounded-3">
                <i class="ti ti-info-circle fs-3 text-info"></i>
                <div>
                    <strong>Lưu ý vận hành:</strong> Điểm hiển thị trên danh sách là điểm tháng hiện tại (<strong>{{ now()->format('m/Y') }}</strong>). Lúc <strong>00:30 ngày mùng 1 hàng tháng</strong>, hệ thống tự động chốt sổ tháng trước và lưu vào lịch sử snapshot cố định.
                </div>
            </div>
        </div>
    </div>
</div>
