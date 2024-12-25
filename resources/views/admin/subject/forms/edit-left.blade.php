<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Chỉnh sửa thông tin môn học') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-md-6">
                <div class="mb-3">
                    <span class="ti ti-math"></span>
                    {{ __('Môn học') }}:</label>
                    <x-input type="text" name="name" :value="$response->name" :required="true" />
                </div>
            </div>

            <div class="col-12">
                <label class="control-label">
                    <span class="ti ti-school"></span>
                    @lang('Lớp học'):</label>
                <x-select class="select2-bs5-ajax" name="class_id[]" id="class_id" :data-url="route('admin.search.select.classes')" multiple>
                    @foreach ($response->classes as $class)
                        <x-select-option :option="$class->id" :value="$class->id" :title="$class->name" />
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>
</div>
