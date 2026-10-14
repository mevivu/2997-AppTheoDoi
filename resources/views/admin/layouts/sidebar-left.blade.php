@php use App\Traits\ImageSystem; use App\Traits\RouteAdminSystem; @endphp

<!-- Sidebar Main Container -->
<aside id="sidebar" class="sidebar">
    <!-- Logo & Brand Header -->
    <div class="sidebar-logo">
        <x-link :href="route(RouteAdminSystem::ADMIN_DASHBOARD)"
                class="d-flex align-items-center gap-2 text-decoration-none w-100">
            @php
                $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
                $settings = $settingRepository->getAll();
                $siteLogo = $settings->where('setting_key', 'site_logo')->first()?->plain_value
                    ?? ImageSystem::DEFAULT_IMAGE;
                $siteName = $settings->where('setting_key', 'site_name')->first()?->plain_value
                    ?? 'Chăm Con';
            @endphp

            <!-- Sleek Avatar Container for Logo -->
            <div class="logo-wrapper flex-shrink-0 d-flex align-items-center justify-content-center">
                <img src="{{ asset($siteLogo) }}" alt="Logo" class="logo-img">
            </div>

            <!-- Brand Name & Subtitle -->
            <div class="brand-details flex-grow-1 min-w-0" style="word-break: break-word; white-space: normal;">
                <div class="brand-text" style="word-break: break-word; white-space: normal; line-height: 1.25; font-size: 13.5px; font-weight: 700; color: #0f172a;">{{ $siteName }}</div>
                <div class="brand-subtitle" style="font-size: 11px; margin-top: 2px; color: #3b82f6; font-weight: 600;">{{ __('Quản lý hệ thống') }}</div>
            </div>
        </x-link>
    </div>

    <!-- Live Module Search Container (FE Live Search) -->
    <div class="sidebar-search-container px-3 py-2">
        <div class="position-relative">
            <input type="text" id="sidebarSearchInput" class="form-control form-control-sm sidebar-search-input"
                   placeholder="🔍 Tìm module..."
                   aria-label="Tìm kiếm module">
            <button type="button" id="btnClearSidebarSearch"
                    class="btn btn-link text-muted position-absolute p-0 border-0 d-none"
                    style="right: 8px; top: 4px; font-size: 14px; text-decoration: none; outline: none; opacity: 0.7;">
                &times;
            </button>
        </div>
    </div>

    <!-- Recently Visited & Pinned Quick Access Section -->
    <div id="sidebarRecentSection" class="sidebar-recent-section d-none"
         data-admin-id="{{ auth('admin')->id() ?? 'guest' }}">
        <div class="sidebar-recent-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-1 min-w-0">
                <i class="ti ti-clock-play text-primary" style="font-size: 14px;"></i>
                <span class="sidebar-recent-heading text-uppercase fw-bold">{{ __('Truy cập gần đây') }}</span>
                <span id="sidebarRecentCount" class="sidebar-recent-count-badge">0</span>
            </div>
            <div class="sidebar-recent-header-actions d-flex align-items-center gap-1">
                <button type="button" id="btnClearSidebarRecent"
                        class="btn-recent-header-action"
                        title="{{ __('Xóa lịch sử gần đây (giữ lại mục đã ghim)') }}">
                    <i class="ti ti-eraser"></i>
                </button>
                <button type="button" id="btnToggleSidebarRecent"
                        class="btn-recent-header-action"
                        title="{{ __('Thu gọn / Mở rộng') }}">
                    <i class="ti ti-chevron-up" id="iconToggleSidebarRecent"></i>
                </button>
            </div>
        </div>
        <ul class="recent-menu-list" id="sidebarRecentList"></ul>
    </div>

    <!-- Navigation Menu Container -->
    <nav class="sidebar-menu">
        <ul class="menu-list" id="accordionSidebar">
            @foreach ($menu as $index => $item)
                @if (empty($item['permissions']) || auth('admin')->user()->checkPermissions($item['permissions']) || in_array('mevivuDev', $item['permissions']))
                    @php
                        // Determine section headings based on module landmarks
                        $showHeading = null;
                        if ($item['routeName'] === 'admin.dashboard') {
                            $showHeading = 'TỔNG QUAN & BÁO CÁO';
                        } elseif ($item['title'] === 'Quá trình phát triển') {
                            $showHeading = 'THEO DÕI & ĐÁNH GIÁ TRẺ';
                        } elseif ($item['title'] === 'Giao dịch') {
                            $showHeading = 'QUẢN LÝ DỊCH VỤ & NỘI DUNG';
                        } elseif ($item['title'] === 'Khách hàng') {
                            $showHeading = 'NGƯỜI DÙNG & HỖ TRỢ';
                        } elseif ($item['title'] === 'Vai trò' || $item['title'] === 'Admin' || $item['title'] === 'Quản trị viên') {
                            $showHeading = 'HỆ THỐNG & PHÂN QUYỀN';
                        } elseif ($item['title'] === 'Cài đặt') {
                            $showHeading = 'CẤU HÌNH HỆ THỐNG';
                        }

                        $displayTitle = $item['title'];
                    @endphp

                    @if ($showHeading)
                        <li class="sidebar-heading-item">
                            <hr class="sidebar-divider my-2">
                            <div class="sidebar-heading text-uppercase text-muted fw-bold px-3 py-1 fs-11">
                                {{ __($showHeading) }}
                            </div>
                        </li>
                    @endif

                    <li class="menu-item {{ !empty($item['sub']) ? 'has-submenu' : '' }}"
                        data-title="{{ strtolower(__($displayTitle)) }}">
                        @php
                            $hasSub = !empty($item['sub']);
                            $parentHref = '#';
                            if ($hasSub) {
                                foreach ($item['sub'] as $subItem) {
                                    if ($subItem['routeName'] === $item['routeName']) {
                                        if (empty($subItem['permissions']) || auth('admin')->user()->checkPermissions($subItem['permissions']) || in_array('mevivuDev', $subItem['permissions'])) {
                                            $parentHref = $routeName($subItem['routeName'], $subItem['param'] ?? []);
                                            break;
                                        }
                                    }
                                }
                                if ($parentHref === '#') {
                                    foreach ($item['sub'] as $subItem) {
                                        if (empty($subItem['permissions']) || auth('admin')->user()->checkPermissions($subItem['permissions']) || in_array('mevivuDev', $subItem['permissions'])) {
                                            $parentHref = $routeName($subItem['routeName'], $subItem['param'] ?? []);
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $parentHref = $item['routeName'] ? $routeName($item['routeName'], $item['param'] ?? []) : '#';
                            }
                            $isTransactionParent = ($item['title'] === 'Giao dịch') || (isset($item['sub']) && collect($item['sub'])->contains('routeName', RouteAdminSystem::TRANSACTION_WITHDRAW));
                        @endphp

                        <x-admin-item-link-sidebar-left class="menu-link" :href="$parentHref" :dropdown="false">
                            <span class="menu-icon position-relative">
                                {!! __($item['icon']) !!}
                                @if ($isTransactionParent)
                                    <span class="mini-badge-dot withdraw-mini-dot {{ (!empty($pendingWithdrawCount) && $pendingWithdrawCount > 0) ? '' : 'd-none' }}"></span>
                                @endif
                            </span>
                            <span class="menu-title">{{ __($displayTitle) }}</span>

                            <div class="menu-right-actions ms-auto d-flex align-items-center">
                                @if ($isTransactionParent)
                                    <span
                                        class="badge bg-danger rounded-pill px-2 py-1 fs-11 fw-bold sidebar-badge-counter withdraw-badge-counter shadow-sm {{ (!empty($pendingWithdrawCount) && $pendingWithdrawCount > 0) ? '' : 'd-none' }}"
                                        title="{{ __('Có :count yêu cầu rút tiền chờ duyệt', ['count' => $pendingWithdrawCount ?? 0]) }}"
                                        style="{{ (!empty($pendingWithdrawCount) && $pendingWithdrawCount > 0) ? '' : 'display: none !important;' }}">
                                        {{ (!empty($pendingWithdrawCount) && $pendingWithdrawCount > 99) ? '99+' : ($pendingWithdrawCount ?? 0) }}
                                    </span>
                                @endif

                                <span class="arrow-slot text-end">
                                    @if ($hasSub)
                                        <i class="ti ti-chevron-right submenu-arrow"></i>
                                    @endif
                                </span>
                            </div>
                        </x-admin-item-link-sidebar-left>

                        @if ($hasSub)
                            <div class="submenu-container">
                                <ul class="submenu-list">
                                    @foreach ($item['sub'] as $subItem)
                                        @if (empty($subItem['permissions']) || auth('admin')->user()->checkPermissions($subItem['permissions']) || in_array('mevivuDev', $subItem['permissions']))
                                            <li class="submenu-item"
                                                data-title="{{ strtolower(__($subItem['title'])) }}">
                                                <x-admin-item-link-sidebar-left class="submenu-link"
                                                                                :href="$routeName($subItem['routeName'], $subItem['param'] ?? [])">
                                                    <span class="submenu-icon">
                                                        {!! __($subItem['icon']) !!}
                                                    </span>
                                                    <span
                                                        class="submenu-text me-auto">{{ __($subItem['title']) }}</span>
                                                    @if (isset($subItem['routeName']) && $subItem['routeName'] === RouteAdminSystem::TRANSACTION_WITHDRAW)
                                                        <span
                                                            class="badge bg-danger rounded-pill px-2 py-1 fs-11 fw-bold sidebar-badge-counter withdraw-badge-counter shadow-sm ms-3 {{ (!empty($pendingWithdrawCount) && $pendingWithdrawCount > 0) ? '' : 'd-none' }}"
                                                            title="{{ __('Có :count yêu cầu rút tiền chờ duyệt', ['count' => $pendingWithdrawCount ?? 0]) }}"
                                                            style="{{ (!empty($pendingWithdrawCount) && $pendingWithdrawCount > 0) ? '' : 'display: none !important;' }}">
                                                            {{ (!empty($pendingWithdrawCount) && $pendingWithdrawCount > 99) ? '99+' : ($pendingWithdrawCount ?? 0) }}
                                                        </span>
                                                    @endif
                                                    @if (isset($subItem['routeName']) && $subItem['routeName'] === RouteAdminSystem::KYC_INDEX)
                                                        <span
                                                            class="badge bg-warning text-white rounded-pill px-2 py-1 fs-11 fw-bold sidebar-badge-counter kyc-badge-counter shadow-sm ms-3 {{ (!empty($pendingKycCount) && $pendingKycCount > 0) ? '' : 'd-none' }}"
                                                            title="{{ __('Có :count hồ sơ CCCD chờ duyệt', ['count' => $pendingKycCount ?? 0]) }}"
                                                            style="{{ (!empty($pendingKycCount) && $pendingKycCount > 0) ? '' : 'display: none !important;' }}">
                                                            {{ (!empty($pendingKycCount) && $pendingKycCount > 99) ? '99+' : ($pendingKycCount ?? 0) }}
                                                        </span>
                                                    @endif
                                                </x-admin-item-link-sidebar-left>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    </nav>
</aside>

<style>
    /* ==========================================
       SIDEBAR DEFAULT STYLES (EXPANDED 250px)
       ========================================== */
    /* Remove all link underlines in sidebar */
    .sidebar a,
    .sidebar a:hover,
    .sidebar a:focus,
    .sidebar a:active,
    .menu-link,
    .menu-link:hover,
    .menu-link:focus,
    .submenu-link,
    .submenu-link:hover,
    .submenu-link:focus,
    .submenu-text,
    .menu-title,
    .brand-text,
    .brand-subtitle {
        text-decoration: none !important;
    }

    /* Sidebar Badge Counter & Arrow Styling */
    .menu-right-actions {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .arrow-slot {
        width: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: flex-end;
        flex-shrink: 0;
        margin-left: auto;
    }

    .sidebar-badge-counter,
    .sidebar .badge.bg-danger {
        background: #ef4444 !important;
        color: #ffffff !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        min-width: 20px !important;
        height: 20px !important;
        padding: 0 6px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        border-radius: 10px !important;
        box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3) !important;
        line-height: 1 !important;
        margin: 0 !important;
        flex-shrink: 0 !important;
    }
    .sidebar-badge-counter.d-none {
        display: none !important;
    }

    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100vh;
        height: 100dvh;
        max-height: 100dvh;
        background: #ffffff;
        z-index: 1001;
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 12px rgba(0, 0, 0, 0.05);
        border-right: 1px solid #eef2f6;
        transition: all 0.3s ease;
    }

    .page-wrapper {
        margin-left: 250px !important;
        padding: 0 24px 24px 24px;
        transition: margin-left 0.3s ease;
    }

    /* Logo Section */
    .sidebar-logo {
        min-height: 72px;
        height: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #ffffff;
        border-bottom: 1px solid #eef2f6;
        padding: 10px 16px;
        flex-shrink: 0;
    }

    .logo-wrapper {
        width: 44px;
        height: 44px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
        border: 1px solid #e2e8f0;
        transition: all 0.25s ease;
    }

    .logo-img {
        width: 32px;
        height: 32px;
        object-fit: contain;
    }

    .brand-text {
        font-size: 15px;
        font-weight: 700;
        letter-spacing: -0.2px;
        color: #0f172a;
        line-height: 1.2;
        transition: opacity 0.2s ease;
    }

    .brand-subtitle {
        font-size: 11px;
        font-weight: 600;
        color: #2563eb;
        letter-spacing: 0.2px;
        line-height: 1.1;
        margin-top: 2px;
    }

    /* Search Container */
    .sidebar-search-container {
        border-bottom: 1px solid #f1f5f9;
        background: #fafafa;
        transition: all 0.3s ease;
    }

    .sidebar-search-input {
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 20px;
        padding: 5px 24px 5px 12px;
        font-size: 12px;
        height: 32px;
        color: #334155;
        transition: all 0.2s ease;
    }

    .sidebar-search-input:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }

    /* Section Headings */
    .sidebar-heading {
        font-size: 11px;
        letter-spacing: 0.5px;
        color: #94a3b8 !important;
        white-space: nowrap;
    }

    .sidebar-divider {
        border-top: 1px solid #f1f5f9;
        opacity: 1;
        margin: 6px 16px;
    }

    /* Menu Container */
    .sidebar-menu {
        flex: 1;
        overflow-y: auto;
        overflow-x: visible;
        padding: 8px 0 80px 0;
        scrollbar-width: none;
        -ms-overflow-style: none;
        -webkit-overflow-scrolling: touch;
    }

    .sidebar-menu::-webkit-scrollbar {
        display: none;
    }

    .menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    /* Menu Item Styling (Expanded Mode: Row Layout) */
    .menu-item {
        position: relative;
        margin: 2px 10px;
    }

    .menu-link {
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: flex-start;
        padding: 10px 14px;
        width: 100%;
        color: #475569;
        text-decoration: none;
        transition: all 0.2s ease;
        position: relative;
        cursor: pointer;
        gap: 12px;
        border-radius: 8px;
    }

    .menu-link:hover {
        color: #2563eb;
        background: #f0f6ff;
    }

    .menu-link.active {
        color: #2563eb;
        background: #eff6ff;
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.08);
    }

    .menu-icon {
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 24px;
    }

    .menu-icon i,
    .menu-icon svg {
        font-size: 20px;
        width: 20px;
        height: 20px;
    }

    .menu-title {
        font-size: 14px;
        color: inherit;
        white-space: nowrap;
        flex: 1;
    }

    .submenu-arrow {
        font-size: 14px;
        transition: transform 0.2s ease;
        color: #94a3b8;
        flex-shrink: 0;
        margin-left: 6px;
    }

    /* Submenu Container (Expanded Mode: Inline Accordion) */
    .submenu-container {
        display: none;
        padding-left: 12px;
        margin-top: 4px;
    }

    .menu-item.has-submenu.open .submenu-container {
        display: block;
    }

    .menu-item.has-submenu.open .submenu-arrow {
        transform: rotate(90deg);
    }

    .submenu-list {
        list-style: none;
        padding: 0;
        margin: 0;
        border-left: 2px solid #e2e8f0;
    }

    .submenu-link {
        display: flex;
        align-items: center;
        padding: 8px 14px;
        color: #64748b;
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 13px;
        border-radius: 6px;
        margin: 2px 0 2px 8px;
    }

    .submenu-link:hover {
        color: #2563eb;
        background: #f1f5f9;
    }

    .submenu-link.active {
        color: #2563eb;
        background: #e0edff;
        font-weight: 600;
    }

    .submenu-icon {
        margin-right: 8px;
        font-size: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .submenu-text {
        flex: 1;
    }

    .sidebar-no-result {
        padding: 16px 8px;
        text-align: center;
        color: #94a3b8;
        font-size: 12px;
    }

    /* ==========================================
       SIDEBAR COLLAPSED / MINI STYLES (80px)
       ========================================== */
    body.sidebar-mini .sidebar {
        width: 80px;
    }

    body.sidebar-mini .page-wrapper {
        margin-left: 80px !important;
    }

    body.sidebar-mini .brand-details,
    body.sidebar-mini .sidebar-toggle-btn,
    body.sidebar-mini .menu-title,
    body.sidebar-mini .submenu-arrow,
    body.sidebar-mini .menu-right-actions,
    body.sidebar-mini .sidebar-badge-counter,
    body.sidebar-mini .sidebar-heading {
        display: none !important;
    }

    /* Smart Notification Dot for Mini Mode */
    .mini-badge-dot {
        display: none;
    }
    body.sidebar-mini .mini-badge-dot {
        display: block !important;
        position: absolute;
        top: -1px;
        right: -1px;
        width: 8px;
        height: 8px;
        background-color: #ef4444;
        border: 2px solid #ffffff;
        border-radius: 50%;
        box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.4);
        z-index: 5;
    }
    body.sidebar-mini .mini-badge-dot.d-none {
        display: none !important;
    }

    body.sidebar-mini .sidebar-logo {
        padding: 10px;
        justify-content: center;
    }

    body.sidebar-mini .logo-wrapper {
        margin: 0 auto;
    }

    body.sidebar-mini .sidebar-search-container {
        padding: 8px 6px;
    }

    body.sidebar-mini .sidebar-search-input {
        padding: 5px 8px;
        text-align: center;
    }

    body.sidebar-mini .sidebar-search-input::placeholder {
        color: transparent;
    }

    body.sidebar-mini .menu-item {
        margin: 4px 6px;
    }

    body.sidebar-mini .menu-link {
        justify-content: center;
        padding: 12px 6px;
        gap: 0;
    }

    /* Floating Hover Card for Submenu in Mini Mode */
    body.sidebar-mini .menu-item.has-submenu .submenu-container {
        position: fixed;
        left: 80px;
        min-width: 245px;
        background: #ffffff;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.15);
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 8px;
        display: block !important;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease;
        transition-delay: 0.15s; /* Delay hiding so cursor has time to bridge over */
        z-index: 10000;
        margin-top: 0;
    }

    /* Invisible hover bridge connecting sidebar menu-item to floating submenu */
    body.sidebar-mini .menu-item.has-submenu .submenu-container::before {
        content: '';
        position: absolute;
        right: 100%;
        top: -15px;
        bottom: -15px;
        width: 30px;
        background: transparent;
    }

    body.sidebar-mini .menu-item.has-submenu:hover .submenu-container,
    body.sidebar-mini .menu-item.has-submenu .submenu-container:hover {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition-delay: 0s; /* Instant show on hover */
    }

    body.sidebar-mini .submenu-list {
        border-left: none;
    }

    /* ==========================================
       RESPONSIVE MOBILE/TABLET (< 992px)
       ========================================== */
    @media (max-width: 991.98px) {
        .sidebar {
            left: -290px !important;
            width: 280px !important;
            height: 100vh !important;
            height: 100dvh !important;
            max-height: 100dvh !important;
            top: 0 !important;
            bottom: 0 !important;
            z-index: 100001 !important;
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.45);
            backdrop-filter: blur(2px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 100000 !important;
        }

        body.mobile-sidebar-open .sidebar-backdrop {
            opacity: 1;
            visibility: visible;
        }

        body.sidebar-mini .sidebar {
            left: -290px !important;
            width: 280px !important;
        }

        body.mobile-sidebar-open .sidebar,
        body.sidebar-mini.mobile-sidebar-open .sidebar {
            left: 0 !important;
            width: 280px !important;
            z-index: 100005 !important;
        }

        .page-wrapper {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 10px 8px !important;
        }

        body.sidebar-mini .page-wrapper {
            margin-left: 0 !important;
        }
    }

    /* ==========================================
       RECENTLY VISITED & PINNED SECTION STYLES
       ========================================== */
    .sidebar-recent-section {
        margin: 8px 10px 4px 10px;
        padding: 6px;
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    .sidebar-recent-header {
        padding: 2px 6px 4px 6px;
        user-select: none;
    }

    .sidebar-recent-heading {
        font-size: 10.5px;
        letter-spacing: 0.4px;
        color: #1e40af;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .sidebar-recent-count-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 17px;
        height: 16px;
        padding: 0 5px;
        font-size: 10px;
        font-weight: 700;
        color: #2563eb;
        background: #dbeafe;
        border-radius: 10px;
        line-height: 1;
    }

    .btn-recent-header-action {
        border: none;
        background: transparent;
        color: #64748b;
        width: 22px;
        height: 22px;
        border-radius: 5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        padding: 0;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .btn-recent-header-action:hover {
        color: #2563eb;
        background: #e2e8f0;
    }

    .recent-menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 195px;
        overflow-y: auto;
        scrollbar-width: none;
    }

    .recent-menu-list::-webkit-scrollbar {
        display: none;
    }

    .sidebar-recent-section.is-collapsed .recent-menu-list {
        display: none;
    }

    .sidebar-recent-section.is-collapsed .sidebar-recent-header {
        padding-bottom: 2px;
    }

    .recent-menu-item {
        position: relative;
        margin: 2px 0;
    }

    .recent-menu-link {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 5px 7px;
        border-radius: 7px;
        color: #334155;
        text-decoration: none !important;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }

    .recent-menu-link:hover {
        background: #ffffff;
        color: #2563eb;
        border-color: #dbeafe;
        box-shadow: 0 1px 3px rgba(15, 23, 42, 0.05);
    }

    .recent-menu-link.active {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
        font-weight: 600;
    }

    .recent-item-icon {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #2563eb;
        flex-shrink: 0;
        position: relative;
    }

    .recent-item-icon i,
    .recent-item-icon svg {
        font-size: 14px;
        width: 14px;
        height: 14px;
    }

    .recent-menu-item.is-pinned .recent-item-icon {
        background: #fffbeb;
        border-color: #fde68a;
        color: #d97706;
    }

    .recent-pin-dot {
        display: none;
        position: absolute;
        top: -2px;
        right: -2px;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #f59e0b;
        border: 1.5px solid #ffffff;
    }

    .recent-item-content {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        line-height: 1.2;
    }

    .recent-item-title {
        font-size: 12.5px;
        font-weight: 600;
        color: inherit;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .recent-item-parent {
        font-size: 10.5px;
        color: #64748b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-top: 1px;
    }

    .recent-item-actions {
        display: flex;
        align-items: center;
        gap: 2px;
        flex-shrink: 0;
    }

    .btn-recent-item-action {
        border: none;
        background: transparent;
        color: #94a3b8;
        width: 20px;
        height: 20px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        padding: 0;
        cursor: pointer;
        opacity: 0;
        transition: all 0.15s ease;
    }

    .recent-menu-item:hover .btn-recent-item-action {
        opacity: 1;
    }

    .btn-recent-pin.is-pinned {
        opacity: 1;
        color: #f59e0b;
    }

    .btn-recent-pin:hover {
        color: #d97706 !important;
        background: #fef3c7;
    }

    .btn-recent-remove:hover {
        color: #ef4444 !important;
        background: #fee2e2;
    }

    /* Mini Sidebar Mode (80px) Support for Recent Section */
    body.sidebar-mini:not(.mobile-sidebar-open) .sidebar-recent-section {
        margin: 6px 6px 2px 6px;
        padding: 4px 2px;
    }

    body.sidebar-mini:not(.mobile-sidebar-open) .sidebar-recent-header,
    body.sidebar-mini:not(.mobile-sidebar-open) .recent-item-content,
    body.sidebar-mini:not(.mobile-sidebar-open) .recent-item-actions {
        display: none !important;
    }

    body.sidebar-mini:not(.mobile-sidebar-open) .sidebar-recent-section.is-collapsed .recent-menu-list {
        display: block;
    }

    body.sidebar-mini:not(.mobile-sidebar-open) .recent-menu-link {
        justify-content: center;
        padding: 6px 4px;
    }

    body.sidebar-mini:not(.mobile-sidebar-open) .recent-item-icon {
        width: 32px;
        height: 32px;
        font-size: 16px;
    }

    body.sidebar-mini:not(.mobile-sidebar-open) .recent-item-icon i,
    body.sidebar-mini:not(.mobile-sidebar-open) .recent-item-icon svg {
        font-size: 16px;
        width: 16px;
        height: 16px;
    }

    body.sidebar-mini:not(.mobile-sidebar-open) .recent-menu-item.is-pinned .recent-pin-dot {
        display: block;
    }
</style>

<script src="{{ asset('public/libs/jquery/jquery.min.js') }}"></script>

<script>
    $(document).ready(function () {
        // Toggle Sidebar Expand / Collapse Mode
        const $body = $('body');
        const $sidebarToggleBtn = $('#btnToggleSidebar, #sidebarToggle');

        // Check stored state
        if (localStorage.getItem('sidebar-mini') === '1') {
            $body.addClass('sidebar-mini');
        }

        $sidebarToggleBtn.on('click', function (e) {
            e.preventDefault();
            if ($(window).width() < 992) {
                $body.toggleClass('mobile-sidebar-open');
            } else {
                $body.toggleClass('sidebar-mini');
                if ($body.hasClass('sidebar-mini')) {
                    localStorage.setItem('sidebar-mini', '1');
                } else {
                    localStorage.setItem('sidebar-mini', '0');
                }
            }
        });

        // Đóng Offcanvas Sidebar khi click màn che mờ
        $('#sidebarBackdrop').on('click', function () {
            $body.removeClass('mobile-sidebar-open');
        });

        // Submenu Accordion Toggle for Expanded Mode
        $('.menu-item.has-submenu > .menu-link').on('click', function (e) {
            if (!$body.hasClass('sidebar-mini')) {
                e.preventDefault();
                const $parentItem = $(this).closest('.menu-item');
                $parentItem.toggleClass('open');
            }
        });

        // Position Floating Submenu in Mini Mode
        $('.menu-item.has-submenu').on('mouseenter', function () {
            if ($body.hasClass('sidebar-mini')) {
                const $submenu = $(this).find('.submenu-container');
                const menuItemRect = this.getBoundingClientRect();
                const windowHeight = $(window).height();
                let topPosition = menuItemRect.top;
                const maxSubmenuHeight = windowHeight - 40;

                $submenu.find('.submenu-list').css('max-height', maxSubmenuHeight + 'px');
                const submenuHeight = Math.min($submenu.outerHeight() || 200, maxSubmenuHeight);

                if (topPosition + submenuHeight > windowHeight - 20) {
                    topPosition = Math.max(20, windowHeight - submenuHeight - 20);
                }

                $submenu.css({
                    'top': topPosition + 'px'
                });
            }
        });

        // Live Module Search Logic (Real-time FE Filtering)
        const $searchInput = $('#sidebarSearchInput');
        const $clearBtn = $('#btnClearSidebarSearch');

        $searchInput.on('keyup input search', function () {
            const query = $.trim($(this).val()).toLowerCase();

            if (query.length > 0) {
                $clearBtn.removeClass('d-none');
            } else {
                $clearBtn.addClass('d-none');
            }

            filterSidebarMenu(query);
        });

        $clearBtn.on('click', function () {
            $searchInput.val('');
            $clearBtn.addClass('d-none');
            filterSidebarMenu('');
            $searchInput.focus();
        });

        function filterSidebarMenu(query) {
            const $menuItems = $('.menu-item');
            const $headings = $('.sidebar-heading-item');
            let hasMatch = false;

            if (!query) {
                $menuItems.show();
                $headings.show();
                $('.submenu-item').show();
                $('#sidebarNoResult').remove();
                if (typeof renderRecentSidebarItems === 'function') {
                    renderRecentSidebarItems();
                }
                return;
            }

            $('#sidebarRecentSection').addClass('d-none');

            $menuItems.each(function () {
                const $item = $(this);
                const itemTitle = $item.find('.menu-title').text().toLowerCase();
                const $subItems = $item.find('.submenu-item');
                let itemMatched = false;

                if (itemTitle.indexOf(query) !== -1) {
                    itemMatched = true;
                    $subItems.show();
                } else if ($subItems.length > 0) {
                    let subMatchedCount = 0;
                    $subItems.each(function () {
                        const $sub = $(this);
                        const subTitle = $sub.find('.submenu-text').text().toLowerCase();
                        if (subTitle.indexOf(query) !== -1) {
                            $sub.show();
                            subMatchedCount++;
                        } else {
                            $sub.hide();
                        }
                    });

                    if (subMatchedCount > 0) {
                        itemMatched = true;
                        $item.addClass('open');
                    }
                }

                if (itemMatched) {
                    $item.show();
                    hasMatch = true;
                } else {
                    $item.hide();
                }
            });

            $headings.hide();

            let $noResult = $('#sidebarNoResult');
            if (!hasMatch) {
                if (!$noResult.length) {
                    $('#accordionSidebar').append(`
                        <li id="sidebarNoResult" class="sidebar-no-result">
                            <i class="ti ti-search-off fs-4 mb-1 text-primary d-block"></i>
                            <span>{{ __('Không tìm thấy module') }}</span>
                        </li>
                    `);
                }
            } else {
                $noResult.remove();
            }
        }

        // Auto Active & Auto Open Submenu for Current Route (Best/Exact Match)
        const currentUrl = window.location.href.split(/[?#]/)[0];
        let $bestMatch = null;
        let maxMatchLength = 0;

        $('.submenu-link').each(function () {
            const href = $(this).attr('href');
            if (href && href !== '#') {
                const linkUrl = href.split(/[?#]/)[0];
                if (currentUrl === linkUrl) {
                    $bestMatch = $(this);
                    maxMatchLength = 99999;
                } else if (currentUrl.startsWith(linkUrl + '/') && linkUrl.length > maxMatchLength && maxMatchLength < 99999) {
                    $bestMatch = $(this);
                    maxMatchLength = linkUrl.length;
                }
            }
        });

        const $sidebarMenu = $('.sidebar-menu');
        let $activeTarget = null;

        if ($bestMatch && $bestMatch.length) {
            $bestMatch.addClass('active');
            const $menuItem = $bestMatch.closest('.menu-item');
            $menuItem.addClass('open');
            $menuItem.find('> .menu-link').addClass('active');
            $activeTarget = $bestMatch; // Cuộn trực tiếp đến submenu link đang active
        } else {
            $('.menu-link').each(function () {
                if ($(this).hasClass('active')) return;
                const href = $(this).attr('href');
                if (href && href !== '#') {
                    const linkUrl = href.split(/[?#]/)[0];
                    if (currentUrl === linkUrl || currentUrl.startsWith(linkUrl + '/')) {
                        $(this).addClass('active');
                        $activeTarget = $(this);
                    }
                }
            });
        }

        // =========================================================================
        // RECENTLY VISITED & PINNED MODULES LOGIC (Max 5 items, Per-Admin Scoped)
        // =========================================================================
        const $recentSection = $('#sidebarRecentSection');
        const $recentList = $('#sidebarRecentList');
        const $recentCountBadge = $('#sidebarRecentCount');
        const $iconToggleRecent = $('#iconToggleSidebarRecent');
        const adminId = $recentSection.attr('data-admin-id') || 'guest';
        const RECENT_STORAGE_KEY = 'admin_sidebar_recent_v1_' + adminId;
        const RECENT_COLLAPSED_KEY = 'admin_sidebar_recent_collapsed_' + adminId;
        const MAX_RECENT_ITEMS = 5;

        function normalizeSidebarUrl(url) {
            if (!url) return '';
            return String(url).split(/[?#]/)[0].replace(/\/+$/, '');
        }

        function escapeHtml(str) {
            return String(str || '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        // Thu thập danh sách tất cả các link hợp lệ mà tài khoản Admin hiện tại được phân quyền
        const permittedLinksMap = {};
        $('#accordionSidebar .submenu-link, #accordionSidebar > .menu-item:not(.has-submenu) > .menu-link').each(function () {
            const $link = $(this);
            const href = $link.attr('href');
            if (!href || href === '#') return;

            const normUrl = normalizeSidebarUrl(href);
            if (!normUrl) return;

            const isSubmenu = $link.hasClass('submenu-link');
            const title = $.trim($link.find(isSubmenu ? '.submenu-text' : '.menu-title').first().text());
            const parentTitle = isSubmenu
                ? $.trim($link.closest('.menu-item').find('> .menu-link .menu-title').first().text())
                : '';

            const $iconClone = $link.find(isSubmenu ? '.submenu-icon' : '.menu-icon').first().clone();
            $iconClone.find('.mini-badge-dot, .sidebar-badge-counter').remove();
            const iconHtml = $.trim($iconClone.html()) || '<i class="ti ti-circle"></i>';

            permittedLinksMap[normUrl] = {
                url: href,
                normUrl: normUrl,
                title: title,
                parentTitle: parentTitle,
                iconHtml: iconHtml
            };
        });

        function loadRecentItems() {
            try {
                const raw = localStorage.getItem(RECENT_STORAGE_KEY);
                const parsed = raw ? JSON.parse(raw) : [];
                if (!Array.isArray(parsed)) return [];

                // Chỉ giữ lại các mục nằm trong danh sách được phân quyền của Admin hiện tại
                const validItems = [];
                const seenUrls = {};

                for (let i = 0; i < parsed.length; i++) {
                    const item = parsed[i];
                    if (!item || !item.url) continue;
                    const normUrl = normalizeSidebarUrl(item.url);
                    if (!normUrl || seenUrls[normUrl] || !permittedLinksMap[normUrl]) continue;

                    seenUrls[normUrl] = true;
                    const meta = permittedLinksMap[normUrl];
                    validItems.push({
                        url: meta.url,
                        normUrl: normUrl,
                        title: meta.title,
                        parentTitle: meta.parentTitle,
                        iconHtml: meta.iconHtml,
                        pinned: Boolean(item.pinned),
                        visitedAt: Number(item.visitedAt) || 0,
                        pinnedAt: Number(item.pinnedAt) || 0
                    });
                }

                return sortAndTrimRecentItems(validItems);
            } catch (e) {
                return [];
            }
        }

        function sortAndTrimRecentItems(items) {
            const pinned = items.filter(function (x) { return x.pinned; })
                .sort(function (a, b) { return (b.pinnedAt || b.visitedAt) - (a.pinnedAt || a.visitedAt); });
            const unpinned = items.filter(function (x) { return !x.pinned; })
                .sort(function (a, b) { return b.visitedAt - a.visitedAt; });

            return pinned.concat(unpinned).slice(0, MAX_RECENT_ITEMS);
        }

        function saveRecentItems(items) {
            try {
                const trimmed = sortAndTrimRecentItems(items);
                localStorage.setItem(RECENT_STORAGE_KEY, JSON.stringify(trimmed));
                return trimmed;
            } catch (e) {
                return items;
            }
        }

        function recordSidebarVisit(href) {
            const normUrl = normalizeSidebarUrl(href);
            if (!normUrl || !permittedLinksMap[normUrl]) return;

            const meta = permittedLinksMap[normUrl];
            const items = loadRecentItems();
            const now = Date.now();
            let found = false;

            for (let i = 0; i < items.length; i++) {
                if (items[i].normUrl === normUrl) {
                    items[i].visitedAt = now;
                    found = true;
                    break;
                }
            }

            if (!found) {
                // Nếu danh sách đã đầy 5 mục mà thêm mục mới, giữ tối đa (MAX_RECENT_ITEMS - 1) mục cũ ưu tiên đã ghim
                items.push({
                    url: meta.url,
                    normUrl: normUrl,
                    title: meta.title,
                    parentTitle: meta.parentTitle,
                    iconHtml: meta.iconHtml,
                    pinned: false,
                    visitedAt: now,
                    pinnedAt: 0
                });
            }

            saveRecentItems(items);
        }

        const activeNormUrl = ($activeTarget && $activeTarget.length)
            ? normalizeSidebarUrl($activeTarget.attr('href'))
            : normalizeSidebarUrl(currentUrl);

        // Tự động ghi nhận trang đang mở hiện tại vào Truy cập gần đây
        if (activeNormUrl && permittedLinksMap[activeNormUrl]) {
            recordSidebarVisit(permittedLinksMap[activeNormUrl].url);
        }

        // Khôi phục trạng thái thu gọn/mở rộng khối Truy cập gần đây
        if (localStorage.getItem(RECENT_COLLAPSED_KEY) === '1') {
            $recentSection.addClass('is-collapsed');
            $iconToggleRecent.removeClass('ti-chevron-up').addClass('ti-chevron-down');
        }

        function renderRecentSidebarItems() {
            const items = loadRecentItems();
            const query = $.trim($searchInput.val());

            if (!items.length || query.length > 0) {
                $recentSection.addClass('d-none');
                return;
            }

            $recentSection.removeClass('d-none');
            $recentCountBadge.text(items.length);
            $recentList.empty();

            items.forEach(function (item) {
                const isActive = item.normUrl === activeNormUrl;
                const isPinned = Boolean(item.pinned);
                const fullTooltip = item.parentTitle
                    ? (item.parentTitle + ' • ' + item.title)
                    : item.title;

                const parentSubtitleHtml = item.parentTitle
                    ? `<span class="recent-item-parent">${escapeHtml(item.parentTitle)}</span>`
                    : '';

                const $li = $(`
                    <li class="recent-menu-item ${isPinned ? 'is-pinned' : ''}" data-norm-url="${escapeHtml(item.normUrl)}">
                        <a href="${escapeHtml(item.url)}" class="recent-menu-link ${isActive ? 'active' : ''}" title="${escapeHtml(fullTooltip)}">
                            <span class="recent-item-icon">
                                ${item.iconHtml}
                                <span class="recent-pin-dot"></span>
                            </span>
                            <span class="recent-item-content">
                                <span class="recent-item-title">${escapeHtml(item.title)}</span>
                                ${parentSubtitleHtml}
                            </span>
                            <span class="recent-item-actions">
                                <button type="button"
                                        class="btn-recent-item-action btn-recent-pin ${isPinned ? 'is-pinned' : ''}"
                                        title="${isPinned ? 'Bỏ ghim khỏi đầu danh sách' : 'Ghim cố định lên đầu'}">
                                    <i class="ti ${isPinned ? 'ti-pinned-filled' : 'ti-pin'}"></i>
                                </button>
                                <button type="button"
                                        class="btn-recent-item-action btn-recent-remove"
                                        title="Xóa khỏi danh sách gần đây">
                                    <i class="ti ti-x"></i>
                                </button>
                            </span>
                        </a>
                    </li>
                `);

                $recentList.append($li);
            });
        }

        renderRecentSidebarItems();

        // Ghi nhận ngay khi click vào mục menu bất kỳ trên Sidebar
        $('#accordionSidebar').on('click', '.submenu-link, .menu-link', function () {
            const href = $(this).attr('href');
            if (href && href !== '#') {
                recordSidebarVisit(href);
            }
        });

        // Xử lý Ghim / Bỏ ghim một mục
        $recentList.on('click', '.btn-recent-pin', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const normUrl = $(this).closest('.recent-menu-item').attr('data-norm-url');
            const items = loadRecentItems();
            const now = Date.now();

            for (let i = 0; i < items.length; i++) {
                if (items[i].normUrl === normUrl) {
                    items[i].pinned = !items[i].pinned;
                    items[i].pinnedAt = items[i].pinned ? now : 0;
                    break;
                }
            }

            saveRecentItems(items);
            renderRecentSidebarItems();
        });

        // Xử lý Xóa từng mục khỏi danh sách
        $recentList.on('click', '.btn-recent-remove', function (e) {
            e.preventDefault();
            e.stopPropagation();
            const normUrl = $(this).closest('.recent-menu-item').attr('data-norm-url');
            const items = loadRecentItems().filter(function (x) {
                return x.normUrl !== normUrl;
            });

            saveRecentItems(items);
            renderRecentSidebarItems();
        });

        // Xử lý Xóa nhanh lịch sử gần đây (giữ lại các mục đã ghim)
        $('#btnClearSidebarRecent').on('click', function (e) {
            e.preventDefault();
            const pinnedOnly = loadRecentItems().filter(function (x) {
                return x.pinned;
            });
            saveRecentItems(pinnedOnly);
            renderRecentSidebarItems();
        });

        // Xử lý Thu gọn / Mở rộng khối Truy cập gần đây
        $('#btnToggleSidebarRecent').on('click', function (e) {
            e.preventDefault();
            $recentSection.toggleClass('is-collapsed');
            const isCollapsed = $recentSection.hasClass('is-collapsed');
            localStorage.setItem(RECENT_COLLAPSED_KEY, isCollapsed ? '1' : '0');
            if (isCollapsed) {
                $iconToggleRecent.removeClass('ti-chevron-up').addClass('ti-chevron-down');
            } else {
                $iconToggleRecent.removeClass('ti-chevron-down').addClass('ti-chevron-up');
            }
        });

        // Tự động cuộn sidebar đến đúng vị trí menu đang active (khắc phục bị nhảy lên đầu trang)
        function scrollToActiveMenu() {
            if (!$sidebarMenu.length) return;

            if ($activeTarget && $activeTarget.length) {
                // Đợi một khoảng ngắn để DOM render xong chiều cao accordion submenu
                setTimeout(function () {
                    try {
                        const containerHeight = $sidebarMenu.height();
                        const containerTop = $sidebarMenu.offset().top;
                        const itemTop = $activeTarget.offset().top;
                        const currentScroll = $sidebarMenu.scrollTop();

                        // Căn menu đang active nằm ở khoảng 1/3 phía trên tầm mắt
                        const targetScroll = currentScroll + (itemTop - containerTop) - (containerHeight / 3);

                        $sidebarMenu.scrollTop(Math.max(0, targetScroll));
                        sessionStorage.setItem('admin_sidebar_scroll', Math.max(0, targetScroll));
                    } catch (err) {
                        console.error('Lỗi cuộn sidebar:', err);
                    }
                }, 60);
            } else {
                const savedScroll = sessionStorage.getItem('admin_sidebar_scroll');
                if (savedScroll !== null) {
                    $sidebarMenu.scrollTop(parseInt(savedScroll, 10));
                }
            }
        }

        scrollToActiveMenu();

        // Ghi nhớ vị trí scroll khi người dùng tự cuộn sidebar
        $sidebarMenu.on('scroll', function () {
            sessionStorage.setItem('admin_sidebar_scroll', $(this).scrollTop());
        });
    });
</script>
