<div class="d-inline-flex align-items-center gap-1 question-hover-wrapper position-relative">
    @php
        $isIq = request()->routeIs('admin.question.iq') || (isset($questionModel) && (($questionModel->question_type?->value ?? $questionModel->question_type) === 'iq'));
        $editUrl = $isIq ? route('admin.question.editIq', $id) : route('admin.question.editEqAq', $id);
        $hasAnswers = isset($questionModel) && $questionModel->answers && $questionModel->answers->count() > 0;
    @endphp

    <a href="{{ $editUrl }}" 
       target="_blank" 
       class="{{ $hasAnswers ? 'question-popover-trigger' : '' }} text-decoration-none fw-medium text-primary d-inline-flex align-items-center gap-1"
       data-id="{{ $id }}"
       title="{{ $hasAnswers ? 'Rê chuột để xem nhanh đáp án' : 'Xem chi tiết câu hỏi' }}">
        <span>{{ $question }}</span>
        @if($hasAnswers)
            <i class="ti ti-eye text-primary fs-6 opacity-75"></i>
        @endif
    </a>

    <i class="ti ti-copy copy-btn ms-1 text-muted" style="font-size: 16px; cursor: pointer;" title="Sao chép câu hỏi" data-value="{{ $question }}"></i>
    <i class="ti ti-check check-icon text-success ms-1" style="display:none; font-size: 16px"></i>

    @if($hasAnswers)
        {{-- Template HTML ẩn để Popover đọc nội dung --}}
        <div class="question-preview-template d-none">
            <div class="question-preview-card" style="max-width: 380px;">
                <!-- Header Card -->
                <div class="d-flex align-items-center justify-content-between mb-2 pb-2 border-bottom">
                    <div>
                        <span class="badge bg-primary text-white me-1">#{{ $questionModel->code ?? $id }}</span>
                        @if($questionModel->age)
                            <span class="badge bg-secondary-subtle text-secondary">{{ $questionModel->age }} tuổi</span>
                        @endif
                    </div>
                    <span class="badge bg-info-subtle text-info fw-semibold">
                        {{ $questionModel->answers->count() }} đáp án
                    </span>
                </div>

                <!-- Đề bài câu hỏi & Ảnh đề bài (nếu có) -->
                <div class="question-header mb-2">
                    <div class="fw-bold text-dark mb-1" style="font-size: 13.5px; line-height: 1.4;">
                        {{ $questionModel->question }}
                    </div>
                    @if(!empty($questionModel->question_image))
                        <div class="question-main-img text-center my-2 p-1 bg-light rounded border">
                            <img src="{{ asset($questionModel->question_image) }}" 
                                 alt="Hình ảnh câu hỏi"
                                 style="max-height: 125px; max-width: 100%; object-fit: contain; border-radius: 4px;">
                        </div>
                    @endif
                </div>

                <!-- Danh sách các đáp án A, B, C, D -->
                <div class="answers-preview-list">
                    <div class="text-muted fs-11 fw-semibold text-uppercase mb-2">
                        <i class="ti ti-list-check me-1"></i>{{ __('Danh sách lựa chọn') }}:
                    </div>
                    <div class="row g-2">
                        @foreach($questionModel->answers as $idx => $ans)
                            @php
                                $letter = chr(65 + ($idx % 26));
                                $isCorrect = (bool) $ans->is_correct;
                                $isImage = ($ans->type?->value ?? $ans->type) === 'image' || 
                                           str_ends_with(strtolower($ans->answer ?? ''), '.png') || 
                                           str_ends_with(strtolower($ans->answer ?? ''), '.jpg') || 
                                           str_ends_with(strtolower($ans->answer ?? ''), '.jpeg') || 
                                           str_ends_with(strtolower($ans->answer ?? ''), '.webp');
                            @endphp
                            <div class="col-6">
                                <div class="p-2 rounded border h-100 d-flex flex-column justify-content-between {{ $isCorrect ? 'border-success bg-success-subtle shadow-sm' : 'border-light-subtle bg-white text-secondary' }}" style="min-height: 54px;">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold {{ $isCorrect ? 'text-success' : 'text-dark' }}">{{ $letter }}.</span>
                                        @if($isCorrect)
                                            <span class="badge bg-success text-white py-0 px-1" style="font-size: 10px;">
                                                <i class="ti ti-check"></i> Đúng
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-center my-auto py-1">
                                        @if($isImage)
                                            <img src="{{ asset($ans->answer) }}" 
                                                 alt="Đáp án {{ $letter }}" 
                                                 style="max-height: 50px; max-width: 100%; object-fit: contain;" 
                                                 class="rounded bg-white p-1 border">
                                        @else
                                            <span class="fw-medium text-break {{ $isCorrect ? 'text-success-emphasis fw-bold' : 'text-dark' }}" style="font-size: 12.5px;">
                                                {{ $ans->answer }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
