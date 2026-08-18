@php use App\Enums\Answser\AnswerType; @endphp
<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin câu hỏi IQ') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu hỏi') }}:</label>
                    <input type="text" value="IQ" class="form-control" disabled>
                    <input type="hidden" name="question[question_type]" value="iq">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
                    <x-input type="number" min="0" name="question[age]" :value="$response->age"/>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nội dung câu hỏi') }}: <span class="text-danger">*</span></label>
                    <x-input type="text" name="question[question]" :value="$response->question" :required="true"/>
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


            <div class="col-md-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu trả lời') }}: <span class="text-danger">*</span></label>
                    <x-select name="answers[type]" :required="true" id="answer_type">
                        @foreach ($answer_types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$type == $key"/>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <label
                        class="control-label">{{ __('Câu trả lời (Check vào ô bên cạnh nếu câu trả lời là đúng)') }}
                        :</label>
                    <span class="text-primary cursor-pointer" id="add_answer">
                        <i class="ti ti-plus"></i>
                        {{ __('Thêm câu trả lời') }}
                    </span>
                </div>

                @php $index = 0; @endphp
                <div class="w-100" id="answer">
                    @foreach ($response->answers as $answer)
                        <div class="form-check d-flex align-items-center justify-content-start gap-2 mb-3">
                            <input class="form-check-input" type="radio" name="answers[is_correct]"
                                   value="{{ $index }}" {{ $answer->is_correct ? 'checked' : '' }}>
                            @if ($type == AnswerType::Normal->value)
                                <x-input class="answer_normal" type="text"
                                         name="answers[answer][{{ $answer->id }}]" placeholder="Nhập câu trả lời"
                                         :value="$answer->answer"/>
                                <div class="answer_image d-none" style="width: 200px;" data-answer="0">
                                    <x-input-image-ckfinder name="answers[image][]" :value="$answer->answer"
                                                            showImage="showAnswerImage-0"/>
                                </div>
                            @else
                                <x-input class="answer_normal d-none" type="text" name="answers[answer][]"
                                         placeholder="Nhập câu trả lời"/>
                                <div class="answer_image" style="width: 200px;" data-answer="{{ $answer->id }}">
                                    <x-input-image-ckfinder name="answers[image][{{ $answer->id }}]"
                                                            :value="$answer->answer"
                                                            showImage="showAnswerImage-{{ $answer->id }}"/>
                                </div>
                            @endif
                            @if (!$loop->first)
                                <button type="button" class="btn btn-danger remove_answer">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                        @php $index++; @endphp
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
