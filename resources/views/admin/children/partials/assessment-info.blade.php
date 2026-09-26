@php
    use App\Enums\Question\QuestionType;
    use App\Traits\RouteAdminSystem;
    $latestIq = $children->ratings()->where('type', QuestionType::IQ)->latest()->first();
    $latestEq = $children->ratings()->where('type', QuestionType::EQ)->latest()->first();
    $latestAq = $children->ratings()->where('type', QuestionType::AQ)->latest()->first();
    $latestPq = $children->ratingPQs()->latest()->first();

    // Memo Game (Trí nhớ) & Thành tích cá nhân
    $memoPersonalBests = $children->memoPersonalBests()->with(['ageConfig', 'theme'])->get();
    $sortedPb = $memoPersonalBests->sortBy(function ($item) {
        return $item->ageConfig ? ($item->ageConfig->rows * $item->ageConfig->columns) : 0;
    })->values();
    $highestWinLevel = $sortedPb->filter(function ($item) {
        return $item->total_wins > 0 && $item->best_time > 0;
    })->last();
    $totalMemoGames = $memoPersonalBests->sum('total_games_played');
    $totalMemoWins = $memoPersonalBests->sum('total_wins');

    // Giải đấu đã tham gia
    $compEntries = $children->memoCompetitionEntries()->with(['competition.ageConfig', 'rounds'])->orderBy('created_at', 'desc')->get();
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
                        <button type="button" class="btn btn-outline-success btn-sm rounded-2 px-3 fw-normal" id="btn-open-pq-debug-modal" data-bs-toggle="modal" data-bs-target="#modal-debug-pq">
                            <i class="ti ti-bug me-1"></i> {{ __('Debug Dữ Liệu & Công Thức PQ') }}
                        </button>
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
    <!-- 5. Thẻ Memo Game (Kỷ Lục Trí Nhớ & Giải Đấu) -->
    <div class="col-12">
        <div class="assessment-detail-card border-top border-3 border-azure">
            <div>
                <div class="assessment-card-header">
                    <div class="d-flex align-items-center gap-2">
                        <div class="p-2 bg-azure-lt rounded-3 fs-3">
                            <i class="ti ti-trophy text-azure"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold text-dark fs-15">{{ __('Thành Tích Cá Nhân & Kỷ Lục Trí Nhớ (Memo Game)') }}</h6>
                            <small class="text-muted">{{ __('Ghi nhận level cao nhất bé đã chinh phục, ván thắng nhanh nhất và lịch sử giải đấu') }}</small>
                        </div>
                    </div>
                    @if ($highestWinLevel)
                        <div class="assessment-score-badge bg-azure-lt text-azure">
                            <i class="ti ti-crown me-1"></i>{{ __('Level cao nhất: ') }} {{ $highestWinLevel->ageConfig?->rows }}×{{ $highestWinLevel->ageConfig?->columns }} ({{ $highestWinLevel->best_time }}s)
                        </div>
                    @else
                        <div class="assessment-score-badge bg-light text-muted">
                            {{ __('Chưa có kỷ lục') }}
                        </div>
                    @endif
                </div>

                <!-- Thống kê tổng quan -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted text-uppercase fs-11 fw-bold d-block">{{ __('Level Đã Chinh Phục') }}</small>
                            <span class="fs-18 fw-bold text-azure mt-1 d-block">
                                {{ $highestWinLevel ? ($highestWinLevel->ageConfig?->name ?? 'Level ' . $highestWinLevel->ageConfig?->rows . '×' . $highestWinLevel->ageConfig?->columns) : __('Chưa có') }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted text-uppercase fs-11 fw-bold d-block">{{ __('Kỷ Lục Nhanh Nhất (Level Max)') }}</small>
                            <span class="fs-18 fw-bold text-success mt-1 d-block">
                                {{ $highestWinLevel ? $highestWinLevel->best_time . 's (' . gmdate("i:s", $highestWinLevel->best_time) . ')' : '--' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted text-uppercase fs-11 fw-bold d-block">{{ __('Lượt Lật Ít Nhất (Tiebreaker)') }}</small>
                            <span class="fs-18 fw-bold text-warning mt-1 d-block">
                                {{ $highestWinLevel ? $highestWinLevel->best_moves . ' ' . __('lần lật') : '--' }}
                            </span>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-3 text-center border">
                            <small class="text-muted text-uppercase fs-11 fw-bold d-block">{{ __('Tổng Ván Thắng / Đã Chơi') }}</small>
                            <span class="fs-18 fw-bold text-primary mt-1 d-block">
                                {{ $totalMemoWins }} / {{ $totalMemoGames }}
                                @if($totalMemoGames > 0)
                                    <small class="fs-12 text-muted">({{ round(($totalMemoWins / $totalMemoGames) * 100) }}%)</small>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Bảng chi tiết kỷ lục từng level -->
                @if ($sortedPb->isNotEmpty())
                    <h6 class="fw-bold text-dark fs-13 mb-2 d-flex align-items-center gap-1">
                        <i class="ti ti-list-check text-azure"></i> {{ __('Kỷ Lục Từng Cấp Độ (Level / Lưới Thẻ)') }}
                    </h6>
                    <div class="table-responsive mb-4">
                        <table class="table table-sm table-bordered table-vcenter">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Cấp Độ / Lưới') }}</th>
                                    <th class="text-center">{{ __('Kỷ Lục Thời Gian') }}</th>
                                    <th class="text-center">{{ __('Số Lần Lật') }}</th>
                                    <th class="text-center">{{ __('Số Lần Sai') }}</th>
                                    <th class="text-center">{{ __('Điểm Cao Nhất') }}</th>
                                    <th>{{ __('Chủ Đề') }}</th>
                                    <th class="text-center">{{ __('Thắng / Chơi') }}</th>
                                    <th class="text-end">{{ __('Thời Điểm Đạt Kỷ Lục') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($sortedPb as $pb)
                                    <tr>
                                        <td>
                                            <span class="badge bg-purple-lt fw-bold me-1">
                                                {{ $pb->ageConfig ? $pb->ageConfig->rows . '×' . $pb->ageConfig->columns : '--' }}
                                            </span>
                                            <span class="fw-semibold text-dark">{{ $pb->ageConfig?->name ?? 'Level' }}</span>
                                        </td>
                                        <td class="text-center fw-bold text-success fs-13">
                                            @if ($pb->best_time > 0)
                                                {{ $pb->best_time }}s <small class="text-muted">({{ gmdate("i:s", $pb->best_time) }})</small>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td class="text-center fw-bold text-warning fs-13">
                                            {{ $pb->best_moves > 0 ? $pb->best_moves : '--' }}
                                        </td>
                                        <td class="text-center text-muted">
                                            {{ $pb->best_mistakes }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-primary fw-bold">{{ $pb->best_score }}</span>
                                        </td>
                                        <td>
                                            <span class="text-muted fs-12">{{ $pb->theme?->name ?? '--' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-azure-lt">{{ $pb->total_wins }}/{{ $pb->total_games_played }}</span>
                                        </td>
                                        <td class="text-end text-muted fs-11">
                                            {{ $pb->achieved_at ? format_datetime($pb->achieved_at) : '--' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <small>{{ __('Bé chưa hoàn thành ván game nào để ghi nhận kỷ lục cá nhân.') }}</small>
                    </div>
                @endif

                <!-- Bảng lịch sử tham gia giải đấu (nếu có) -->
                @if ($compEntries->isNotEmpty())
                    <h6 class="fw-bold text-dark fs-13 mb-2 d-flex align-items-center gap-1">
                        <i class="ti ti-trophy text-warning"></i> {{ __('Lịch Sử Tham Gia Giải Đấu & Cuộc Thi') }}
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered table-vcenter">
                            <thead class="bg-light">
                                <tr>
                                    <th>{{ __('Giải Đấu') }}</th>
                                    <th class="text-center">{{ __('Hạng') }}</th>
                                    <th class="text-center">{{ __('Lần Thi') }}</th>
                                    <th class="text-center">{{ __('Tổng Thời Gian') }}</th>
                                    <th class="text-center">{{ __('Tổng Lần Lật') }}</th>
                                    <th class="text-center">{{ __('Game Thắng') }}</th>
                                    <th class="text-center">{{ __('Trạng Thái') }}</th>
                                    <th class="text-end">{{ __('Thời Điểm Hoàn Thành') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($compEntries as $ce)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $ce->competition?->name ?? 'Giải đấu' }}</span>
                                        </td>
                                        <td class="text-center">
                                            @if ($ce->ranking === 1)
                                                <span class="badge bg-warning text-dark fw-bold">🥇 Hạng 1</span>
                                            @elseif ($ce->ranking === 2)
                                                <span class="badge bg-secondary text-white fw-bold">🥈 Hạng 2</span>
                                            @elseif ($ce->ranking === 3)
                                                <span class="badge bg-danger text-white fw-bold">🥉 Hạng 3</span>
                                            @elseif ($ce->ranking)
                                                <span class="badge bg-light text-dark fw-bold">#{{ $ce->ranking }}</span>
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="text-muted fs-12">Lần {{ $ce->attempt_number }}</span>
                                        </td>
                                        <td class="text-center fw-bold text-primary">
                                            {{ $ce->total_time }}s ({{ gmdate("i:s", $ce->total_time) }})
                                        </td>
                                        <td class="text-center fw-bold text-warning">
                                            {{ $ce->total_moves }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $ce->games_won >= 4 ? 'bg-success' : 'bg-secondary' }}">
                                                {{ $ce->games_won }}/4
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            @if ($ce->is_valid)
                                                <span class="badge bg-success-lt fw-bold">Hợp lệ</span>
                                            @else
                                                <span class="badge bg-danger-lt fw-bold">Chưa đạt</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted fs-11">
                                            {{ $ce->completed_at ? format_datetime($ce->completed_at) : '--' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                <span class="text-muted fs-12">
                    {{ __('Tổng số bài test Memo Game:') }} <strong>{{ $children->memoRatings()->count() }}</strong>
                </span>
                <div class="d-flex gap-2">
                    <a href="{{ route(RouteAdminSystem::MEMO_PLAY_INDEX) }}" class="btn btn-sm btn-outline-primary d-flex align-items-center gap-1" target="_blank">
                        <i class="ti ti-device-gamepad-2"></i> <span>{{ __('Chơi thử') }}</span>
                    </a>
                    <a href="{{ route(RouteAdminSystem::MEMO_COMPETITION_INDEX) }}" class="btn btn-sm btn-azure-lt d-flex align-items-center gap-1" target="_blank">
                        <i class="ti ti-trophy"></i> <span>{{ __('Quản lý Giải đấu') }}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@include('admin.children.partials.pq-debug-modal')
