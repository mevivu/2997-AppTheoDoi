<style>
    .select2-bs5-ajax {
        width: 100% !important;
    }
</style>
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
            <!-- hotline -->
            <div class="col-md-12 col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-phone"></span>
                        {{ __('hotline') }}: <span class="text-danger">*</span></label>
                    <x-input type="tel"
                             :placeholder="__('hotline')"
                             name="hotline"
                             :value="old('hotline')"
                             :required="true" />
                </div>
            </div>

            <!-- clinic_type-->
            <div class="col-md-12 col-12 mb-3">
                <label class="form-label fw-bold">@lang('clinic_type') <span class="text-danger">*</span></label>
                <x-select name="clinic_type_id"
                          id="clinic_type_id"
                          :required="true"
                          class="select2-bs5-ajax form-select"
                          data-url="{{ route('admin.search.select.clinicType') }}">
                </x-select>
            </div>


            <!-- opening_time -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <i class="ti ti-clock"></i>
                        @lang('opening_time') <span class="text-danger">*</span></label>
                    <x-input type="time" name="opening_time"
                             :value="old('opening_time')"
                             :required="true"
                             :placeholder="__('opening_time')" />
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
                             :value="old('closing_time')"
                             :placeholder="__('closing_time')" />
                </div>
            </div>


            <!-- address -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('address') <span class="text-danger">*</span></label>
                    <x-input name="address"
                             :value="old('address')"
                             :required="true"
                             :placeholder="__('address')"/>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for=""><i class="ti ti-building"></i> {{ __('Tỉnh/Thành phố') }} <span
                        class="text-danger">*</span></label>
                <x-select name="province_id" id="province_id" class="select2-bs5-ajax"
                          data-url="{{ route('admin.search.select.province') }}" :required="true">
                </x-select>
            </div>
            <div class="col-md-6 mb-3">
                <label for=""><i class="ti ti-building"></i> {{ __('Phường/Xã') }} <span
                        class="text-danger">*</span></label>
                <x-select name="ward_id" id="ward_id" class="select2-bs5-ajax" data-url="" :required="true">
                </x-select>
            </div>



        </div>
    </div>
</div>
