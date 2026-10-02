@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    <!-- Ảnh chân dung -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-photo-heart text-primary me-2 fs-4"></i>
                {{ __('Ảnh chân dung') }}
            </h5>
        </div>
        <div class="card-body p-4 text-center">
            <div class="settings-logo-upload-wrapper text-center my-2 p-3">
                <x-input-image-ckfinder name="avatar" showImage="avatar" />
            </div>
            <p class="text-muted fs-12 mb-0">{{ __('Ảnh đại diện tỷ lệ 1:1, rõ khuôn mặt chuyên gia.') }}</p>
        </div>
    </div>

    <!-- Xác thực uy tín -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-discount-check text-primary me-2 fs-4"></i>
                {{ __('Huy hiệu xác minh') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <x-input-switch name="is_verified" value="1" :checked="true"
                :label="__('Hiển thị tích xanh xác thực uy tín trên App')" />
        </div>
    </div>

    <!-- Thứ tự hiển thị -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-sort-ascending text-primary me-2 fs-4"></i>
                {{ __('Thứ tự sắp xếp') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <x-input type="number" name="sort_order" :value="old('sort_order', 0)" placeholder="0" />
        </div>
    </div>

    <!-- Trạng thái -->
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

    {{-- Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu hồ sơ')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::EXPERT_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
