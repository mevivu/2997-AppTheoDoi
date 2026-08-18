<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">
            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên') <span class="text-danger">*</span></label>
                    <x-input name="name"
                             :value="old('name')"
                             :required="true"
                             :placeholder="__('name')"/>
                </div>
            </div>


            <!-- description -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('description')</label>
                    <textarea name="description"
                              class="form-control"
                              rows="4"
                              placeholder="{{ __('description') }}"
                    >{{ old('description') }}</textarea>
                </div>
            </div>


            <!--reminder_time-->
            @if (request('back') !== 'admin')
                <div class="col-6">
                    <div class="mb-3">
                        <i class="ti ti-clock"></i>
                        <label class="control-label">@lang('performed_on') <span class="text-danger">*</span></label>
                        <x-input input
                                 type="datetime-local"
                                 name="performed_on"
                                 :value="old('performed_on')"
                                 :required="true"
                                 :placeholder="__('performed_on')"/>
                    </div>
                </div>
            @endif
            <!--vaccination Type-->
            <div class="col-md-12 col-sm-12">
                <label class="control-label">
                    <span class="ti ti-user"></span>
                    @lang('Loại tiêm chủng'):</label>
                <x-select class="select2-bs5-ajax" name="vaccination_type_id" id="vaccination_type_id"
                          :data-url="route('admin.search.select.vaccinationType')">
                </x-select>
            </div>
        </div>
    </div>
</div>
