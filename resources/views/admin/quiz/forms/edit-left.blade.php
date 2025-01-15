<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- type -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('type')</label>
                    <x-select name="type" disabled :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option :value="$key" :title="$value" :selected="$instance->type->value == $key"/>
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('title')</label>
                    <x-input type="text" name="title" :value="$instance->title" :required="true"
                             :placeholder="__('title')"/>
                </div>
            </div>
            @if ($instance->type == \App\Enums\Question\QuestionType::IQ->value)
                <!-- age -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="control-label">@lang('age')</label>
                        <x-input type="text" name="age" :value="$instance->age" :required="true"
                                 :placeholder="__('age')"/>
                    </div>
                </div>
            @endif

            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('description')</label>
                    <textarea name="description" class="form-control" rows="4"
                              placeholder="{{ __('description') }}">{{ $instance->description }}</textarea>
                </div>
            </div>

            <!-- show questions -->
            <div class="col-12">
                <div class="mb-3">
                    <div class="col-12">
                        <div id="count-checked" class="mb-3">
                            Các câu hỏi được chọn: <span id="checked-count">0</span>
                        </div>
                    </div>
                    <div>
                        @foreach ($questions_type as $question)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="question_ids[]"
                                       value="{{ $question->id }}"
                                       id="question-{{ $question->id }}"
                                       data-group-id="{{ $question->group->id }}"
                                    {{ $selected_questions->contains($question->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="question-{{ $question->id }}">
                                    {{ $question->question }} <span class="fw-bold">({{ $question->group->name }})</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
