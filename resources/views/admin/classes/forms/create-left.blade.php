<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Lớp') }}</h2>
        </div>
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên')</label>
                    <x-input name="name"
                             :value="old('name')"
                             :required="true"
                             :placeholder="__('name')"/>
                </div>
            </div>
            <div class=" col-12">
                <label class="form-label fw-bold">@lang('Môn')</label>
                <x-select name="subject_id[]"
                          id="subject_id"
                          :required="true"
                          multiple
                          class="select2-bs5-ajax form-select"
                          data-url="{{ route('admin.search.select.subject') }}">
                </x-select>
            </div>


        </div>
    </div>
</div>
