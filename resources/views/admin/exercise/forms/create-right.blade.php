@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    {{-- Quyền truy cập --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-lock-access text-primary me-2 fs-4"></i>
                {{ __('Quyền truy cập') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name="access_type">
                @foreach ($accessTypes as $key => $value)
                    <x-select-option :value="$key" :title="$value" />
                @endforeach
            </x-select>
            <small class="text-muted d-block mt-2">{{ __('Tài khoản VIP được mở toàn bộ; Free xem các bài được phân quyền.') }}</small>
        </div>
    </div>

    {{-- Trạng thái --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>

    {{-- Thứ tự sắp xếp --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-sort-ascending text-primary me-2 fs-4"></i>
                {{ __('Thứ tự sắp xếp') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <x-input type="number" name="sort_order" :value="old('sort_order', 0)" min="0" />
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu bài tập')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::EXERCISE_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
