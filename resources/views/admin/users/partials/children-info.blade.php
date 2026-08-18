@php
    use App\Enums\Question\QuestionType;
    $children = $user->children;
@endphp

<div class="row g-4">
    <div class="col-12">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <div>
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="ti ti-baby-carriage text-primary fs-4"></i>
                    {{ __('Danh Sách Trẻ Em & Chỉ Số Đánh Giá') }}
                </h5>
                <small class="text-muted">{{ __('Theo dõi kết quả đánh giá IQ, EQ, AQ, PQ và quá trình phát triển của từng bé') }}</small>
            </div>
            <span class="badge bg-primary-lt px-3 py-2 fs-13 rounded-pill fw-bold">
                {{ __('Tổng số:') }} {{ $children->count() }} {{ __('bé') }}
            </span>
        </div>
    </div>

    @forelse($children as $child)
        @php
            $latestIq = $child->ratings()->where('type', QuestionType::IQ)->latest()->first();
            $latestEq = $child->ratings()->where('type', QuestionType::EQ)->latest()->first();
            $latestAq = $child->ratings()->where('type', QuestionType::AQ)->latest()->first();
            $latestPq = $child->ratingPQs()->latest()->first();

            // Child initials
            $childNameParts = explode(' ', trim($child->fullname ?? ''));
            $childInitials = '';
            if (count($childNameParts) >= 2) {
                $childInitials = mb_substr($childNameParts[0], 0, 1) . mb_substr(end($childNameParts), 0, 1);
            } else {
                $childInitials = mb_substr($child->fullname ?? 'B', 0, 2);
            }
            $childInitials = mb_strtoupper($childInitials);
        @endphp
        <div class="col-12 col-xl-6">
            <div class="child-assessment-card">
                <div>
                    <!-- Child Top Header -->
                    <div class="d-flex align-items-center gap-3 mb-2">
                        @if($child->avatar && file_exists(public_path($child->avatar)))
                            <img src="{{ asset($child->avatar) }}" alt="{{ $child->fullname }}" class="child-avatar" style="width: 58px; height: 58px;">
                        @else
                            <div class="child-avatar-initials">
                                {{ $childInitials ?: 'BÉ' }}
                            </div>
                        @endif

                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1">
                                <h5 class="mb-0 fw-bold text-dark fs-15">{{ $child->fullname }}</h5>
                                <div class="d-flex align-items-center gap-1">
                                    @if($child->gender)
                                        <span class="badge {{ $child->gender->value == 1 ? 'bg-blue-lt' : 'bg-pink-lt' }}">
                                            {{ $child->gender->description() }}
                                        </span>
                                    @endif
                                    @if($child->status)
                                        <span class="badge {{ $child->status->badge() }}">{{ $child->status->description() }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-3 text-muted fs-12 mt-1 flex-wrap">
                                <span><i class="ti ti-calendar text-primary me-1"></i> {{ $child->birthday ? format_date($child->birthday, 'd/m/Y') : __('Chưa cập nhật') }}</span>
                                @if($child->age || $child->month)
                                    <span><i class="ti ti-clock text-info me-1"></i> {{ $child->age ? $child->age . ' tuổi' : ($child->month . ' tháng') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Scores Summary Grid -->
                    <div class="child-scores-grid">
                        <!-- IQ Score -->
                        <div class="child-score-box box-iq">
                            <div class="score-label">🧠 IQ</div>
                            <div class="score-val">{{ $latestIq ? $latestIq->score : '--' }}</div>
                        </div>

                        <!-- EQ Score -->
                        <div class="child-score-box box-eq">
                            <div class="score-label">❤️ EQ</div>
                            <div class="score-val">{{ $latestEq ? $latestEq->score : '--' }}</div>
                        </div>

                        <!-- AQ Score -->
                        <div class="child-score-box box-aq">
                            <div class="score-label">🧗 AQ</div>
                            <div class="score-val">{{ $latestAq ? $latestAq->score : '--' }}</div>
                        </div>

                        <!-- PQ Score -->
                        <div class="child-score-box box-pq">
                            <div class="score-label">🏃 PQ</div>
                            <div class="score-val">{{ $latestPq ? ($latestPq->score ?? ($latestPq->bmi ? 'BMI ' . $latestPq->bmi : '--')) : '--' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Assessment Action Buttons -->
                <div class="child-actions-bar">
                    <a href="{{ route('admin.rating.iq', ['child_id' => $child->id]) }}"
                       class="btn-assessment-action btn-iq"
                       title="{{ __('Xem chi tiết đánh giá IQ của bé') }}">
                        <i class="ti ti-brain fs-5"></i>
                        <span>{{ __('Xem IQ') }}</span>
                    </a>

                    <a href="{{ route('admin.rating.eq', ['child_id' => $child->id]) }}"
                       class="btn-assessment-action btn-eq"
                       title="{{ __('Xem chi tiết đánh giá EQ của bé') }}">
                        <i class="ti ti-heart fs-5"></i>
                        <span>{{ __('Xem EQ') }}</span>
                    </a>

                    <a href="{{ route('admin.rating.aq', ['child_id' => $child->id]) }}"
                       class="btn-assessment-action btn-aq"
                       title="{{ __('Xem chi tiết đánh giá AQ của bé') }}">
                        <i class="ti ti-leaf fs-5"></i>
                        <span>{{ __('Xem AQ') }}</span>
                    </a>

                    <a href="{{ route('admin.ratingPQ.index', ['child_id' => $child->id]) }}"
                       class="btn-assessment-action btn-pq"
                       title="{{ __('Xem chi tiết đánh giá Thể chất PQ của bé') }}">
                        <i class="ti ti-activity fs-5"></i>
                        <span>{{ __('Xem PQ') }}</span>
                    </a>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center py-5">
            <div class="text-muted fs-1 text-opacity-50 mb-2">
                <i class="ti ti-baby-carriage"></i>
            </div>
            <h6 class="text-muted">{{ __('Chưa có thông tin trẻ em nào được liên kết với tài khoản này.') }}</h6>
        </div>
    @endforelse
</div>
