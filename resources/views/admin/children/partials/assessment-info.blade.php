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
</div>
