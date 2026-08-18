<button type="button" {{ $attributes->class(['btn', 'btn-success', 'open-approve-confirm'])
    ->merge([
        'data-bs-toggle' => 'modal',
        'data-bs-target' => '#modalApproveConfirm',
    ]) }}>
    {{ $title ?? '' }}
    {{ $slot }}
</button>
<div class="modal modal-blur fade" id="modalApproveConfirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content custom-confirm-modal">
            <div class="modal-body">
                <div class="modal-icon-badge badge-success">
                    <i class="ti ti-check"></i>
                </div>
                <div class="modal-title">{{ __('Xác nhận phê duyệt?') }}</div>
                <p class="modal-desc">{{ __('Bạn có chắc chắn muốn phê duyệt mục này không?') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                <x-form id="modalFormApprove" action="#" type="delete">
                    <button type="submit" class="btn btn-success">{{ __('Phê duyệt') }}</button>
                </x-form>
            </div>
        </div>
    </div>
</div>
