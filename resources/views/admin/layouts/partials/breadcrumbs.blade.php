@if (isset($breadcrumbs) && !empty($breadcrumbs->getBreadcrumbs()))
    <div class="page-header d-print-none">
        <div class="container-fluid">
            <div class="row g-2 align-items-center">
                <div class="col">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb" id="breadcrumbs-one">
                            @foreach ($breadcrumbs = $breadcrumbs->getBreadcrumbs() as $item)
                                @if (!$loop->last)
                                    <li class="breadcrumb-item">
                                        @if ($item['url'])
                                            <a href="{{ $item['url'] }}" class="breadcrumb-link">
                                                {{ $item['label'] }}
                                            </a>
                                        @else
                                            <span class="breadcrumb-text">{{ $item['label'] }}</span>
                                        @endif
                                    </li>
                                @else
                                    <li class="breadcrumb-item active" aria-current="page">
                                        {{ $item['label'] }}
                                    </li>
                                @endif
                            @endforeach
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
    /* Page Header */
    .page-header {
        background: #ffffff;
        border-bottom: 1px solid #f0f0f0;
        padding: 16px 0;
        margin-bottom: 20px;
    }

    /* Breadcrumb Container */
    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        list-style: none;
    }

    /* Breadcrumb Items */
    .breadcrumb-item {
        display: flex;
        align-items: center;
        font-size: 14px;
        color: rgba(0, 0, 0, 0.65);
    }

    /* Separator */
    .breadcrumb-item + .breadcrumb-item::before {
        content: '/';
        padding: 0 8px;
        color: rgba(0, 0, 0, 0.35);
        font-weight: 400;
    }

    /* Breadcrumb Links */
    .breadcrumb-link {
        color: rgba(0, 0, 0, 0.65);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .breadcrumb-link:hover {
        color: #1890ff;
    }

    /* Breadcrumb Text (non-link) */
    .breadcrumb-text {
        color: rgba(0, 0, 0, 0.65);
    }

    /* Active Breadcrumb */
    .breadcrumb-item.active {
        color: rgba(0, 0, 0, 0.85);
        font-weight: 500;
    }

    .breadcrumb-item.active a {
        color: rgba(0, 0, 0, 0.85);
        pointer-events: none;
        text-decoration: none;
    }

    /* Responsive */
    @media (max-width: 991.98px) {
        .page-header {
            padding: 12px 0;
            margin-bottom: 16px;
        }

        .breadcrumb-item {
            font-size: 13px;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            padding: 0 6px;
        }
    }

    /* Animation khi hover */
    .breadcrumb-link {
        position: relative;
    }

    .breadcrumb-link::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 0;
        height: 1px;
        background: #1890ff;
        transition: width 0.3s ease;
    }

    .breadcrumb-link:hover::after {
        width: 100%;
    }

    /* Optional: Thêm icon home cho breadcrumb đầu tiên */
    .breadcrumb-item:first-child .breadcrumb-link::before {
        content: '🏠';
        margin-right: 6px;
        font-size: 14px;
    }

    /* Hoặc nếu dùng icon font */
    .breadcrumb-item:first-child .breadcrumb-link i,
    .breadcrumb-item:first-child .breadcrumb-link svg {
        margin-right: 6px;
        font-size: 14px;
    }
</style>
