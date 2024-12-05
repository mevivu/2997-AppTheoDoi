<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin câu hỏi') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu hỏi') }}:</label>
                    <x-select name="question[question_type]" :required="true" id="question_type" disabled>
                        <x-select-option :value="null" :title="__('Chọn loại câu hỏi')" />
                        @foreach ($types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :option="$response->question_type->value" />
                        @endforeach
                    </x-select>
                    <input type="hidden" name="question[question_type]" value="{{ $response->question_type->value }}">
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Câu hỏi') }}:</label>
                    <x-input type="text" name="question[question]" :value="$response->question" :required="true" />
                </div>
            </div>

            @if ($response->question_type->value == \App\Enums\Question\QuestionType::IQ->value)
                @include('admin.question.partials.form-edit-iq')
            @elseif (
                $response->question_type->value == \App\Enums\Question\QuestionType::AQ->value ||
                    $response->question_type->value == \App\Enums\Question\QuestionType::EQ->value)
                @include('admin.question.partials.form-edit-aq-eq')
            @endif
        </div>
    </div>
</div>
