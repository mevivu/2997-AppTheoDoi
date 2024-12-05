<div class="row" id="question_iq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
            <x-input type="number" min="0" name="question[age]" :value="$response->age" :required="true" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Câu trả lời đúng') }}:</label>
            <x-input type="text" name="answer[correct_answer]" :value="$correctAnswer->answer" :required="true" />
            <x-input type="hidden" name="answer[correct_answer_id]" :value="$correctAnswer->id" />
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

        @foreach ($wrongAnswers as $wrong_answer)
            <div class="d-flex align-items-center justify-content-between gap-3 position-relative mb-3">
                <x-input type="text" name="answer[wrong_answers][{{ $wrong_answer->id }}]" :value="$wrong_answer->answer"
                    :placeholder="'Nhập câu trả lời sai'" :required="true" />
                <x-input type="hidden" name="answer[wrong_answers_ids][]" :value="$wrong_answer->id" />
                @if ($loop->index != 0)
                    <button type="button" class="btn btn-danger remove_wrong_answer">
                        <i class="ti ti-x fs-2"></i>
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</div>
