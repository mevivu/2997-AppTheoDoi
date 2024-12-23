<div class="row" id="question_iq_group">
    <div class="col-md-6">
        <div class="mb-3">
            <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
            <x-input type="number" min="0" name="question[age]" :value="$response->age" :required="true" />
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
                <input type="radio" name="answer[is_correct][{{ $response->id }}]" class="form-check-input"
                    value="{{ $answer->id }}" {{ $answer->is_correct ? 'checked' : '' }} />

                <x-input type="text" required name="answer[iq_answers][{{ $answer->id }}]"
                    value="{{ $answer->answer }}" onclick="toggleCheckbox(this)" />

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
