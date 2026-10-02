<div class="card custom-shadow mt-4">
    <div class="card-header d-flex align-items-center justify-content-between bg-light-lt">
        <h3 class="card-title mb-0 d-flex align-items-center gap-2">
            <i class="ti ti-school text-primary fs-2"></i>
            <span>{{ __('Học vị & Bằng cấp Chuyên môn') }}</span>
        </h3>
        <button type="button" class="btn btn-sm btn-primary" id="btn-add-qualification">
            <i class="ti ti-plus me-1"></i>{{ __('Thêm học vị') }}
        </button>
    </div>
    <div class="card-body p-3">
        <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2 fs-13">
            <i class="ti ti-info-circle fs-2 flex-shrink-0"></i>
            <div>
                {{ __('Một chuyên gia có thể có nhiều bằng cấp/học vị. Vui lòng bấm "+ Thêm học vị" để bổ sung các văn bằng tương ứng.') }}
            </div>
        </div>

        <div id="qualifications-container" class="d-flex flex-column gap-3">
            <!-- Sẽ được render động qua JavaScript -->
        </div>

        <div id="no-qualifications-alert" class="text-center py-4 border border-dashed rounded-3 text-muted" style="display: none;">
            <i class="ti ti-certificate-off fs-1 text-secondary mb-2 d-block"></i>
            <span>{{ __('Chưa có học vị nào được thêm. Bấm "+ Thêm học vị" ở góc trên để tạo mới.') }}</span>
        </div>
    </div>
</div>
