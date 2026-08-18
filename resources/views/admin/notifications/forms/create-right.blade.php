@php use App\Traits\RouteAdminSystem; @endphp
<div class="col-12 col-lg-4 col-xl-3">
    {{-- Floating Form Actions --}}
    <x-admin.form-actions
        :submit-title="__('Gửi thông báo')"
        submit-icon="ti ti-send"
        :back-route="route(RouteAdminSystem::NOTIFICATION_INDEX)"
        :back-title="__('Quay lại')"
    />
</div>
