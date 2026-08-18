<div class="modal modal-blur fade" id="modalLogout" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content custom-confirm-modal">
            <div class="modal-body">
                <div class="modal-icon-badge badge-warning">
                    <i class="ti ti-logout"></i>
                </div>
                <div class="modal-title">{{ __('Đăng xuất tài khoản?') }}</div>
                <p class="modal-desc">{{ __('Bạn có chắc chắn muốn kết thúc phiên làm việc và đăng xuất?') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                <x-form :action="route('admin.logout')" type="post">
                    <button type="submit" class="btn btn-warning">{{ __('Đăng xuất') }}</button>
                </x-form>
            </div>
        </div>
    </div>
</div>