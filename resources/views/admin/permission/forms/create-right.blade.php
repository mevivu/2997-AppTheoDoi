@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Lưu quyền')"
        submit-icon="ti ti-device-floppy"
        :back-route="route(RouteAdminSystem::PERMISSION_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
