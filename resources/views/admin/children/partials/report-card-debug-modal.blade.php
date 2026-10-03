<!-- MODAL DEBUG DỮ LIỆU & THUẬT TOÁN HỌC BẠ ĐIỆN TỬ (TT22 / TT27) -->
<div class="modal modal-blur fade" id="modal-debug-report-card" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header py-3 px-4 bg-light-subtle border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-warning-lt rounded-3 fs-3">
                        <i class="ti ti-book-2 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-semibold text-slate fs-15">
                            {{ __('Chẩn Đoán Thuật Toán Học Bạ Điện Tử (TT22 / TT27)') }}
                        </h5>
                        <div class="text-muted fs-12 d-flex align-items-center gap-2 flex-wrap">
                            <span>{{ __('Bé: ') }} <strong>{{ $children->fullname }}</strong> (#{{ $children->id }})</span>
                            <span>•</span>
                            <span class="badge bg-primary-lt" id="modal-debug-rc-class-label">--</span>
                            <span class="badge bg-warning-lt" id="modal-debug-rc-semester-label">--</span>
                            <span class="badge bg-light text-muted" id="modal-debug-rc-regulation-label">--</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 px-2 py-1" id="btn-copy-rc-debug-json" title="{{ __('Sao chép toàn bộ JSON chẩn đoán học bạ') }}">
                        <i class="ti ti-copy me-1"></i> <span id="btn-copy-rc-text">{{ __('Sao chép JSON') }}</span>
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Navigation Tabs bên trong Modal -->
            <div class="bg-white border-bottom px-4 pt-2">
                <ul class="nav nav-tabs nav-fill border-0" id="rcDebugModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-2 fs-13 fw-semibold text-slate" id="tab-rc-subjects" data-bs-toggle="tab" data-bs-target="#pane-rc-subjects" type="button" role="tab">
                            <i class="ti ti-list-check me-1 text-primary"></i> {{ __('1. Chi Tiết Điểm & Mức Đạt Từng Môn') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 fs-13 fw-semibold text-slate" id="tab-rc-rules" data-bs-toggle="tab" data-bs-target="#pane-rc-rules" type="button" role="tab">
                            <i class="ti ti-scale me-1 text-warning"></i> {{ __('2. Đối Soát Quy Tắc & Hạ Bậc') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 fs-13 fw-semibold text-slate" id="tab-rc-json" data-bs-toggle="tab" data-bs-target="#pane-rc-json" type="button" role="tab">
                            <i class="ti ti-code me-1 text-azure"></i> {{ __('3. Dữ Liệu Gốc & JSON Snapshot') }}
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4 bg-light-subtle">
                <div class="tab-content" id="rcDebugModalTabsContent">
                    <!-- Loading state -->
                    <div id="debug-rc-loading" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-warning mb-2" role="status"></div>
                        <div class="fs-12">{{ __('Đang nạp và tính toán kiểm tra dữ liệu học bạ...') }}</div>
                    </div>

                    <!-- Error state -->
                    <div id="debug-rc-error" class="alert alert-danger d-none py-3 mb-0">
                        <i class="ti ti-alert-circle me-1"></i> <span id="debug-rc-error-msg"></span>
                    </div>

                    <!-- Main Content Wrapper -->
                    <div id="debug-rc-content" class="d-none">
                        <!-- TAB 1: Chi Tiết Điểm Môn Học -->
                        <div class="tab-pane fade show active" id="pane-rc-subjects" role="tabpanel">
                            <!-- Banner Tóm Tắt Kết Quả Tính -->
                            <div class="card p-3 border border-light-subtle rounded-3 bg-white mb-3 shadow-none">
                                <div class="row g-2 align-items-center">
                                    <div class="col-12 col-md-4">
                                        <div class="fs-11 text-muted">{{ __('XẾP LOẠI TÍNH ĐƯỢC (ENGINE)') }}</div>
                                        <div class="fs-15 fw-bold text-dark mt-1" id="rc-modal-calc-rating">--</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="fs-11 text-muted">{{ __('XẾP LOẠI HIỆN TẠI (DATABASE)') }}</div>
                                        <div class="fs-15 fw-bold text-dark mt-1" id="rc-modal-current-rating">--</div>
                                    </div>
                                    <div class="col-12 col-md-4">
                                        <div class="fs-11 text-muted">{{ __('TRẠNG THÁI TÍNH TOÁN') }}</div>
                                        <div class="mt-1" id="rc-modal-calc-status">--</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Bảng Điểm & Đánh Giá Môn Học -->
                            <div class="table-responsive bg-white rounded-3 border border-light-subtle">
                                <table class="table table-hover text-center align-middle mb-0 fs-13" id="rc-debug-subjects-table">
                                    <thead class="bg-light text-muted">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th class="text-start">{{ __('Tên Môn Học') }}</th>
                                            <th>{{ __('Hình Thức') }}</th>
                                            <th>{{ __('Điểm Số / Mức Đạt') }}</th>
                                            <th>{{ __('Điểm Cả Năm') }}</th>
                                            <th>{{ __('Nguồn Điểm Cả Năm') }}</th>
                                            <th>{{ __('Kết Quả') }}</th>
                                            <th>{{ __('Môn Bắt Buộc') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="rc-debug-subjects-tbody">
                                        <!-- Render qua JS -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- TAB 2: Đối Soát Quy Tắc & Hạ Bậc -->
                        <div class="tab-pane fade" id="pane-rc-rules" role="tabpanel">
                            <div class="row g-3">
                                <!-- Thống kê bộ đếm môn -->
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-primary-subtle rounded-3 bg-white h-100 shadow-none">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-primary text-white fw-semibold">{{ __('BỘ ĐẾM THỐNG KÊ (RULE MATCH)') }}</span>
                                        </div>
                                        <div id="rc-debug-counters-content" class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12">
                                            <!-- Render qua JS -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Quy tắc hạ bậc -->
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border border-warning-subtle rounded-3 bg-white h-100 shadow-none">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-warning text-white fw-semibold">{{ __('ĐIỀU CHỈNH & HẠ BẬC KHỐNG CHẾ') }}</span>
                                        </div>
                                        <div id="rc-debug-adjustment-content" class="p-2 rounded-2 bg-light-subtle border border-light-subtle fs-12">
                                            <!-- Render qua JS -->
                                        </div>
                                    </div>
                                </div>

                                <!-- Cảnh báo & Môn học còn thiếu -->
                                <div class="col-12">
                                    <div class="card p-3 border border-danger-subtle rounded-3 bg-white shadow-none">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="badge bg-danger text-white fw-semibold">{{ __('CẢNH BÁO & MÔN HỌC THIẾU ĐIỂM') }}</span>
                                        </div>
                                        <div id="rc-debug-warnings-content" class="fs-12">
                                            <!-- Render qua JS -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TAB 3: Dữ Liệu Gốc & JSON Snapshot -->
                        <div class="tab-pane fade" id="pane-rc-json" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-semibold text-slate fs-13 mb-0">
                                            <i class="ti ti-code me-1 text-muted"></i> {{ __('Calculation Snapshot Payload (JSON đầy đủ)') }}
                                        </h6>
                                    </div>
                                    <pre class="bg-dark text-light p-3 rounded-3 fs-11" id="rc-debug-json-viewer" style="max-height: 450px; overflow: auto;"></pre>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2 px-4 bg-white border-top justify-content-between">
                <span class="text-muted fs-12">
                    <i class="ti ti-info-circle me-1"></i> {{ __('Hệ thống áp dụng tự động các điều kiện của Thông tư GD&ĐT.') }}
                </span>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-light btn-sm px-3" data-bs-dismiss="modal">
                        {{ __('Đóng') }}
                    </button>
                    <button type="button" class="btn btn-primary btn-sm px-3" id="btn-rc-recalc-from-modal">
                        <i class="ti ti-refresh me-1"></i> {{ __('Tính Lại & Lưu Kết Quả') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
