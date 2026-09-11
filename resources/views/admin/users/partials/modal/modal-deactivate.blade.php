<div class="modal modal-blur fade" id="modalDeactivateUser" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 440px;">
        <div class="modal-content custom-confirm-modal border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pt-1 pb-4">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center" 
                     style="width: 64px; height: 64px; border-radius: 50%; background: #fef3c7; color: #d97706; font-size: 30px; box-shadow: 0 4px 14px rgba(217, 119, 6, 0.2);">
                    <i class="ti ti-user-off"></i>
                </div>
                <h4 class="modal-title fw-bold text-dark mb-2" style="font-size: 1.2rem;">
                    {{ __('Xác nhận ngưng hoạt động tài khoản?') }}
                </h4>
                <p class="modal-desc text-muted fs-13 mb-3">
                    {{ __('Bạn đang thực hiện chuyển trạng thái của thành viên') }} <strong class="text-dark" id="deactivateUserName">-</strong> {{ __('sang ngưng hoạt động:') }}
                </p>

                <div class="p-3 text-start mb-3" style="background: #fffbeb; border: 1px solid #fde68a; border-radius: 10px;">
                    <ul class="mb-0 ps-3 fs-12" style="line-height: 1.75; color: #92400e !important;">
                        <li><strong>{{ __('Cập nhật trạng thái:') }}</strong> {{ __('Tài khoản chuyển sang "Ngưng hoạt động", thành viên sẽ bị đăng xuất và tạm thời không thể đăng nhập app.') }}</li>
                        <li><strong>{{ __('Dữ liệu an toàn tuyệt đối:') }}</strong> {{ __('Toàn bộ hồ sơ các bé, số dư ví, hoa hồng và lịch sử giao dịch vẫn được LƯU NGUYÊN trên hệ thống.') }}</li>
                        <li><strong>{{ __('Khôi phục dễ dàng:') }}</strong> {{ __('Bạn có thể kích hoạt lại tài khoản này bất kỳ lúc nào tại trang chỉnh sửa thông tin.') }}</li>
                    </ul>
                </div>

                <div class="text-muted fs-11 text-center">
                    <i class="ti ti-info-circle me-0.5"></i>{{ __('(Nếu muốn xóa hoàn toàn dữ liệu khỏi hệ thống, vui lòng dùng nút Xóa vĩnh viễn màu đỏ)') }}
                </div>
            </div>
            <div class="modal-footer border-top bg-light px-4 py-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                    {{ __('Hủy bỏ') }}
                </button>
                <x-form id="modalFormDeactivate" action="#" type="delete">
                    <button type="submit" class="btn btn-warning px-3 d-inline-flex align-items-center gap-1 fw-semibold text-dark">
                        <i class="ti ti-user-off fs-5"></i>
                        <span>{{ __('Xác nhận ngưng hoạt động') }}</span>
                    </button>
                </x-form>
            </div>
        </div>
    </div>
</div>
