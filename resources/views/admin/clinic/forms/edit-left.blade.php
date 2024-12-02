<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">

            <!-- Name -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('Tên')</label>
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
                        {{ __('hotline') }}:</label>
                    <x-input-phone name="hotline"
                                   :value="$instance->hotline"
                                   :required="true"/>
                </div>
            </div>

            <!-- clinic_type-->
            <div class="col-md-6 col-12 mb-3">
                <i class="ti ti-user-plus"></i>
                <label class="control-label">@lang('clinic_type'):</label>
                <x-select class="select2-bs5-ajax"
                          name="clinic_type_id"
                          id="clinic_type_id"
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
                        @lang('opening_time')</label>
                    <x-input type="time" name="opening_time"
                             :value="$instance->opening_time"
                             :required="true"
                             :placeholder="__('opening_time')" />
                </div>
            </div>

            <!-- closing_time-->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <i class="ti ti-clock"></i>
                        @lang('closing_time')</label>
                    <x-input type="time" name="closing_time"
                             :value="$instance->closing_time"
                             :placeholder="__('closing_time')" />
                </div>
            </div>

            <!-- address -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('address')</label>
                    <x-input name="address"
                             :value="$instance->address"
                             :required="true"
                             :placeholder="__('address')"/>
                </div>
            </div>

            <div class="col-12">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('province')</label>
                        <x-select name="province" :required="true" :selected="$instance->province->code">
                            <x-select-option value="" :title="__('choose')"/>
                            @foreach ($provinces  as $province)
                                <x-select-option :value="$province->code"
                                                 :title="__($province->name)"
                                                 :selected="$province->code == $instance->province->code"/>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('district')</label>
                        <x-select name="district" data-district-code="{{ $instance->district->code }}" required>
                            <option value="{{ $district->code }}" {{ $district->code == $instance->district->code ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>

                        </x-select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('ward')</label>
                        <x-select name="ward" required data-ward-code="{{ $instance->ward->code }}">
                            <option value="{{ $ward->code }}" {{ $ward->code == $instance->ward->code ? 'selected' : '' }}>
                                {{ $ward->name }}
                            </option>
                        </x-select>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
