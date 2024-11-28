<div class="card h-100">
    <div class="card-header justify-content-center">
        <h2 class="mb-0">{{ $title ?? __('Thông tin cài đặt') }}</h2>
    </div>
    <div class="row card-body wrap-loop-input">
        @php
            $previousSetting = null;
        @endphp
        @foreach ($settings as $setting)
            @if ($setting->setting_key == 'c_Delivery_option')
                <div class="col-12  ">
                    <div class="mb-3">
                        <label for="">{{ $setting->desc }}</label>
                        <x-dynamic-component :component="$setting->getNameComponentTypeInput()" :name="$setting->setting_key" :value="$setting->plain_value" :label="$setting->setting_name"
                            :checked="$setting->plain_value" :required="true" />
                    </div>
                </div>
                @php
                    $previousSetting = $setting->setting_key;
                @endphp
                @continue
            @endif
            <div class="col-12">
                <div class="mb-3">
                    <label for="">{{ $setting->desc }}</label>
                    <x-dynamic-component :component="$setting->getNameComponentTypeInput()" :name="$setting->setting_key" :value="$setting->plain_value" :label="$setting->setting_name"
                        :checked="$setting->plain_value == 1" showImage="{{ $setting->setting_key }}" :required="true" />
                </div>
            </div>
        @endforeach
    </div>
</div>
