<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin câu hỏi') }}</h2>
        </div>

        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu hỏi') }}:</label>
                    @if ($response->question_type->value == \App\Enums\Question\QuestionType::EQ->value)
                        <input type="text"value="EQ" class="form-control" disabled>
                        <input type="hidden" name="question[question_type]" value="eq">
                    @elseif($response->question_type->value == \App\Enums\Question\QuestionType::AQ->value)
                        <input type="text"value="AQ" class="form-control" disabled>
                        <input type="hidden" name="question[question_type]" value="aq">
                    @endif
                </div>
            </div>


            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nhóm câu hỏi') }}:</label>
                    <x-select name="question[question_group_id]" :required="true">
                        @foreach ($questionGroups as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$response->question_group_id == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Câu hỏi') }}:</label>
                    <x-input name="question[question]" :required="true" :value="$response->question" />
                </div>
            </div>

            <div class="col-4">
                <div class="mb-3">
                    <label class="control-label">{{ __('Hình ảnh câu hỏi') }}:</label>
                    <div class="card-body p-2">
                        <input type="file" onchange="showPreviewImage()" class="form-control"
                               name="question[question_image]"
                               id="customFile" accept=".jpg, .jpeg, .png"/>

                        <div class="preview-image">
                            <img id="preview-image" src="{{ $response->question_image ? asset($response->question_image) : asset('/public/assets/images/default-image.png') }}" style="max-width: 100%;">
                        </div>
                        @if($response->question_image)
                            <button type="button" id="remove-image" class="btn btn-danger" onclick="removeImage()">Xoá
                                ảnh
                            </button>

                        @endif
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu trả lời') }}:</label>
                    <x-select name="answers[type]" :required="true" id="answer_type">
                        @foreach ($answer_types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$type == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nhóm tuổi') }}:</label>
                    <x-select name="question[age_group]" :required="true">
                        @foreach ($age_group as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$response->age_group && $response->age_group->value == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>


            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <label
                        class="control-label">{{ __('Câu trả lời (Nhập điểm đánh giá ở mục đầu tiên của mỗi câu trả lời)') }}:</label>
                    <span class="text-primary cursor-pointer" id="add_answer">
                        <i class="ti ti-plus"></i>
                        {{ __('Thêm câu trả lời') }}
                    </span>
                </div>

                <div class="w-100" id="answer">
                    @foreach ($response->answers as $key => $answer)
                        <div class="d-flex align-items-center justify-content-start gap-2 mb-3">
                            <x-input type="number" name="answers[score][{{ $answer->id }}]" :required="true"
                                min="1" max="5" step="1" style="width:60px" :value="$answer->score" />

                            @if ($answer->type->value == \App\Enums\Answser\AnswerType::Normal->value)
                                <x-input class="answer_normal" type="text"
                                    name="answers[answer][{{ $answer->id }}]" :value="$answer->answer" />

                                <div class="answer_image d-none" style="width: 200px;"
                                    data-answer="{{ $answer->id }}">
                                    <x-input-image-ckfinder name="answers[image][{{ $answer->id }}]"
                                        showImage="showAnswerImage-{{ $answer->id }}" />
                                </div>
                            @else
                                <x-input class="answer_normal d-none" type="text"
                                    name="answers[answer][{{ $answer->id }}]" :value="$answer->answer" />

                                <div class="answer_image" style="width: 200px;" data-answer="{{ $answer->id }}">
                                    <x-input-image-ckfinder name="answers[image][{{ $answer->id }}]"
                                        showImage="showAnswerImage-{{ $answer->id }}" :value="$answer->answer" />
                                </div>
                            @endif

                            @if (!$loop->first)
                                <button type="button" class="btn btn-danger remove_answer">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let index = {{ $response->answers->last()->id }};
</script>

@push('custom-js')
    <script>
        function showPreviewImage() {
            const fileInput = document.getElementById("customFile");
            let image = $("#preview-image");
            const selectedFiles = fileInput.files;

            if (selectedFiles.length > 0) {
                const file = selectedFiles[0];
                const fileType = file.type;

                if (fileType === "image/jpeg" || fileType === "image/png") {
                    image.attr('src', URL.createObjectURL(file));
                } else {
                    alert("Chỉ chấp nhận file JPG và PNG.");
                    fileInput.value = "";
                    image.attr('src', "");
                }
            }
        }

        function removeImage() {
            const fileInput = document.getElementById("customFile");
            const image = document.getElementById("preview-image");
            const removeButton = document.getElementById("remove-image");

            fileInput.value = "";
            image.src = "";
            image.style.display = "none";
            removeButton.style.display = 'none';
        }
    </script>
@endpush
