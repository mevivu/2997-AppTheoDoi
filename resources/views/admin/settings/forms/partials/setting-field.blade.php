@php
    $fieldSetting = $setting ?? null;
@endphp

@if($fieldSetting)
    <div class="mb-3">
        <label for="{{ $fieldSetting->setting_key }}" class="form-label fw-bold">
            {{ $label ?? $fieldSetting->setting_name }}
            @if($fieldSetting->type_input != App\Enums\Setting\SettingTypeInput::Checkbox())
                <span class="text-danger">*</span>
            @endif
        </label>
        
        @if ($fieldSetting->type_input == App\Enums\Setting\SettingTypeInput::Checkbox())
            <div class="form-check form-switch">
                <input type="hidden" name="{{ $fieldSetting->setting_key }}" value="0">
                <input class="form-check-input"
                       type="checkbox"
                       id="{{ $fieldSetting->setting_key }}"
                       name="{{ $fieldSetting->setting_key }}" value="1"
                    {{ $fieldSetting->plain_value == '1' ? 'checked' : '' }}>
                <label class="form-check-label" for="{{ $fieldSetting->setting_key }}"></label>
            </div>
        @elseif ($fieldSetting->type_input == App\Enums\Setting\SettingTypeInput::Textarea())
            <textarea name="{{ $fieldSetting->setting_key }}" class="ckeditor visually-hidden" placeholder="{{ $fieldSetting->setting_name }}">{{ $fieldSetting->plain_value }}</textarea>
        @else
            <x-dynamic-component
                :component="$fieldSetting->getNameComponentTypeInput()"
                :name="$fieldSetting->setting_key"
                :value="$fieldSetting->plain_value"
                showImage="{{ $fieldSetting->setting_key }}"
                :required="true">
            </x-dynamic-component>
        @endif

        @if(!empty($hint) || !empty($fieldSetting->desc))
            <small class="form-hint text-muted mt-1 d-block">{{ $hint ?? $fieldSetting->desc }}</small>
        @endif
    </div>
@endif
