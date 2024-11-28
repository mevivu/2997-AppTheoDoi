<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">
            <!-- types -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-user-pin"></i>
                    <label for="">{{ __('Đối tượng') }}</label>
                    <x-select class="notification-type" name="types" :required="true">
                        @foreach ($types as $key => $value)
                            <x-select-option :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>
            <div style="display: none" id="notification-option-select" class="col-12">
                <div class="mb-3">
                    <i class="ti ti-moped"></i>
                    <label for="">{{ __('Loại') }}</label>
                    <x-select class="notification-option-select-value" name="option">
                        <x-select-option value="100" :title="__('Chọn loại thông báo')" selected />
                        @foreach ($options as $key => $value)
                            <x-select-option :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>

            <!-- customer -->
            <div style="display: none" id="notification-customer-select" class="col-12">
                <div class="mb-3">
                    <i class="ti ti-user-plus"></i>
                    <label for="">{{ __('Nhân viên') }}</label>
                    <x-select name="user_id[]" class="select2-bs5-ajax" :data-url="route('admin.search.select.user')" id="user_id"
                        multiple="multiple">
                    </x-select>
                </div>
            </div>

            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-bell-ringing"></i>
                    <label class="control-label">@lang('title')</label>
                    <x-input name="title" :value="old('title')" :placeholder="__('title')" />
                </div>
            </div>

            <!-- message -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-chart-bubble"></i>
                    <label class="control-label">@lang('message')</label>
                    <x-input name="message" :value="old('message')" :placeholder="__('message')" />
                </div>
            </div>

        </div>
    </div>
</div>
