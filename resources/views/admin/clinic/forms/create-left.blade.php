<div class="col-12 col-md-9">
    <div class="card">
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
            <!-- hotline -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <label class="control-label">
                        <span class="ti ti-phone"></span>
                        {{ __('hotline') }}:</label>
                    <x-input-phone name="hotline" :value="old('hotline')" :required="true" />
                </div>
            </div>

            <!-- clinic_type-->
            <div class="col-md-6 col-12 mb-3">
                <label class="form-label fw-bold">@lang('clinic_type')</label>
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
                        @lang('opening_time')</label>
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
                        @lang('closing_time')</label>
                    <x-input type="time" name="closing_time"
                             :value="old('closing_time')"
                             :placeholder="__('closing_time')" />
                </div>
            </div>


            <!-- address -->
            <div class="col-12">
                <div class="mb-3">
                    <label class="control-label">@lang('address')</label>
                    <x-input name="address"
                             :value="old('address')"
                             :required="true"
                             :placeholder="__('address')"/>
                </div>
            </div>

            {{-- address --}}
            <div class="col-12">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('province'):</label>
                        <x-select name="province" :required="true">
                            <x-select-option value="" :title="__('choose')"/>
                            @foreach ($provinces as $province)
                                <x-select-option :value="$province->code" :title="__($province->name)"/>
                            @endforeach
                        </x-select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('district'):</label>
                        <x-select name="district" required>
                            <option value="">-- Chọn Quận/Huyện --</option>
                        </x-select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="control-label">@lang('ward'):</label>
                        <x-select name="ward" required>
                            <option value="">-- Chọn Phường/Xã --</option>
                        </x-select>
                    </div>
                </div>
            </div>



        </div>
    </div>
</div>
