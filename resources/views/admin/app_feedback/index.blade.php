@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    <style>
        .dt-action-feedback-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .btn-action-fb {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
            border-radius: 8px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none !important;
            line-height: 1.25;
            white-space: nowrap;
        }

        .btn-action-fb.btn-action-vip {
            background: #eef9f9;
            color: #1F7A80;
            border-color: #99d5d9;
        }

        .btn-action-fb.btn-action-vip:hover {
            background: #1F7A80;
            color: #ffffff;
            border-color: #1F7A80;
            box-shadow: 0 4px 12px rgba(31, 122, 128, 0.3);
        }

        .btn-action-fb.btn-action-approve {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .btn-action-fb.btn-action-approve:hover {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
        }

        .btn-action-fb.btn-action-reject {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .btn-action-fb.btn-action-reject:hover {
            background: #e11d48;
            color: #ffffff;
            border-color: #e11d48;
        }

        /* KPI Cards */
        .fb-kpi-card {
            border-radius: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none !important;
            user-select: none;
        }

        .fb-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
        }

        .fb-kpi-card.active-tab {
            border: 2px solid #1F7A80 !important;
            background-color: #f4fbfb;
        }

        /* Config Banner */
        .fb-config-banner {
            background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 100%);
            border: 1px solid #bce3e6;
            border-left: 4px solid #1F7A80;
            border-radius: 12px;
        }

        /* Unified Filter Bar */
        .fb-filter-bar {
            background-color: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            padding: 14px 16px;
        }

        .fb-filter-label {
            font-size: 11.5px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.02em;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .fb-filter-control {
            height: 38px;
            font-size: 13px;
            border-radius: 8px;
            border-color: #cbd5e1;
        }

        .fb-filter-control:focus {
            border-color: #1F7A80;
            box-shadow: 0 0 0 3px rgba(31, 122, 128, 0.12);
        }

        /* Gộp gọn thanh công cụ DataTable */
        #AppFeedbackTable_wrapper .dataTables_filter {
            display: none !important;
        }

        #AppFeedbackTable_wrapper .dt-buttons {
            margin-bottom: 10px;
            display: inline-flex;
            gap: 6px;
            flex-wrap: wrap;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            {{-- Khối Cấu hình Gói VIP thưởng & Link Cửa hàng (Từ Quản lý phiên bản) --}}
            <div class="card fb-config-banner shadow-2xs mb-3">
                <div class="card-body py-3 px-3.5">
                    <div class="row g-3 align-items-center">
                        {{-- Cột trái: Cấu hình Gói VIP thưởng --}}
                        <div class="col-12 col-xl-7">
                            <label for="rewardPackageSelect" class="form-label fw-bold text-dark fs-13 mb-1.5 d-flex align-items-center">
                                <i class="ti ti-gift text-teal fs-18 me-1.5"></i>
                                <span>{{ __('Cấu hình Gói VIP thưởng (Chọn từ danh sách gói đang hoạt động):') }}</span>
                            </label>
                            <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
                                <select id="rewardPackageSelect" class="form-select fb-filter-control flex-grow-1">
                                    @forelse ($activePackages as $pkg)
                                        <option value="{{ $pkg->id }}"
                                            {{ $selectedRewardPackage && $selectedRewardPackage->id === $pkg->id ? 'selected' : '' }}>
                                            {{ $pkg->name }} — {{ number_format((float) $pkg->price, 0, ',', '.') }}đ ({{ (int) $pkg->days }} ngày)
                                        </option>
                                    @empty
                                        <option value="">{{ __('Chưa có gói dịch vụ nào đang hoạt động') }}</option>
                                    @endforelse
                                </select>
                                <button type="button"
                                        id="btnSaveRewardPackage"
                                        class="btn text-white fw-bold px-3 text-nowrap d-inline-flex align-items-center justify-content-center"
                                        style="background-color: #1F7A80; height: 38px; border-radius: 8px;">
                                    <span class="spinner-border spinner-border-sm me-1.5 d-none" role="status"></span>
                                    <i class="ti ti-device-floppy me-1.5"></i>
                                    <span>{{ __('Lưu gói thưởng') }}</span>
                                </button>
                            </div>
                        </div>

                        {{-- Cột phải: Link cửa hàng từ Quản lý phiên bản --}}
                        <div class="col-12 col-xl-5 border-start-xl ps-xl-4">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1.5">
                                <span class="fw-bold text-dark fs-13 d-inline-flex align-items-center">
                                    <i class="ti ti-versions me-1.5 text-primary fs-16"></i>
                                    <span>{{ __('Link cửa hàng (Lấy từ Quản lý phiên bản):') }}</span>
                                </span>
                                <a href="{{ route(\App\Traits\RouteAdminSystem::APP_VERSION_INDEX) }}"
                                   class="btn btn-sm btn-outline-secondary py-1 px-2.5 fs-12 fw-semibold text-nowrap d-inline-flex align-items-center"
                                   style="border-radius: 7px;">
                                    <i class="ti ti-settings me-1"></i>
                                    <span>{{ __('Quản lý phiên bản') }}</span>
                                </a>
                            </div>
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                @if ($googlePlayUrl)
                                    <a href="{{ $googlePlayUrl }}" target="_blank"
                                       class="badge bg-green-lt text-green text-decoration-none px-2.5 py-1.5 fs-12 d-inline-flex align-items-center">
                                        <i class="ti ti-brand-google-play me-1.5 fs-14"></i>
                                        <span>Google Play</span>
                                        <i class="ti ti-external-link ms-1 fs-11 opacity-75"></i>
                                    </a>
                                @else
                                    <span class="badge bg-secondary-lt text-muted px-2.5 py-1.5 fs-12">Google Play: Chưa cấu hình</span>
                                @endif

                                @if ($appStoreUrl)
                                    <a href="{{ $appStoreUrl }}" target="_blank"
                                       class="badge bg-blue-lt text-blue text-decoration-none px-2.5 py-1.5 fs-12 d-inline-flex align-items-center">
                                        <i class="ti ti-brand-apple me-1.5 fs-14"></i>
                                        <span>Apple App Store</span>
                                        <i class="ti ti-external-link ms-1 fs-11 opacity-75"></i>
                                    </a>
                                @else
                                    <span class="badge bg-secondary-lt text-muted px-2.5 py-1.5 fs-12">Apple App Store: Chưa cấu hình</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 5 Thẻ KPI Trạng Thái (Bấm lọc tức thì qua AJAX) --}}
            <div class="row g-3 mb-3" id="kpiStatusCards">
                <div class="col-6 col-md">
                    <div data-status="all"
                         onclick="selectStatusTab('all')"
                         class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'all' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">{{ __('Tổng đánh giá') }}</span>
                            <div class="fs-22 fw-extrabold text-dark mt-1">{{ number_format($totalCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-primary-lt p-2 d-flex align-items-center justify-content-center text-primary" style="width: 42px; height: 42px;">
                            <i class="ti ti-message-star fs-22"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md">
                    <div data-status="pending"
                         onclick="selectStatusTab('pending')"
                         class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'pending' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-warning fs-12 text-uppercase fw-bold">{{ __('Chờ duyệt') }}</span>
                            <div class="fs-22 fw-extrabold text-warning mt-1">{{ number_format($pendingCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-warning-lt p-2 d-flex align-items-center justify-content-center text-warning" style="width: 42px; height: 42px;">
                            <i class="ti ti-clock-hour-4 fs-22"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md">
                    <div data-status="approved"
                         onclick="selectStatusTab('approved')"
                         class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'approved' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-info fs-12 text-uppercase fw-bold">{{ __('Đã duyệt') }}</span>
                            <div class="fs-22 fw-extrabold text-info mt-1">{{ number_format($approvedCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-info-lt p-2 d-flex align-items-center justify-content-center text-info" style="width: 42px; height: 42px;">
                            <i class="ti ti-circle-check fs-22"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md">
                    <div data-status="vip_granted"
                         onclick="selectStatusTab('vip_granted')"
                         class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'vip_granted' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-success fs-12 text-uppercase fw-bold">{{ __('Đã cấp VIP') }}</span>
                            <div class="fs-22 fw-extrabold text-success mt-1">{{ number_format($vipGrantedCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-success-lt p-2 d-flex align-items-center justify-content-center text-success" style="width: 42px; height: 42px;">
                            <i class="ti ti-crown fs-22"></i>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md">
                    <div data-status="rejected"
                         onclick="selectStatusTab('rejected')"
                         class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'rejected' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-danger fs-12 text-uppercase fw-bold">{{ __('Từ chối') }}</span>
                            <div class="fs-22 fw-extrabold text-danger mt-1">{{ number_format($rejectedCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-danger-lt p-2 d-flex align-items-center justify-content-center text-danger" style="width: 42px; height: 42px;">
                            <i class="ti ti-circle-x fs-22"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bảng danh sách Đánh giá Chăm Con 360 --}}
            <div class="card custom-shadow border-0 overflow-hidden">
                <x-admin.page-header
                    icon="message-star"
                    :title="__('Form Đánh Giá Chăm Con 360 – Chia sẻ trải nghiệm, Nhận ưu đãi VIP')"
                    :subtitle="__('Kiểm tra phản hồi người dùng theo thể lệ chương trình góp ý, duyệt và cấp gói VIP cho đúng User ID (hệ thống tự động lưu lịch sử chống cấp trùng).')"
                />

                {{-- Thanh Bộ Lọc Tập Trung (Unified Filter Bar) --}}
                <div class="fb-filter-bar">
                    <div class="row g-2.5 align-items-end">
                        {{-- 1. Tìm kiếm nhanh User ID / Email --}}
                        <div class="col-12 col-md-3">
                            <label for="filterKeyword" class="fb-filter-label">
                                <i class="ti ti-search text-teal"></i>
                                <span>{{ __('Tìm người dùng / Nội dung') }}</span>
                            </label>
                            <input type="text"
                                   id="filterKeyword"
                                   class="form-control fb-filter-control"
                                   placeholder="{{ __('Nhập User ID, Email, Họ tên...') }}">
                        </div>

                        {{-- 2. Lọc Trạng thái --}}
                        <div class="col-6 col-md-2">
                            <label for="filterStatus" class="fb-filter-label">
                                <i class="ti ti-filter text-teal"></i>
                                <span>{{ __('Trạng thái') }}</span>
                            </label>
                            <select id="filterStatus" class="form-select fb-filter-control">
                                <option value="all" {{ $currentStatus === 'all' ? 'selected' : '' }}>{{ __('Tất cả trạng thái') }}</option>
                                @foreach (\App\Enums\Feedback\AppFeedbackStatus::asSelectArray() as $val => $label)
                                    <option value="{{ $val }}" {{ $currentStatus === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 3. Lọc Câu 1: Mức độ hài lòng (1-5 sao) --}}
                        <div class="col-6 col-md-2">
                            <label for="filterRating" class="fb-filter-label">
                                <i class="ti ti-star text-warning"></i>
                                <span>{{ __('Câu 1: Số sao') }}</span>
                            </label>
                            <select id="filterRating" class="form-select fb-filter-control">
                                <option value="all">{{ __('Tất cả (1–5 sao)') }}</option>
                                <option value="5">5 sao (★★★★★)</option>
                                <option value="4">4 sao (★★★★)</option>
                                <option value="3">3 sao (★★★)</option>
                                <option value="2">2 sao (★★)</option>
                                <option value="1">1 sao (★)</option>
                            </select>
                        </div>

                        {{-- 4. Lọc Câu 2: Tính năng hài lòng nhất --}}
                        <div class="col-12 col-md-3">
                            <label for="filterFeature" class="fb-filter-label">
                                <i class="ti ti-sparkles text-primary"></i>
                                <span>{{ __('Câu 2: Tính năng hài lòng') }}</span>
                            </label>
                            <select id="filterFeature" class="form-select fb-filter-control">
                                <option value="all">{{ __('Tất cả tính năng') }}</option>
                                @foreach (\App\Enums\Feedback\AppFeedbackFeature::options() as $featureOption)
                                    <option value="{{ $featureOption }}">{{ $featureOption }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- 5. Nút Đặt lại bộ lọc --}}
                        <div class="col-12 col-md-2">
                            <button type="button"
                                    id="btnResetFilter"
                                    class="btn btn-outline-secondary w-100 fw-semibold d-inline-flex align-items-center justify-content-center"
                                    style="height: 38px; border-radius: 8px; font-size: 13px;">
                                <i class="ti ti-refresh me-1.5"></i>
                                <span>{{ __('Đặt lại lọc') }}</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable/>
                        {{ $dataTable->table(['class' => 'table table-bordered table-striped table-hover align-middle mb-0'], false) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Duyệt & Cấp Gói VIP --}}
    <div class="modal fade" id="modalGrantVip" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header text-white py-3 px-3.5" style="background-color: #1F7A80;">
                    <h5 class="modal-title d-flex align-items-center mb-0 fw-bold fs-16">
                        <i class="ti ti-crown fs-2 me-2"></i>
                        {{ __('Duyệt & Cấp Gói VIP Cho Người Dùng') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <input type="hidden" id="grantFeedbackId">
                <div class="modal-body p-3.5">
                    <div class="p-3 bg-light rounded-3 mb-3 border">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted fs-12">{{ __('User ID (Chăm Con 360)') }}</span>
                            <span class="badge bg-teal-lt text-teal fw-bold font-monospace fs-13" id="grantUserIdText">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <span class="text-muted fs-12">{{ __('Tài khoản / Email') }}</span>
                            <span class="fw-bold text-dark fs-13" id="grantUserEmailText">-</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted fs-12">{{ __('Đánh giá') }}</span>
                            <span class="fw-bold text-warning fs-13" id="grantRatingText">-</span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="grantPackageSelect" class="form-label fw-bold fs-13 text-dark">
                            {{ __('Chọn gói VIP cấp thưởng (Danh sách gói đang hoạt động)') }} <span class="text-danger">*</span>
                        </label>
                        <select id="grantPackageSelect" class="form-select fs-13">
                            @foreach ($activePackages as $pkg)
                                <option value="{{ $pkg->id }}"
                                    {{ $selectedRewardPackage && $selectedRewardPackage->id === $pkg->id ? 'selected' : '' }}>
                                    {{ $pkg->name }} — {{ number_format((float) $pkg->price, 0, ',', '.') }}đ ({{ (int) $pkg->days }} ngày)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="alert alert-info py-2.5 px-3 fs-12 border-info-subtle d-flex align-items-center gap-2 rounded-2 mb-0">
                        <i class="ti ti-shield-check fs-3 text-info flex-shrink-0"></i>
                        <div>
                            {{ __('Hệ thống sẽ kích hoạt gói VIP cho đúng User ID trên, lưu lịch sử để chống cấp trùng và gửi thông báo kết quả đến ứng dụng của người dùng.') }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2.5 px-3.5 bg-light border-top d-flex justify-content-end gap-2">
                    <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                    <button type="button" class="btn text-white fw-bold px-3 d-flex align-items-center" style="background-color: #1F7A80;" id="btnConfirmGrantVip">
                        <span class="spinner-border spinner-border-sm me-1.5 d-none" role="status"></span>
                        <i class="ti ti-crown me-1"></i>{{ __('Xác nhận Cấp VIP') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])

    <script>
        let _grantVipSubmitting = false;
        let _keywordTimer = null;

        function _getModalInstance(id) {
            var el = document.getElementById(id);
            if (!el || typeof bootstrap === 'undefined' || !bootstrap.Modal) return null;
            return bootstrap.Modal.getInstance(el) || new bootstrap.Modal(el);
        }

        function _notifySuccess(message) {
            if (typeof msgSuccess === 'function') {
                msgSuccess(message);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'success', title: 'Thành công', text: message, timer: 2000, showConfirmButton: false });
            } else {
                alert(message);
            }
        }

        function _notifyError(message) {
            if (typeof msgError === 'function') {
                msgError(message);
            } else if (typeof Swal !== 'undefined') {
                Swal.fire({ icon: 'error', title: 'Lỗi', text: message });
            } else {
                alert(message);
            }
        }

        function _reloadFeedbackTable() {
            if (window.LaravelDataTables && window.LaravelDataTables['AppFeedbackTable']) {
                window.LaravelDataTables['AppFeedbackTable'].ajax.reload(null, false);
            } else {
                location.reload();
            }
        }

        function _syncKpiActiveCard(statusVal) {
            document.querySelectorAll('#kpiStatusCards .fb-kpi-card').forEach(function (card) {
                if (card.getAttribute('data-status') === statusVal) {
                    card.classList.add('active-tab');
                } else {
                    card.classList.remove('active-tab');
                }
            });
        }

        // Bấm chọn thẻ KPI phía trên -> đồng bộ xuống dropdown Trạng thái và lọc ngay
        window.selectStatusTab = function (statusVal) {
            var statusSelect = document.getElementById('filterStatus');
            if (statusSelect) {
                statusSelect.value = statusVal;
            }
            _syncKpiActiveCard(statusVal);
            _reloadFeedbackTable();
        };

        // Mở modal Cấp VIP
        window.openGrantVipModal = function (target) {
            var $el = $(target);
            var feedbackId = $el.data('id');
            var userId = $el.data('userid');
            var fullname = $el.data('fullname');
            var email = $el.data('email');
            var rating = $el.data('rating');

            document.getElementById('grantFeedbackId').value = feedbackId;
            document.getElementById('grantUserIdText').textContent = '#' + userId;
            document.getElementById('grantUserEmailText').textContent = (fullname ? fullname + ' (' + email + ')' : email);
            document.getElementById('grantRatingText').textContent = rating + ' / 5 sao';

            var topPkgSelect = document.getElementById('rewardPackageSelect');
            var modalPkgSelect = document.getElementById('grantPackageSelect');
            if (topPkgSelect && modalPkgSelect && topPkgSelect.value) {
                modalPkgSelect.value = topPkgSelect.value;
            }

            _grantVipSubmitting = false;
            var modal = _getModalInstance('modalGrantVip');
            if (modal) modal.show();
        };

        // Cập nhật trạng thái Đã duyệt / Từ chối
        window.updateFeedbackStatus = function (feedbackId, status, userId) {
            var label = status === 'approved' ? 'Đã duyệt' : 'Từ chối';
            if (!confirm('Xác nhận chuyển trạng thái đánh giá của User ID #' + userId + ' sang "' + label + '"?')) {
                return;
            }

            fetch(`{{ url('/admin/danh-gia-ung-dung/status') }}/${feedbackId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="X-TOKEN"]')?.getAttribute('content') || '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: status })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 200 || data.success) {
                    _notifySuccess(data.message);
                    _reloadFeedbackTable();
                } else {
                    _notifyError(data.message || 'Có lỗi xảy ra.');
                }
            })
            .catch(() => _notifyError('Lỗi kết nối máy chủ.'));
        };

        document.addEventListener('DOMContentLoaded', function () {
            // Gắn tham số từ Thanh Bộ Lọc Tập Trung vào mọi request AJAX của DataTable
            $('#AppFeedbackTable').on('preXhr.dt', function (e, settings, data) {
                data.feedback_status = document.getElementById('filterStatus')?.value || 'all';
                data.filter_rating = document.getElementById('filterRating')?.value || 'all';
                data.filter_feature = document.getElementById('filterFeature')?.value || 'all';
                data.filter_keyword = document.getElementById('filterKeyword')?.value || '';
            });

            // Sự kiện thay đổi trên các ô lọc
            var filterStatus = document.getElementById('filterStatus');
            if (filterStatus) {
                filterStatus.addEventListener('change', function () {
                    _syncKpiActiveCard(this.value);
                    _reloadFeedbackTable();
                });
            }

            var filterRating = document.getElementById('filterRating');
            if (filterRating) {
                filterRating.addEventListener('change', function () {
                    _reloadFeedbackTable();
                });
            }

            var filterFeature = document.getElementById('filterFeature');
            if (filterFeature) {
                filterFeature.addEventListener('change', function () {
                    _reloadFeedbackTable();
                });
            }

            var filterKeyword = document.getElementById('filterKeyword');
            if (filterKeyword) {
                filterKeyword.addEventListener('input', function () {
                    clearTimeout(_keywordTimer);
                    _keywordTimer = setTimeout(function () {
                        _reloadFeedbackTable();
                    }, 350);
                });
            }

            // Nút Đặt lại bộ lọc
            var btnResetFilter = document.getElementById('btnResetFilter');
            if (btnResetFilter) {
                btnResetFilter.addEventListener('click', function () {
                    if (filterKeyword) filterKeyword.value = '';
                    if (filterStatus) filterStatus.value = 'all';
                    if (filterRating) filterRating.value = 'all';
                    if (filterFeature) filterFeature.value = 'all';
                    _syncKpiActiveCard('all');
                    _reloadFeedbackTable();
                });
            }

            // Lưu cấu hình gói VIP thưởng mặc định trên CMS
            var btnSavePkg = document.getElementById('btnSaveRewardPackage');
            if (btnSavePkg) {
                btnSavePkg.addEventListener('click', function () {
                    var pkgId = document.getElementById('rewardPackageSelect').value;
                    if (!pkgId) {
                        _notifyError('Vui lòng chọn một gói đang hoạt động.');
                        return;
                    }
                    var spinner = btnSavePkg.querySelector('.spinner-border');
                    btnSavePkg.disabled = true;
                    if (spinner) spinner.classList.remove('d-none');

                    fetch(`{{ route('admin.app_feedback.update_reward_package') }}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="X-TOKEN"]')?.getAttribute('content') || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ package_id: pkgId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        btnSavePkg.disabled = false;
                        if (spinner) spinner.classList.add('d-none');
                        if (data.status === 200 || data.success) {
                            _notifySuccess(data.message);
                        } else {
                            _notifyError(data.message || 'Không thể lưu cấu hình gói.');
                        }
                    })
                    .catch(() => {
                        btnSavePkg.disabled = false;
                        if (spinner) spinner.classList.add('d-none');
                        _notifyError('Lỗi kết nối máy chủ.');
                    });
                });
            }

            // Xác nhận Duyệt & Cấp VIP trong Modal
            var btnConfirmVip = document.getElementById('btnConfirmGrantVip');
            if (btnConfirmVip) {
                btnConfirmVip.addEventListener('click', function () {
                    if (_grantVipSubmitting) return;
                    var feedbackId = document.getElementById('grantFeedbackId').value;
                    var pkgId = document.getElementById('grantPackageSelect').value;
                    if (!pkgId) {
                        _notifyError('Vui lòng chọn gói VIP cần cấp.');
                        return;
                    }

                    _grantVipSubmitting = true;
                    var spinner = btnConfirmVip.querySelector('.spinner-border');
                    btnConfirmVip.disabled = true;
                    if (spinner) spinner.classList.remove('d-none');

                    fetch(`{{ url('/admin/danh-gia-ung-dung/grant-vip') }}/${feedbackId}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="X-TOKEN"]')?.getAttribute('content') || '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ package_id: pkgId })
                    })
                    .then(res => res.json())
                    .then(data => {
                        _grantVipSubmitting = false;
                        btnConfirmVip.disabled = false;
                        if (spinner) spinner.classList.add('d-none');

                        if (data.status === 200 || data.success) {
                            var modal = _getModalInstance('modalGrantVip');
                            if (modal) modal.hide();
                            _notifySuccess(data.message);
                            _reloadFeedbackTable();
                        } else {
                            _notifyError(data.message || 'Cấp gói VIP thất bại.');
                        }
                    })
                    .catch(() => {
                        _grantVipSubmitting = false;
                        btnConfirmVip.disabled = false;
                        if (spinner) spinner.classList.add('d-none');
                        _notifyError('Lỗi kết nối máy chủ.');
                    });
                });
            }
        });
    </script>
@endpush
