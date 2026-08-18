@php
    use App\Enums\Question\QuestionType;
    use App\Enums\Answser\AnswerType;
    $isEq = $response->question_type->value == QuestionType::EQ->value;
@endphp
<div class="col-12 col-lg-8 col-xl-9">
    <!-- Card 1: Thông tin câu hỏi -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti {{ $isEq ? 'ti-heart text-danger' : 'ti-leaf text-success' }} me-2 fs-4"></i>
                {{ $isEq ? __('Thông tin câu hỏi EQ (Cảm xúc)') : __('Thông tin câu hỏi AQ (Vượt khó)') }}
            </h5>
        </div>

        <div class="row card-body p-4 g-3">
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted fs-13 mb-2">
                        <i class="ti ti-category text-primary me-1"></i> {{ __('Loại câu hỏi') }}:
                    </label>
                    <div>
                        <span class="question-type-badge {{ $isEq ? 'badge-eq' : 'badge-aq' }}">
                            <i class="ti {{ $isEq ? 'ti-heart' : 'ti-leaf' }} fs-4"></i>
                            {{ $isEq ? __('Đánh giá cảm xúc EQ') : __('Đánh giá vượt khó AQ') }}
                        </span>
                        <input type="hidden" name="question[question_type]" value="{{ $isEq ? 'eq' : 'aq' }}">
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ti ti-folders text-primary me-1"></i> {{ __('Nhóm câu hỏi') }}: <span class="text-danger">*</span></label>
                    <x-select name="question[question_group_id]" :required="true">
                        @foreach ($questionGroups as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$response->question_group_id == $key"/>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ti ti-users-group text-primary me-1"></i> {{ __('Nhóm độ tuổi') }}: <span class="text-danger">*</span></label>
                    <x-select name="question[age_group]" :required="true">
                        @foreach ($age_group as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$response->age_group && $response->age_group->value == $key"/>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ti ti-adjustments text-primary me-1"></i> {{ __('Phương thức đáp án') }}: <span class="text-danger">*</span></label>
                    <x-select name="answers[type]" :required="true" id="answer_type">
                        @foreach ($answer_types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$type == $key"/>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="ti ti-message-2-question text-primary me-1"></i> {{ __('Nội dung câu hỏi') }}: <span class="text-danger">*</span></label>
                    <x-input name="question[question]" :value="$response->question" :required="true" placeholder="{{ __('Nhập nội dung câu hỏi...') }}" />
                </div>
            </div>

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

    <!-- Card 2: Danh sách đáp án & Điểm số -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-list-check text-primary me-2 fs-4"></i>
                {{ __('Danh sách câu trả lời & Thang điểm đánh giá') }}
            </h5>
            <button type="button" class="btn-add-answer-pill" id="add_answer">
                <i class="ti ti-plus fs-5"></i>
                {{ __('Thêm câu trả lời') }}
            </button>
        </div>

        <div class="card-body p-4">
            <div class="text-muted fs-12 mb-3">
                <i class="ti ti-info-circle text-info me-1"></i>
                {{ __('Nhập điểm số đánh giá từ 1 đến 5 tương ứng với mỗi phương án lựa chọn.') }}
            </div>

            <div class="w-100" id="answer">
                @foreach ($response->answers as $key => $answer)
                    <div class="answer-card-item d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center gap-1 flex-shrink-0" style="min-width: 120px;">
                            <span class="badge bg-primary-lt px-2 py-1 fs-12 fw-bold">{{ __('Điểm:') }}</span>
                            <x-input type="number" name="answers[score][{{ $answer->id }}]" :required="true" min="1" max="5" step="1" style="width: 65px;" :value="$answer->score" />
                        </div>

                        <div class="flex-grow-1">
                            @if ($answer->type->value == AnswerType::Normal->value)
                                <x-input class="answer_normal form-control" type="text" name="answers[answer][{{ $answer->id }}]" :value="$answer->answer" placeholder="{{ __('Nhập nội dung câu trả lời...') }}" />
                                <div class="answer_image d-none" style="width: 220px;" data-answer="{{ $answer->id }}">
                                    <x-input-image-ckfinder name="answers[image][{{ $answer->id }}]" showImage="showAnswerImage-{{ $answer->id }}" />
                                </div>
                            @else
                                <x-input class="answer_normal form-control d-none" type="text" name="answers[answer][{{ $answer->id }}]" :value="$answer->answer" placeholder="{{ __('Nhập nội dung câu trả lời...') }}" />
                                <div class="answer_image" style="width: 220px;" data-answer="{{ $answer->id }}">
                                    <x-input-image-ckfinder name="answers[image][{{ $answer->id }}]" showImage="showAnswerImage-{{ $answer->id }}" :value="$answer->answer" />
                                </div>
                            @endif
                        </div>

                        @if (!$loop->first || count($response->answers) > 1)
                            <button type="button" class="btn btn-delete-answer remove_answer" title="{{ __('Xóa mục này') }}">
                                <i class="ti ti-trash fs-5"></i>
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    let index = {{ $response->answers->last()->id ?? 0 }};
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
