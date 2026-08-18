<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên') <span class="text-danger">*</span></label>
                    <x-input type="text"
                             name="name"
                             :value="$instance->name"
                             :required="true"
                             :placeholder="__('name')"/>
                </div>
            </div>

            <!-- hotline -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-phone"></span>
                        {{ __('hotline') }}: <span class="text-danger">*</span></label>
                    <x-input name="hotline"
                             type="tel"
                             :value="$instance->hotline"
                             :required="true"
                             placeholder="0807070707"/>
                </div>
            </div>

            <!-- clinic_type-->
            <div class="col-md-6 col-12 mb-3">
                <i class="ti ti-user-plus"></i>
                <label class="control-label">@lang('clinic_type'): <span class="text-danger">*</span></label>
                <x-select class="select2-bs5-ajax"
                          name="clinic_type_id"
                          id="clinic_type_id"
                          :required="true"
                          :data-url="route('admin.search.select.clinicType')">
                    <x-select-option :option="$instance->clinic_type_id"
                                     :value="$instance->clinic_type_id"
                                     :title="$instance->clinicType->name"/>
                </x-select>
            </div>

            <!-- opening_time -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <i class="ti ti-clock"></i>
                        @lang('opening_time') <span class="text-danger">*</span></label>
                    <x-input type="time"
                             name="opening_time"
                             :value="$instance->opening_time"
                             :required="true"
                             :placeholder="__('opening_time')"/>
                </div>
            </div>

            <!-- closing_time-->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <i class="ti ti-clock"></i>
                        @lang('closing_time') <span class="text-danger">*</span></label>
                    <x-input type="time"
                             name="closing_time"
                             :required="true"
                             :value="$instance->closing_time"
                             :placeholder="__('closing_time')"/>
                </div>
            </div>

            <!-- address -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('address') <span class="text-danger">*</span></label>
                    <x-input name="address"
                             :value="$instance->address"
                             :required="true"
                             :placeholder="__('address')"/>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for=""><i class="ti ti-building"></i> {{ __('Tỉnh/Thành phố:') }} <span
                        class="text-danger">*</span></label>
                <x-select name="province_id" id="province_id" class="select2-bs5-ajax"
                          data-url="{{ route('admin.search.select.province') }}" :required="true">
                    <x-select-option :option="$instance->province_id" :value="$instance->province_id" :title="$instance->province->name" />
                </x-select>
            </div>
            <div class="col-md-6 mb-3">
                <label for=""><i class="ti ti-building"></i> {{ __('Phường/Xã:') }} <span
                        class="text-danger">*</span></label>
                <x-select name="ward_id" id="ward_id" class="select2-bs5-ajax" data-url="" :required="true">
                    <x-select-option :option="$instance->ward_id" :value="$instance->ward_id" :title="$instance->ward->name" />
                </x-select>
            </div>

        </div>
    </div>
</div>
