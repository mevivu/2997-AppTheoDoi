@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-md-4 col-xl-3">
    <div class="card custom-shadow mb-3">
        <div class="card-header">
            <h4 class="card-title mb-0">{{ __('Cài đặt & Trạng thái') }}</h4>
        </div>
        <div class="card-body p-4">
            <div class="mb-0">
                <label class="form-label fw-bold">{{ __('Trạng thái') }}: <span class="text-danger">*</span></label>
                <x-select name="status" :required="true">
                    @foreach ($status as $key => $value)
                        <x-select-option :value="$key" :title="$value" :selected="old('status') == $key" />
                    @endforeach
                </x-select>
            </div>
        </div>
    </div>

    <x-admin.form-actions
        :submit-title="__('Lưu thẻ bài')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::MEMO_CARD_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
