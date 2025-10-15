<!-- Navbar -->
<header class="modern-header">
    <div class="header-container">
        <div class="header-left">
            <!-- Search Box -->
            <div class="header-search">
                <div class="search-wrapper">
                    <i class="ti ti-search search-icon"></i>
                    <input type="text"
                           id="globalMenuSearch"
                           class="search-input"
                           placeholder="Tìm kiếm menu...">
                    <button class="search-clear" id="clearSearch" style="display: none;">
                        <i class="ti ti-x"></i>
                    </button>
                </div>
                <!-- Search Results Dropdown -->
                <div class="search-results" id="searchResults" style="display: none;">
                    <div class="search-results-content"></div>
                </div>
            </div>
        </div>

        <div class="header-right">
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
    }

    .header-container {
        max-width: 1400px;
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
        margin-left: 20px;
    }

    /* Search Box Styles */
    .header-search {
        position: relative;
        width: 100%;
    }

    .search-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        background: #f5f5f5;
        border: 1px solid #e8e8e8;
        border-radius: 8px;
        transition: all 0.3s ease;
        margin-left: 60px;
    }

    .search-wrapper:focus-within {
        background: #fff;
        border-color: #1890ff;
        box-shadow: 0 0 0 3px rgba(24, 144, 255, 0.1);
    }

    .search-icon {
        position: absolute;
        left: 12px;
        color: rgba(0, 0, 0, 0.45);
        font-size: 18px;
        pointer-events: none;
    }

    .search-input {
        width: 100%;
        padding: 10px 40px 10px 40px;
        border: none;
        background: transparent;
        outline: none;
        font-size: 14px;
        color: rgba(0, 0, 0, 0.85);
    }

    .search-input::placeholder {
        color: rgba(0, 0, 0, 0.45);
    }

    .search-clear {
        position: absolute;
        right: 8px;
        background: transparent;
        border: none;
        color: rgba(0, 0, 0, 0.45);
        cursor: pointer;
        padding: 4px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .search-clear:hover {
        background: rgba(0, 0, 0, 0.06);
        color: rgba(0, 0, 0, 0.85);
    }

    /* Search Results Dropdown */
    .search-results {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #f0f0f0;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        max-height: 400px;
        overflow-y: auto;
        z-index: 1001;
    }

    .search-results-content {
        padding: 8px 0;
    }

    .search-result-item {
        padding: 10px 16px;
        cursor: pointer;
        transition: background 0.2s ease;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        color: inherit;
    }

    .search-result-item:hover {
        background: #f5f5f5;
    }

    .search-result-icon {
        font-size: 18px;
        color: rgba(0, 0, 0, 0.65);
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
    }

    .search-result-text {
        flex: 1;
    }

    .search-result-title {
        font-size: 14px;
        color: rgba(0, 0, 0, 0.85);
        margin-bottom: 2px;
    }

    .search-result-subtitle {
        font-size: 12px;
        color: rgba(0, 0, 0, 0.45);
    }

    .search-no-results {
        padding: 20px;
        text-align: center;
        color: rgba(0, 0, 0, 0.45);
        font-size: 14px;
    }

    .search-category {
        padding: 8px 16px;
        font-size: 12px;
        font-weight: 600;
        color: rgba(0, 0, 0, 0.45);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
    }

    .search-highlight {
        background: #fff3cd;
        padding: 2px 0;
        font-weight: 500;
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
    @media (max-width: 1200px) {
        .header-left {
            max-width: 400px;
        }
    }

    @media (max-width: 991.98px) {
        .header-container {
            padding: 0 1rem;
            height: 56px;
            gap: 0.5rem;
        }

        /* Ẩn search ở header trên mobile/tablet */
        .header-left {
            display: none;
        }

        .header-divider {
            display: none;
        }

        .header-right {
            gap: 0.5rem;
            flex-shrink: 0;
            width: 100%;
            justify-content: flex-end;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('globalMenuSearch');
        const searchResults = document.getElementById('searchResults');
        const clearBtn = document.getElementById('clearSearch');
        let menuData = [];

        // Lấy dữ liệu menu từ sidebar
        function getMenuData() {
            const menuItems = document.querySelectorAll('.menu-item');
            const data = [];

            menuItems.forEach(item => {
                const link = item.querySelector('.menu-link');
                if (!link) return;

                const iconEl = link.querySelector('.menu-icon');
                const titleEl = link.querySelector('.menu-tooltip');

                if (!iconEl || !titleEl) return;

                const icon = iconEl.innerHTML;
                const title = titleEl.textContent.trim();
                const href = link.getAttribute('href');

                // Menu chính
                if (href && href !== '#') {
                    data.push({
                        type: 'main',
                        title: title,
                        icon: icon,
                        url: href,
                        parent: null
                    });
                }

                // Submenu
                const submenuItems = item.querySelectorAll('.submenu-link');
                submenuItems.forEach(subItem => {
                    const subIconEl = subItem.querySelector('.submenu-icon');
                    const subTitleEl = subItem.querySelector('.submenu-text');

                    if (!subIconEl || !subTitleEl) return;

                    const subIcon = subIconEl.innerHTML;
                    const subTitle = subTitleEl.textContent.trim();
                    const subHref = subItem.getAttribute('href');

                    data.push({
                        type: 'sub',
                        title: subTitle,
                        icon: subIcon,
                        url: subHref,
                        parent: title
                    });
                });
            });

            return data;
        }

        // Highlight text
        function highlightText(text, query) {
            if (!text || !query) return text;
            const regex = new RegExp(`(${query.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
            return text.replace(regex, '<span class="search-highlight">$1</span>');
        }

        // Tìm kiếm
        function search(query) {
            if (!query.trim()) {
                searchResults.style.display = 'none';
                return;
            }

            const lowerQuery = query.toLowerCase();
            const results = menuData.filter(item =>
                item.title.toLowerCase().includes(lowerQuery)
            );

            displayResults(results, query);
        }

        // Hiển thị kết quả
        function displayResults(results, query) {
            const container = searchResults.querySelector('.search-results-content');

            if (results.length === 0) {
                container.innerHTML = '<div class="search-no-results">Không tìm thấy kết quả</div>';
                searchResults.style.display = 'block';
                return;
            }

            // Nhóm theo main và sub
            const mainResults = results.filter(r => r.type === 'main');
            const subResults = results.filter(r => r.type === 'sub');

            let html = '';

            if (mainResults.length > 0) {
                html += '<div class="search-category">Menu chính</div>';
                mainResults.forEach(item => {
                    html += `
                    <a href="${item.url}" class="search-result-item">
                        <div class="search-result-icon">${item.icon}</div>
                        <div class="search-result-text">
                            <div class="search-result-title">${highlightText(item.title, query)}</div>
                        </div>
                    </a>
                `;
                });
            }

            if (subResults.length > 0) {
                html += '<div class="search-category">Menu con</div>';
                subResults.forEach(item => {
                    html += `
                    <a href="${item.url}" class="search-result-item">
                        <div class="search-result-icon">${item.icon}</div>
                        <div class="search-result-text">
                            <div class="search-result-title">${highlightText(item.title, query)}</div>
                            <div class="search-result-subtitle">${item.parent}</div>
                        </div>
                    </a>
                `;
                });
            }

            container.innerHTML = html;
            searchResults.style.display = 'block';
        }

        // Khởi tạo - đợi DOM load hoàn toàn
        setTimeout(() => {
            menuData = getMenuData();
        }, 500);

        // Events
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const value = this.value;
                if (clearBtn) clearBtn.style.display = value ? 'flex' : 'none';
                search(value);
            });
        }

        if (clearBtn) {
            clearBtn.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.focus();
                this.style.display = 'none';
                searchResults.style.display = 'none';
            });
        }

        // Đóng khi click bên ngoài
        document.addEventListener('click', function(e) {
            if (searchResults && !e.target.closest('.header-search')) {
                searchResults.style.display = 'none';
            }
        });

        // Keyboard navigation
        if (searchInput) {
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    searchResults.style.display = 'none';
                    searchInput.blur();
                }
            });
        }
    });
</script>
