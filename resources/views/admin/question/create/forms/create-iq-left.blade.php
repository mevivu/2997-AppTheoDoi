<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin câu hỏi IQ') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu hỏi') }}:</label>
                    <input type="text"value="IQ" class="form-control" disabled>
                    <input type="hidden" name="question[question_type]" value="iq">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
                    <x-input type="number" min="0" name="question[age]" :value="old('question.age')" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nội dung câu hỏi') }}:</label>
                    <x-input type="text" name="question[question]" :value="old('question.question')" :required="true" />
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

            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <label
                        class="control-label">{{ __('Câu trả lời (Check vào ô bên cạnh nếu câu trả lời là đúng)') }}:</label>
                    <span class="text-primary cursor-pointer" id="add_answer">
                        <i class="ti ti-plus"></i>
                        {{ __('Thêm câu trả lời') }}
                    </span>
                </div>

                <div class="w-100" id="answer">
                    <div class="form-check d-flex align-items-center justify-content-start gap-2 mb-3">
                        <input class="form-check-input" type="radio" name="answers[is_correct]" value="0">
                        <x-input class="answer_normal" type="text" name="answers[answer][]"
                            placeholder="Nhập câu trả lời" />
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
