<button type="button" {{ $attributes->class(['btn', 'btn-danger', 'open-cancel-confirm'])
    ->merge([
        'data-bs-toggle' => 'modal',
        'data-bs-target' => '#modalCancelConfirm',
    ]) }}>
    {{ $title ?? '' }}
    {{ $slot }}
</button>
<div class="modal modal-blur fade" id="modalCancelConfirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content custom-confirm-modal">
            <div class="modal-body">
                <div class="modal-icon-badge badge-danger">
                    <i class="ti ti-x"></i>
                </div>
                <div class="modal-title">{{ __('Hủy xác nhận?') }}</div>
                <p class="modal-desc">{{ __('Bạn có chắc chắn muốn huỷ xác nhận mục này không?') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                <x-form id="modalFormCancel" action="#" type="delete">
                    <button type="submit" class="btn btn-danger">{{ __('Xác nhận') }}</button>
                </x-form>
            </div>
        </div>
    </div>
</div>
