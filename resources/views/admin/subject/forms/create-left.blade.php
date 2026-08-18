<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thêm môn học mới') }}</h2>
        </div>
        <div class="row card-body">

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-math"></span>
                        {{ __('Môn học') }}: <span class="text-danger">*</span></label>
                    <x-input type="text" name="name" :value="old('name')" :required="true" />
                </div>
            </div>

            <div class="col-12">
                <label class="control-label">
                    <span class="ti ti-school"></span>
                    @lang('Lớp học'):</label>
                <x-select class="select2-bs5-ajax" name="class_id[]" id="class_id" :data-url="route('admin.search.select.classes')" multiple>
                </x-select>
            </div>
        </div>
    </div>
</div>
