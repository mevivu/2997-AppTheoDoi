@php use App\Traits\RouteAdminSystem; @endphp

<!-- Modal Nạp tiền vào ví thành viên -->
<div class="modal fade"
     id="depositModal"
     tabindex="-1"
     aria-labelledby="depositModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-shadow border-0 rounded-3">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center mb-0" id="depositModalLabel">
                    <span class="avatar avatar-sm bg-green-lt rounded-circle me-2">
                        <i class="ti ti-wallet fs-3 text-success"></i>
                    </span>
                    {{ __('Nạp tiền vào ví thành viên') }}
                </h5>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>
            <form id="formDepositWallet" action="{{ route(RouteAdminSystem::USER_DEPOSIT) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="depositUserId" value="">
                
                <div class="modal-body px-4 py-3">
                    <!-- Alert báo lỗi -->
                    <div id="depositErrorAlert" class="alert alert-danger d-none py-2 px-3 mb-3 fs-13"></div>

                    <!-- Thẻ tóm tắt thông tin thành viên -->
                    <div class="card bg-muted-lt border-0 mb-3">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <div class="fw-bold text-dark fs-14" id="depositUserFullname">---</div>
                                <span class="badge bg-blue-lt font-monospace" id="depositUserCode">---</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-secondary-subtle">
                                <span class="text-muted fs-13">{{ __('Số dư hiện tại:') }}</span>
                                <span class="badge bg-green-lt fw-bold font-monospace fs-14" id="depositUserCurrentBalance">0 đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Nhập số tiền nạp -->
                    <div class="mb-3">
                        <label for="depositAmountInput" class="form-label fw-bold d-flex justify-content-between align-items-center">
                            <span>{{ __('Số tiền cần nạp') }} <span class="text-danger">*</span></span>
                            <span class="text-muted fs-12">{{ __('Tối thiểu 1.000đ') }}</span>
                        </label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control font-monospace fw-bold fs-15"
                                   id="depositAmountInput"
                                   name="amount"
                                   placeholder="0"
                                   autocomplete="off"
                                   required>
                            <span class="input-group-text fw-bold">VNĐ</span>
                        </div>

                        <!-- Gợi ý chọn nhanh số tiền -->
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fs-12 quick-amount-btn" data-amount="50000">+50.000đ</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fs-12 quick-amount-btn" data-amount="100000">+100.000đ</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fs-12 quick-amount-btn" data-amount="200000">+200.000đ</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fs-12 quick-amount-btn" data-amount="500000">+500.000đ</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fs-12 quick-amount-btn" data-amount="1000000">+1.000.000đ</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2 fs-12 quick-amount-btn" data-amount="2000000">+2.000.000đ</button>
                        </div>

                        <!-- Dự toán số dư mới sau nạp -->
                        <div class="mt-2 p-2 rounded bg-light border d-flex justify-content-between align-items-center fs-13">
                            <span class="text-muted">{{ __('Số dư sau khi nạp:') }}</span>
                            <strong class="text-success font-monospace fs-14" id="depositPreviewNewBalance">0 đ</strong>
                        </div>
                    </div>

                    <!-- Nhập lý do nạp tiền -->
                    <div class="mb-3">
                        <label for="depositAdminNote" class="form-label fw-bold">
                            {{ __('Lý do nạp tiền') }} <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control"
                                  id="depositAdminNote"
                                  name="admin_note"
                                  rows="2"
                                  placeholder="{{ __('Nhập lý do hoặc nội dung ghi chú nạp tiền (Bắt buộc)...') }}"
                                  required></textarea>
                        
                        <!-- Gợi ý lý do nhanh -->
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            <span class="badge bg-secondary-lt cursor-pointer quick-reason-chip" data-reason="Thưởng thành tích giới thiệu">Thưởng thành tích</span>
                            <span class="badge bg-secondary-lt cursor-pointer quick-reason-chip" data-reason="Hỗ trợ sự kiện cộng đồng">Hỗ trợ sự kiện</span>
                            <span class="badge bg-secondary-lt cursor-pointer quick-reason-chip" data-reason="Điều chỉnh sai lệch hoa hồng">Điều chỉnh hoa hồng</span>
                            <span class="badge bg-secondary-lt cursor-pointer quick-reason-chip" data-reason="Bù tiền đơn hàng nạp lỗi">Bù nạp lỗi</span>
                        </div>
                    </div>

                    <!-- Tùy chọn gửi thông báo FCM -->
                    <div class="form-check form-switch mb-1">
                        <input class="form-check-input"
                               type="checkbox"
                               role="switch"
                               id="depositSendNotification"
                               name="send_notification"
                               value="1"
                               checked>
                        <label class="form-check-label fs-13 fw-semibold text-dark" for="depositSendNotification">
                            {{ __('Gửi thông báo đẩy (FCM) & thông báo app cho thành viên') }}
                        </label>
                    </div>
                </div>

                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-3" data-bs-dismiss="modal">
                        {{ __('Đóng') }}
                    </button>
                    <button type="submit" class="btn btn-success px-4 d-flex align-items-center gap-2" id="btnSubmitDeposit">
                        <i class="ti ti-check fs-4"></i>
                        <span class="button-text">{{ __('Xác nhận nạp tiền') }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
