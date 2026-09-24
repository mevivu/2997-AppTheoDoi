<!-- MODAL DEBUG DỮ LIỆU & CÔNG THỨC ĐÁNH GIÁ THỂ CHẤT (PQ RADAR) -->
<div class="modal modal-blur fade" id="modal-debug-pq" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg rounded-3">
            <div class="modal-header py-3 px-4 bg-light-subtle border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="p-2 bg-success-lt rounded-3 fs-3">
                        <i class="ti ti-polygon text-success"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-semibold text-slate fs-15">
                            {{ __('Tra Cứu Chi Tiết Công Thức & Dữ Liệu Thể Chất (PQ Radar)') }}
                        </h5>
                        <span class="text-muted fs-12">
                            {{ __('Bé: ') }} <strong>{{ $children->fullname }}</strong> (#{{ $children->id }})
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-2 px-2 py-1" id="btn-copy-pq-debug-json" title="{{ __('Sao chép toàn bộ JSON chẩn đoán thể chất') }}">
                        <i class="ti ti-copy me-1"></i> <span id="btn-copy-pq-text">{{ __('Sao chép JSON') }}</span>
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Navigation Tabs bên trong Modal -->
            <div class="bg-white border-bottom px-4 pt-2">
                <ul class="nav nav-tabs nav-fill border-0" id="pqDebugModalTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active py-2 fs-13 fw-semibold text-slate" id="tab-pq-calc-steps" data-bs-toggle="tab" data-bs-target="#pane-pq-calc-steps" type="button" role="tab">
                            <i class="ti ti-calculator me-1 text-success"></i> {{ __('1. Chi Tiết 5 Thuộc Tính & Thang Điểm') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 fs-13 fw-semibold text-slate" id="tab-pq-history" data-bs-toggle="tab" data-bs-target="#pane-pq-history" type="button" role="tab">
                            <i class="ti ti-history me-1 text-primary"></i> {{ __('2. Lịch Sử Các Lần Đo PQ') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link py-2 fs-13 fw-semibold text-slate" id="tab-pq-json" data-bs-toggle="tab" data-bs-target="#pane-pq-json" type="button" role="tab">
                            <i class="ti ti-code me-1 text-azure"></i> {{ __('3. Dữ Liệu Gốc & JSON') }}
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4 bg-light-subtle">
                <div class="tab-content" id="pqDebugModalTabsContent">
                    <!-- TAB 1: Chi Tiết 5 Thuộc Tính & Thang Điểm -->
                    <div class="tab-pane fade show active" id="pane-pq-calc-steps" role="tabpanel">
                        <div id="debug-pq-steps-loading" class="text-center py-5 text-muted">
                            <div class="spinner-border spinner-border-sm text-success mb-2" role="status"></div>
                            <div class="fs-12">{{ __('Đang tổng hợp dữ liệu chẩn đoán thể chất...') }}</div>
                        </div>
                        <div id="debug-pq-steps-content" class="d-none">
                            <!-- Đổ nội dung 5 thuộc tính + tổng hợp điểm qua JS -->
                        </div>
                    </div>

                    <!-- TAB 2: Lịch Sử Các Lần Đo PQ -->
                    <div class="tab-pane fade" id="pane-pq-history" role="tabpanel">
                        <div class="table-responsive bg-white rounded-3 border border-light-subtle p-2">
                            <table class="table table-slim text-center align-middle mb-0" id="debug-table-pq-history-all">
                                <thead class="bg-light text-muted">
                                    <tr>
                                        <th>#ID</th>
                                        <th>{{ __('Ngày đo') }}</th>
                                        <th>{{ __('Tháng tuổi') }}</th>
                                        <th>{{ __('Chiều cao (cm)') }}</th>
                                        <th>{{ __('Cân nặng (kg)') }}</th>
                                        <th>{{ __('BMI') }}</th>
                                        <th>{{ __('Phân loại BMI') }}</th>
                                        <th>{{ __('Sức mạnh') }}</th>
                                        <th>{{ __('Sức bền') }}</th>
                                        <th>{{ __('Điểm PQ') }}</th>
                                    </tr>
                                </thead>
                                <tbody id="debug-tbody-pq-history-all">
                                    <!-- Đổ dữ liệu lịch sử đo PQ qua JS -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 3: Dữ Liệu Gốc & JSON -->
                    <div class="tab-pane fade" id="pane-pq-json" role="tabpanel">
                        <div class="row g-3">
                            <div class="col-12">
                                <h6 class="fw-semibold text-slate fs-13 mb-2">
                                    <i class="ti ti-code me-1 text-muted"></i> {{ __('Dữ Liệu JSON Chẩn Đoán Chi Tiết (PQ Diagnostic Payload)') }}
                                </h6>
                                <pre class="bg-dark text-light p-3 rounded-3 fs-11" id="debug-pq-json-viewer" style="max-height: 400px; overflow: auto;"></pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer py-2 px-4 bg-white border-top justify-content-between">
                <span class="fs-12 text-muted">
                    <i class="ti ti-shield-check text-success me-1"></i> {{ __('Điểm số thể chất được ánh xạ theo thang điểm 10 chuẩn WHO & bảng Cột R Excel Chăm Con 360.') }}
                </span>
                <button type="button" class="btn btn-secondary btn-sm rounded-2 px-3" data-bs-dismiss="modal">
                    {{ __('Đóng') }}
                </button>
            </div>
        </div>
    </div>
</div>
