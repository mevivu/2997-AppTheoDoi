<div class="row" id="question_aq_eq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Nhóm câu hỏi') }}:</label>
            <x-select name="question[question_group_id]" :required="true">
                @foreach ($questionGroups as $key => $value)
                    <x-select-option :value="$key" :title="$value" :option="$response->question_group_id" />
                @endforeach
            </x-select>
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Loại câu trả lời') }}:</label>
            <x-select name="answer[answer_type_aq_eq]" :required="true" id="answer_type_aq_eq" disabled>
                @foreach ($answer_types as $key => $value)
                    <x-select-option :value="$key" :title="$value" :option="$response->answers->first()->type->value" />
                @endforeach
            </x-select>
            <input type="hidden" name="answer[answer_type_aq_eq]"
                value="{{ $response->answers->first()->type->value }}" />
        </div>
    </div>


    <div class="col-12" id="answers">
        <div class="d-flex align-items-center justify-content-between">
            <label class="control-label">{{ __('Câu trả lời') }}:</label>
            <span class="text-primary cursor-pointer" id="add_answer">
                <i class="ti ti-plus"></i>
                {{ __('Thêm câu trả lời') }}
            </span>
        </div>

        @foreach ($answers as $answer)
            <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
                @if ($answer->type->value == \App\Enums\Answser\AnswerType::Normal->value)
                    <div class="w-50 default-aqeq">
                        <x-input type="text" name="answer[answers][{{ $answer->id }}]" class="flex-grow-1"
                            :value="$answer->answer" :placeholder="'Nhập câu trả lời'" />
                    </div>
                @endif

                @if ($answer->type->value == \App\Enums\Answser\AnswerType::Image->value)
                    <div class="img-ckfinder-eqaq" style="width:200px; object-fit:cover;">
                        <x-input-image-ckfinder name="answer[image-aqeq][{{ $answer->id }}]" :value="$answer->image"
                            showImage="answerImageEQAQ-{{ $answer->id }}" />
                    </div>
                @endif

                <div class="w-50">
                    <x-input type="number" name="answer[answers][{{ $answer->id }}][score]" min="1"
                        max="5" class="flex-grow-1" :value="$answer->score" :placeholder="'Nhập điểm đánh giá'" />
                </div>

                @if ($loop->index != 0)
                    <button type="button" class="btn btn-danger remove_answer">
                        <i class="ti ti-x fs-2"></i>
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</div>
