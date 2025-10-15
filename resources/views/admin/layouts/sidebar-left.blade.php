<!-- Sidebar Collapsed with Hover -->
<aside id="sidebar" class="sidebar-collapsed">
    <!-- Logo -->
    <div class="sidebar-logo">
        <x-link :href="route('admin.dashboard')">
            @php
                $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
                $settings = $settingRepository->getAll();
            @endphp
            <img src="{{ asset($settings->where('setting_key', 'site_logo')->first()->plain_value) }}"
                 alt="Logo" class="logo-img">
        </x-link>
    </div>

    <!-- Mobile Search - Chỉ hiện trên mobile -->
    <div class="sidebar-mobile-search">
        <div class="mobile-search-wrapper">
            <i class="ti ti-search mobile-search-icon"></i>
            <input type="text"
                   id="sidebarMobileSearch"
                   class="mobile-search-input"
                   placeholder="Tìm kiếm menu...">
            <button class="mobile-search-clear" id="mobileClearSearch" style="display: none;">
                <i class="ti ti-x"></i>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-menu">
        <ul class="menu-list">
            @foreach ($menu as $index => $item)
                @if (auth('admin')->user()->checkPermissions($item['permissions']) || in_array('mevivuDev', $item['permissions']))
                    <li class="menu-item {{ count($item['sub']) > 0 ? 'has-submenu' : '' }}">
                        <x-admin-item-link-sidebar-left
                            class="menu-link"
                            :href="count($item['sub']) > 0 ? '#' : $routeName($item['routeName'], $item['param'] ?? [])"
                            :dropdown="false">
                            <span class="menu-icon">
                                {!! __($item['icon']) !!}
                            </span>
                            <span class="menu-tooltip">{{ __($item['title']) }}</span>
                        </x-admin-item-link-sidebar-left>

                        @if (count($item['sub']))
                            <div class="submenu-container">
                                <ul class="submenu-list">
                                    @foreach ($item['sub'] as $subItem)
                                        @if (auth('admin')->user()->checkPermissions($subItem['permissions']) || in_array('mevivuDev', $subItem['permissions']))
                                            <li class="submenu-item">
                                                <x-admin-item-link-sidebar-left
                                                    class="submenu-link"
                                                    :href="$routeName($subItem['routeName'], $subItem['param'] ?? [])">
                                                    <span class="submenu-icon">
                                                        {!! __($subItem['icon']) !!}
                                                    </span>
                                                    <span class="submenu-text">{{ __($subItem['title']) }}</span>
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
    /* Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Sidebar Container */
    .sidebar-collapsed {
        position: fixed;
        top: 0;
        left: 0;
        width: 120px;
        height: 100vh;
        background: #ffffff;
        z-index: 1001;
        display: flex;
        flex-direction: column;
        box-shadow: 2px 0 8px rgba(0, 0, 0, 0.08);
        border-right: 1px solid #f0f0f0;
    }

    /* Logo Section */
    .sidebar-logo {
        height: 64px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
        padding: 8px;
        flex-shrink: 0;
    }

    .sidebar-logo a {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .logo-img {
        width: 40px;
        height: 40px;
        object-fit: contain;
        display: block;
    }

    .sidebar-logo a {
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 40px;
        min-height: 40px;
    }

    /* Menu Container */
    .sidebar-menu {
        flex: 1;
        overflow-y: auto;
        overflow-x: visible;
        padding: 4px 0;
    }

    .sidebar-menu::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-menu::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.15);
        border-radius: 4px;
    }

    .menu-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    /* Menu Item */
    .menu-item {
        position: relative;
        margin: 2px 0;
    }

    .menu-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 8px 4px;
        width: 100px;
        color: rgba(0, 0, 0, 0.65);
        text-decoration: none;
        transition: all 0.3s ease;
        position: relative;
        cursor: pointer;
        gap: 4px;
    }

    .menu-link:hover {
        color: #1890ff;
        background: rgba(24, 144, 255, 0.08);
    }

    .menu-link.active {
        color: #1890ff;
        background: rgba(24, 144, 255, 0.12);
    }

    .menu-link.active::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 3px;
        background: #1890ff;
    }

    /* Menu Icon */
    .menu-icon {
        font-size: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .menu-icon i,
    .menu-icon svg {
        font-size: 20px;
        width: 20px;
        height: 20px;
    }

    /* Tooltip - Hiển thị dưới icon như label */
    .menu-tooltip {
        font-size: 11px;
        line-height: 1.2;
        text-align: center;
        max-width: 92px;
        word-wrap: break-word;
        color: inherit;
        opacity: 1;
        pointer-events: auto;
        position: static;
        transform: none;
        background: transparent;
        padding: 0;
        box-shadow: none;
        border: none;
        transition: none;
        z-index: auto;
    }

    .menu-tooltip::before {
        display: none;
    }

    /* Submenu Container */
    .submenu-container {
        position: fixed;
        left: 100px;
        min-width: 220px;
        max-width: 280px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        border-radius: 4px;
        opacity: 0;
        visibility: hidden;
        pointer-events: none;
        transition: all 0.2s cubic-bezier(0.645, 0.045, 0.355, 1);
        z-index: 10000;
        border: 1px solid #f0f0f0;
    }

    /* Tạo vùng hover "cầu nối" để không bị mất hover */
    .submenu-container::before {
        content: '';
        position: absolute;
        right: 100%;
        top: 0;
        bottom: 0;
        width: 20px;
        background: transparent;
    }

    /* CRITICAL: Show submenu on hover */
    .menu-item.has-submenu:hover .submenu-container {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
        transition-delay: 0s;
    }

    /* Keep submenu visible when hovering over it */
    .submenu-container:hover {
        opacity: 1;
        visibility: visible;
        pointer-events: auto;
    }

    /* Loại bỏ delay khi hover ra để tránh mất submenu quá nhanh */
    .menu-item.has-submenu .submenu-container {
        transition: opacity 0.15s ease, visibility 0.15s ease;
    }

    .menu-item.has-submenu:not(:hover) .submenu-container {
        transition-delay: 0.1s;
    }

    /* Submenu List */
    .submenu-list {
        list-style: none;
        padding: 8px 0;
        margin: 0;
        max-height: 400px;
        overflow-y: auto;
    }

    .submenu-list::-webkit-scrollbar {
        width: 6px;
    }

    .submenu-list::-webkit-scrollbar-thumb {
        background: #d9d9d9;
        border-radius: 4px;
    }

    .submenu-item {
        margin: 0;
    }

    .submenu-link {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        color: rgba(0, 0, 0, 0.65);
        text-decoration: none;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .submenu-link:hover {
        color: #1890ff;
        background: #e6f7ff;
    }

    .submenu-link.active {
        color: #1890ff;
        background: #e6f7ff;
        font-weight: 500;
    }

    .submenu-icon {
        margin-right: 10px;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 20px;
    }

    .submenu-icon i,
    .submenu-icon svg {
        font-size: 16px;
        width: 16px;
        height: 16px;
    }

    .submenu-text {
        flex: 1;
    }

    /* Page Wrapper Adjustment */
    .page-wrapper {
        margin-left: 100px;
        transition: margin-left 0.3s ease;
    }

    /* Mobile Search - Ẩn trên desktop */
    .sidebar-mobile-search {
        display: none;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .sidebar-collapsed {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            width: 280px;
        }

        .sidebar-collapsed.mobile-open {
            transform: translateX(0);
        }

        /* Logo lớn hơn trên mobile */
        .sidebar-logo {
            height: 70px;
        }

        .logo-img {
            width: 48px;
            height: 48px;
        }

        /* Mobile Search - Hiện trên mobile */
        .sidebar-mobile-search {
            display: block;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .mobile-search-wrapper {
            position: relative;
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid #e8e8e8;
            border-radius: 6px;
        }

        .mobile-search-icon {
            position: absolute;
            left: 10px;
            color: rgba(0, 0, 0, 0.45);
            font-size: 16px;
            pointer-events: none;
        }

        .mobile-search-input {
            width: 100%;
            padding: 8px 36px 8px 36px;
            border: none;
            background: transparent;
            outline: none;
            font-size: 14px;
            color: rgba(0, 0, 0, 0.85);
        }

        .mobile-search-input::placeholder {
            color: rgba(0, 0, 0, 0.45);
        }

        .mobile-search-clear {
            position: absolute;
            right: 6px;
            background: transparent;
            border: none;
            color: rgba(0, 0, 0, 0.45);
            cursor: pointer;
            padding: 4px;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-search-clear:active {
            background: rgba(0, 0, 0, 0.06);
        }

        /* Menu items mở rộng trên mobile */
        .menu-item {
            margin: 0;
        }

        .menu-link {
            flex-direction: row;
            justify-content: flex-start;
            width: 100%;
            padding: 12px 16px;
            gap: 12px;
        }

        .menu-icon {
            font-size: 22px;
        }

        .menu-icon i,
        .menu-icon svg {
            font-size: 22px;
            width: 22px;
            height: 22px;
        }

        .menu-tooltip {
            display: block !important;
            font-size: 14px;
            text-align: left;
            max-width: none;
            position: static;
            opacity: 1;
        }

        /* Submenu trên mobile */
        .submenu-container {
            position: static !important;
            left: auto !important;
            top: auto !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
            box-shadow: none;
            border: none;
            border-radius: 0;
            background: #f5f5f5;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .submenu-container::before {
            display: none;
        }

        .menu-item.has-submenu.mobile-submenu-open .submenu-container {
            max-height: 500px;
        }

        .submenu-list {
            padding: 0;
        }

        .submenu-item {
            border-bottom: 1px solid #e8e8e8;
        }

        .submenu-item:last-child {
            border-bottom: none;
        }

        .submenu-link {
            padding: 12px 16px 12px 52px;
            background: transparent;
        }

        .submenu-link:hover {
            background: #e0e0e0;
        }

        .submenu-link.active {
            background: #d6f0ff;
        }

        /* Thêm icon mũi tên cho menu có submenu */
        .menu-item.has-submenu .menu-link::after {
            content: '';
            margin-left: auto;
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 6px solid currentColor;
            transition: transform 0.3s ease;
        }

        .menu-item.has-submenu.mobile-submenu-open .menu-link::after {
            transform: rotate(180deg);
        }

        /* Mobile toggle button */
        .mobile-toggle {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
            background: #ffffff;
            color: rgba(0, 0, 0, 0.85);
            border: 1px solid #f0f0f0;
            border-radius: 4px;
            padding: 10px 12px;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .mobile-toggle i,
        .mobile-toggle svg {
            width: 24px;
            height: 24px;
        }

        /* Overlay khi sidebar mở */
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .page-wrapper {
            margin-left: 0;
        }

        /* Cải thiện scroll trên mobile */
        .sidebar-menu {
            padding: 8px 0;
        }

        .sidebar-menu::-webkit-scrollbar {
            width: 6px;
        }
    }
</style>

<script src="{{ asset('public/libs/jquery/jquery.min.js') }}"></script>

<script>
    $(document).ready(function() {
        let submenuUpdateTimeout;
        let isUpdating = false;
        let activeSubmenu = null;

        // Function to update submenu position
        function updateSubmenuPosition($menuItem) {
            if ($(window).width() > 991.98) {
                const $submenu = $menuItem.find('.submenu-container');
                const $sidebar = $('.sidebar-collapsed');

                // Lấy vị trí của menu item so với viewport (không phải document)
                const menuItemRect = $menuItem[0].getBoundingClientRect();
                const sidebarWidth = $sidebar.outerWidth();
                const windowHeight = $(window).height();

                // Tính toán vị trí dựa trên viewport
                let topPosition = menuItemRect.top;
                const maxSubmenuHeight = windowHeight - 40; // Padding top + bottom

                // Điều chỉnh max-height động
                $submenu.find('.submenu-list').css('max-height', maxSubmenuHeight + 'px');

                // Nếu submenu quá cao, căn chỉnh để không bị cắt
                const submenuHeight = Math.min($submenu.outerHeight(), maxSubmenuHeight);

                if (topPosition + submenuHeight > windowHeight - 20) {
                    topPosition = Math.max(20, windowHeight - submenuHeight - 20);
                }

                // Set position với fixed positioning
                $submenu.css({
                    'position': 'fixed',
                    'left': sidebarWidth + 'px',
                    'top': topPosition + 'px'
                });
            }
        }

        // Update all visible submenus
        function updateAllVisibleSubmenus() {
            if (!isUpdating && activeSubmenu) {
                isUpdating = true;
                requestAnimationFrame(function() {
                    updateSubmenuPosition(activeSubmenu);
                    isUpdating = false;
                });
            }
        }

        // Track active submenu on hover
        $('.menu-item.has-submenu').on('mouseenter', function() {
            activeSubmenu = $(this);
            updateSubmenuPosition($(this));
        });

        $('.menu-item.has-submenu').on('mouseleave', function() {
            // Delay clearing to allow hover transition to submenu
            setTimeout(function() {
                if (!$('.submenu-container:hover').length) {
                    activeSubmenu = null;
                }
            }, 100);
        });

        // Keep tracking when hovering submenu
        $(document).on('mouseenter', '.submenu-container', function() {
            activeSubmenu = $(this).closest('.menu-item.has-submenu');
        });

        $(document).on('mouseleave', '.submenu-container', function() {
            activeSubmenu = null;
        });

        // Update submenu positions on scroll - chỉ khi có submenu đang hiển thị
        let scrollTimeout;
        $(window).on('scroll', function() {
            if (activeSubmenu) {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(updateAllVisibleSubmenus, 5);
            }
        });

        // Update on sidebar scroll
        $('.sidebar-menu').on('scroll', function() {
            if (activeSubmenu) {
                clearTimeout(submenuUpdateTimeout);
                submenuUpdateTimeout = setTimeout(updateAllVisibleSubmenus, 5);
            }
        });

        // Active state for current page
        const currentPath = window.location.pathname;

        // Check main menu
        $('.menu-link').each(function() {
            const href = $(this).attr('href');
            if (href && href !== '#' && href === currentPath) {
                $(this).addClass('active');
            }
        });

        // Check submenu
        $('.submenu-link').each(function() {
            const href = $(this).attr('href');
            if (href && href === currentPath) {
                $(this).addClass('active');
                $(this).closest('.menu-item').find('.menu-link').addClass('active');
            }
        });

        // Mobile: Create toggle button if not exists
        if ($(window).width() <= 991.98 && $('#mobileToggle').length === 0) {
            $('body').prepend(`
            <button id="mobileToggle" class="mobile-toggle">
                <i class="ti ti-menu-2"></i>
            </button>
        `);
            if ($('.sidebar-overlay').length === 0) {
                $('body').append('<div class="sidebar-overlay"></div>');
            }
        }

        // Toggle sidebar on mobile
        $(document).on('click', '#mobileToggle', function() {
            $('.sidebar-collapsed').toggleClass('mobile-open');
            $('.sidebar-overlay').toggleClass('active');
            $('body').toggleClass('sidebar-mobile-open');
        });

        // Đóng sidebar khi click overlay
        $(document).on('click', '.sidebar-overlay', function() {
            $('.sidebar-collapsed').removeClass('mobile-open');
            $(this).removeClass('active');
            $('body').removeClass('sidebar-mobile-open');
        });

        // Toggle submenu trên mobile
        $('.menu-item.has-submenu .menu-link').on('click', function(e) {
            if ($(window).width() <= 991.98) {
                e.preventDefault();
                const $menuItem = $(this).closest('.menu-item');
                const isOpen = $menuItem.hasClass('mobile-submenu-open');

                // Đóng tất cả submenu khác
                $('.menu-item.has-submenu').removeClass('mobile-submenu-open');

                // Toggle submenu hiện tại
                if (!isOpen) {
                    $menuItem.addClass('mobile-submenu-open');
                }
            }
        });

        // Close sidebar mobile when click on submenu link
        $('.submenu-link').on('click', function() {
            if ($(window).width() <= 991.98) {
                $('.sidebar-collapsed').removeClass('mobile-open');
                $('.sidebar-overlay').removeClass('active');
                $('body').removeClass('sidebar-mobile-open');
                $('.menu-item.has-submenu').removeClass('mobile-submenu-open');
            }
        });

        // Handle resize
        $(window).on('resize', function() {
            if ($(window).width() > 991.98) {
                $('.sidebar-collapsed').removeClass('mobile-open');
                $('.sidebar-overlay').removeClass('active');
                $('body').removeClass('sidebar-mobile-open');
                $('.menu-item.has-submenu').removeClass('mobile-submenu-open');
                $('#mobileToggle').remove();
                $('.sidebar-overlay').remove();
                activeSubmenu = null;
            } else if ($('#mobileToggle').length === 0) {
                $('body').prepend(`
                <button id="mobileToggle" class="mobile-toggle">
                    <i class="ti ti-menu-2"></i>
                </button>
            `);
                if ($('.sidebar-overlay').length === 0) {
                    $('body').append('<div class="sidebar-overlay"></div>');
                }
            }
        });

        // Ngăn scroll body khi sidebar mở trên mobile
        $(document).on('click', '#mobileToggle', function() {
            if ($('.sidebar-collapsed').hasClass('mobile-open')) {
                $('body').css('overflow', 'hidden');
            } else {
                $('body').css('overflow', '');
            }
        });

        $(document).on('click', '.sidebar-overlay, .submenu-link', function() {
            $('body').css('overflow', '');
        });

        // Mobile Search Functionality
        const mobileSearchInput = $('#sidebarMobileSearch');
        const mobileClearBtn = $('#mobileClearSearch');

        if (mobileSearchInput.length) {
            mobileSearchInput.on('input', function() {
                const value = $(this).val().toLowerCase();
                mobileClearBtn.toggle(value.length > 0);

                if (value) {
                    $('.menu-item').each(function() {
                        const $menuItem = $(this);
                        const menuText = $menuItem.find('.menu-tooltip').text().toLowerCase();
                        let hasMatch = menuText.includes(value);

                        $menuItem.find('.submenu-text').each(function() {
                            if ($(this).text().toLowerCase().includes(value)) {
                                hasMatch = true;
                            }
                        });

                        $menuItem.toggle(hasMatch);
                    });
                } else {
                    $('.menu-item').show();
                }
            });

            mobileClearBtn.on('click', function() {
                mobileSearchInput.val('').trigger('input').focus();
            });
        }
    });
</script>
