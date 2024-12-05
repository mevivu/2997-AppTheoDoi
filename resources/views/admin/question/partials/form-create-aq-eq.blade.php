<div class="row d-none" id="question_aq_eq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Nhóm câu hỏi') }}:</label>
            <x-select name="question[question_group_id]">
                <x-select-option :value="null" :title="__('Chọn nhóm câu hỏi')" />
                @foreach ($questionGroups as $key => $value)
                    <x-select-option :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>

    <div class="col-md-6">
    </div>

    <div class="col-12" id="answers">
        <div class="d-flex align-items-center justify-content-between">
            <label class="control-label">{{ __('Câu trả lời:') }}:</label>
            <span class="text-primary cursor-pointer" id="add_answer">
                <i class="ti ti-plus"></i>
                {{ __('Thêm câu trả lời') }}
            </span>
        </div>
        <div class="d-flex align-items-center justify-content-between gap-2 mb-3">
            <div class="w-50">
                <x-input type="text" name="answer[answers][]" class="flex-grow-1" :value="old('answer.answer.0')"
                    :placeholder="'Nhập câu trả lời'" />
            </div>
            <div class="w-50">
                <x-input type="number" name="answer[scores][]" min="1" max="5" class="flex-grow-1"
                    :value="old('answer.point.0')" :placeholder="'Nhập điểm đánh giá'" />
            </div>
        </div>
    </div>
</div>
