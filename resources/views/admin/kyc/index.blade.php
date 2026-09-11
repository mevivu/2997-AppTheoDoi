@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    <style>
        /* ===================================================
           KYC DATATABLE & ACTION BUTTONS STYLES
           =================================================== */
        .dt-action-kyc-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .btn-action-kyc {
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
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none !important;
            line-height: 1.25;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
            white-space: nowrap;
        }

        .btn-action-kyc i {
            font-size: 14px;
            line-height: 1;
            transition: transform 0.2s ease;
        }

        /* Nút Duyệt (Emerald Green) */
        .btn-action-kyc.btn-action-approve {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .btn-action-kyc.btn-action-approve:hover {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
            transform: translateY(-1px);
        }

        .btn-action-kyc.btn-action-approve:hover i {
            transform: scale(1.15);
        }

        /* Nút Từ chối (Rose Red) */
        .btn-action-kyc.btn-action-reject {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .btn-action-kyc.btn-action-reject:hover {
            background: #e11d48;
            color: #ffffff;
            border-color: #e11d48;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);
            transform: translateY(-1px);
        }

        .btn-action-kyc.btn-action-reject:hover i {
            transform: scale(1.15);
        }

        /* Nút Lịch sử (Neutral) */
        .btn-action-kyc.btn-action-history {
            background: #f8fafc;
            color: #64748b;
            border-color: #e2e8f0;
            padding: 5px 8px;
        }

        .btn-action-kyc.btn-action-history:hover {
            background: #64748b;
            color: #ffffff;
            border-color: #64748b;
            transform: translateY(-1px);
        }

        /* Thumbnails ảnh CCCD */
        .kyc-thumb-box {
            width: 78px;
            height: 52px;
            border-radius: 6px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-block;
        }

        .kyc-thumb-box:hover {
            transform: scale(1.06);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18);
        }

        .kyc-thumb-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .kyc-thumb-empty {
            width: 78px;
            height: 52px;
            background: #f8fafc;
        }

        /* KPI Cards */
        .kyc-kpi-card {
            border-radius: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none !important;
        }

        .kyc-kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08) !important;
        }

        .kyc-kpi-card.active-tab {
            border: 2px solid #3b82f6 !important;
        }

        /* Quick reason chips */
        .reject-reason-chip {
            display: inline-flex;
            align-items: center;
            padding: 5px 12px;
            font-size: 12px;
            font-weight: 500;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 18px;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }

        .reject-reason-chip:hover {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
        }

        .reject-reason-chip.active {
            background: #dc2626;
            color: #ffffff;
            border-color: #dc2626;
            box-shadow: 0 2px 6px rgba(220, 38, 38, 0.25);
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            {{-- KPI Summary Cards --}}
            <div class="row g-3 mb-3">
                {{-- Tổng hồ sơ --}}
                <div class="col-6 col-md-3">
                    <a href="{{ route('admin.kyc.index', ['kyc_status' => 'all']) }}" 
                       class="card border shadow-sm kyc-kpi-card {{ $currentStatus === 'all' ? 'active-tab' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-muted fs-12 text-uppercase fw-bold">{{ __('Tổng hồ sơ') }}</span>
                            <div class="fs-22 fw-extrabold text-dark mt-1">{{ number_format($totalKyc) }}</div>
                        </div>
                        <div class="rounded-circle bg-primary-lt p-2.5 d-flex align-items-center justify-content-center text-primary" style="width: 46px; height: 46px;">
                            <i class="ti ti-id-badge-2 fs-24"></i>
                        </div>
                    </a>
                </div>

                {{-- Chờ duyệt --}}
                <div class="col-6 col-md-3">
                    <a href="{{ route('admin.kyc.index', ['kyc_status' => 'pending']) }}" 
                       class="card border shadow-sm kyc-kpi-card {{ $currentStatus === 'pending' ? 'active-tab border-warning' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-warning fs-12 text-uppercase fw-bold d-flex align-items-center gap-1">
                                <span class="spinner-grow spinner-grow-sm" style="width: 6px; height: 6px;" role="status"></span>
                                {{ __('Chờ duyệt') }}
                            </span>
                            <div class="fs-22 fw-extrabold text-warning mt-1">{{ number_format($pendingCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-warning-lt p-2.5 d-flex align-items-center justify-content-center text-warning" style="width: 46px; height: 46px;">
                            <i class="ti ti-clock-hour-4 fs-24"></i>
                        </div>
                    </a>
                </div>

                {{-- Đã duyệt --}}
                <div class="col-6 col-md-3">
                    <a href="{{ route('admin.kyc.index', ['kyc_status' => 'approved']) }}" 
                       class="card border shadow-sm kyc-kpi-card {{ $currentStatus === 'approved' ? 'active-tab border-success' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-success fs-12 text-uppercase fw-bold">{{ __('Đã duyệt') }}</span>
                            <div class="fs-22 fw-extrabold text-success mt-1">{{ number_format($approvedCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-success-lt p-2.5 d-flex align-items-center justify-content-center text-success" style="width: 46px; height: 46px;">
                            <i class="ti ti-shield-check fs-24"></i>
                        </div>
                    </a>
                </div>

                {{-- Bị từ chối --}}
                <div class="col-6 col-md-3">
                    <a href="{{ route('admin.kyc.index', ['kyc_status' => 'rejected']) }}" 
                       class="card border shadow-sm kyc-kpi-card {{ $currentStatus === 'rejected' ? 'active-tab border-danger' : '' }} p-3 d-flex flex-row align-items-center justify-content-between">
                        <div>
                            <span class="text-danger fs-12 text-uppercase fw-bold">{{ __('Bị từ chối') }}</span>
                            <div class="fs-22 fw-extrabold text-danger mt-1">{{ number_format($rejectedCount) }}</div>
                        </div>
                        <div class="rounded-circle bg-danger-lt p-2.5 d-flex align-items-center justify-content-center text-danger" style="width: 46px; height: 46px;">
                            <i class="ti ti-shield-x fs-24"></i>
                        </div>
                    </a>
                </div>
            </div>

            {{-- Main Table Card --}}
            <div class="card custom-shadow border-0">
                <x-admin.page-header
                    icon="id-badge-2"
                    :title="__('Quản lý Duyệt CCCD & Mã số thuế (KYC)')"
                    :subtitle="__('Kiểm tra ảnh Căn cước công dân mặt trước/sau, đối chiếu tên trên CCCD với tài khoản ngân hàng và MST của đối tác trước khi chi trả hoa hồng.')"
                />

                {{-- Filter Tabs --}}
                <div class="card-header bg-white border-bottom px-3 py-2">
                    <ul class="nav nav-pills card-header-pills fs-13 gap-1">
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === 'all' ? 'active' : '' }}" 
                               href="{{ route('admin.kyc.index', ['kyc_status' => 'all']) }}">
                                {{ __('Tất cả') }} ({{ $totalKyc }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === 'pending' ? 'active bg-warning text-white' : '' }}" 
                               href="{{ route('admin.kyc.index', ['kyc_status' => 'pending']) }}">
                                <i class="ti ti-clock-hour-4 me-1"></i>{{ __('Chờ duyệt') }} 
                                <span class="badge {{ $currentStatus === 'pending' ? 'bg-white text-warning' : 'bg-warning text-white' }} ms-1">{{ $pendingCount }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === 'approved' ? 'active bg-success text-white' : '' }}" 
                               href="{{ route('admin.kyc.index', ['kyc_status' => 'approved']) }}">
                                <i class="ti ti-shield-check me-1"></i>{{ __('Đã duyệt') }} ({{ $approvedCount }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $currentStatus === 'rejected' ? 'active bg-danger text-white' : '' }}" 
                               href="{{ route('admin.kyc.index', ['kyc_status' => 'rejected']) }}">
                                <i class="ti ti-shield-x me-1"></i>{{ __('Từ chối') }} ({{ $rejectedCount }})
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-3">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable/>
                        {{ $dataTable->table(['class' => 'table table-bordered table-striped table-hover align-middle mb-0'], true) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Phóng To Xem Ảnh CCCD (Zoom & Rotate) --}}
    <div class="modal fade" id="modalPreviewKycImage" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header py-2.5 px-3.5 bg-dark text-white d-flex align-items-center justify-content-between">
                    <h6 class="modal-title fs-14 fw-bold d-flex align-items-center gap-1.5 mb-0" id="previewImageTitle">
                        <i class="ti ti-photo fs-16"></i>
                        <span>{{ __('Xem ảnh CCCD') }}</span>
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-light py-1 px-2 fs-12" onclick="rotatePreviewImage(-90)" title="{{ __('Xoay trái') }}">
                            <i class="ti ti-rotate-2"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-light py-1 px-2 fs-12" onclick="rotatePreviewImage(90)" title="{{ __('Xoay phải') }}">
                            <i class="ti ti-rotate-clockwise-2"></i>
                        </button>
                        <a id="previewOpenNewTab" href="#" target="_blank" class="btn btn-sm btn-outline-light py-1 px-2 fs-12" title="{{ __('Mở tab mới') }}">
                            <i class="ti ti-external-link"></i>
                        </a>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-3 text-center bg-black d-flex align-items-center justify-content-center" style="min-height: 420px; overflow: auto;">
                    <img id="previewImageTarget" src="" alt="CCCD Preview" 
                         style="max-width: 100%; max-height: 72vh; object-fit: contain; transition: transform 0.25s ease; border-radius: 6px;">
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Xác Nhận Duyệt CCCD --}}
    <div class="modal fade" id="modalApproveKyc" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header text-white py-3 px-3.5" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <h5 class="modal-title d-flex align-items-center mb-0 fw-bold fs-16">
                        <i class="ti ti-circle-check fs-2 me-2"></i>
                        {{ __('Xác nhận Phê Duyệt CCCD') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formApproveKyc">
                    <input type="hidden" id="approveUserId" name="id">
                    <div class="modal-body p-3.5">
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted fs-12">{{ __('Đối tác') }}</span>
                                <span class="fw-bold text-dark fs-13" id="approvePartnerName">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted fs-12">{{ __('Mã số thuế (MST)') }}</span>
                                <span class="fw-bold font-monospace text-primary fs-13" id="approveTaxCode">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted fs-12">{{ __('Tên chủ TK Ngân hàng') }}</span>
                                <span class="fw-bold text-success text-uppercase fs-13" id="approveBankHolder">-</span>
                            </div>
                        </div>

                        <div class="alert alert-info py-2.5 px-3 fs-12 border-info-subtle d-flex align-items-center gap-2 rounded-2 mb-0">
                            <i class="ti ti-info-circle fs-3 text-info flex-shrink-0"></i>
                            <div>
                                {{ __('Sau khi duyệt, đối tác sẽ nhận được thông báo đẩy Firebase và đủ điều kiện rút tiền hoa hồng về ngân hàng.') }}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2.5 px-3.5 bg-light border-top d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                        <button type="submit" class="btn btn-success fw-bold px-3 d-flex align-items-center" id="btnSubmitApprove">
                            <span class="spinner-border spinner-border-sm me-1.5 d-none" role="status"></span>
                            <i class="ti ti-check me-1"></i>{{ __('Xác nhận Duyệt') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Từ Chối CCCD --}}
    <div class="modal fade" id="modalRejectKyc" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header text-white py-3 px-3.5" style="background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%);">
                    <h5 class="modal-title d-flex align-items-center mb-0 fw-bold fs-16">
                        <i class="ti ti-alert-triangle fs-2 me-2"></i>
                        {{ __('Từ Chối Hồ Sơ CCCD') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formRejectKyc">
                    <input type="hidden" id="rejectUserId" name="id">
                    <div class="modal-body p-3.5">
                        <div class="d-flex justify-content-between align-items-center p-2.5 bg-light rounded-2 mb-3 border fs-12">
                            <span class="text-muted">{{ __('Đối tác') }}</span>
                            <span class="fw-bold text-dark fs-13" id="rejectPartnerName">-</span>
                        </div>

                        {{-- Gợi ý lý do chọn nhanh --}}
                        <div class="mb-2.5">
                            <label class="form-label fw-semibold fs-12 text-muted mb-1.5">{{ __('Gợi ý lý do thường gặp:') }}</label>
                            <div class="d-flex flex-wrap gap-1.5" id="rejectReasonChips">
                                <span class="reject-reason-chip" data-reason="Ảnh CCCD bị mờ hoặc lóa sáng, không đọc rõ thông tin.">
                                    {{ __('Ảnh mờ/lóa') }}
                                </span>
                                <span class="reject-reason-chip" data-reason="Tên trên CCCD không trùng khớp với tên chủ tài khoản ngân hàng.">
                                    {{ __('Sai tên tài khoản NH') }}
                                </span>
                                <span class="reject-reason-chip" data-reason="Mã số thuế cá nhân (MST) không đúng hoặc không hợp lệ.">
                                    {{ __('MST không hợp lệ') }}
                                </span>
                                <span class="reject-reason-chip" data-reason="Ảnh CCCD bị cắt mất góc hoặc không hiển thị đầy đủ giấy tờ.">
                                    {{ __('Ảnh bị mất góc') }}
                                </span>
                                <span class="reject-reason-chip" data-reason="Căn cước công dân đã hết hạn sử dụng.">
                                    {{ __('CCCD hết hạn') }}
                                </span>
                            </div>
                        </div>

                        {{-- Ô nhập lý do chi tiết --}}
                        <div class="mb-2">
                            <label for="rejectReason" class="form-label fw-semibold fs-13 text-dark">
                                {{ __('Lý do từ chối') }} <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control fs-13" id="rejectReason" name="reason" rows="3" 
                                      placeholder="{{ __('Nhập cụ thể lý do từ chối để đối tác hiểu và chụp lại...') }}" required></textarea>
                            <small class="text-muted fs-11 mt-1 d-block"><i class="ti ti-info-circle me-0.5"></i>{{ __('Lý do này sẽ được gửi trực tiếp qua thông báo đẩy Firebase đến điện thoại của đối tác.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer py-2.5 px-3.5 bg-light border-top d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                        <button type="submit" class="btn btn-danger fw-bold px-3 d-flex align-items-center" id="btnSubmitReject">
                            <span class="spinner-border spinner-border-sm me-1.5 d-none" role="status"></span>
                            <i class="ti ti-x me-1"></i>{{ __('Xác nhận Từ Chối') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- button in datatable -->
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id'),
    ])

    <script>
        let currentRotation = 0;

        // Mở modal xem ảnh CCCD phóng to
        function previewKycImage(imageUrl, title) {
            currentRotation = 0;
            const img = document.getElementById('previewImageTarget');
            img.style.transform = 'rotate(0deg)';
            img.src = imageUrl;

            document.getElementById('previewImageTitle').innerHTML = '<i class="ti ti-photo fs-16"></i> ' + title;
            document.getElementById('previewOpenNewTab').href = imageUrl;

            const modal = new bootstrap.Modal(document.getElementById('modalPreviewKycImage'));
            modal.show();
        }

        // Xoay ảnh trong modal
        function rotatePreviewImage(degree) {
            currentRotation = (currentRotation + degree) % 360;
            document.getElementById('previewImageTarget').style.transform = `rotate(${currentRotation}deg)`;
        }

        // Mở modal Duyệt CCCD
        function openApproveKycModal(userId, partnerName, taxCode, bankHolder) {
            document.getElementById('approveUserId').value = userId;
            document.getElementById('approvePartnerName').textContent = partnerName;
            document.getElementById('approveTaxCode').textContent = taxCode;
            document.getElementById('approveBankHolder').textContent = bankHolder;

            const modal = new bootstrap.Modal(document.getElementById('modalApproveKyc'));
            modal.show();
        }

        // Mở modal Từ chối CCCD
        function openRejectKycModal(userId, partnerName) {
            document.getElementById('rejectUserId').value = userId;
            document.getElementById('rejectPartnerName').textContent = partnerName;
            document.getElementById('rejectReason').value = '';

            // Reset chips
            document.querySelectorAll('.reject-reason-chip').forEach(c => c.classList.remove('active'));

            const modal = new bootstrap.Modal(document.getElementById('modalRejectKyc'));
            modal.show();
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Xử lý chọn nhanh chip lý do từ chối
            document.querySelectorAll('.reject-reason-chip').forEach(function (chip) {
                chip.addEventListener('click', function () {
                    const reason = this.getAttribute('data-reason');
                    const textarea = document.getElementById('rejectReason');

                    document.querySelectorAll('.reject-reason-chip').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    textarea.value = reason;
                    textarea.focus();
                });
            });

            // Submit Phê Duyệt CCCD via AJAX
            document.getElementById('formApproveKyc').addEventListener('submit', function (e) {
                e.preventDefault();
                const userId = document.getElementById('approveUserId').value;
                const btn = document.getElementById('btnSubmitApprove');
                const spinner = btn.querySelector('.spinner-border');

                btn.disabled = true;
                spinner.classList.remove('d-none');

                fetch(`/admin/giao-dich/duyet-cccd/approve/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');

                    if (data.status === 200 || data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalApproveKyc'))?.hide();
                        if (typeof msgSuccess === 'function') {
                            msgSuccess(data.message || 'Phê duyệt thành công!');
                        } else {
                            alert(data.message || 'Phê duyệt thành công!');
                        }
                        // Reload datatable
                        if (window.LaravelDataTables && window.LaravelDataTables['KycApprovalTable']) {
                            window.LaravelDataTables['KycApprovalTable'].ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    } else {
                        alert(data.message || 'Có lỗi xảy ra.');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                    alert('Lỗi kết nối máy chủ.');
                });
            });

            // Submit Từ Chối CCCD via AJAX
            document.getElementById('formRejectKyc').addEventListener('submit', function (e) {
                e.preventDefault();
                const userId = document.getElementById('rejectUserId').value;
                const reason = document.getElementById('rejectReason').value.trim();
                const btn = document.getElementById('btnSubmitReject');
                const spinner = btn.querySelector('.spinner-border');

                if (!reason) {
                    alert('Vui lòng nhập lý do từ chối.');
                    return;
                }

                btn.disabled = true;
                spinner.classList.remove('d-none');

                fetch(`/admin/giao-dich/duyet-cccd/reject/${userId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ reason: reason })
                })
                .then(res => res.json())
                .then(data => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');

                    if (data.status === 200 || data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('modalRejectKyc'))?.hide();
                        if (typeof msgSuccess === 'function') {
                            msgSuccess(data.message || 'Từ chối hồ sơ thành công!');
                        } else {
                            alert(data.message || 'Từ chối hồ sơ thành công!');
                        }
                        // Reload datatable
                        if (window.LaravelDataTables && window.LaravelDataTables['KycApprovalTable']) {
                            window.LaravelDataTables['KycApprovalTable'].ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    } else {
                        alert(data.message || 'Có lỗi xảy ra.');
                    }
                })
                .catch(err => {
                    btn.disabled = false;
                    spinner.classList.add('d-none');
                    alert('Lỗi kết nối máy chủ.');
                });
            });
        });
    </script>
@endpush
