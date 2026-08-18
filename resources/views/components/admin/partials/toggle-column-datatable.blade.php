<div class="toggle-columns-table mb-3">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <!-- Segment Control Switcher (iOS Style) -->
        <div class="datatable-view-switcher p-1 bg-light rounded-pill border d-inline-flex align-items-center">
            <button type="button" class="btn btn-sm rounded-pill btn-datatable-mode active" data-mode="table" style="padding: 5px 14px; font-weight: 600; font-size: 13px;">
                <i class="ti ti-table me-1 fs-6"></i>
                <span>Bảng</span>
            </button>
            <button type="button" class="btn btn-sm rounded-pill btn-datatable-mode" data-mode="grid" style="padding: 5px 14px; font-weight: 600; font-size: 13px;">
                <i class="ti ti-layout-grid me-1 fs-6"></i>
                <span>Thẻ</span>
            </button>
        </div>

        <div class="d-flex align-items-center gap-2">
            <!-- Nút Chọn cột -->
            <div class="dropdown">
                <button type="button" class="btn btn-success rounded-pill px-3 fw-bold d-inline-flex align-items-center gap-1 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" style="font-size: 13px; height: 36px;">
                    <i class="ti ti-columns fs-6"></i>
                    <span>@lang('column')</span>
                </button>
                <div class="drop-toggle-columns dropdown-menu dropdown-menu-end shadow-sm border-0 mt-1"
                     style="overflow-y: auto; max-height: 250px; min-width: 200px; padding: 10px;" role="menu"></div>
            </div>
        </div>
    </div>
</div>
