@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    {{-- Avatar Card --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-photo-heart text-primary me-2 fs-4"></i>
                {{ __('Ảnh đại diện') }}
            </h5>
        </div>
        <div class="card-body p-4 text-center">
            <div class="settings-logo-upload-wrapper text-center my-2 p-3">
                <x-input-image-ckfinder name="avatar" :value="old('avatar')" showImage="featureImage"/>
            </div>
            <p class="text-muted fs-12 mb-0">{{ __('Hỗ trợ định dạng JPG, PNG, WEBP. Nhấp vào ảnh để tải hoặc thay đổi qua CKFinder.') }}</p>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu khách hàng')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::USER_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
