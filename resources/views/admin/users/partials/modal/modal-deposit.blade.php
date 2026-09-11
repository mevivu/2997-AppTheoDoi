@php use App\Traits\RouteAdminSystem; @endphp

<!-- Modal Nạp tiền vào ví thành viên -->
<div class="modal fade"
     id="depositModal"
     tabindex="-1"
     aria-labelledby="depositModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 660px;">
        <div class="modal-content shadow border-0 rounded-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-3">
                    <span class="avatar avatar-md bg-success-lt rounded-circle">
                        <i class="ti ti-wallet fs-2 text-success"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="depositModalLabel">
                            {{ __('Nạp tiền vào ví thành viên') }}
                        </h5>
                        <small class="text-muted fs-12">{{ __('Cộng tiền trực tiếp vào số dư ví của tài khoản') }}</small>
                    </div>
                </div>
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
                    <div id="depositErrorAlert" class="alert alert-danger d-none py-2 px-3 mb-3 fs-13 rounded-3"></div>

                    <!-- Thẻ tóm tắt thông tin thành viên -->
                    <div class="card border border-primary-subtle bg-primary-lt rounded-3 mb-3 p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <span class="avatar avatar-md rounded-circle bg-primary text-white fw-bold shadow-sm" id="depositUserAvatarInitials">U</span>
                                <div>
                                    <div class="fw-bold text-dark fs-15 lh-sm" id="depositUserFullname">---</div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-white text-primary border border-primary-subtle font-monospace px-2 py-1 fs-11" id="depositUserCode">---</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-end">
                                <span class="text-muted fs-12 d-block mb-1">{{ __('Số dư hiện tại') }}</span>
                                <span class="badge bg-white text-success border border-success-subtle fw-bold font-monospace fs-14 px-3 py-1 shadow-sm" id="depositUserCurrentBalance">0 đ</span>
                            </div>
                        </div>
                    </div>

                    <!-- Nhập số tiền nạp -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label for="depositAmountInput" class="form-label fw-bold mb-0 text-dark">
                                {{ __('Số tiền cần nạp') }} <span class="text-danger">*</span>
                            </label>
                            <span class="badge bg-secondary-lt fs-11 fw-normal">{{ __('Tối thiểu 1.000đ') }}</span>
                        </div>

                        <!-- Hero Input -->
                        <div class="position-relative">
                            <div class="input-group input-group-lg shadow-sm rounded-3 overflow-hidden border border-success-subtle">
                                <span class="input-group-text bg-white border-0 text-success ps-3 pe-2">
                                    <i class="ti ti-cash fs-2"></i>
                                </span>
                                <input type="text"
                                       class="form-control border-0 font-monospace fw-bold fs-20 text-success text-end pe-2"
                                       id="depositAmountInput"
                                       name="amount"
                                       placeholder="0"
                                       autocomplete="off"
                                       required>
                                <span class="input-group-text bg-white border-0 fw-bold text-muted pe-3 fs-15">VNĐ</span>
                            </div>
                            <button type="button"
                                    class="btn btn-sm btn-link text-muted position-absolute top-50 translate-middle-y d-none"
                                    id="btnClearDepositAmount"
                                    style="right: 62px; z-index: 5; text-decoration: none;"
                                    title="{{ __('Xóa số tiền') }}">
                                <i class="ti ti-circle-x fs-3"></i>
                            </button>
                        </div>

                        <!-- Gợi ý chọn nhanh số tiền: Grid 3x2 cân đối hoàn hảo -->
                        <div class="row g-2 mt-2" id="quickAmountGrid">
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-amount-btn rounded-2" data-amount="50000">50.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-amount-btn rounded-2" data-amount="100000">100.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-amount-btn rounded-2" data-amount="200000">200.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-amount-btn rounded-2" data-amount="500000">500.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-amount-btn rounded-2" data-amount="1000000">1.000.000đ</button>
                            </div>
                            <div class="col-4">
                                <button type="button" class="btn btn-outline-secondary w-100 py-2 fs-12 fw-semibold quick-amount-btn rounded-2" data-amount="2000000">2.000.000đ</button>
                            </div>
                        </div>

                        <!-- Tóm tắt biến động số dư dạng Transaction Flow -->
                        <div class="card bg-light border-0 rounded-3 mt-3 p-3">
                            <div class="d-flex align-items-center justify-content-between fs-13">
                                <div class="text-center flex-fill">
                                    <div class="text-muted fs-11 mb-1">{{ __('Hiện tại') }}</div>
                                    <div class="fw-semibold text-secondary font-monospace" id="previewOldBalance">0 đ</div>
                                </div>
                                <div class="text-muted px-2">
                                    <i class="ti ti-plus fs-4 text-success"></i>
                                </div>
                                <div class="text-center flex-fill">
                                    <div class="text-muted fs-11 mb-1">{{ __('Nạp thêm') }}</div>
                                    <div class="fw-bold text-success font-monospace" id="previewAddAmount">0 đ</div>
                                </div>
                                <div class="text-muted px-2">
                                    <i class="ti ti-arrow-narrow-right fs-4 text-primary"></i>
                                </div>
                                <div class="text-center flex-fill">
                                    <div class="text-muted fs-11 mb-1">{{ __('Số dư mới') }}</div>
                                    <div class="fw-bold text-primary font-monospace fs-14" id="depositPreviewNewBalance">0 đ</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Nhập lý do nạp tiền -->
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="depositAdminNote" class="form-label fw-bold mb-0 text-dark">
                                {{ __('Lý do nạp tiền') }} <span class="text-danger">*</span>
                            </label>
                            <span class="text-muted fs-11">{{ __('Lưu vào lịch sử ví') }}</span>
                        </div>
                        <textarea class="form-control rounded-3"
                                  id="depositAdminNote"
                                  name="admin_note"
                                  rows="2"
                                  placeholder="{{ __('Nhập lý do hoặc nội dung ghi chú nạp tiền (Bắt buộc)...') }}"
                                  required></textarea>

                        <!-- Gợi ý lý do nhanh dạng Tag Chips có Icon -->
                        <div class="d-flex flex-wrap gap-2 mt-2" id="quickReasonChips">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 fs-12 quick-reason-chip" data-reason="Thưởng thành tích giới thiệu">
                                <i class="ti ti-award me-1 text-warning"></i>{{ __('Thưởng thành tích') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 fs-12 quick-reason-chip" data-reason="Hỗ trợ sự kiện cộng đồng">
                                <i class="ti ti-confetti me-1 text-info"></i>{{ __('Hỗ trợ sự kiện') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 fs-12 quick-reason-chip" data-reason="Điều chỉnh sai lệch hoa hồng">
                                <i class="ti ti-scale me-1 text-primary"></i>{{ __('Điều chỉnh hoa hồng') }}
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-3 fs-12 quick-reason-chip" data-reason="Bù tiền đơn hàng nạp lỗi">
                                <i class="ti ti-refresh me-1 text-danger"></i>{{ __('Bù nạp lỗi') }}
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
                                    <label class="form-check-label fs-13 fw-bold text-dark d-block cursor-pointer" for="depositSendNotification">
                                        {{ __('Gửi thông báo đến thành viên') }}
                                    </label>
                                    <small class="text-muted fs-12">{{ __('Bắn notification FCM & lưu biến động số dư trong app') }}</small>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input fs-4 cursor-pointer"
                                       type="checkbox"
                                       role="switch"
                                       id="depositSendNotification"
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
                    <button type="submit" class="btn btn-success px-4 py-2 fw-semibold d-flex align-items-center gap-2 shadow-sm" id="btnSubmitDeposit">
                        <i class="ti ti-check fs-3"></i>
                        <span class="button-text">{{ __('Xác nhận nạp tiền') }}</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

