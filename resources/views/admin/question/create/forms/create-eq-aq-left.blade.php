<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin câu hỏi') }}</h2>
        </div>

        <div class="row card-body">
            @if (request()->routeIs('admin.question.createEq'))
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="control-label">{{ __('Loại câu hỏi') }}:</label>
                        <input type="text"value="EQ" class="form-control" disabled>
                        <input type="hidden" name="question[question_type]" value="eq">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="control-label">{{ __('Nhóm câu hỏi') }}:</label>
                        <x-select name="question[question_group_id]" :required="true">
                            @foreach ($questionGroups as $key => $value)
                                <x-select-option :value="$key" :title="$value" />
                            @endforeach
                        </x-select>
                    </div>
                </div>
            @elseif(request()->routeIs('admin.question.createAq'))
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="control-label">{{ __('Loại câu hỏi') }}:</label>
                        <input type="text"value="AQ" class="form-control" disabled>
                        <input type="hidden" name="question[question_type]" value="aq">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="control-label">{{ __('Nhóm câu hỏi') }}:</label>
                        <x-select name="question[question_group_id]" :required="true">
                            @foreach ($questionGroups as $key => $value)
                                <x-select-option :value="$key" :title="$value" />
                            @endforeach
                        </x-select>
                    </div>
                </div>

            @endif



            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Câu hỏi') }}:</label>
                    <x-input name="question[question]" :required="true" />
                </div>
            </div>

                <div class="col-4">
                    <div class="mb-3">
                        <label class="control-label">{{ __('Hình ảnh câu hỏi') }}:</label>
                        <div class="card-body p-2">
                            <input type="file"  onchange="showPreviewImage()" class="form-control" name="question[question_image]"
                                   id="customFile" accept=".jpg, .jpeg, .png"/>

                            <div class="preview-image">
                                <image id="preview-image" src=""/>
                            </div>
                        </div>
                    </div>
                </div>

            <div class="col-md-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu trả lời') }}:</label>
                    <x-select name="answers[type]" :required="true" id="answer_type">
                        @foreach ($answer_types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="old('answer.type') == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nhóm tuổi') }}:</label>
                    <x-select name="question[age_group]" :required="true">
                        @foreach ($age_group as $key => $value)
                            <x-select-option :value="$key" :title="$value" />
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
                    <div class="d-flex align-items-center justify-content-start gap-2 mb-3">
                        <x-input type="number" name="answers[score][]" :required="true" min="1" max="5"
                            step="1" style="width:60px" />
                        <x-input class="answer_normal" type="text" name="answers[answer][]" />
                        <div class="answer_image d-none" style="width: 200px;" data-answer="0">
                            <x-input-image-ckfinder name="answers[image][0]" :value="old('answers.image[0]')"
                                showImage="showAnswerImage-0" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let index = 0;
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
    </script>
@endpush
