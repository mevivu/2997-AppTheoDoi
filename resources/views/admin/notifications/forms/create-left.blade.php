<div class="col-12 col-md-9">
    <div class="card custom-shadow">
        <div class="row card-body">
            <!-- types -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-user-pin"></i>
                    <label for="">{{ __('Đối tượng') }} <span class="text-danger">*</span></label>
                    <x-select class="notification-type" name="types" :required="true">
                        @foreach ($types as $key => $value)
                            <x-select-option :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>
            <div id="notification-option-select" class="col-12">
                <div class="mb-3">
                    <i class="ti ti-moped"></i>
                    <label for="">{{ __('Loại') }} <span class="text-danger">*</span></label>
                    <x-select class="notification-option-select-value" name="option" required>
                        <x-select-option value="" :title="__('Chọn loại thông báo')" selected disabled />
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
                    <label for="">{{ __('Khách hàng') }}</label>
                    <x-select name="user_id[]" class="select2-bs5-ajax" :data-url="route('admin.search.select.user')" id="user_id"
                        multiple="multiple">
                    </x-select>
                </div>
            </div>

            <!-- excel file -->
            <div style="display: none" id="notification-excel-file-wrapper" class="col-12">
                <div class="mb-3">
                    <i class="ti ti-file-spreadsheet"></i>
                    <label for="excel_file">{{ __('Tải lên file Excel') }} <span class="text-danger">*</span></label>
                    <input type="file" class="form-control" id="excel_file" name="excel_file" accept=".xlsx, .xls, .csv">
                    <div class="form-text">
                        {{ __('Tải file Excel mẫu tại đây:') }}
                        <a href="{{ route('admin.notification.downloadTemplate') }}" class="text-primary fw-bold">
                            <i class="ti ti-download"></i> {{ __('Tải template mẫu') }}
                        </a>
                    </div>
                </div>
            </div>

            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-bell-ringing"></i>
                    <label class="control-label">@lang('title')
                        @lang('message'): <span class="text-danger">*</span></label>
                    <x-input name="title" :value="old('title')" required :placeholder="__('title')" />
                </div>
            </div>

            <!-- message -->
            <div class="col-12">
                <div class="mb-3">
                    <i class="ti ti-chart-bubble"></i>
                    <label class="control-label">
                        @lang('message'): <span class="text-danger">*</span>
                    </label>
                    <textarea name="message" class="form-control" rows="4" placeholder="{{ __('message') }}" required>{{ old('message') }}</textarea>
                </div>
            </div>

        </div>
    </div>
</div>
