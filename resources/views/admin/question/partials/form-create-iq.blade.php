<div class="row d-none" id="question_iq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
            <x-input type="number" min="0" name="question[age]" :value="old('question.age')" />
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
            <input type="hidden" name="answer[is_correct][]" value="0" />
            <input type="checkbox" name="answer[is_correct][]" class="form-check-input" value="1"
                onchange="toggleCheckbox(this)" />
            <x-input type="text" name="answer[iq_answers][]" :placeholder="'Nhập nội dung câu trả lời'" />
        </div>
    </div>
</div>
