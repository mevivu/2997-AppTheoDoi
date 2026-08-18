<div class="col-12 col-lg-4 col-xl-3">
    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu cấu hình')"
        submit-icon="ti ti-device-floppy"
        :back-route="route('admin.dashboard')"
        :back-title="__('Dashboard')"
    />
</div>
