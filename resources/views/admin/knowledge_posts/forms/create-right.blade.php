@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    <!-- Nổi bật -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-star text-primary me-2 fs-4"></i>
                {{ __('Nổi bật') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <input type="hidden" name="is_featured" value="{{ App\Enums\FeaturedStatus::Featureless->value }}">
            <x-input-switch name="is_featured" value="{{ App\Enums\FeaturedStatus::Featured->value }}"
                :label="__('Đánh dấu là kiến thức nổi bật')" />
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

    <!-- Ảnh đại diện -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-photo-heart text-primary me-2 fs-4"></i>
                {{ __('Ảnh đại diện') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4 text-center">
            <div class="settings-logo-upload-wrapper text-center my-2 p-3">
                <x-input-image-ckfinder name="image" showImage="image" />
            </div>
            <p class="text-muted fs-12 mb-0">{{ __('Hỗ trợ định dạng JPG, PNG, WEBP. Nhấp vào ảnh để tải ảnh lên qua CKFinder.') }}</p>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu kiến thức')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::KNOWLEDGE_POST_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
