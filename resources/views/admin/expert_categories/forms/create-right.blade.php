@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    <!-- Icon chuyên mục -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-icons text-primary me-2 fs-4"></i>
                {{ __('Icon đại diện') }}
            </h5>
        </div>
        <div class="card-body p-4 text-center">
            <div class="settings-logo-upload-wrapper text-center my-2 p-3">
                <x-input-image-ckfinder name="icon" showImage="icon" />
            </div>
            <p class="text-muted fs-12 mb-0">{{ __('Ảnh icon PNG/SVG hiển thị trên thanh cuộn danh mục của App.') }}</p>
        </div>
    </div>

    <!-- Thứ tự sắp xếp -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-sort-ascending text-primary me-2 fs-4"></i>
                {{ __('Thứ tự hiển thị') }}
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
        :submit-title="__('Lưu chuyên mục')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::EXPERT_CATEGORY_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
