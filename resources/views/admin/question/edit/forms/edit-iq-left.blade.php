<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin câu hỏi IQ') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu hỏi') }}:</label>
                    <input type="text"value="IQ" class="form-control" disabled>
                    <input type="hidden" name="question[question_type]" value="iq">
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Độ tuổi phù hợp') }}:</label>
                    <x-input type="number" min="0" name="question[age]" :value="$response->age" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nội dung câu hỏi') }}:</label>
                    <x-input type="text" name="question[question]" :value="$response->question" :required="true" />
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu trả lời') }}:</label>
                    <x-select name="answers[type]" :required="true" id="answer_type">
                        @foreach ($answer_types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$type == $key" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <label
                        class="control-label">{{ __('Câu trả lời (Check vào ô bên cạnh nếu câu trả lời là đúng)') }}:</label>
                    <span class="text-primary cursor-pointer" id="add_answer">
                        <i class="ti ti-plus"></i>
                        {{ __('Thêm câu trả lời') }}
                    </span>
                </div>

                <div class="w-100" id="answer">
                    @foreach ($response->answers as $answer)
                        <div class="form-check d-flex align-items-center justify-content-start gap-2 mb-3">
                            <input class="form-check-input" type="radio" name="answers[is_correct]"
                                value="{{ $answer->id }}" {{ $answer->is_correct ? 'checked' : '' }}>
                            @if ($type == \App\Enums\Answser\AnswerType::Normal->value)
                                <x-input class="answer_normal" type="text"
                                    name="answers[answer][{{ $answer->id }}]" placeholder="Nhập câu trả lời"
                                    :value="$answer->answer" />
                                <div class="answer_image d-none" style="width: 200px;" data-answer="0">
                                    <x-input-image-ckfinder name="answers[image][]" :value="$answer->answer"
                                        showImage="showAnswerImage-0" />
                                </div>
                            @else
                                <x-input class="answer_normal d-none" type="text" name="answers[answer][]"
                                    placeholder="Nhập câu trả lời" />
                                <div class="answer_image" style="width: 200px;" data-answer="{{ $answer->id }}">
                                    <x-input-image-ckfinder name="answers[image][{{ $answer->id }}]"
                                        :value="$answer->answer" showImage="showAnswerImage-{{ $answer->id }}" />
                                </div>
                            @endif
                            @if (!$loop->first)
                                <button type="button" class="btn btn-danger remove_answer">
                                    <i class="ti ti-x"></i>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let index = {{ $response->answers->last()->id }};
</script>
