<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Lớp') }}</h2>
        </div>
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên') <span class="text-danger">*</span></label>
                    <x-input type="text"
                             name="name"
                             :value="$response->name"
                             :required="true"
                             :placeholder="__('name')"/>
                </div>
            </div>

            <div class=" col-12">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    @lang('Môn'): <span class="text-danger">*</span></label>
                <x-select class="select2-bs5-ajax"
                          name="subject_id[]"
                          id="subject_id"
                          :required="true"
                          multiple
                          :data-url="route('admin.search.select.subject')">
                    @foreach ($response->subjects as $subject)
                        <x-select-option :option="$subject->id" :value="$subject->id" :title="$subject->name" />
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>
</div>
