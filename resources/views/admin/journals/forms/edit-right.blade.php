@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-category text-primary me-2 fs-4"></i>
                {{ __('Kiểu nhật ký') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name="type" :required="true">
                @foreach ($type as $key => $value)
                    <x-select-option :value="$key" :title="$value" :option="$response->type->value"/>
                @endforeach
            </x-select>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu thay đổi')"
        submit-icon="ti ti-device-floppy"
        :back-route="$response->exercise_type == \App\Enums\Journal\JournalType::Moment ? route('admin.journal.moment') : route('admin.journal.prescription')"
        :back-title="__('Quay lại')"
    />
</div>
