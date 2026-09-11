@php use App\Traits\RouteAdminSystem; @endphp

<!-- Modal Rút tiền từ ví thành viên -->
<div class="modal fade"
     id="withdrawModal"
     tabindex="-1"
     aria-labelledby="withdrawModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 660px;">
        <div class="modal-content shadow border-0 rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar avatar-md bg-danger-lt rounded-circle">
                        <i class="ti ti-cash-off fs-2 text-danger"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="withdrawModalLabel">
                            {{ __('Rút tiền từ ví thành viên') }}
                        </h5>
                        <small class="text-muted fs-12">{{ __('Thực hiện trừ tiền trực tiếp từ số dư ví của tài khoản') }}</small>
                    </div>
                </div>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <form id="formWithdrawWallet" action="{{ route(RouteAdminSystem::USER_WITHDRAW) }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" id="withdrawUserId" value="">

                <div class="modal-body px-4 py-3">
                    <!-- Alert báo lỗi -->
                    <div id="withdrawErrorAlert" class="alert alert-danger d-none py-2 px-3 mb-3 fs-13 rounded-3"></div>

                    <!-- Thẻ tóm tắt thông tin thành viên -->
                    <div class="card border border-danger-subtle bg-danger-lt rounded-3 mb-3 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar avatar-md rounded-circle bg-danger text-white fw-bold shadow-sm" id="withdrawUserAvatarInitials">U</span>
                                <div>
                                    <div class="fw-bold text-dark fs-15 lh-sm" id="withdrawUserFullname">---</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-white text-danger border border-danger-subtle font-monospace px-2 py-1 fs-11" id="withdrawUserCode">---</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="text-muted fs-12 d-block mb-1">{{ __('Số dư hiện tại') }}</span>
                                <span class="badge bg-white text-danger border border-danger-subtle fw-bold font-monospace fs-14 px-3 py-1 shadow-sm" id="withdrawUserCurrentBalance">0 đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Nhập số tiền rút -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="withdrawAmountInput" class="form-label fw-bold mb-0 text-dark">
                                {{ __('Số tiền cần rút / trừ') }} <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-secondary-lt fs-11 fw-normal">{{ __('Tối thiểu 1.000đ') }}</span>
                        </div>

                        <!-- Hero Input -->
                        <div class="position-relative">
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-danger-subtle">
                                <span class="input-group-text bg-white border-0 text-danger ps-3 pe-2">
                                    <i class="ti ti-credit-card-off fs-2"></i>
                                </span>
                                <input type="text"
                                       class="form-control border-0 font-monospace fw-bold fs-20 text-danger text-end pe-2"
                                       id="withdrawAmountInput"
                                       name="amount"
                                       placeholder="0"
                                       autocomplete="off"
                                       required>
                                <span class="input-group-text bg-white border-0 fw-bold text-muted pe-3 fs-15">VNĐ</span>
                            </div>
                            <button type="button"
                                    class="btn btn-sm btn-link text-muted position-absolute top-50 translate-middle-y d-none"
                                    id="btnClearWithdrawAmount"
                                    style="right: 62px; z-index: 5; text-decoration: none;"
                                    title="{{ __('Xóa số tiền') }}">
                                <i class="ti ti-circle-x fs-3"></i>
                            </button>
                        </div>

                        <!-- Cảnh báo số dư không đủ -->
                        <div id="withdrawOverBalanceAlert" class="text-danger fs-12 fw-semibold mt-1 d-none">
                            <i class="ti ti-alert-triangle me-1"></i>{{ __('Số tiền rút vượt quá số dư hiện có!') }}
                        </div>

                        <!-- Lưới chọn nhanh số tiền: Grid 3x2 cân đối -->
                        <div class="row g-2 mt-2" id="quickWithdrawAmountGrid">
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-withdraw-amount-btn rounded-2" data-amount="50000">50.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-withdraw-amount-btn rounded-2" data-amount="100000">100.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-withdraw-amount-btn rounded-2" data-amount="200000">200.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-withdraw-amount-btn rounded-2" data-amount="500000">500.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-withdraw-amount-btn rounded-2" data-amount="1000000">1.000.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-danger w-100 py-2 fs-12 fw-semibold quick-withdraw-amount-btn rounded-2" data-amount="all">
                                    <i class="ti ti-wallet me-1"></i>{{ __('Toàn bộ số dư') }}
                                </button>
                            </div>
                        </div>

                        <!-- Tóm tắt biến động số dư dạng Transaction Flow -->
                        <div class="card bg-light border-0 rounded-3 mt-3 p-3">
                            <div class="d-flex align-items-center justify-content-between fs-13">
                                <div class="text-center flex-fill">
                                    <div class="text-muted fs-11 mb-1">{{ __('Hiện tại') }}</div>
                                    <div class="fw-semibold text-secondary font-monospace" id="withdrawPreviewOldBalance">0 đ</div>
                                </div>
                                <div class="text-muted px-2">
                                    <i class="ti ti-minus fs-4 text-danger"></i>
                                </div>
                                <div class="text-center flex-fill">
                                    <div class="text-muted fs-11 mb-1">{{ __('Rút ra') }}</div>
                                    <div class="fw-bold text-danger font-monospace" id="withdrawPreviewSubtractAmount">0 đ</div>
                                </div>
                                <div class="text-muted px-2">
                                    <i class="ti ti-arrow-narrow-right fs-4 text-primary"></i>
                                </div>
                                <div class="text-center flex-fill">
                                    <div class="text-muted fs-11 mb-1">{{ __('Còn lại') }}</div>
                                    <div class="fw-bold text-primary font-monospace fs-14" id="withdrawPreviewNewBalance">0 đ</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nhập lý do rút tiền -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="withdrawAdminNote" class="form-label fw-bold mb-0 text-dark">
                                {{ __('Lý do rút / trừ tiền') }} <span class="text-danger">*</span>
                            </label>
                            <span class="text-muted fs-11">{{ __('Lưu vào lịch sử ví') }}</span>
                        </div>
                        <textarea class="form-control rounded-3"
                                  id="withdrawAdminNote"
                                  name="admin_note"
                                  rows="2"
                                  placeholder="{{ __('Nhập lý do hoặc nội dung ghi chú rút tiền (Bắt buộc)...') }}"
                                  required></textarea>

                        <!-- Gợi ý lý do nhanh dạng Tag Chips có Icon -->
                        <div class="d-flex flex-wrap gap-2 mt-2" id="quickWithdrawReasonChips">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 fs-12 quick-withdraw-reason-chip" data-reason="Chi trả hoa hồng trực tiếp">
                                <i class="ti ti-cash me-1 text-success"></i>{{ __('Chi trả hoa hồng') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 fs-12 quick-withdraw-reason-chip" data-reason="Thu hồi tiền thưởng sai lệch">
                                <i class="ti ti-rotate-rectangle me-1 text-warning"></i>{{ __('Thu hồi sai lệch') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 fs-12 quick-withdraw-reason-chip" data-reason="Điều chỉnh sai lệch số dư ví">
                                <i class="ti ti-scale me-1 text-primary"></i>{{ __('Điều chỉnh số dư') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 fs-12 quick-withdraw-reason-chip" data-reason="Khấu trừ số dư theo thỏa thuận">
                                <i class="ti ti-file-text me-1 text-danger"></i>{{ __('Khấu trừ thỏa thuận') }}
                            </button>
                        </div>
                    </div>

                    <!-- Khối Tùy chọn gửi thông báo FCM -->
                    <div class="card border border-info-subtle bg-info-lt rounded-3 p-3 mb-1">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar avatar-sm rounded-circle bg-info text-white shadow-sm">
                                    <i class="ti ti-bell-ringing fs-3"></i>
                                </span>
                                <div>
                                    <label class="form-check-label fs-13 fw-bold text-dark d-block cursor-pointer" for="withdrawSendNotification">
                                        {{ __('Gửi thông báo đến thành viên') }}
                                    </label>
                                    <small class="text-muted fs-12">{{ __('Bắn notification FCM & lưu biến động số dư trong app') }}</small>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input fs-4 cursor-pointer"
                                       type="checkbox"
                                       role="switch"
                                       id="withdrawSendNotification"
                                       name="send_notification"
                                       value="1"
                                       checked>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="modal-footer bg-light px-4 py-3 border-top d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary px-3 d-flex align-items-center gap-1" data-bs-dismiss="modal">
                        <i class="ti ti-x fs-4"></i>
                        <span>{{ __('Đóng') }}</span>
                    </button>
                    <button type="submit" class="btn btn-danger px-4 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm" id="btnSubmitWithdraw">
                        <i class="ti ti-check fs-3"></i>
                        <span class="button-text">{{ __('Xác nhận rút tiền') }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
