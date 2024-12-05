<div class="row d-none" id="question_iq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
            <x-input type="number" min="0" name="question[age]" :value="old('question.age')" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Câu trả lời đúng') }}:</label>
            <x-input type="text" name="answer[correct_answer]" :value="old('answer.correct_answer')" />
        </div>
    </div>
    <div class="col-12" id="wrong_answers">
        <div class="d-flex align-items-center justify-content-between">
            <label class="control-label">{{ __('Câu trả lời sai') }}:</label>
            <span class="text-primary cursor-pointer" id="add_wrong_answer">
                <i class="ti ti-plus"></i>
                {{ __('Thêm câu trả lời sai') }}
            </span>
        </div>
        <x-input type="text" name="answer[wrong_answers][]" class="mb-3" :placeholder="'Nhập câu trả lời sai'" />
    </div>
</div>
