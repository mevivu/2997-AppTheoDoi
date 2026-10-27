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

        .fb-kpi-card {
            border-radius: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none !important;
        }

        .fb-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
        }

        .fb-kpi-card.active-tab {
            border: 2px solid #1F7A80 !important;
        }

        .fb-config-banner {
            background: linear-gradient(135deg, #f0fdfa 0%, #ffffff 100%);
            border: 1px solid #bce3e6;
            border-left: 4px solid #1F7A80;
            border-radius: 12px;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            {{-- Cấu hình Gói VIP thưởng (chọn từ DS gói đang hoạt động) & Link Cửa hàng (từ Quản lý phiên bản) --}}
            <div class="card fb-config-banner shadow-2xs mb-3">
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-lg-7">
                            <label class="form-label fw-bold text-dark fs-13 mb-1 d-flex align-items-center gap-1.5">
                                <i class="ti ti-gift text-teal fs-18"></i>
                                <span>{{ __('Cấu hình Gói VIP thưởng (Chọn từ danh sách gói đang hoạt động):') }}</span>
                            </label>
                            <div class="d-flex flex-wrap flex-sm-nowrap gap-2 align-items-center">
                                <select id="rewardPackageSelect" class="form-select fs-13">
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
                                        class="btn text-white fw-bold px-3 text-nowrap"
                                        style="background-color: #1F7A80;">
                                    <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                                    <i class="ti ti-device-floppy me-1"></i>{{ __('Lưu gói thưởng') }}
                                </button>
                            </div>
                        </div>

                        <div class="col-12 col-lg-5 border-start-lg ps-lg-3">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <span class="fw-bold text-dark fs-12">
                                    <i class="ti ti-versions me-1 text-primary"></i>{{ __('Link đánh giá cửa hàng (Lấy từ Quản lý phiên bản):') }}
                                </span>
                                <a href="{{ route(\App\Traits\RouteAdminSystem::APP_VERSION_INDEX) }}" class="fs-12 text-decoration-none fw-semibold" style="color: #1F7A80;">
                                    <i class="ti ti-settings me-0.5"></i>{{ __('Quản lý phiên bản') }}
                                </a>
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @if ($googlePlayUrl)
                                    <a href="{{ $googlePlayUrl }}" target="_blank" class="badge bg-green-lt text-green text-decoration-none px-2.5 py-1.5 fs-11">
                                        <i class="ti ti-brand-google-play me-1"></i>Google Play
                                    </a>
                                @else
                                    <span class="badge bg-secondary-lt text-muted px-2 py-1 fs-11">Google Play: Chưa cấu hình</span>
                                @endif

                                @if ($appStoreUrl)
                                    <a href="{{ $appStoreUrl }}" target="_blank" class="badge bg-blue-lt text-blue text-decoration-none px-2.5 py-1.5 fs-11">
                                        <i class="ti ti-brand-apple me-1"></i>Apple App Store
                                    </a>
                                @else
                                    <span class="badge bg-secondary-lt text-muted px-2 py-1 fs-11">Apple App Store: Chưa cấu hình</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thẻ thống kê theo 4 trạng thái --}}
            <div class="row g-3 mb-3">
                <div class="col-6 col-md">
                    <a href="{{ route('admin.app_feedback.index', ['feedback_status' => 'all']) }}"
                       class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'all' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">{{ __('Tổng đánh giá') }}</span>
                            <div class="fs-22 fw-extrabold text-dark mt-1">{{ number_format($totalCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-primary-lt p-2 d-flex align-items-center justify-content-center text-primary" style="width: 42px; height: 42px;">
                            <i class="ti ti-message-star fs-22"></i>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md">
                    <a href="{{ route('admin.app_feedback.index', ['feedback_status' => 'pending']) }}"
                       class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'pending' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-warning fs-12 text-uppercase fw-bold">{{ __('Chờ duyệt') }}</span>
                            <div class="fs-22 fw-extrabold text-warning mt-1">{{ number_format($pendingCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-warning-lt p-2 d-flex align-items-center justify-content-center text-warning" style="width: 42px; height: 42px;">
                            <i class="ti ti-clock-hour-4 fs-22"></i>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md">
                    <a href="{{ route('admin.app_feedback.index', ['feedback_status' => 'approved']) }}"
                       class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'approved' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-info fs-12 text-uppercase fw-bold">{{ __('Đã duyệt') }}</span>
                            <div class="fs-22 fw-extrabold text-info mt-1">{{ number_format($approvedCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-info-lt p-2 d-flex align-items-center justify-content-center text-info" style="width: 42px; height: 42px;">
                            <i class="ti ti-circle-check fs-22"></i>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md">
                    <a href="{{ route('admin.app_feedback.index', ['feedback_status' => 'vip_granted']) }}"
                       class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'vip_granted' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-success fs-12 text-uppercase fw-bold">{{ __('Đã cấp VIP') }}</span>
                            <div class="fs-22 fw-extrabold text-success mt-1">{{ number_format($vipGrantedCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-success-lt p-2 d-flex align-items-center justify-content-center text-success" style="width: 42px; height: 42px;">
                            <i class="ti ti-crown fs-22"></i>
                        </div>
                    </a>
                </div>

                <div class="col-6 col-md">
                    <a href="{{ route('admin.app_feedback.index', ['feedback_status' => 'rejected']) }}"
                       class="card border shadow-2xs fb-kpi-card {{ $currentStatus === 'rejected' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-danger fs-12 text-uppercase fw-bold">{{ __('Từ chối') }}</span>
                            <div class="fs-22 fw-extrabold text-danger mt-1">{{ number_format($rejectedCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-danger-lt p-2 d-flex align-items-center justify-content-center text-danger" style="width: 42px; height: 42px;">
                            <i class="ti ti-circle-x fs-22"></i>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Bảng danh sách Đánh giá Chăm Con 360 --}}
            <div class="card custom-shadow border-0">
                <x-admin.page-header
                    icon="message-star"
                    :title="__('Form Đánh Giá Chăm Con 360 – Chia sẻ trải nghiệm, Nhận ưu đãi VIP')"
                    :subtitle="__('Kiểm tra phản hồi người dùng theo thể lệ chương trình góp ý, duyệt và cấp gói VIP cho đúng User ID (hệ thống tự động lưu lịch sử chống cấp trùng).')"
                />

                <div class="card-body p-3">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable/>
                        {{ $dataTable->table(['class' => 'table table-bordered table-striped table-hover align-middle mb-0'], true) }}
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

            // Đồng bộ gói mặc định đang chọn ở thanh cấu hình phía trên
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
