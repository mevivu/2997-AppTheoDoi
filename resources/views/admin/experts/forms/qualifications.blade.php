<div class="card custom-shadow mt-4 border-0">
    <div class="card-header d-flex align-items-center justify-content-between py-3 bg-white border-bottom">
        <div class="d-flex align-items-center gap-3">
            <span class="avatar avatar-md rounded-circle bg-primary-lt text-primary shadow-xs">
                <i class="ti ti-school fs-1"></i>
            </span>
            <div>
                <h3 class="card-title fw-bold text-dark mb-0 fs-16" style="color: #1e293b !important;">
                    {{ __('Học vị & Bằng cấp Chuyên môn') }}
                </h3>
                <div class="text-muted fs-12 mt-1">
                    {{ __('Quản lý danh sách các văn bằng, học vị, chứng chỉ của chuyên gia / bác sĩ') }}
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-primary d-flex align-items-center gap-2 shadow-xs px-3 py-2 rounded-2" id="btn-add-qualification">
            <i class="ti ti-plus fs-2"></i>
            <span class="fw-semibold fs-13">{{ __('Thêm học vị mới') }}</span>
        </button>
    </div>

    <div class="card-body p-3 p-md-4 bg-light-subtle">
        <div class="alert alert-info-lt rounded-3 p-3 mb-3 d-flex align-items-center gap-3 border-0 shadow-xs" style="background: #eff6ff; border-left: 4px solid #206bc4 !important;">
            <i class="ti ti-info-circle fs-1 text-primary flex-shrink-0"></i>
            <div class="text-dark fs-13 lh-base">
                <strong>{{ __('Thông tin:') }}</strong> {{ __('Một chuyên gia có thể có nhiều bằng cấp/học vị (Bác sĩ CKII, Thạc sĩ, Tiến sĩ...). Hệ thống sẽ hiển thị đầy đủ trên ứng dụng theo thứ tự ưu tiên.') }}
            </div>
        </div>

        <div id="qualifications-container" class="d-flex flex-column gap-3">
            <!-- Render động danh sách học vị qua JavaScript -->
        </div>

        <div id="no-qualifications-alert" class="text-center py-5 border border-2 border-dashed rounded-3 bg-white" style="display: none;">
            <div class="avatar avatar-lg rounded-circle bg-light text-muted shadow-xs mx-auto mb-3" style="width: 56px; height: 56px;">
                <i class="ti ti-certificate-off fs-1 text-secondary"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1 fs-15">{{ __('Chưa có học vị nào được thêm') }}</h4>
            <p class="text-muted fs-13 mb-3">{{ __('Bấm nút bên dưới để bổ sung văn bằng, học vị chuyên môn cho chuyên gia.') }}</p>
            <button type="button" class="btn btn-outline-primary btn-sm px-3 rounded-2" onclick="document.getElementById('btn-add-qualification').click()">
                <i class="ti ti-plus me-1"></i>{{ __('Thêm học vị ngay') }}
            </button>
        </div>
    </div>
</div>

<style>
    .qualification-item {
        transition: all 0.2s ease-in-out;
        border: 1px solid #e2e8f0 !important;
        border-left: 4px solid #206bc4 !important;
        background: #ffffff !important;
        border-radius: 10px;
    }
    .qualification-item:hover {
        box-shadow: 0 4px 12px rgba(32, 107, 196, 0.08) !important;
        border-color: #cbd5e1 !important;
        border-left-color: #1a569d !important;
    }
    .btn-remove-qualification {
        transition: all 0.15s ease-in-out;
    }
    .btn-remove-qualification:hover {
        background-color: #fee2e2 !important;
        color: #dc2626 !important;
        border-color: #fca5a5 !important;
    }
</style>
