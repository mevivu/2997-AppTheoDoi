<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thêm môn học mới') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">{{ __('name') }}:</label>
                    <x-input type="text" name="name" :value="old('name')" :required="true" />
                </div>
            </div>

            <div class="col-md-6">
                <label class="control-label">{{ __('Lớp học') }}:</label>
                <x-select name="class_id" class="select2" :required="true">
                    <option value="">{{ __('Chọn lớp học') }}</option>
                    @foreach ($classes as $key => $value)
                        <x-select-option :value="$key" :title="$value" />
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>
</div>
