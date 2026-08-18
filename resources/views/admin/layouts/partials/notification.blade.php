@php
    use App\Traits\RouteAdminSystem;
@endphp
<div class="nav-item dropdown custom-notification-dropdown" id="message-box">
    <a href="#" class="nav-link d-flex align-items-center px-3 py-1.5 rounded-3 text-dark hover-bg-light gap-2 text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false">
        <div class="notification-icon-wrapper position-relative d-inline-flex align-items-center justify-content-center">
            <i class="ti ti-bell text-dark" style="font-size: 1.25rem;"></i>
            <span class="notification-dot bg-danger border border-2 border-white rounded-circle"></span>
        </div>
        <span class="fw-bold text-dark fs-13 d-none d-sm-inline-block">{{ __('Thông báo') }}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-3 p-2 mt-2 dropdown-menu-animated message-box" style="min-width: 280px;">
        <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark fs-13">{{ __('Thông báo hệ thống') }}</h6>
            <span class="badge bg-primary-subtle text-primary rounded-pill fs-11">0 mới</span>
        </div>
        <div class="py-3 text-center text-muted">
            <i class="ti ti-bell-off fs-2 text-secondary opacity-50 mb-1 d-block"></i>
            <span class="fs-12 d-block">{{ __('Không có thông báo mới nào') }}</span>
        </div>
        <div class="dropdown-divider my-1 opacity-50"></div>
        <a href="{{ route(RouteAdminSystem::NOTIFICATION_INDEX) }}" class="dropdown-item text-center fw-semibold text-primary py-1.5 rounded-2 fs-12">
            {{ __('Xem tất cả thông báo') }} <i class="ti ti-arrow-right ms-1"></i>
        </a>
    </div>
</div>

<style>
    .notification-icon-wrapper {
        width: 28px;
        height: 28px;
    }

    .notification-dot {
        position: absolute;
        top: 2px;
        right: 2px;
        width: 8px;
        height: 8px;
    }

    .hover-bg-light:hover {
        background-color: #f1f5f9;
    }

    .custom-notification-dropdown .dropdown-menu {
        box-shadow: 0 10px 30px -5px rgba(15, 23, 42, 0.12), 0 0 1px rgba(15, 23, 42, 0.1) !important;
        border: 1px solid #e2e8f0 !important;
        animation: fadeInFast 0.15s ease-out;
    }
</style>
