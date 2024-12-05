<div class="row" id="question_aq_eq_group">
    <div class="col-12">
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
                <div class="w-full">
                    <x-input type="text" name="answer[answers][{{ $answer->id }}]" class="flex-grow-1"
                        :value="$answer->answer" :placeholder="'Nhập câu trả lời'" :required="true" />
                </div>

                <div class="w-full">
                    <x-input type="number" min="1" max="5" name="answer[scores][{{ $answer->id }}]"
                        class="flex-grow-1" :value="$answer->score" :placeholder="'Nhập điểm đánh giá'" :required="true" />
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
