<!-- Offline Banner -->
<div id="admin-offline-banner" class="alert alert-warning mb-0 rounded-0 border-0 text-center py-2 shadow-sm" style="display: none; width: 100%;">
    <i class="ti ti-wifi-off me-1"></i><strong>Cảnh báo:</strong> Đang ở chế độ ngoại tuyến. Một số tính năng CMS Quản trị có thể bị gián đoạn.
</div>

<!-- Navbar -->
<header class="modern-header">
    <div class="header-container">
        <div class="header-left d-flex align-items-center">
            <!-- Sidebar Toggle Button -->
            <button type="button" id="sidebarToggle" class="btn btn-sm btn-light text-muted me-3 d-flex align-items-center justify-content-center rounded-circle border shadow-sm" title="Thu nhỏ / Phóng to Sidebar" style="width: 38px; height: 38px;">
                <i class="ti ti-menu-2 fs-4"></i>
            </button>
        </div>

        <div class="header-right d-flex align-items-center">
            <!-- PWA Install Button -->
            <button type="button" id="install-pwa-admin" class="btn btn-sm btn-outline-primary align-items-center me-2 rounded-pill px-3 py-1 shadow-sm" style="display: none;" title="Cài đặt ứng dụng Admin CMS">
                <i class="ti ti-device-mobile-down me-1 fs-3"></i>
                <span class="fw-semibold">Cài ứng dụng Admin</span>
            </button>

            @include('admin.layouts.partials.notification')

            <div class="header-divider"></div>

            <div class="header-account">
                @include('admin.layouts.partials.account')
            </div>
        </div>
    </div>
</header>

<style>
    .modern-header {
        background: #fff;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        padding: 0.5rem 0;
        position: sticky;
        top: 0;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        margin-left: 250px;
        transition: margin-left 0.3s ease;
    }

    body.sidebar-mini .modern-header {
        margin-left: 80px;
    }

    .header-container {
        width: 100%;
        max-width: 100%;
        margin: 0 auto;
        padding: 0 1.5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 60px;
    }

    .header-left {
        display: flex;
        align-items: center;
        flex: 1;
        max-width: 500px;
        margin-left: 0;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .header-divider {
        width: 1px;
        height: 32px;
        background: rgba(0,0,0,0.1);
        margin: 0 0.5rem;
    }

    .header-account {
        display: flex;
        align-items: center;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .modern-header {
            margin-left: 0 !important;
            width: 100% !important;
        }

        body.sidebar-mini .modern-header {
            margin-left: 0 !important;
        }

        .header-container {
            padding: 0 0.75rem !important;
            height: 56px;
            gap: 0.5rem;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
        }

        .header-left {
            max-width: none !important;
            flex: 0 0 auto !important;
        }

        .header-divider {
            display: none !important;
        }

        .header-right {
            gap: 0.5rem !important;
            flex: 0 0 auto !important;
            width: auto !important;
            display: flex !important;
            align-items: center !important;
            justify-content: flex-end !important;
        }
    }
</style>
