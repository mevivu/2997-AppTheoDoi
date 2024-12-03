<div class="row" id="question_iq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Tuổi') }}:</label>
            <x-input type="number" min="0" name="age" :value="$response->age" :required="true" />
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Câu trả lời đúng') }}:</label>
            <x-input type="text" name="correct_answer" :value="$response->correct_answer" :required="true" />
        </div>
    </div>

    {{-- <div class="col-12" id="wrong_answers">
        <div class="d-flex align-items-center justify-content-between">
            <label class="control-label">{{ __('Câu trả lời sai') }}:</label>
            <span class="text-primary cursor-pointer" id="add_wrong_answer">
                <i class="ti ti-plus"></i>
                {{ __('Thêm câu trả lời sai') }}
            </span>
        </div>
        <x-input type="text" name="wrong_answers[]" class="mb-3" :value="old('wrong_answer')" :placeholder="'Nhập câu trả lời sai'"
            :required="true" />
    </div> --}}


    <div class="col-12" id="wrong_answers">
        <div class="d-flex align-items-center justify-content-between">
            <label class="control-label">{{ __('Câu trả lời sai') }}:</label>
            <span class="text-primary cursor-pointer" id="add_wrong_answer">
                <i class="ti ti-plus"></i>
                {{ __('Thêm câu trả lời sai') }}
            </span>
        </div>

        @foreach (json_decode($response->wrong_answers) as $wrong_answer)
            <div class="d-flex align-items-center justify-content-between position-relative mb-3">
                <x-input type="text" name="wrong_answers[]" :value="$wrong_answer" :placeholder="'Nhập câu trả lời sai'" :required="true" />
                <button type="button" class="btn btn-sm btn-danger position-absolute end-0 me-2"
                    id="remove_wrong_answer">
                    <i class="ti ti-x fs-2"></i>
                </button>
            </div>
        @endforeach
    </div>
</div>
