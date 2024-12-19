<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-upload me-1"></span>
            {{ __('Đăng') }}
        </div>
        <div class="card-body p-2">
            <div class="w-100 d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save"
                                 class="flex-column gap-1 text-wrap p-2 flex-grow-1"/>
                <x-button type="submit" name="submitter" value="saveAndExit" class="p-2 text-wrap w-50">
                    @lang('save&exit')
                </x-button>
            </div>
        </div>
    </div>


    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-status-change me-1"></span>
            {{ __('Kiểu') }}
        </div>
        <div class="card-body p-2">
            <x-select name="type" :required="true">
                @foreach ($type as $key => $value)
                    <x-select-option :value="$key" :title="$value"/>
                @endforeach
            </x-select>
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <span class="ti ti-photo me-1"></span>
            @lang('avatar')
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="image" :value="old('image')" showImage="featureImage"/>
        </div>
    </div>
</div>
