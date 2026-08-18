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

    <!-- Navigation Menu Container -->
    <nav class="sidebar-menu">
        <ul class="menu-list" id="accordionSidebar">
            @foreach ($menu as $index => $item)
                @if (auth('admin')->user()->checkPermissions($item['permissions']) || in_array('mevivuDev', $item['permissions']))
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
                        } elseif ($item['title'] === 'Vai trò' || $item['title'] === 'Admin') {
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

                    <li class="menu-item {{ count($item['sub']) > 0 ? 'has-submenu' : '' }}"
                        data-title="{{ strtolower(__($displayTitle)) }}">
                        @php
                            $parentHref = '#';
                            if (count($item['sub']) > 0) {
                                foreach ($item['sub'] as $subItem) {
                                    if ($subItem['routeName'] === $item['routeName']) {
                                        if (auth('admin')->user()->checkPermissions($subItem['permissions']) || in_array('mevivuDev', $subItem['permissions'])) {
                                            $parentHref = $routeName($subItem['routeName'], $subItem['param'] ?? []);
                                            break;
                                        }
                                    }
                                }
                                if ($parentHref === '#') {
                                    foreach ($item['sub'] as $subItem) {
                                        if (auth('admin')->user()->checkPermissions($subItem['permissions']) || in_array('mevivuDev', $subItem['permissions'])) {
                                            $parentHref = $routeName($subItem['routeName'], $subItem['param'] ?? []);
                                            break;
                                        }
                                    }
                                }
                            } else {
                                $parentHref = $item['routeName'] ? $routeName($item['routeName'], $item['param'] ?? []) : '#';
                            }
                        @endphp

                        <x-admin-item-link-sidebar-left class="menu-link" :href="$parentHref" :dropdown="false">
                            <span class="menu-icon position-relative">
                                {!! __($item['icon']) !!}
                            </span>
                            <span class="menu-title">{{ __($displayTitle) }}</span>

                            <div class="menu-right-actions ms-auto d-flex align-items-center">
                                <span class="arrow-slot text-end">
                                    @if (count($item['sub']))
                                        <i class="ti ti-chevron-right submenu-arrow"></i>
                                    @endif
                                </span>
                            </div>
                        </x-admin-item-link-sidebar-left>

                        @if (count($item['sub']))
                            <div class="submenu-container">
                                <ul class="submenu-list">
                                    @foreach ($item['sub'] as $subItem)
                                        @if (auth('admin')->user()->checkPermissions($subItem['permissions']) || in_array('mevivuDev', $subItem['permissions']))
                                            <li class="submenu-item"
                                                data-title="{{ strtolower(__($subItem['title'])) }}">
                                                <x-admin-item-link-sidebar-left class="submenu-link"
                                                                                :href="$routeName($subItem['routeName'], $subItem['param'] ?? [])">
                                                    <span class="submenu-icon">
                                                        {!! __($subItem['icon']) !!}
                                                    </span>
                                                    <span
                                                        class="submenu-text me-auto">{{ __($subItem['title']) }}</span>
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
    body.sidebar-mini .sidebar-heading {
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
                return;
            }

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

        if ($bestMatch) {
            $bestMatch.addClass('active');
            const $menuItem = $bestMatch.closest('.menu-item');
            $menuItem.addClass('open');
            $menuItem.find('> .menu-link').addClass('active');
        } else {
            $('.menu-link').each(function () {
                if ($(this).hasClass('active')) return;
                const href = $(this).attr('href');
                if (href && href !== '#') {
                    const linkUrl = href.split(/[?#]/)[0];
                    if (currentUrl === linkUrl || currentUrl.startsWith(linkUrl + '/')) {
                        $(this).addClass('active');
                    }
                }
            });
        }
    });
</script>
