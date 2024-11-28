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
                            <input class="form-check-input"
                                   type="checkbox"
                                   id="{{ $setting->setting_key }}"
                                   name="{{ $setting->setting_key }}" value="1"
                                {{ $setting->plain_value == '1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="{{ $setting->setting_key }}"></label>
                        </div>
                    @elseif ($setting->type_input == App\Enums\Setting\SettingTypeInput::Textarea())
                        <!-- Handling Textarea Type -->
                        <textarea name="{{ $setting->setting_key }}" class="ckeditor visually-hidden" placeholder="{{ $setting->setting_name }}">{{ $setting->plain_value }}</textarea>
                    @else
                        <!-- Handling Other Types -->
                        <x-dynamic-component
                            :component="$setting->getNameComponentTypeInput()"
                            :name="$setting->setting_key"
                            :value="$setting->plain_value"
                            showImage="{{ $setting->setting_key }}"
                            :required="true">
                        </x-dynamic-component>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    @if (isset($bikeC_Intercity_Rush_Hour))
        @include('admin.settings.forms.c_intercity.setting_c_intercity')
    @elseif(isset($bikeC_Multi_Rush_Hour))
        @include('admin.settings.forms.c_multi.setting_c_multi')
    @endif
</div>
