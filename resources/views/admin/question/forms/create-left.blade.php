<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin câu hỏi') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại câu hỏi') }}:</label>
                    <x-select name="question[question_type]" :required="true" id="question_type">
                        <x-select-option :value="null" :title="__('Chọn loại câu hỏi')" />
                        @foreach ($types as $key => $value)
                            <x-select-option :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Nội dung câu hỏi') }}:</label>
                    <x-input type="text" name="question[question]" :value="old('question.question')" :required="true" />
                </div>
            </div>

            <div class="col-md-6"></div>

            @include('admin.question.partials.form-create-iq')

            @include('admin.question.partials.form-create-aq-eq')
        </div>
    </div>
</div>
