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
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">{{ __('Bạn có chắc không?') }}</div>
                <div>{{ __('Bạn có muốn huỷ xác nhận  không?') }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto"
                        data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                <x-form id="modalFormCancel" action="#" type="delete">
                    <button type="submit" class="btn btn-danger">{{ __('Xác nhận') }}</button>
                </x-form>
            </div>
        </div>
    </div>
</div>

