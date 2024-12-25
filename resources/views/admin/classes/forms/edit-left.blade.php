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
                    @lang('Môn'):</label>
                <x-select class="select2-bs5-ajax"
                          name="subject_id"
                          id="subject_id"
                          :data-url="route('admin.search.select.subject')">
                    <x-select-option
                        :option="$response->subject_id"
                        :value="$response->subject_id"
                        :title="$response->nameSubject"
                        :selected="old('subject_id') ? (old('subject_id') == $response->subject_id) : true"
                    />
                </x-select>
            </div>
        </div>
    </div>
</div>
