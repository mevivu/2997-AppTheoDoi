@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4">
    <!-- Stage Card -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="ti ti-category text-primary me-2 fs-4"></i>
                {{ __('Giai đoạn phát triển') }} <span class="text-danger ms-1">*</span>
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select name="stage" :required="true">
                @foreach ($stages as $stage)
                    <x-select-option :value="$stage->value" :title="$stage->label()" :isSelected="$response->stage === $stage || $response->stage?->value === $stage->value"/>
                @endforeach
            </x-select>
        </div>
    </div>

    <!-- Active Status Card -->
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái hiển thị') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ $response->is_active ? 'checked' : '' }}>
                <label class="form-check-label fw-bold" for="is_active">
                    Bật hiển thị cho người dùng
                </label>
            </div>
            <small class="text-muted d-block mt-2">
                Khi tắt, cột mốc này sẽ không hiển thị trong danh mục gợi ý tạo mới của app (nhưng các nhật ký cũ đã liên kết vẫn được giữ nguyên).
            </small>
        </div>
    </div>

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Cập nhật cột mốc')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::JOURNAL_MILESTONE_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
