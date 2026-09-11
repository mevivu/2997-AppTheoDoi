@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
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
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-success text-white py-2.5">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="ti ti-check-circle fs-3 me-2"></i>
                        {{ __('Xác nhận Duyệt Chi Trả Hoa Hồng') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formApproveWithdraw">
                    <input type="hidden" id="approveWithdrawId" name="id">
                    <div class="modal-body p-3.5">
                        <div class="p-3 bg-light rounded-3 mb-3 border">
                            <div class="row g-2 fs-13">
                                <div class="col-5 text-muted">{{ __('Mã giao dịch') }}:</div>
                                <div class="col-7 fw-bold text-dark font-monospace" id="approveCode">-</div>

                                <div class="col-5 text-muted">{{ __('Đối tác nhận') }}:</div>
                                <div class="col-7 fw-bold text-primary" id="approveUser">-</div>

                                <div class="col-5 text-muted">{{ __('Số tiền chi trả') }}:</div>
                                <div class="col-7 fw-extrabold text-success fs-14" id="approveAmount">-</div>

                                <div class="col-5 text-muted">{{ __('Tài khoản nhận') }}:</div>
                                <div class="col-7 fw-semibold text-dark" id="approveBank">-</div>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label for="approveNote" class="form-label fw-semibold fs-13">{{ __('Mã giao dịch ngân hàng / Ghi chú (tùy chọn)') }}</label>
                            <input type="text" class="form-control" id="approveNote" name="note" placeholder="{{ __('VD: FT2609110001, đã chuyển khoản qua Vietcombank...') }}">
                            <small class="text-muted fs-12">{{ __('Thông tin này sẽ được lưu trữ làm biên lai đối soát.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer py-2 bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                        <button type="submit" class="btn btn-success fw-bold" id="btnSubmitApprove">
                            <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
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
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white py-2.5">
                    <h5 class="modal-title d-flex align-items-center">
                        <i class="ti ti-alert-triangle fs-3 me-2"></i>
                        {{ __('Từ Chối Lệnh Rút Tiền') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formRejectWithdraw">
                    <input type="hidden" id="rejectWithdrawId" name="id">
                    <div class="modal-body p-3.5">
                        <div class="alert alert-warning py-2 px-3 mb-3 fs-13 border-warning-subtle" role="alert">
                            <i class="ti ti-info-circle me-1"></i>
                            {{ __('Lưu ý: Khi từ chối, số tiền rút sẽ được hệ thống ') }}
                            <strong>{{ __('TỰ ĐỘNG HOÀN LẠI VÀO VÍ') }}</strong>
                            {{ __(' của đối tác ngay lập tức.') }}
                        </div>

                        <div class="p-2.5 bg-light rounded-2 mb-3 border fs-13">
                            <div>{{ __('Mã giao dịch') }}: <strong class="text-dark font-monospace" id="rejectCode">-</strong></div>
                            <div>{{ __('Số tiền hoàn lại') }}: <strong class="text-danger" id="rejectAmount">-</strong></div>
                            <div>{{ __('Đối tác') }}: <strong class="text-primary" id="rejectUser">-</strong></div>
                        </div>

                        <div class="mb-2">
                            <label for="rejectReason" class="form-label fw-semibold fs-13">
                                {{ __('Lý do từ chối') }} <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="rejectReason" name="reason" rows="3" required placeholder="{{ __('VD: Số tài khoản ngân hàng không chính xác, tên chủ tài khoản không khớp...') }}"></textarea>
                            <small class="text-muted fs-12">{{ __('Lý do từ chối sẽ được lưu lại và gửi thông báo đến đối tác để kiểm tra.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer py-2 bg-light">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">{{ __('Hủy bỏ') }}</button>
                        <button type="submit" class="btn btn-danger fw-bold" id="btnSubmitReject">
                            <span class="spinner-border spinner-border-sm me-1 d-none" role="status"></span>
                            <i class="ti ti-arrow-back-up me-1"></i>{{ __('Từ chối & Hoàn tiền vào ví') }}
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
        $(document).ready(function () {
            const tableId = '{{ $dataTable->getTableAttribute("id") }}';

            // 1. Mở Modal Duyệt Chi Trả
            $(document).on('click', '.btn-approve-withdraw', function () {
                const id = $(this).data('id');
                const code = $(this).data('code');
                const amount = $(this).data('amount');
                const user = $(this).data('user');
                const bank = $(this).data('bank');

                $('#approveWithdrawId').val(id);
                $('#approveCode').text(code);
                $('#approveAmount').text(amount);
                $('#approveUser').text(user);
                $('#approveBank').text(bank);
                $('#approveNote').val('');

                $('#modalApproveWithdraw').modal('show');
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
                        $('#modalApproveWithdraw').modal('hide');
                        if (typeof msgSuccess === 'function') {
                            msgSuccess(res.message || '{{ __("Duyệt chi trả thành công.") }}');
                        } else {
                            alert(res.message);
                        }
                        if (window.LaravelDataTables && window.LaravelDataTables[tableId]) {
                            window.LaravelDataTables[tableId].ajax.reload(null, false);
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '{{ __("Đã có lỗi xảy ra khi duyệt chi trả.") }}';
                        if (typeof msgError === 'function') {
                            msgError(msg);
                        } else {
                            alert(msg);
                        }
                    },
                    complete: function () {
                        $btn.prop('disabled', false).find('.spinner-border').addClass('d-none');
                    }
                });
            });

            // 2. Mở Modal Từ Chối Chi Trả
            $(document).on('click', '.btn-reject-withdraw', function () {
                const id = $(this).data('id');
                const code = $(this).data('code');
                const amount = $(this).data('amount');
                const user = $(this).data('user');

                $('#rejectWithdrawId').val(id);
                $('#rejectCode').text(code);
                $('#rejectAmount').text(amount);
                $('#rejectUser').text(user);
                $('#rejectReason').val('');

                $('#modalRejectWithdraw').modal('show');
            });

            // Submit Từ Chối
            $('#formRejectWithdraw').on('submit', function (e) {
                e.preventDefault();
                const id = $('#rejectWithdrawId').val();
                const reason = $('#rejectReason').val();
                const $btn = $('#btnSubmitReject');

                if (!reason.trim()) {
                    if (typeof msgError === 'function') {
                        msgError('{{ __("Vui lòng nhập lý do từ chối.") }}');
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
                        $('#modalRejectWithdraw').modal('hide');
                        if (typeof msgSuccess === 'function') {
                            msgSuccess(res.message || '{{ __("Từ chối lệnh rút tiền thành công.") }}');
                        } else {
                            alert(res.message);
                        }
                        if (window.LaravelDataTables && window.LaravelDataTables[tableId]) {
                            window.LaravelDataTables[tableId].ajax.reload(null, false);
                        }
                    },
                    error: function (xhr) {
                        const msg = xhr.responseJSON?.message || '{{ __("Đã có lỗi xảy ra khi từ chối lệnh rút.") }}';
                        if (typeof msgError === 'function') {
                            msgError(msg);
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
