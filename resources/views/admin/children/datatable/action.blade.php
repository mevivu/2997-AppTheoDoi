<x-button.modal-confirm class="btn-icon" data-route="{{ route('admin.children.delete', $id) }}">
    <i class="ti ti-trash"></i>
</x-button.modal-confirm>

<div class="modal modal-blur fade" id="modalConfirm" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-title">{{ __('Bạn có chắc?') }}</div>
                <div>{{ __('Bạn có chắc muốn chuyển thành trạng thái không hoạt động?') }}</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto"
                        data-bs-dismiss="modal">{{ __('Hủy') }}</button>
                <x-form id="modalFormConfirm" action="#" type="delete">
                    <button type="submit" class="btn btn-danger">{{ __('Xác nhận') }}</button>
                </x-form>
            </div>
        </div>
    </div>
</div>
