@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-md-4 col-xl-3">
    <div class="card custom-shadow mb-3">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('Cài đặt & Trạng thái') }}</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Thứ tự sắp xếp') }}:</label>
                <x-input type="number" name="position" :value="$response->position" min="0" />
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Trạng thái') }}: <span class="text-danger">*</span></label>
                <x-select name="status" :required="true">
                    @foreach ($status as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="$response->status->value == $key" />
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>

    <x-admin.form-actions
        :submit-title="__('Cập nhật chủ đề')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::MEMO_THEME_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
