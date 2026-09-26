@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4">
    {{-- Card: Phân quyền truy cập --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
            <span class="avatar avatar-sm bg-warning-lt rounded-circle">
                <i class="ti ti-lock-access fs-3 text-warning"></i>
            </span>
            <h5 class="mb-0 fw-bold text-dark">{{ __('Quyền xem & Gói dịch vụ') }}</h5>
            <span class="text-danger ms-auto">*</span>
        </div>
        <div class="card-body p-4">
            <label class="form-label fw-bold mb-2">{{ __('Đối tượng được xem video') }}:</label>

            {{-- Visual Choice Cards for Access Type --}}
            <div class="d-flex flex-column gap-2 mb-3">
                <label class="form-selectgroup-item flex-fill">
                    <input type="radio" name="access_type" value="free" class="form-selectgroup-input" {{ old('access_type', $instance->access_type->value) == 'free' ? 'checked' : '' }} onchange="togglePreviewSwitch()">
                    <div class="form-selectgroup-label d-flex align-items-center p-3 border rounded-3 text-start transition-all">
                        <div class="me-3">
                            <span class="avatar bg-success-lt rounded-circle">
                                <i class="ti ti-gift fs-2 text-success"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                {{ __('Miễn phí (Free)') }}
                                <span class="badge bg-success-lt fs-10 px-2">{{ __('Phổ thông') }}</span>
                            </div>
                            <small class="text-muted d-block mt-1">{{ __('Mọi tài khoản phụ huynh đều được xem toàn bộ video này.') }}</small>
                        </div>
                    </div>
                </label>

                <label class="form-selectgroup-item flex-fill">
                    <input type="radio" name="access_type" value="vip" class="form-selectgroup-input" {{ old('access_type', $instance->access_type->value) == 'vip' ? 'checked' : '' }} onchange="togglePreviewSwitch()">
                    <div class="form-selectgroup-label d-flex align-items-center p-3 border rounded-3 text-start transition-all">
                        <div class="me-3">
                            <span class="avatar bg-warning-lt rounded-circle">
                                <i class="ti ti-crown fs-2 text-warning"></i>
                            </span>
                        </div>
                        <div class="flex-fill">
                            <div class="fw-bold text-dark d-flex align-items-center gap-1">
                                {{ __('Gói VIP') }}
                                <span class="badge bg-warning text-dark fs-10 px-2 fw-bold">VIP Only</span>
                            </div>
                            <small class="text-muted d-block mt-1">{{ __('Chỉ tài khoản đăng ký gói hội viên VIP mới được xem.') }}</small>
                        </div>
                    </div>
                </label>
            </div>

            {{-- Cho phép Free xem thử --}}
            <div id="preview_switch_wrapper" class="p-3 bg-light rounded-3 border mt-3">
                <div class="form-check form-switch m-0">
                    <input class="form-check-input" type="checkbox" name="is_preview" id="is_preview" value="1" {{ old('is_preview', $instance->is_preview) ? 'checked' : '' }}>
                    <label class="form-check-label fw-bold text-dark" for="is_preview">
                        <i class="ti ti-eye text-primary me-1"></i> {{ __('Cho phép Free xem thử') }}
                    </label>
                </div>
                <div class="mt-2 text-muted fs-12 lh-base">
                    {{ __('Khi bật tùy chọn này, tài khoản Free vẫn được xem video này và hiển thị nhãn "Xem thử", giúp khuyến khích phụ huynh nâng cấp VIP.') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Card: Trạng thái xuất bản --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
            <span class="avatar avatar-sm bg-success-lt rounded-circle">
                <i class="ti ti-toggle-right fs-3 text-success"></i>
            </span>
            <h5 class="mb-0 fw-bold text-dark">{{ __('Trạng thái hiển thị') }}</h5>
            <span class="text-danger ms-auto">*</span>
        </div>
        <div class="card-body p-4">
            <select name="status" class="form-select form-select-lg fs-14" required>
                @foreach ($status as $key => $value)
                    <option value="{{ $key }}" {{ old('status', $instance->status->value) == $key ? 'selected' : '' }}>
                        {{ $key === 'active' ? '🟢 ' : ($key === 'draft' ? '🟡 ' : '🔴 ') }}{{ $value }}
                    </option>
                @endforeach
            </select>
            <small class="text-muted mt-2 d-block">
                {{ __('Video ở trạng thái "Bản nháp" sẽ không hiển thị trên ứng dụng của người dùng.') }}
            </small>
        </div>
    </div>

    {{-- Card: Ảnh thu nhỏ (Thumbnail) --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
            <span class="avatar avatar-sm bg-purple-lt rounded-circle">
                <i class="ti ti-photo fs-3 text-purple"></i>
            </span>
            <h5 class="mb-0 fw-bold text-dark">{{ __('Ảnh thu nhỏ (Thumbnail)') }}</h5>
        </div>
        <div class="card-body p-4">
            {{-- Preview Thumbnail YouTube hiện tại --}}
            <div class="mb-3">
                <label class="form-label fw-bold">{{ __('Ảnh tự động từ YouTube:') }}</label>
                <div class="border rounded-3 overflow-hidden position-relative bg-dark text-center" style="max-height: 180px;">
                    <img id="yt_auto_thumb_img"
                         src="{{ $instance->youtube_id ? 'https://img.youtube.com/vi/' . $instance->youtube_id . '/hqdefault.jpg' : asset('assets/images/default.png') }}"
                         alt="YouTube Auto Thumbnail"
                         class="w-100"
                         style="object-fit: cover; max-height: 180px;"
                         onerror="this.onerror=null;this.src='{{ asset('assets/images/default.png') }}';" />
                    <div class="position-absolute bottom-0 start-0 w-100 p-2 text-white bg-dark bg-opacity-75 fs-11 text-truncate">
                        <i class="ti ti-sparkles text-warning"></i> {{ __('Tự động lấy ảnh chất lượng cao từ YouTube') }}
                    </div>
                </div>
            </div>

            <div class="hr-text text-muted my-3">{{ __('HOẶC TẢI ẢNH TÙY CHỈNH') }}</div>

            <div>
                <label class="form-label fw-bold">{{ __('Tải ảnh riêng từ thiết bị:') }}</label>
                <x-input-image name="thumbnail" :value="$instance->thumbnail" />
                <small class="text-muted d-block mt-2">
                    {{ __('Nếu tải ảnh riêng, hệ thống sẽ ưu tiên sử dụng ảnh này thay cho thumbnail của YouTube.') }}
                </small>
            </div>
        </div>
    </div>

    {{-- Card: Thống kê hiệu suất --}}
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom py-3 px-4 d-flex align-items-center gap-2">
            <span class="avatar avatar-sm bg-blue-lt rounded-circle">
                <i class="ti ti-chart-bar fs-3 text-blue"></i>
            </span>
            <h5 class="mb-0 fw-bold text-dark">{{ __('Thống kê lượt xem') }}</h5>
        </div>
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center p-2 rounded bg-light mb-2">
                <span class="text-muted d-flex align-items-center gap-1">
                    <i class="ti ti-eye text-primary"></i> {{ __('Tổng lượt xem:') }}
                </span>
                <span class="badge bg-blue text-white fs-13 px-3 py-1 fw-bold">
                    {{ number_format($instance->view_count) }}
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-center text-muted fs-12 px-1 mb-1">
                <span>{{ __('Ngày tạo:') }}</span>
                <span class="text-dark fw-semibold">{{ $instance->created_at ? date('d/m/Y H:i', strtotime($instance->created_at)) : '—' }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center text-muted fs-12 px-1">
                <span>{{ __('Cập nhật lần cuối:') }}</span>
                <span class="text-dark fw-semibold">{{ $instance->updated_at ? date('d/m/Y H:i', strtotime($instance->updated_at)) : '—' }}</span>
            </div>
        </div>
    </div>

    {{-- Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Cập nhật thay đổi')"
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
                if (switchInput) switchInput.disabled = false;
            } else {
                wrapper.classList.add('opacity-50');
                if (switchInput) {
                    switchInput.checked = false;
                    switchInput.disabled = true;
                }
            }
        }
    }
    document.addEventListener('DOMContentLoaded', togglePreviewSwitch);
</script>
