@php
    use App\Enums\Notification\MessageType;
    use App\Traits\RouteAdminSystem;
@endphp
<div class="col-12 col-lg-4 col-xl-3">
    <div class="card border-0 custom-shadow rounded-3 mb-4">
        <div class="card-header bg-white border-bottom px-4 py-3">
            <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                <i class="ti ti-toggle-right text-primary me-2 fs-4"></i>
                {{ __('Trạng thái') }}
            </h5>
        </div>
        <div class="card-body p-4">
            <x-select class="select2-bs5-ajax" name="status" :value="old('status')" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :option="$notification->status->value" :value="$key" :title="__($value)"/>
                @endforeach
            </x-select>
        </div>
    </div>

    @if($notification->type == MessageType::PAYMENT)
        <div class="card border-0 custom-shadow rounded-3 mb-4">
            <div class="card-header bg-white border-bottom px-4 py-3">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                    <i class="ti ti-check text-primary me-2 fs-4"></i>
                    {{ __('Admin xác nhận') }}
                </h5>
            </div>
            <div class="card-body p-4">
                <x-select class="select2-bs5-ajax" name="approval_status"
                          :value="old('status')"
                          :required="true"
                          :disabled="$notification->approval_status != \App\Enums\ApprovalStatus::PENDING">
                    @foreach ($approval_status as $key => $value)
                        <x-select-option :option="$notification->approval_status->value" :value="$key"
                                         :title="__($value)"/>
                    @endforeach
                </x-select>
            </div>
        </div>

        <div class="card border-0 custom-shadow rounded-3 mb-4">
            <div class="card-header bg-white border-bottom px-4 py-3">
                <h5 class="mb-0 fw-bold text-dark d-flex align-items-center" style="font-size: 1.05rem;">
                    <i class="ti ti-photo-heart text-primary me-2 fs-4"></i>
                    {{ __('Ảnh xác nhận thanh toán') }}
                </h5>
            </div>
            <div class="card-body p-4 text-center">
                <div class="settings-logo-upload-wrapper text-center my-2 p-3">
                    <x-input-image-ckfinder name="payment_confirmation_image" showImage="avatar" class="img-fluid"
                                            :value="$notification->payment_confirmation_image"/>
                </div>
            </div>
        </div>
    @endif

    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu thay đổi')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::NOTIFICATION_INDEX)"
        :back-title="__('Quay lại')"
    >
        <x-button.modal-delete data-route="{{ route('admin.notification.delete', $notification->id) }}"
                               :title="__('Xóa thông báo')"/>
    </x-admin.form-actions>
</div>
