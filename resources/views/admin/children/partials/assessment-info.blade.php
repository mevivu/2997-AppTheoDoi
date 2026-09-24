@php
    use App\Enums\Question\QuestionType;
    $latestIq = $children->ratings()->where('type', QuestionType::IQ)->latest()->first();
    $latestEq = $children->ratings()->where('type', QuestionType::EQ)->latest()->first();
    $latestAq = $children->ratings()->where('type', QuestionType::AQ)->latest()->first();
    $latestPq = $children->ratingPQs()->latest()->first();
@endphp

<div class="row g-4">
    <!-- Header Section -->
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-1">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-chart-dots text-primary fs-4"></i>
                    {{ __('Tổng Hợp Thống Kê & Chỉ Số Đánh Giá Của Trẻ') }}
                </h5>
                <small class="text-muted">{{ __('Theo dõi quá trình làm bài và phát triển toàn diện IQ, EQ, AQ, PQ của bé') }}</small>
            </div>
        </div>
    </div>

    <!-- 1. Thẻ IQ (Trí Tuệ) -->
    <div class="col-12 col-md-6">
        <div class="assessment-detail-card border-top border-3 border-purple">
            <div>
                <div class="assessment-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-purple-lt rounded-3 fs-3">
                            <i class="ti ti-brain text-purple"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-15">{{ __('Chỉ Số IQ (Trí Tuệ)') }}</h6>
                            <small class="text-muted">{{ __('Đánh giá tư duy & nhận thức') }}</small>
                        </div>
                    </div>
                    <div class="assessment-score-badge bg-purple-lt text-purple">
                        {{ $latestIq ? $latestIq->score . ' ' . __('điểm') : __('Chưa có') }}
                    </div>
                </div>

                <div class="assessment-subscore-grid">
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Kết quả xếp loại') }}</div>
                        <div class="subscore-val text-purple">{{ $latestIq->result ?? ($latestIq->label ?? __('Chưa đánh giá')) }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Ngày đánh giá gần nhất') }}</div>
                        <div class="subscore-val">{{ $latestIq ? format_datetime($latestIq->created_at) : __('Chưa có') }}</div>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted fs-12">{{ __('Tổng số bài đã làm:') }} <strong>{{ $children->ratings()->where('type', QuestionType::IQ)->count() }}</strong></span>
                <a href="{{ route('admin.rating.iq', ['child_id' => $children->id]) }}" class="btn btn-sm btn-purple-lt d-flex align-items-center gap-1" target="_blank">
                    <span>{{ __('Xem lịch sử IQ') }}</span> <i class="ti ti-arrow-right fs-12"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Thẻ EQ (Cảm Xúc) -->
    <div class="col-12 col-md-6">
        <div class="assessment-detail-card border-top border-3 border-pink">
            <div>
                <div class="assessment-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-pink-lt rounded-3 fs-3">
                            <i class="ti ti-heart text-pink"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-15">{{ __('Chỉ Số EQ (Cảm Xúc)') }}</h6>
                            <small class="text-muted">{{ __('Đánh giá trí tuệ cảm xúc') }}</small>
                        </div>
                    </div>
                    <div class="assessment-score-badge bg-pink-lt text-pink">
                        {{ $latestEq ? $latestEq->score . ' ' . __('điểm') : __('Chưa có') }}
                    </div>
                </div>

                <div class="assessment-subscore-grid">
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Nhận thức cảm xúc') }}</div>
                        <div class="subscore-val text-pink">{{ $latestEq?->social_awareness ?? '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Kiểm soát cảm xúc') }}</div>
                        <div class="subscore-val text-pink">{{ $latestEq?->self_regulation ?? '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Kỹ năng xã hội & Đồng cảm') }}</div>
                        <div class="subscore-val">{{ $latestEq?->relationship_management ?? '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Động lực & Lạc quan') }}</div>
                        <div class="subscore-val">{{ $latestEq?->optimism ?? '--' }}</div>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted fs-12">{{ __('Tổng số bài đã làm:') }} <strong>{{ $children->ratings()->where('type', QuestionType::EQ)->count() }}</strong></span>
                <a href="{{ route('admin.rating.eq', ['child_id' => $children->id]) }}" class="btn btn-sm btn-pink-lt d-flex align-items-center gap-1" target="_blank">
                    <span>{{ __('Xem lịch sử EQ') }}</span> <i class="ti ti-arrow-right fs-12"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 3. Thẻ AQ (Vượt Khó) -->
    <div class="col-12 col-md-6">
        <div class="assessment-detail-card border-top border-3 border-teal">
            <div>
                <div class="assessment-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-teal-lt rounded-3 fs-3">
                            <i class="ti ti-leaf text-teal"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-15">{{ __('Chỉ Số AQ (Vượt Khó)') }}</h6>
                            <small class="text-muted">{{ __('Đánh giá tính kiên trì & vượt khó') }}</small>
                        </div>
                    </div>
                    <div class="assessment-score-badge bg-teal-lt text-teal">
                        {{ $latestAq ? $latestAq->score . ' ' . __('điểm') : __('Chưa có') }}
                    </div>
                </div>

                <div class="assessment-subscore-grid">
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Tính kiên trì') }}</div>
                        <div class="subscore-val text-teal">{{ $latestAq?->perseverance ?? '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Tính tích cực') }}</div>
                        <div class="subscore-val text-teal">{{ $latestAq?->positivity ?? '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Tính linh hoạt') }}</div>
                        <div class="subscore-val">{{ $latestAq?->flexibility ?? '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Khả năng chịu đựng') }}</div>
                        <div class="subscore-val">{{ $latestAq?->endurance ?? '--' }}</div>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted fs-12">{{ __('Tổng số bài đã làm:') }} <strong>{{ $children->ratings()->where('type', QuestionType::AQ)->count() }}</strong></span>
                <a href="{{ route('admin.rating.aq', ['child_id' => $children->id]) }}" class="btn btn-sm btn-teal-lt d-flex align-items-center gap-1" target="_blank">
                    <span>{{ __('Xem lịch sử AQ') }}</span> <i class="ti ti-arrow-right fs-12"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 4. Thẻ PQ (Thể Chất) -->
    <div class="col-12 col-md-6">
        <div class="assessment-detail-card border-top border-3 border-orange">
            <div>
                <div class="assessment-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-orange-lt rounded-3 fs-3">
                            <i class="ti ti-activity text-orange"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-15">{{ __('Thể Chất & Tăng Trưởng (PQ)') }}</h6>
                            <small class="text-muted">{{ __('Chỉ số BMI, Chiều cao & Cân nặng') }}</small>
                        </div>
                    </div>
                    <div class="assessment-score-badge bg-orange-lt text-orange">
                        {{ $latestPq ? ($latestPq->score ? $latestPq->score . ' ' . __('điểm') : 'BMI ' . $latestPq->bmi) : __('Chưa có') }}
                    </div>
                </div>

                <div class="assessment-subscore-grid">
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Chiều cao hiện tại') }}</div>
                        <div class="subscore-val text-orange">{{ $latestPq?->height ? $latestPq->height . ' cm' : '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Cân nặng hiện tại') }}</div>
                        <div class="subscore-val text-orange">{{ $latestPq?->weight ? $latestPq->weight . ' kg' : '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Chỉ số BMI & Thể trạng') }}</div>
                        <div class="subscore-val">{{ $latestPq?->bmi ? $latestPq->bmi . ' (' . ($latestPq->bmi_result ?? '') . ')' : '--' }}</div>
                    </div>
                    <div class="assessment-subscore-item">
                        <div class="subscore-name">{{ __('Sức mạnh / Sức bền') }}</div>
                        <div class="subscore-val">{{ ($latestPq?->strength ?? '--') . ' / ' . ($latestPq?->endurance ?? '--') }}</div>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted fs-12">{{ __('Tổng số lần đo:') }} <strong>{{ $children->ratingPQs()->count() }}</strong></span>
                <a href="{{ route('admin.ratingPQ.index', ['child_id' => $children->id]) }}" class="btn btn-sm btn-orange-lt d-flex align-items-center gap-1" target="_blank">
                    <span>{{ __('Xem lịch sử PQ') }}</span> <i class="ti ti-arrow-right fs-12"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 5. Biểu Đồ Radar Đánh Giá Thể Chất 5 Thuộc Tính (PQ Radar Chart) -->
    <div class="col-12">
        <div class="card border border-light-subtle rounded-3 shadow-none overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-success-lt rounded-3 fs-3">
                        <i class="ti ti-polygon text-success"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold text-dark fs-15">{{ __('Biểu Đồ Radar Thể Chất 5 Thuộc Tính (PQ Radar Chart)') }}</h6>
                        <small class="text-muted">{{ __('Mô phỏng 5 chỉ số thể chất theo thang điểm 10 tương ứng trên Mobile App') }}</small>
                    </div>
                </div>
                @if(!empty($pqOverall))
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-success-lt text-success fs-13 px-3 py-2 border border-success-subtle">
                            <i class="ti ti-activity me-1"></i>
                            {{ __('Điểm Tổng Hợp PQ:') }} <strong>{{ $latestPq?->score ? $latestPq->score . ' / 10' : ($pqOverall['score'] ?? '--') }}</strong>
                        </span>
                    </div>
                @endif
            </div>

            <div class="card-body p-4">
                @if(!empty($pqOverall))
                    <div class="row align-items-center g-4">
                        <!-- Cột 1: Radar Chart (ApexCharts) -->
                        <div class="col-12 col-lg-6">
                            <div class="p-3 border rounded-3 bg-light-subtle d-flex flex-column align-items-center justify-content-center">
                                <div id="pq-radar-chart" class="w-100" style="min-height: 350px;"></div>
                                <div class="text-center text-muted fs-12 mt-1">
                                    <span class="badge bg-success me-1" style="width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>
                                    {{ __('Đa giác đánh giá thể chất (Thang điểm 0 - 10)') }}
                                </div>
                            </div>
                        </div>

                        <!-- Cột 2: Bảng chi tiết 5 thuộc tính đối chiếu -->
                        <div class="col-12 col-lg-6">
                            <div class="table-responsive">
                                <table class="table table-vcenter table-bordered table-sm fs-12 mb-0">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th>{{ __('Thuộc tính') }}</th>
                                            <th class="text-center">{{ __('Số đo thực tế') }}</th>
                                            <th class="text-center">{{ __('So với WHO / Đánh giá') }}</th>
                                            <th class="text-center" style="width: 85px;">{{ __('Điểm') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- 1. Chiều cao hiện tại -->
                                        <tr>
                                            <td class="fw-semibold text-dark">
                                                <i class="ti ti-ruler-2 text-primary me-1"></i>
                                                {{ __('Chiều cao hiện tại') }}
                                            </td>
                                            <td class="text-center fw-bold">{{ $pqOverall['height'] ? $pqOverall['height'] . ' cm' : '--' }}</td>
                                            <td class="text-center">
                                                @php
                                                    $diffH = $pqOverall['height_comparison']['height_who_current'] ?? 0;
                                                @endphp
                                                <span class="badge {{ $diffH >= 0 ? 'bg-success-lt' : 'bg-warning-lt' }}">
                                                    {{ ($diffH > 0 ? '+' : '') . $diffH }} cm
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success fs-13 px-2 py-1 text-white">
                                                    {{ $pqOverall['current_height_percent'] ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>

                                        <!-- 2. Chiều cao trưởng thành -->
                                        <tr>
                                            <td class="fw-semibold text-dark">
                                                <i class="ti ti-trending-up text-info me-1"></i>
                                                {{ __('Chiều cao trưởng thành') }}
                                            </td>
                                            <td class="text-center fw-bold">
                                                {{ ($predHeight > 0) ? $predHeight . ' cm' : (($heightPrediction['predicting_adult_height'] ?? 0) > 0 ? $heightPrediction['predicting_adult_height'] . ' cm' : '--') }}
                                            </td>
                                            <td class="text-center">
                                                @php
                                                    $whoAdultH = ($children->gender->value == 1 ? 176.5 : 163.0);
                                                    $adultDiff = ($predHeight > 0) ? round($predHeight - $whoAdultH, 1) : null;
                                                @endphp
                                                @if($adultDiff !== null)
                                                    <span class="badge {{ $adultDiff >= 0 ? 'bg-info-lt' : 'bg-warning-lt' }}">
                                                        {{ ($adultDiff > 0 ? '+' : '') . $adultDiff }} cm (WHO 19t)
                                                    </span>
                                                @else
                                                    <span class="text-muted">--</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info fs-13 px-2 py-1 text-white">
                                                    {{ $pqOverall['height_adulthood'] ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>

                                        <!-- 3. BMI (Cân nặng) -->
                                        <tr>
                                            <td class="fw-semibold text-dark">
                                                <i class="ti ti-weight text-warning me-1"></i>
                                                {{ __('BMI / Cân nặng') }}
                                            </td>
                                            <td class="text-center fw-bold">
                                                {{ $pqOverall['bmi'] ? 'BMI ' . $pqOverall['bmi'] : '--' }}
                                                @if($pqOverall['weight'])
                                                    <span class="text-muted fw-normal">({{ $pqOverall['weight'] }} kg)</span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-muted">{{ $pqOverall['bmi_result'] ?? '--' }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-warning fs-13 px-2 py-1 text-white">
                                                    {{ $pqOverall['bmi_percent'] ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>

                                        <!-- 4. Sức mạnh -->
                                        <tr>
                                            <td class="fw-semibold text-dark">
                                                <i class="ti ti-bolt text-danger me-1"></i>
                                                {{ __('Sức mạnh cơ bắp') }}
                                            </td>
                                            <td class="text-center fw-bold">{{ $pqOverall['strength'] ?? '--' }}</td>
                                            <td class="text-center text-muted">{{ __('Hiệu suất tăng trưởng') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-danger fs-13 px-2 py-1 text-white">
                                                    {{ $pqOverall['strength_percent'] ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>

                                        <!-- 5. Sức bền -->
                                        <tr>
                                            <td class="fw-semibold text-dark">
                                                <i class="ti ti-heart-rate-monitor text-primary me-1"></i>
                                                {{ __('Sức bền vận động') }}
                                            </td>
                                            <td class="text-center fw-bold">{{ $pqOverall['endurance'] ?? '--' }}</td>
                                            <td class="text-center text-muted">{{ __('Hiệu suất tim mạch') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary fs-13 px-2 py-1 text-white">
                                                    {{ $pqOverall['endurance_percent'] ?? 0 }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-activity-heartbeat fs-1 text-muted opacity-50 mb-2 d-block"></i>
                        <h6 class="fw-bold">{{ __('Chưa có dữ liệu đánh giá thể chất (PQ)') }}</h6>
                        <p class="fs-12 mb-0">{{ __('Bé chưa có lần đo thể chất nào trên hệ thống hoặc cần cập nhật thêm số đo để vẽ biểu đồ mạng nhện.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
