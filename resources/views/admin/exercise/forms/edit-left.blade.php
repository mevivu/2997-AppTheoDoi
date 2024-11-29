<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin bài tập') }}</h2>

        </div>
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Tên bài tập') }}:</label>
                    <x-input type="text" name="name" :value="$response->name" :required="true" />
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Loại bài tập') }}:</label>
                    <x-select name="exercise_type" :required="true">
                        @foreach ($types as $key => $value)
                            <x-select-option :value="$key" :title="$value" :option="$response->exercise_type->value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">{{ __('Mô tả') }}:</label>
                    <textarea name="description" class="ckeditor visually-hidden">{{ $response->description }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
