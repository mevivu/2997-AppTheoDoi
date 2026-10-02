<div class="card custom-shadow mt-4 border-0">
    <div class="card-header d-flex align-items-center justify-content-between py-3 bg-white border-0">
        <div class="d-flex align-items-center gap-3">
            <span class="avatar avatar-md rounded-circle shadow-none" style="background: #eff6ff; color: #2563eb;">
                <i class="ti ti-school fs-1" style="color: #2563eb;"></i>
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
        <button type="button" class="btn btn-add-qual-custom d-flex align-items-center gap-2 px-3 py-2 rounded-2" id="btn-add-qualification">
            <i class="ti ti-plus fs-2 text-white"></i>
            <span class="fw-semibold fs-13 text-white">{{ __('Thêm học vị mới') }}</span>
        </button>
    </div>

    <div class="card-body p-3 p-md-4 bg-white">
        <div class="alert rounded-3 p-3 mb-3 d-flex align-items-center gap-3 border-0 shadow-none" style="background: #f0f7ff; border: none !important;">
            <i class="ti ti-info-circle fs-1 flex-shrink-0" style="color: #2563eb;"></i>
            <div class="text-dark fs-13 lh-base">
                <strong>{{ __('Thông tin:') }}</strong> {{ __('Một chuyên gia có thể có nhiều bằng cấp/học vị (Bác sĩ CKII, Thạc sĩ, Tiến sĩ...). Hệ thống sẽ hiển thị đầy đủ trên ứng dụng theo thứ tự ưu tiên.') }}
            </div>
        </div>

        <div id="qualifications-container" class="d-flex flex-column gap-3">
            <!-- Render động danh sách học vị qua JavaScript -->
        </div>

        <div id="no-qualifications-alert" class="text-center py-5 rounded-3 border-0" style="display: none; background: #f8fafc;">
            <div class="avatar avatar-lg rounded-circle text-muted mx-auto mb-3" style="width: 56px; height: 56px; background: #e2e8f0;">
                <i class="ti ti-certificate-off fs-1 text-secondary"></i>
            </div>
            <h4 class="fw-bold text-dark mb-1 fs-15">{{ __('Chưa có học vị nào được thêm') }}</h4>
            <p class="text-muted fs-13 mb-3">{{ __('Bấm nút bên dưới để bổ sung văn bằng, học vị chuyên môn cho chuyên gia.') }}</p>
            <button type="button" class="btn btn-add-qual-custom btn-sm px-3 rounded-2 text-white" onclick="document.getElementById('btn-add-qualification').click()">
                <i class="ti ti-plus me-1 text-white"></i>{{ __('Thêm học vị ngay') }}
            </button>
        </div>
    </div>
</div>

<style>
    .btn-add-qual-custom {
        background: #2563eb !important;
        color: #ffffff !important;
        border: none !important;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25) !important;
        transition: all 0.2s ease-in-out !important;
    }
    .btn-add-qual-custom:hover {
        background: #1d4ed8 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35) !important;
        transform: translateY(-1px);
    }
    .btn-add-qual-custom:focus, .btn-add-qual-custom:active {
        background: #1e40af !important;
        color: #ffffff !important;
        box-shadow: none !important;
    }
    .qualification-item {
        transition: all 0.2s ease-in-out;
        border: none !important;
        border-left: none !important;
        background: #f8fafc !important;
        border-radius: 12px;
        box-shadow: none !important;
    }
    .qualification-item:hover {
        background: #f1f5f9 !important;
    }
    .btn-remove-qualification {
        background: #fee2e2 !important;
        color: #dc2626 !important;
        border: none !important;
        transition: all 0.15s ease-in-out;
    }
    .btn-remove-qualification:hover {
        background: #fecaca !important;
        color: #b91c1c !important;
    }
    .qualification-item .form-control {
        background: #ffffff !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px;
    }
    .qualification-item .form-control:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15) !important;
    }
</style>
