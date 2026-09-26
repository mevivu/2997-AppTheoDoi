@php use App\Traits\RouteAdminSystem; @endphp
<style>
    .form-label-lg-custom {
        font-size: 18.5px !important;
        font-weight: 700 !important;
        color: #0f172a !important;
        margin-bottom: 8px !important;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        letter-spacing: -0.25px;
    }
    .form-label-lg-custom .required-star {
        color: #ef4444;
        font-size: 19px;
        font-weight: 800;
        margin-left: 2px;
    }
    .select-custom-lg {
        height: 52px !important;
        font-size: 16px !important;
        font-weight: 600 !important;
        border: 2px solid #cbd5e1 !important;
        border-radius: 14px !important;
        padding: 10px 18px !important;
        color: #0f172a !important;
        background-color: #ffffff !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
    }
    .select-custom-lg:focus {
        border-color: #0284c7 !important;
        box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.12) !important;
    }
</style>

<div class="col-12 col-lg-4 settings-column">
    {{-- Card: Phân quyền truy cập --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
            <span class="avatar avatar-md bg-warning-lt rounded-circle" style="width: 44px; height: 44px;">
                <i class="ti ti-lock-access fs-2 text-warning"></i>
            </span>
            <h4 class="mb-0 fw-bold text-dark fs-19">{{ __('Quyền xem & Gói dịch vụ') }}</h4>
            <span class="text-danger ms-auto fs-18 fw-bold">*</span>
        </div>
        <div class="card-body p-4">
            <label class="form-label-lg-custom mb-3">
                <i class="ti ti-users text-warning fs-20"></i>
                <span>{{ __('Đối tượng được xem video') }}:</span>
            </label>

            {{-- Visual Choice Cards for Access Type --}}
            <div class="d-flex flex-column gap-2 mb-3">
                <label class="form-selectgroup-item flex-fill access-choice">
                    <input type="radio" name="access_type" value="free" class="form-selectgroup-input" {{ old('access_type', 'free') == 'free' ? 'checked' : '' }} onchange="togglePreviewSwitch()">
                    <div class="form-selectgroup-label d-flex align-items-center p-3 border rounded-3 text-start transition-all cursor-pointer">
                        <div class="me-3">
                            <span class="avatar bg-success-lt rounded-circle" style="width: 46px; height: 46px;">
                                <i class="ti ti-gift fs-2 text-success"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="fw-bold text-dark d-flex align-items-center gap-2 fs-17 mb-1">
                                <span>{{ __('Miễn phí (Free)') }}</span>
                                <span class="badge bg-success-lt fs-12 px-2 py-1 fw-bold">{{ __('Phổ thông') }}</span>
                            </div>
                            <small class="text-muted d-block fs-14">{{ __('Mọi tài khoản phụ huynh đều được xem toàn bộ video này.') }}</small>
                        </div>
                    </div>
                </label>

                <label class="form-selectgroup-item flex-fill access-choice">
                    <input type="radio" name="access_type" value="vip" class="form-selectgroup-input" {{ old('access_type') == 'vip' ? 'checked' : '' }} onchange="togglePreviewSwitch()">
                    <div class="form-selectgroup-label d-flex align-items-center p-3 border rounded-3 text-start transition-all cursor-pointer">
                        <div class="me-3">
                            <span class="avatar bg-warning-lt rounded-circle" style="width: 46px; height: 46px;">
                                <i class="ti ti-crown fs-2 text-warning"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="fw-bold text-dark d-flex align-items-center gap-2 fs-17 mb-1">
                                <span>{{ __('Gói VIP') }}</span>
                                <span class="badge bg-warning text-dark fs-12 px-2 py-1 fw-bold">VIP Only</span>
                            </div>
                            <small class="text-muted d-block fs-14">{{ __('Chỉ tài khoản đăng ký gói hội viên VIP mới được xem.') }}</small>
                        </div>
                    </div>
                </label>
            </div>

            {{-- Cho phép Free xem thử --}}
            <div id="preview_switch_wrapper" class="p-3 bg-light rounded-3 border mt-3" aria-live="polite">
                <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                    <input class="form-check-input" type="checkbox" name="is_preview" id="is_preview" value="1" style="width: 44px; height: 22px; cursor: pointer;" {{ old('is_preview') ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark fs-17 cursor-pointer" for="is_preview">
                        <i class="ti ti-eye text-primary me-1 fs-18"></i> {{ __('Cho phép Free xem thử') }}
                    </label>
                </div>
                <div class="mt-2 text-muted fs-13 lh-base">
                    {{ __('Khi bật tùy chọn này, tài khoản Free vẫn được xem video này và hiển thị nhãn "Xem thử", giúp khuyến khích phụ huynh nâng cấp VIP.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Trạng thái xuất bản --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
            <span class="avatar avatar-md bg-success-lt rounded-circle" style="width: 44px; height: 44px;">
                <i class="ti ti-toggle-right fs-2 text-success"></i>
            </span>
            <h4 class="mb-0 fw-bold text-dark fs-19">{{ __('Trạng thái hiển thị') }}</h4>
            <span class="text-danger ms-auto fs-18 fw-bold">*</span>
        </div>
        <div class="card-body p-4">
            <label class="form-label-lg-custom mb-2" for="status_select">
                <i class="ti ti-checkup-list text-success fs-20"></i>
                <span>{{ __('Trạng thái xuất bản') }}:</span>
            </label>
            <select name="status" id="status_select" class="form-select select-custom-lg fs-16 fw-semibold" required>
                @foreach ($status as $key => $value)
                    <option value="{{ $key }}" {{ old('status', 'active') == $key ? 'selected' : '' }}>
                        {{ $key === 'active' ? '🟢 ' : ($key === 'draft' ? '🟡 ' : '🔴 ') }}{{ $value }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted mt-2 d-block fs-14">
                {{ __('Video ở trạng thái "Bản nháp" sẽ không hiển thị trên ứng dụng của người dùng.') }}
            </small>
        </div>
    </div>

    {{-- Card: Ảnh thu nhỏ (Thumbnail) --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
            <span class="avatar avatar-md bg-blue-lt rounded-circle" style="width: 44px; height: 44px;">
                <i class="ti ti-photo fs-2 text-blue"></i>
            </span>
            <h4 class="mb-0 fw-bold text-dark fs-19">{{ __('Ảnh thu nhỏ (Thumbnail)') }}</h4>
        </div>
        <div class="card-body p-4">
            {{-- Preview Thumbnail YouTube tự động (Ẩn khi chọn R2) --}}
            <div id="yt_thumbnail_wrapper">
                <div class="mb-3">
                    <label class="form-label-lg-custom mb-2">
                        <i class="ti ti-brand-youtube text-danger fs-18"></i>
                        <span>{{ __('Ảnh tự động từ YouTube:') }}</span>
                    </label>
                    <div id="yt_auto_thumb_empty" class="thumbnail-empty border rounded-3 text-center p-3">
                        <i class="ti ti-photo-off fs-1 mb-2 text-blue"></i>
                        <strong class="text-dark">{{ __('Chưa có ảnh xem trước') }}</strong>
                        <small>{{ __('Dán link YouTube để tải thumbnail tự động') }}</small>
                    </div>
                    <div id="yt_auto_thumb_preview" class="border rounded-3 overflow-hidden position-relative bg-dark text-center" style="max-height: 180px; display: none;">
                        <img id="yt_auto_thumb_img"
                             src="data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 9'%3E%3C/svg%3E"
                             alt="YouTube Auto Thumbnail"
                             class="w-100"
                             style="object-fit: cover; max-height: 180px;"
                             onerror="this.onerror=null;this.src='{{ asset('assets/images/default.png') }}';" />
                        <div class="position-absolute bottom-0 start-0 w-100 p-2 text-white bg-dark bg-opacity-75 fs-12 text-truncate">
                            <i class="ti ti-sparkles text-warning"></i> {{ __('Tự động lấy ảnh chất lượng cao từ YouTube') }}
                        </div>
                    </div>
                </div>

                <div class="hr-text text-muted my-3 fw-bold">{{ __('HOẶC TẢI ẢNH TÙY CHỈNH') }}</div>
            </div>

            <div>
                <label class="form-label-lg-custom mb-2">
                    <i class="ti ti-upload text-primary fs-18"></i>
                    <span>{{ __('Tải ảnh riêng từ thiết bị:') }}</span>
                </label>
                <x-input-image name="thumbnail" />
                <small class="text-muted d-block mt-2 fs-14">
                    {{ __('Nếu tải ảnh riêng, hệ thống sẽ ưu tiên sử dụng ảnh này thay cho thumbnail của YouTube.') }}
                </small>
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu video mới')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::VIDEO_INDEX)"
        :back-title="__('Quay lại danh sách')"
    />
</div>

<script>
    function togglePreviewSwitch() {
        const isVip = document.querySelector('input[name="access_type"]:checked')?.value === 'vip';
        const wrapper = document.getElementById('preview_switch_wrapper');
        const switchInput = document.getElementById('is_preview');
        if (wrapper) {
            if (isVip) {
                wrapper.classList.remove('opacity-50');
                wrapper.removeAttribute('aria-disabled');
                if (switchInput) switchInput.disabled = false;
            } else {
                wrapper.classList.add('opacity-50');
                wrapper.setAttribute('aria-disabled', 'true');
                if (switchInput) {
                    switchInput.checked = false;
                    switchInput.disabled = true;
                }
            }
        }
    }
    document.addEventListener('DOMContentLoaded', togglePreviewSwitch);
</script>
