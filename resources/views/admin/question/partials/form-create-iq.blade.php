<div class="row d-none" id="question_iq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
            <x-input type="number" min="0" name="question[age]" :value="old('question.age')" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Loại câu trả lời') }}:</label>
            <x-select name="answer[answer_type]" :required="true" id="answer_type">
                @foreach ($answer_types as $key => $value)
                    <x-select-option :value="$key" :title="$value" :selected="old('answer.answer_type') == $key" />
                @endforeach
            </x-select>
        </div>
    </div>

    <div class="col-12" id="wrong_answers">
        <div class="d-flex align-items-center justify-content-between">
            <label class="control-label">{{ __('Câu trả lời (Check vào ô bên cạnh nếu câu trả lời là đúng)') }}:</label>
            <span class="text-primary cursor-pointer" id="add_wrong_answer">
                <i class="ti ti-plus"></i>
                {{ __('Thêm câu trả lời') }}
            </span>
        </div>
        <div class="d-flex align-items-center justify-content-start gap-2 mb-3">
            <input type="hidden" name="answer[is_correct][][0]" value="0" />
            <input type="radio" name="answer[is_correct][][0]" class="form-check-input" value="1"
                onchange="toggleCheckbox(this)" />
            <x-input type="text" name="answer[iq_answers][]" :placeholder="'Nhập nội dung câu trả lời'" class="default" />
            <div class="d-none img-ckfinder" style="width:200px; object-fit:cover;">
                <x-input-image-ckfinder name="answer[image-iq][]" :value="old('image[0]')" showImage="answerImage-1" />
            </div>
        </div>

        <script>
            let question_id = 0
        </script>
    </div>
</div>
