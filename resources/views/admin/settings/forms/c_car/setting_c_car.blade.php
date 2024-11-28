<div class="card h-100">
    <div class="card-header justify-content-center">
        <h2 class="mb-0">{{ $title ?? __('Thông tin cài đặt') }}</h2>
    </div>
    <div class="row card-body wrap-loop-input">
        @foreach ($settings as $setting)
            <div class="col-12">
                <div class="mb-3">
                    <label for="{{ $setting->setting_key }}" class="form-label">{{ $setting->setting_name }}</label>
                    @if ($setting->type_input == App\Enums\Setting\SettingTypeInput::Checkbox())
                        <!-- Handling Checkbox Type -->
                        <div class="form-check form-switch">
                            <input type="hidden" name="{{ $setting->setting_key }}" value="0">
                            <input class="form-check-input" type="checkbox" id="{{ $setting->setting_key }}"
                                name="{{ $setting->setting_key }}" value="1"
                                {{ $setting->plain_value == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $setting->setting_key }}"></label>
                        </div>
                    @elseif ($setting->type_input == App\Enums\Setting\SettingTypeInput::Textarea())
                        <!-- Handling Textarea Type -->
                        <textarea name="{{ $setting->setting_key }}" class="ckeditor visually-hidden"
                            placeholder="{{ $setting->setting_name }}">{{ $setting->plain_value }}</textarea>
                    @else
                        <!-- Handling Other Types -->
                        <x-dynamic-component :component="$setting->getNameComponentTypeInput()" :name="$setting->setting_key" :value="$setting->plain_value"
                            showImage="{{ $setting->setting_key }}" :required="true">
                        </x-dynamic-component>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
   
    <div class="card mb-4 shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
            <h2 class="mb-0">
                <i class="ti ti-timeline me-2"></i> {{ __('Khung giờ cao điểm') }}
            </h2>
        </div>
        <div class="card-body">
            <h4 class="text-secondary mb-3">{{ __('Buổi sáng') }}</h4>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-danger fw-bold">
                            <i class="ti ti-clock me-2"></i>{{ __('Giờ bắt đầu') }}:
                        </label>
                        <input type="time" 
                            name="bike_C_Car_morning_start" 
                            class="form-control" 
                            id="bike_C_Car_morning_start"
                            value="{{ $bikeC_Car_Rush_Hour['bike_C_Car_morning_start'] }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-danger fw-bold">
                            <i class="ti ti-clock me-2"></i>{{ __('Giờ kết thúc') }}:
                        </label>
                        <input type="time" 
                            name="bike_C_Car_morning_end" 
                            class="form-control" 
                            id="bike_C_Car_morning_end"
                            value="{{ $bikeC_Car_Rush_Hour['bike_C_Car_morning_end'] }}">
                    </div>
                </div>
            </div>
    
            <h4 class="text-secondary mb-3">{{ __('Buổi chiều') }}</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-danger fw-bold">
                            <i class="ti ti-clock me-2"></i>{{ __('Giờ bắt đầu') }}:
                        </label>
                        <input type="time" 
                            name="bike_C_Car_afternoon_start" 
                            class="form-control" 
                            id="bike_C_Car_afternoon_start"
                            value="{{ $bikeC_Car_Rush_Hour['bike_C_Car_afternoon_start'] }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label text-danger fw-bold">
                            <i class="ti ti-clock me-2"></i>{{ __('Giờ kết thúc') }}:
                        </label>
                        <input type="time" 
                            name="bike_C_Car_afternoon_end" 
                            class="form-control" 
                            id="bike_C_Car_afternoon_end"
                            value="{{ $bikeC_Car_Rush_Hour['bike_C_Car_afternoon_end'] }}">
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    

    <div class="card mb-3">
        <div class="card-header justify-content-between">
            <h2 class="mb-0"><i class="ti ti-motorbike"></i> {{ __('Cài đặt Dịch vụ') }}</h2>
        </div>
        <div class="row card-body">

            <div class="setRatePerKmDiv">
                <!-- bike_C_Car_base_distance -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="control-label text-danger">
                            <i class="ti ti-coin"></i>
                            {{ __('Quãng đường tính giá mở cửa') }}:
                        </label>
                        <p class="fst-italic">Nếu khoảng cách quãng đường <strong>dưới</strong></p>
                        <x-input :value="$bikeC_Car_Settings['bike_C_Car_base_distance']"
                                 id="bike_C_Car_base_distance_input"
                                 type="number"
                                 min=0
                                 required
                                 name="bike_C_Car_base_distance"
                                 placeholder="{{ __('Đơn vị Km') }}" />
                    </div>
                </div>

                <!-- bike_C_Car_base_fare -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="control-label text-success">{{ __('... thì sẽ có giá mở cửa (VNĐ) là') }}:</label>
                        <p class="fst-italic">Dùng để tính giá tài xế xử lý đơn.</p>

                        <x-input-price name="bike_C_Car_base_fare"
                            id="bike_C_Car_base_fare"
                            :value="$bikeC_Car_Settings['bike_C_Car_base_fare']"
                            :required="true"
                            :placeholder="__('Ví dụ: 20000')"/>
                    </div>
                </div>
            </div>

            <div class="setRatePerKmDiv">
                <!-- bike_C_Car_distance_to_discount -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="control-label text-danger">
                            <i class="ti ti-coin"></i> {{ __('Quãng đường tính giá sau giá mở cửa') }}:
                        </label>
                        <p class="fst-italic">
                            Quãng đường sau km thứ <span class="fw-bold"
                                                         id="bike_C_Car_base_distance_text">
                                {{ $bikeC_Car_Settings['bike_C_Car_base_distance'] }}
                            </span> đến hết km thứ
                        </p>
                        <x-input :value="$bikeC_Car_Settings['bike_C_Car_distance_to_discount']"
                                 type="number"
                                 min=0
                                 required
                                 id="bike_C_Car_distance_to_discount_input"
                                 name="bike_C_Car_distance_to_discount"
                                 placeholder="{{ __('Đơn vị Km') }}" />
                    </div>
                </div>

                <!-- bike_C_Car_rate_per_km -->
                <div class="col-12">
                    <div class="mb-3">
                        <label class="control-label text-success">{{ __('... thì sẽ có giá / 1 km (VNĐ) là') }}:</label>
                        <p class="fst-italic">Dùng để tính giá tài xế xử lý đơn. </p>

                        <x-input-price name="bike_C_Car_rate_per_km"
                            id="bike_C_Car_rate_per_km"
                            :value="$bikeC_Car_Settings['bike_C_Car_rate_per_km']"
                            :required="true"
                            :placeholder="__('Ví dụ: 20000')"/>
                    </div>
                </div>

            </div>


            <div class="setRatePerKmDiv">
                <!-- bike_C_Car_rate_per_km_discount -->
                <div class="col-12">
                    <div class="mb-3">
                        <p class="fst-italic">Quãng đường sau km thứ
                            <span class="fw-bold" id="bike_C_Car_distance_to_discount_text">{{  $bikeC_Car_Settings['bike_C_Car_distance_to_discount'] }}</span>
                        </p>
                        <label class="control-label text-success">{{ __('... thì sẽ có giá / 1 km (VNĐ) là') }}:</label>

                        <x-input-price name="bike_C_Car_rate_per_km_discount"
                            id="bike_C_Car_rate_per_km_discount"
                            :value="$bikeC_Car_Settings['bike_C_Car_rate_per_km_discount']"
                            :required="true"
                            :placeholder="__('Ví dụ: 20000')"/>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
