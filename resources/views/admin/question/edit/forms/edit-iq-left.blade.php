@php use App\Enums\Answser\AnswerType; @endphp
<div class="col-12 col-lg-8 col-xl-9">
    <!-- Card 1: Thông tin câu hỏi -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-help-circle text-primary me-2 fs-4"></i>
                {{ __('Thông tin câu hỏi IQ') }}
            </h5>
        </div>
        <div class="row card-body p-4 g-3">
            <!-- Loại câu hỏi & Độ tuổi -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted fs-13 mb-2">
                        <i class="ti ti-category text-primary me-1"></i> {{ __('Loại câu hỏi') }}: <span class="text-danger">*</span>
                    </label>
                    <div>
                        <span class="question-type-badge badge-iq">
                            <i class="ti ti-brain fs-4"></i>
                            {{ __('Trắc nghiệm IQ (Trí tuệ)') }}
                        </span>
                        <input type="hidden" name="question[question_type]" value="iq">
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ti ti-calendar-event text-primary me-1"></i> {{ __('Độ tuổi phù hợp (Tuổi)') }}: <span class="text-danger">*</span></label>
                    <x-input type="number" min="0" name="question[age]" :value="$response->age" :required="true" placeholder="{{ __('Ví dụ: 6') }}" />
                </div>
            </div>

            <!-- Nội dung câu hỏi -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ti ti-message-2-question text-primary me-1"></i> {{ __('Nội dung câu hỏi') }}: <span class="text-danger">*</span></label>
                    <x-input type="text" name="question[question]" :value="$response->question" :required="true" placeholder="{{ __('Nhập nội dung câu hỏi trắc nghiệm...') }}" />
                </div>
            </div>

            <!-- Hình ảnh minh họa -->
            <div class="col-12">
                <div class="mb-2">
                    <label class="form-label fw-bold"><i class="ti ti-photo text-primary me-1"></i> {{ __('Hình ảnh minh họa cho câu hỏi') }}:</label>
                    <div class="question-image-upload-wrapper">
                        <input type="file" onchange="showPreviewImage()" class="form-control" name="question[question_image]"
                               id="customFile" accept=".jpg, .jpeg, .png, .webp"/>
                        <p class="text-muted fs-12 mt-2 mb-0">{{ __('Hỗ trợ định dạng JPG, PNG, WEBP. Nhấp vào ảnh để thay đổi hoặc xem trước.') }}</p>

                        <div class="question-preview-box {{ $response->question_image ? '' : 'd-none' }}" id="preview-wrapper">
                            <img id="preview-image" src="{{ $response->question_image ? asset($response->question_image) : '' }}" alt="Xem trước ảnh">
                            <button type="button" class="btn-remove-preview" onclick="removeImage()" title="{{ __('Xóa ảnh') }}">
                                <i class="ti ti-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Danh sách câu trả lời & Đáp án -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-list-check text-success me-2 fs-4"></i>
                {{ __('Danh sách đáp án trắc nghiệm') }} <span class="text-danger ms-1">*</span>
            </h5>
            <button type="button" class="btn-add-answer-pill" id="add_answer">
                <i class="ti ti-plus fs-5"></i>
                {{ __('Thêm đáp án') }}
            </button>
        </div>
        <div class="card-body p-4">
            <!-- Loại câu trả lời (Text / Image) -->
            <div class="row mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label fw-bold"><i class="ti ti-adjustments text-primary me-1"></i> {{ __('Phương thức đáp án') }}: <span class="text-danger">*</span></label>
                    <x-select name="answers[type]" :required="true" id="answer_type">
                        @foreach ($answer_types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$type == $key"/>
                        @endforeach
                    </x-select>
                </div>
                <div class="col-12 col-md-6 d-flex align-items-end">
                    <div class="text-muted fs-12 mb-2">
                        <i class="ti ti-info-circle text-info me-1"></i>
                        {{ __('Chọn "Đáp án đúng" bằng cách nhấn vào nhãn tương ứng trên mỗi đáp án.') }}
                    </div>
                </div>
            </div>

            <!-- Answer Cards Container -->
            <div id="answer" class="w-100">
                @php $index = 0; @endphp
                @foreach ($response->answers as $answer)
                    @php
                        $letter = chr(65 + ($index % 26));
                        $isCorrect = (bool) $answer->is_correct;
                    @endphp
                    <div class="answer-card-item {{ $isCorrect ? 'is-correct-card' : '' }} d-flex align-items-center gap-3">
                        <div class="answer-letter-badge">{{ $letter }}</div>
                        
                        <div class="flex-grow-1">
                            @if ($type == AnswerType::Normal->value)
                                <input class="answer_normal form-control" type="text"
                                       name="answers[answer][{{ $answer->id }}]"
                                       placeholder="{{ __('Nhập nội dung đáp án ') . $letter }}..."
                                       value="{{ $answer->answer }}" required />
                                <div class="answer_image d-none" style="width: 220px;" data-answer="{{ $index }}">
                                    <x-input-image-ckfinder name="answers[image][]" :value="$answer->answer" showImage="showAnswerImage-{{ $answer->id }}"/>
                                </div>
                            @else
                                <input class="answer_normal form-control d-none" type="text"
                                       name="answers[answer][]"
                                       placeholder="{{ __('Nhập nội dung đáp án ') . $letter }}..." />
                                <div class="answer_image" style="width: 220px;" data-answer="{{ $answer->id }}">
                                    <x-input-image-ckfinder name="answers[image][{{ $answer->id }}]" :value="$answer->answer" showImage="showAnswerImage-{{ $answer->id }}"/>
                                </div>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <label class="correct-option-pill {{ $isCorrect ? 'active' : '' }} mb-0">
                                <input class="d-none radio-correct-answer" type="radio" name="answers[is_correct]" value="{{ $index }}" {{ $isCorrect ? 'checked' : '' }}>
                                <i class="ti ti-check-circle fs-5"></i>
                                <span class="correct-text">{{ __('Đáp án đúng') }}</span>
                            </label>

                            @if (!$loop->first || count($response->answers) > 1)
                                <button type="button" class="btn btn-delete-answer remove_answer" title="{{ __('Xóa đáp án này') }}">
                                    <i class="ti ti-trash fs-5"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                    @php $index++; @endphp
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    let index = {{ $response->answers->count() }};
</script>

@push('custom-js')
    <script>
        function showPreviewImage() {
            const fileInput = document.getElementById("customFile");
            let image = $("#preview-image");
            let wrapper = $("#preview-wrapper");
            const selectedFiles = fileInput.files;

            if (selectedFiles.length > 0) {
                const file = selectedFiles[0];
                const fileType = file.type;

                if (fileType === "image/jpeg" || fileType === "image/png" || fileType === "image/webp") {
                    image.attr('src', URL.createObjectURL(file));
                    wrapper.removeClass('d-none');
                } else {
                    alert("Chỉ chấp nhận file định dạng JPG, PNG, WEBP.");
                    fileInput.value = "";
                    image.attr('src', "");
                    wrapper.addClass('d-none');
                }
            }
        }

        function removeImage() {
            const fileInput = document.getElementById("customFile");
            let image = $("#preview-image");
            let wrapper = $("#preview-wrapper");

            fileInput.value = "";
            image.attr('src', "");
            wrapper.addClass('d-none');
        }
    </script>
@endpush
