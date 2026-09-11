@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    <style>
        /* ===================================================
           MODERN WITHDRAW DATATABLE ACTION BUTTONS (SOFT-UI)
           =================================================== */
        .dt-action-withdraw-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            flex-wrap: nowrap;
            white-space: nowrap;
        }

        .btn-action-withdraw {
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

        .btn-action-withdraw i {
            font-size: 14px;
            line-height: 1;
            transition: transform 0.2s ease;
        }

        /* Duyệt chi (Soft Emerald) */
        .btn-action-withdraw.btn-action-approve {
            background: #ecfdf5;
            color: #059669;
            border-color: #a7f3d0;
        }

        .btn-action-withdraw.btn-action-approve:hover {
            background: #059669;
            color: #ffffff;
            border-color: #059669;
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
            transform: translateY(-1px);
        }

        .btn-action-withdraw.btn-action-approve:hover i {
            transform: scale(1.15);
        }

        .btn-action-withdraw.btn-action-approve:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(5, 150, 105, 0.2);
        }

        /* Từ chối (Soft Rose/Red) */
        .btn-action-withdraw.btn-action-reject {
            background: #fff1f2;
            color: #e11d48;
            border-color: #fecdd3;
        }

        .btn-action-withdraw.btn-action-reject:hover {
            background: #e11d48;
            color: #ffffff;
            border-color: #e11d48;
            box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);
            transform: translateY(-1px);
        }

        .btn-action-withdraw.btn-action-reject:hover i {
            transform: scale(1.15);
        }

        .btn-action-withdraw.btn-action-reject:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(225, 29, 72, 0.2);
        }

        /* Quick reason chips in reject modal */
        .reject-reason-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            font-size: 11.5px;
            font-weight: 500;
            color: #475569;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            cursor: pointer;
            transition: all 0.15s ease;
            user-select: none;
        }

        .reject-reason-chip:hover {
            background: #fee2e2;
            color: #dc2626;
            border-color: #fca5a5;
            transform: translateY(-1px);
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
            <div class="card custom-shadow border-0">
                <x-admin.page-header
                    icon="wallet"
                    :title="__('Danh sách Yêu cầu Rút tiền Hoa hồng')"
                    :subtitle="__('Quản lý các yêu cầu rút tiền từ ví đối tác Affiliate. Kiểm tra thông tin tài khoản ngân hàng, duyệt chuyển khoản chi trả hoặc từ chối hoàn tiền.')"
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

    {{-- Modal Xác Nhận Duyệt Chi Trả Rút Tiền --}}
    <div class="modal fade" id="modalApproveWithdraw" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header text-white py-3 px-3.5" style="background: linear-gradient(135deg, #059669 0%, #10b981 100%);">
                    <h5 class="modal-title d-flex align-items-center mb-0 fw-bold fs-16">
                        <i class="ti ti-circle-check fs-2 me-2"></i>
                        {{ __('Xác nhận Duyệt Chi Trả Hoa Hồng') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formApproveWithdraw">
                    <input type="hidden" id="approveWithdrawId" name="id">
                    <div class="modal-body p-3.5">
                        <div class="p-3 bg-light rounded-3 mb-3 border border-1">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted fs-12">{{ __('Mã yêu cầu') }}</span>
                                <span class="fw-bold font-monospace text-dark fs-13 px-2 py-0.5 bg-white border rounded" id="approveCode">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted fs-12">{{ __('Đối tác nhận') }}</span>
                                <span class="fw-bold text-primary fs-13" id="approveUser">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="text-muted fs-12">{{ __('Số tiền chi trả') }}</span>
                                <span class="fw-extrabold text-success fs-16 font-monospace" id="approveAmount">-</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-start">
                                <span class="text-muted fs-12 mt-0.5">{{ __('Tài khoản nhận') }}</span>
                                <span class="fw-semibold text-dark text-end fs-12" id="approveBank" style="max-width: 65%;">-</span>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="approveNote" class="form-label fw-semibold fs-13 text-dark">
                                <i class="ti ti-receipt me-1 text-success"></i>{{ __('Mã giao dịch ngân hàng / Ghi chú đối soát (tùy chọn)') }}
                            </label>
                            <input type="text" class="form-control fs-13" id="approveNote" name="note" placeholder="{{ __('VD: FT2609110001, đã chuyển khoản qua Vietcombank...') }}">
                            <small class="text-muted fs-11 mt-1 d-block"><i class="ti ti-info-circle me-0.5"></i>{{ __('Thông tin này sẽ được lưu trữ làm biên lai đối soát và gửi đến đối tác.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer py-2.5 px-3.5 bg-light border-top d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                        <button type="submit" class="btn btn-success fw-bold px-3 d-flex align-items-center" id="btnSubmitApprove">
                            <span class="spinner-border spinner-border-sm me-1.5 d-none" role="status"></span>
                            <i class="ti ti-check me-1"></i>{{ __('Xác nhận Đã Chuyển Tiền') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Từ Chối Lệnh Rút Tiền --}}
    <div class="modal fade" id="modalRejectWithdraw" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 14px; overflow: hidden;">
                <div class="modal-header text-white py-3 px-3.5" style="background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%);">
                    <h5 class="modal-title d-flex align-items-center mb-0 fw-bold fs-16">
                        <i class="ti ti-alert-triangle fs-2 me-2"></i>
                        {{ __('Từ Chối Lệnh Rút Tiền') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formRejectWithdraw">
                    <input type="hidden" id="rejectWithdrawId" name="id">
                    <div class="modal-body p-3.5">
                        <div class="alert alert-warning py-2.5 px-3 mb-3 fs-12 border-warning-subtle d-flex align-items-center gap-2 rounded-2" role="alert">
                            <i class="ti ti-alert-circle fs-3 text-warning flex-shrink-0"></i>
                            <div>
                                {{ __('Khi từ chối, số tiền rút sẽ được hệ thống ') }}
                                <strong>{{ __('TỰ ĐỘNG HOÀN LẠI VÀO VÍ') }}</strong>
                                {{ __(' của đối tác ngay lập tức.') }}
                            </div>
                        </div>

                        <div class="p-2.5 bg-light rounded-2 mb-3 border fs-12">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ __('Mã yêu cầu') }}:</span>
                                <strong class="text-dark font-monospace" id="rejectCode">-</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-1">
                                <span class="text-muted">{{ __('Đối tác') }}:</span>
                                <strong class="text-primary" id="rejectUser">-</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">{{ __('Số tiền hoàn lại') }}:</span>
                                <strong class="text-danger fs-13 font-monospace" id="rejectAmount">-</strong>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="rejectReason" class="form-label fw-semibold fs-13 text-dark">
                                <i class="ti ti-pencil me-1 text-danger"></i>{{ __('Lý do từ chối') }} <span class="text-danger">*</span>
                            </label>

                            {{-- Gợi ý lý do từ chối nhanh --}}
                            <div class="d-flex flex-wrap gap-1.5 mb-2">
                                <span class="reject-reason-chip" data-reason="Số tài khoản ngân hàng không chính xác">Số tài khoản sai</span>
                                <span class="reject-reason-chip" data-reason="Tên chủ tài khoản không khớp với hồ sơ đăng ký">Tên chủ thẻ không khớp</span>
                                <span class="reject-reason-chip" data-reason="Ngân hàng thụ hưởng tạm ngưng nhận tiền hoặc tài khoản bị khóa">TK ngân hàng bị khóa</span>
                                <span class="reject-reason-chip" data-reason="Giao dịch cần xác minh thêm thông tin đối soát">Cần đối soát lại</span>
                            </div>

                            <textarea class="form-control fs-13" id="rejectReason" name="reason" rows="3" required placeholder="{{ __('Chọn gợi ý phía trên hoặc nhập lý do từ chối chi tiết...') }}"></textarea>
                            <small class="text-muted fs-11 mt-1 d-block"><i class="ti ti-info-circle me-0.5"></i>{{ __('Lý do từ chối sẽ được thông báo ngay đến ứng dụng của đối tác.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer py-2.5 px-3.5 bg-light border-top d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                        <button type="submit" class="btn btn-danger fw-bold px-3 d-flex align-items-center" id="btnSubmitReject">
                            <span class="spinner-border spinner-border-sm me-1.5 d-none" role="status"></span>
                            <i class="ti ti-arrow-back-up me-1"></i>{{ __('Từ chối & Hoàn tiền') }}
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
    @include('admin.common.copy')

    <script>
        // Các hàm mở modal toàn cục (sử dụng được từ onclick inline hoặc event listener)
        window.openApproveWithdrawModal = function (target, code, amount, user, bank) {
            var id = target;
            if (typeof target === 'object' && target !== null) {
                var $el = $(target);
                id = $el.data('id');
                code = $el.data('code');
                amount = $el.data('amount');
                user = $el.data('user');
                bank = $el.data('bank');
            }

            $('#approveWithdrawId').val(id);
            $('#approveCode').text(code || '-');
            $('#approveAmount').text(amount || '-');
            $('#approveUser').text(user || '-');
            $('#approveBank').text(bank || '-');
            $('#approveNote').val('');

            var modalEl = document.getElementById('modalApproveWithdraw');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.show();
                } else if (typeof $(modalEl).modal === 'function') {
                    $(modalEl).modal('show');
                }
            }
        };

        window.openRejectWithdrawModal = function (target, code, amount, user) {
            var id = target;
            if (typeof target === 'object' && target !== null) {
                var $el = $(target);
                id = $el.data('id');
                code = $el.data('code');
                amount = $el.data('amount');
                user = $el.data('user');
            }

            $('#rejectWithdrawId').val(id);
            $('#rejectCode').text(code || '-');
            $('#rejectAmount').text(amount || '-');
            $('#rejectUser').text(user || '-');
            $('#rejectReason').val('');
            $('.reject-reason-chip').removeClass('active');

            var modalEl = document.getElementById('modalRejectWithdraw');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.show();
                } else if (typeof $(modalEl).modal === 'function') {
                    $(modalEl).modal('show');
                }
            }
        };

        function hideApproveModal() {
            var modalEl = document.getElementById('modalApproveWithdraw');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else if (typeof $(modalEl).modal === 'function') {
                    $(modalEl).modal('hide');
                }
            }
        }

        function hideRejectModal() {
            var modalEl = document.getElementById('modalRejectWithdraw');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) modal.hide();
                } else if (typeof $(modalEl).modal === 'function') {
                    $(modalEl).modal('hide');
                }
            }
        }

        $(document).ready(function () {
            const tableId = '{{ $dataTable->getTableAttribute("id") }}';

            // Kích hoạt Bootstrap tooltips an toàn không gây lỗi
            function initTooltips() {
                try {
                    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                        document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
                            if (!bootstrap.Tooltip.getInstance(el)) {
                                new bootstrap.Tooltip(el, { trigger: 'hover' });
                            }
                        });
                    } else if (typeof $.fn.tooltip === 'function') {
                        $('[data-bs-toggle="tooltip"]').tooltip({ trigger: 'hover' });
                    }
                } catch (e) {
                    console.warn('Tooltip init error:', e);
                }
            }

            initTooltips();
            $(document).on('draw.dt', function () {
                initTooltips();
            });

            // Chọn chip lý do từ chối nhanh
            $(document).on('click', '.reject-reason-chip', function () {
                const reason = $(this).data('reason');
                $('#rejectReason').val(reason);
                $('.reject-reason-chip').removeClass('active');
                $(this).addClass('active');
            });

            // Sự kiện click mở modal Duyệt chi
            $(document).on('click', '.btn-approve-withdraw', function (e) {
                if (!$(this).attr('onclick')) {
                    e.preventDefault();
                    window.openApproveWithdrawModal(this);
                }
            });

            // Submit Duyệt Chi Trả
            $('#formApproveWithdraw').on('submit', function (e) {
                e.preventDefault();
                const id = $('#approveWithdrawId').val();
                const note = $('#approveNote').val();
                const $btn = $('#btnSubmitApprove');

                $btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

                $.ajax({
                    url: '{{ url("admin/giao-dich/approve-withdraw") }}/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        note: note
                    },
                    success: function (res) {
                        hideApproveModal();
                        if (typeof msgSuccess === 'function') {
                            msgSuccess(res.message || '{{ __("Duyệt chi trả thành công.") }}');
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Thành công") }}',
                                text: res.message || '{{ __("Duyệt chi trả thành công.") }}',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert(res.message);
                        }
                        if (window.LaravelDataTables && window.LaravelDataTables[tableId]) {
                            window.LaravelDataTables[tableId].ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '{{ __("Đã có lỗi xảy ra khi duyệt chi trả.") }}';
                        if (typeof msgError === 'function') {
                            msgError(msg);
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Lỗi") }}',
                                text: msg
                            });
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function () {
                        $btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                    }
                });
            });

            // Sự kiện click mở modal Từ chối
            $(document).on('click', '.btn-reject-withdraw', function (e) {
                if (!$(this).attr('onclick')) {
                    e.preventDefault();
                    window.openRejectWithdrawModal(this);
                }
            });

            // Submit Từ Chối
            $('#formRejectWithdraw').on('submit', function (e) {
                e.preventDefault();
                const id = $('#rejectWithdrawId').val();
                const reason = $('#rejectReason').val();
                const $btn = $('#btnSubmitReject');

                if (!reason || !reason.trim()) {
                    if (typeof msgError === 'function') {
                        msgError('{{ __("Vui lòng nhập lý do từ chối.") }}');
                    } else if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("Chú ý") }}',
                            text: '{{ __("Vui lòng nhập lý do từ chối.") }}'
                        });
                    } else {
                        alert('{{ __("Vui lòng nhập lý do từ chối.") }}');
                    }
                    return;
                }

                $btn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

                $.ajax({
                    url: '{{ url("admin/giao-dich/reject-withdraw") }}/' + id,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        reason: reason
                    },
                    success: function (res) {
                        hideRejectModal();
                        if (typeof msgSuccess === 'function') {
                            msgSuccess(res.message || '{{ __("Từ chối lệnh rút tiền thành công.") }}');
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'success',
                                title: '{{ __("Thành công") }}',
                                text: res.message || '{{ __("Từ chối lệnh rút tiền thành công.") }}',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            alert(res.message);
                        }
                        if (window.LaravelDataTables && window.LaravelDataTables[tableId]) {
                            window.LaravelDataTables[tableId].ajax.reload(null, false);
                        } else {
                            location.reload();
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '{{ __("Đã có lỗi xảy ra khi từ chối lệnh rút.") }}';
                        if (typeof msgError === 'function') {
                            msgError(msg);
                        } else if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'error',
                                title: '{{ __("Lỗi") }}',
                                text: msg
                            });
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function () {
                        $btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                    }
                });
            });
        });
    </script>
@endpush
