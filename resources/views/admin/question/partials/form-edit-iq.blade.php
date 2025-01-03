<div class="row" id="question_iq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
            <x-input type="number" min="0" name="question[age]" :value="$response->age" :required="true" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Loại câu trả lời') }}:</label>
            <x-select name="answer[answer_type]" :required="true" id="answer_type_aq_eq" disabled>
                @foreach ($answer_types as $key => $value)
                    <x-select-option :value="$key" :title="$value" :option="$response->answers->first()->type->value" />
                @endforeach
            </x-select>
            <input type="hidden" name="answer[answer_type]" value="{{ $response->answers->first()->type->value }}" />
        </div>
    </div>

    <div class="col-12" id="wrong_answers">
        <div class="d-flex align-items-center justify-content-between">
            <label class="control-label">{{ __('Câu trả lời (Check vào ô bên cạnh nếu câu trả lời là đúng)') }}:</label>
            <span class="text-primary cursor-pointer" id="add_wrong_answer_edit">
                <i class="ti ti-plus"></i>
                {{ __('Thêm câu trả lời') }}
            </span>
        </div>
        @foreach ($iq_answers as $index => $answer)
            <div class="d-flex align-items-center justify-content-start gap-2 mb-3">
                <input type="radio" name="answer[is_correct][{{ $answer->question_id }}]" class="form-check-input"
                    value="{{ $answer->id }}" {{ $answer->is_correct ? 'checked' : '' }} />
                @if ($answer->type->value == \App\Enums\Answser\AnswerType::Normal->value)
                    <x-input type="text" name="answer[iq_answers][{{ $answer->id }}]" class="default"
                        value="{{ $answer->answer }}" onclick="toggleCheckbox(this)" />
                @endif

                @if ($answer->type->value == \App\Enums\Answser\AnswerType::Image->value)
                    <div class="img-ckfinder" style="width:200px; object-fit:cover;">
                        <x-input-image-ckfinder name="answer[image-iq][{{ $answer->id }}]"
                            value="{{ $answer->image }}" showImage="answerImage-{{ $answer->id }}" />
                    </div>
                @endif

                @if (!$loop->first)
                    <button type="button" class="btn btn-danger remove_wrong_answer">
                        <i class="ti ti-x fs-2"></i>
                    </button>
                @endif
            </div>
        @endforeach
    </div>

    <script>
        let question_id = {{ $response->id }}
    </script>
</div>
