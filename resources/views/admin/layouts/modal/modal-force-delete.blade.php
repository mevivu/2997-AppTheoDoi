<div class="modal modal-blur fade" id="modalForceDelete" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document" style="max-width: 450px;">
        <div class="modal-content custom-confirm-modal border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header border-0 pb-0 pt-4 px-4 d-flex justify-content-end">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center px-4 pt-1 pb-4">
                <div class="mb-3 d-inline-flex align-items-center justify-content-center" 
                     style="width: 68px; height: 68px; border-radius: 50%; background: #fee2e2; color: #dc2626; font-size: 32px; box-shadow: 0 4px 14px rgba(220, 38, 38, 0.25);">
                    <i class="ti ti-alert-triangle"></i>
                </div>
                <h4 class="modal-title fw-bold text-danger mb-2" style="font-size: 1.25rem;">
                    {{ __('Xác nhận xóa vĩnh viễn tài khoản?') }}
                </h4>
                <p class="modal-desc text-muted fs-13 mb-3">
                    {{ __('Hành động này sẽ') }} <strong class="text-danger">{{ __('xóa vĩnh viễn và không thể khôi phục') }}</strong> {{ __('tài khoản') }} <strong class="text-dark" id="forceDeleteUserName"></strong> {{ __('cùng toàn bộ dữ liệu liên quan khỏi hệ thống:') }}
                </p>
                <div class="p-3 text-start mb-3" style="background: #fff5f5; border: 1px solid #fed7d7; border-radius: 10px;">
                    <ul class="mb-0 ps-3 text-danger fs-12" style="line-height: 1.7;">
                        <li>{{ __('Thông tin cá nhân, hồ sơ và tài khoản đăng nhập') }}</li>
                        <li>{{ __('Hồ sơ trẻ em, nhật ký, đánh giá EQ/PQ, lịch tiêm phòng') }}</li>
                        <li>{{ __('Lịch sử gói dịch vụ và các giao dịch thanh toán') }}</li>
                        <li>{{ __('Danh sách thiết bị liên kết và phiên đăng nhập') }}</li>
                    </ul>
                </div>
                <div class="alert alert-warning py-2 px-3 mb-0 text-start d-flex align-items-center gap-2 small">
                    <i class="ti ti-alert-circle fs-4 text-warning flex-shrink-0"></i>
                    <span>{{ __('Dữ liệu sau khi xóa sẽ biến mất hoàn toàn và không thể hoàn tác!') }}</span>
                </div>
            </div>
            <div class="modal-footer border-top bg-light px-4 py-3 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-secondary px-3" data-bs-dismiss="modal">
                    {{ __('Hủy bỏ') }}
                </button>
                <x-form id="modalFormForceDelete" action="#" type="delete">
                    <button type="submit" class="btn btn-danger px-3 d-inline-flex align-items-center gap-1 fw-semibold">
                        <i class="ti ti-trash-x fs-5"></i>
                        <span>{{ __('Xác nhận xóa vĩnh viễn') }}</span>
                    </button>
                </x-form>
            </div>
        </div>
    </div>
</div>
<button type="button" class="d-none" data-bs-toggle="modal" data-bs-target="#modalForceDelete">{{ __('OpenModelForceDelete') }}</button>
