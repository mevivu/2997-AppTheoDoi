@extends('admin.layouts.master')
@php use App\Traits\RouteAdminSystem; @endphp

@push('libs-css')
    <link rel="stylesheet" href="{{ asset('public/libs/tabler/dist/litepicker/dist/css/litepicker.css') }}"/>
    <style>
        .litepicker {
            font-family: inherit;
            border-radius: 14px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e2e8f0;
            z-index: 1050;
        }
        .litepicker .container__months .month-item-header {
            font-weight: 700;
        }
    </style>
@endpush

@section('content')
<style>
    .page-body {
        background: #F8FAFC;
        min-height: 100vh;
        padding: 1.5rem 0 3rem 0;
    }

    .report-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #0f172a;
        letter-spacing: -0.02em;
    }

    .card-kpi {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        background: #ffffff;
        position: relative;
        overflow: hidden;
    }

    .card-kpi:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08);
    }

    .kpi-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        flex-shrink: 0;
    }

    .kpi-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .kpi-label {
        font-size: 0.78rem;
        font-weight: 700;
        color: #64748b;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    .kpi-footer {
        min-height: 26px;
        line-height: 1.4;
    }

    .chart-card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        background: #ffffff;
        transition: box-shadow 0.2s ease;
    }

    .filter-pill-container {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        gap: 4px;
    }

    .btn-filter-pill {
        border: none;
        background: transparent;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-filter-pill:hover {
        color: #0f172a;
    }

    .btn-filter-pill.active {
        background: #ffffff;
        color: #0284c7;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .rank-pill-container {
        display: inline-flex;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 12px;
        gap: 4px;
    }

    .btn-rank-pill {
        border: none;
        background: transparent;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        transition: all 0.2s ease;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-rank-pill:hover {
        color: #0f172a;
    }

    .btn-rank-pill.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    /* Thứ hạng Huy hiệu */
    .badge-rank {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 800;
        font-size: 0.95rem;
    }

    .badge-rank.rank-1 {
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(255, 165, 0, 0.4);
    }

    .badge-rank.rank-2 {
        background: linear-gradient(135deg, #E0E0E0 0%, #9E9E9E 100%);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(158, 158, 158, 0.35);
    }

    .badge-rank.rank-3 {
        background: linear-gradient(135deg, #E6A070 0%, #CD7F32 100%);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(205, 127, 50, 0.35);
    }

    .badge-rank.rank-other {
        background: #f1f5f9;
        color: #64748b;
        font-weight: 700;
    }

    .avatar-partner {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e2e8f0;
    }

    .avatar-fallback {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        color: #475569;
        font-size: 1rem;
    }
</style>

<div class="page-body">
    <div class="container-fluid px-4">

        <!-- Header: Tiêu đề & Nút hành động -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="report-title mb-1 d-flex align-items-center gap-2">
                    <i class="ti ti-award text-warning"></i>
                    <span>Thống Kê Doanh Thu Đối Tác</span>
                </h1>
                <p class="text-muted mb-0 font-weight-medium">
                    Theo dõi và xếp hạng các tài khoản mẹ giới thiệu có doanh thu tốt nhất từ người dùng mua gói dịch vụ
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="badge bg-blue-lt px-3 py-2 font-weight-bold" id="badge-date-range" style="font-size: 0.85rem;">
                    <i class="ti ti-calendar me-1"></i>{{ $stats['date_range_label'] }}
                </span>
                <button type="button" id="btn-sync-ranks" class="btn btn-primary d-flex align-items-center gap-1 font-weight-semibold shadow-sm px-3">
                    <i class="ti ti-refresh" id="icon-sync-ranks"></i>
                    <span>Đồng bộ cấp bậc</span>
                </button>
            </div>
        </div>

        <!-- Filter Control Bar -->
        <div class="card mb-4 border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-xl-row align-items-xl-center justify-content-between gap-3">
                    <!-- Lọc theo Cấp bậc -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="text-muted font-weight-semibold me-1 fs-13">Cấp bậc:</span>
                        <div class="rank-pill-container">
                            <button type="button" class="btn-rank-pill btn-rank {{ $currentRank === 'all' ? 'active' : '' }}" data-rank="all">
                                <i class="ti ti-apps"></i> Tất cả
                            </button>
                            <button type="button" class="btn-rank-pill btn-rank {{ $currentRank === '1' ? 'active' : '' }}" data-rank="1">
                                <span style="color: #8A9BA8;">●</span> Bạc
                            </button>
                            <button type="button" class="btn-rank-pill btn-rank {{ $currentRank === '2' ? 'active' : '' }}" data-rank="2">
                                <span style="color: #E6A100;">●</span> Vàng
                            </button>
                            <button type="button" class="btn-rank-pill btn-rank {{ $currentRank === '3' ? 'active' : '' }}" data-rank="3">
                                <span style="color: #6366F1;">●</span> Bạch Kim
                            </button>
                            <button type="button" class="btn-rank-pill btn-rank {{ $currentRank === '4' ? 'active' : '' }}" data-rank="4">
                                <span style="color: #00B4D8;">●</span> Kim Cương
                            </button>
                        </div>
                    </div>

                    <!-- Lọc theo Khoảng thời gian nhanh -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="text-muted font-weight-semibold me-1 fs-13">Thời gian:</span>
                        <div class="filter-pill-container">
                            <button type="button" class="btn-filter-pill btn-period {{ $currentPeriod === '1d' ? 'active' : '' }}" data-period="1d">Hôm nay</button>
                            <button type="button" class="btn-filter-pill btn-period {{ $currentPeriod === '7d' ? 'active' : '' }}" data-period="7d">7 Ngày</button>
                            <button type="button" class="btn-filter-pill btn-period {{ $currentPeriod === '30d' ? 'active' : '' }}" data-period="30d">30 Ngày</button>
                            <button type="button" class="btn-filter-pill btn-period {{ $currentPeriod === 'this_month' ? 'active' : '' }}" data-period="this_month">Tháng này</button>
                            <button type="button" class="btn-filter-pill btn-period {{ $currentPeriod === 'last_month' ? 'active' : '' }}" data-period="last_month">Tháng trước</button>
                            <button type="button" class="btn-filter-pill btn-period {{ $currentPeriod === 'all' ? 'active' : '' }}" data-period="all">Tất cả</button>
                        </div>
                    </div>
                </div>

                <!-- Custom Date Range & Search Row -->
                <div class="mt-3 pt-3 border-top d-flex align-items-center justify-content-between gap-3 flex-wrap">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span class="text-muted fs-13 font-weight-medium d-flex align-items-center gap-1">
                            <i class="ti ti-calendar-event text-primary fs-3"></i> Khoảng ngày tùy chọn:
                        </span>
                        <div class="input-icon" style="min-width: 270px;">
                            <span class="input-icon-addon">
                                <i class="ti ti-calendar text-muted"></i>
                            </span>
                            <input type="text" id="datepicker-range" class="form-control form-control-sm bg-white shadow-none rounded-pill" 
                                   placeholder="Chọn khoảng ngày (dd/mm/yyyy - dd/mm/yyyy)" 
                                   value="{{ $stats['date_range_label'] ?? '' }}" 
                                   readonly style="cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                        </div>
                        <button type="button" id="btn-clear-custom-date" class="btn btn-sm btn-ghost-danger rounded-pill px-2 d-flex align-items-center gap-1" style="display: {{ ($currentPeriod === 'custom' && $from && $to) ? 'inline-flex' : 'none' }}; font-size: 0.8rem;">
                            <i class="ti ti-x"></i> Xóa lọc ngày
                        </button>
                    </div>

                    <!-- Ô tìm kiếm đối tác -->
                    <div class="input-icon" style="min-width: 250px;">
                        <span class="input-icon-addon">
                            <i class="ti ti-search text-muted"></i>
                        </span>
                        <input type="text" id="input-search-partner" class="form-control form-control-sm rounded-pill" 
                               placeholder="Tìm tên, SĐT hoặc mã giới thiệu..." 
                               value="{{ $search ?? '' }}">
                    </div>
                </div>
            </div>
        </div>

        <!-- 4 Khối Thẻ Chỉ Số KPI -->
        <div class="row g-3 mb-4">
            <!-- KPI 1: Tổng Doanh Thu Giới Thiệu -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-kpi h-100 p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="kpi-label">DOANH THU TRONG KỲ</span>
                            <div class="kpi-icon-box bg-green-lt text-green">
                                <i class="ti ti-wallet"></i>
                            </div>
                        </div>
                        <div class="kpi-value text-success" id="kpi-total-revenue">
                            {{ $stats['kpis']['total_revenue_formatted'] }}
                        </div>
                    </div>
                    <div class="kpi-footer mt-3 pt-2 border-top border-light d-flex align-items-center fs-12">
                        <div id="kpi-growth-container" class="d-flex align-items-center flex-wrap">
                            @if($stats['kpis']['growth'] >= 0)
                                <span class="text-success font-weight-bold d-inline-flex align-items-center">
                                    <i class="ti ti-arrow-up-right me-0.5"></i>+{{ $stats['kpis']['growth'] }}%
                                </span>
                            @else
                                <span class="text-danger font-weight-bold d-inline-flex align-items-center">
                                    <i class="ti ti-arrow-down-right me-0.5"></i>{{ $stats['kpis']['growth'] }}%
                                </span>
                            @endif
                            <span class="text-muted ms-1">so với kỳ trước</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI 2: Đối Tác Có Doanh Thu -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-kpi h-100 p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="kpi-label">ĐỐI TÁC CÓ DOANH THU</span>
                            <div class="kpi-icon-box bg-blue-lt text-blue">
                                <i class="ti ti-users"></i>
                            </div>
                        </div>
                        <div class="kpi-value text-primary" id="kpi-active-partners">
                            {{ number_format($stats['kpis']['active_partners']) }}
                        </div>
                    </div>
                    <div class="kpi-footer mt-3 pt-2 border-top border-light d-flex align-items-center fs-12 text-muted">
                        <span>Trên tổng số <strong id="kpi-total-partners" class="text-dark">{{ number_format($stats['total_partners']) }}</strong> đối tác</span>
                    </div>
                </div>
            </div>

            <!-- KPI 3: Đơn Mua Gói Thành Công -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-kpi h-100 p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="kpi-label">ĐƠN MUA GÓI</span>
                            <div class="kpi-icon-box bg-indigo-lt text-indigo">
                                <i class="ti ti-shopping-cart"></i>
                            </div>
                        </div>
                        <div class="kpi-value text-indigo" id="kpi-total-orders">
                            {{ number_format($stats['kpis']['total_orders']) }}
                        </div>
                    </div>
                    <div class="kpi-footer mt-3 pt-2 border-top border-light d-flex align-items-center fs-12 text-muted">
                        <span>Giao dịch thanh toán thành công</span>
                    </div>
                </div>
            </div>

            <!-- KPI 4: Giá Trị Đơn Trung Bình (AOV) -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-kpi h-100 p-3 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="kpi-label">GIÁ TRỊ ĐƠN TRUNG BÌNH</span>
                            <div class="kpi-icon-box bg-warning-lt text-warning">
                                <i class="ti ti-chart-dots"></i>
                            </div>
                        </div>
                        <div class="kpi-value text-warning" id="kpi-aov">
                            {{ $stats['kpis']['aov_formatted'] }}
                        </div>
                    </div>
                    <div class="kpi-footer mt-3 pt-2 border-top border-light d-flex align-items-center fs-12 text-muted">
                        <span>Doanh thu trung bình / mỗi đơn hàng</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khối Biểu Đồ 1 & 2: Xu Hướng & Cơ Cấu Cấp Bậc -->
        <div class="row g-3 mb-4">
            <!-- Biểu đồ 1: Xu hướng Doanh thu theo Thời gian -->
            <div class="col-12 col-xl-8">
                <div class="card chart-card h-100">
                    <div class="card-header border-0 bg-transparent pt-3 pb-2 d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title font-weight-bold mb-0">
                                <i class="ti ti-chart-line text-primary me-2"></i>Xu hướng Doanh thu Affiliate
                            </h3>
                            <div class="text-muted fs-12 mt-1">Doanh thu phát sinh và số lượng đơn mua gói theo các mốc thời gian</div>
                        </div>
                    </div>
                    <div class="card-body position-relative">
                        <div id="chart-trend" style="width: 100%; height: 380px;"></div>
                        <div id="chart-loading-trend" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center d-none" style="background: rgba(255,255,255,0.7); z-index: 10; border-radius: 12px;">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ 2: Cơ cấu Cấp bậc Đối tác -->
            <div class="col-12 col-xl-4">
                <div class="card chart-card h-100">
                    <div class="card-header border-0 bg-transparent pt-3 pb-2">
                        <div>
                            <h3 class="card-title font-weight-bold mb-0">
                                <i class="ti ti-chart-donut text-success me-2"></i>Tỷ trọng theo Cấp bậc
                            </h3>
                            <div class="text-muted fs-12 mt-1">Cơ cấu doanh thu đóng góp giữa các cấp bậc mẹ</div>
                        </div>
                    </div>
                    <div class="card-body position-relative">
                        <div id="chart-donut" style="width: 100%; height: 380px;"></div>
                        <div id="chart-loading-donut" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center d-none" style="background: rgba(255,255,255,0.7); z-index: 10; border-radius: 12px;">
                            <div class="spinner-border text-primary" role="status"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Khối Biểu Đồ 3: Top 10 Đối Tác Doanh Thu Khủng Nhất -->
        <div class="card chart-card mb-4">
            <div class="card-header border-0 bg-transparent pt-3 pb-2">
                <div>
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="ti ti-chart-bar text-warning me-2"></i>Top 10 Đối Tác Dẫn Đầu Doanh Thu
                    </h3>
                    <div class="text-muted fs-12 mt-1">Các đối tác mang lại doanh số cao nhất trong kỳ</div>
                </div>
            </div>
            <div class="card-body position-relative">
                <div id="chart-top10" style="width: 100%; height: 350px;"></div>
                <div id="chart-loading-top10" class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center d-none" style="background: rgba(255,255,255,0.7); z-index: 10; border-radius: 12px;">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>

        <!-- Bảng Xếp Hạng Chi Tiết (Leaderboard Table) -->
        <div class="card chart-card">
            <div class="card-header border-bottom bg-white py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="ti ti-list-numbers text-indigo me-2"></i>Bảng xếp hạng Doanh thu Đối tác
                    </h3>
                    <div class="text-muted fs-12 mt-1">Danh sách sắp xếp theo doanh thu trong kỳ từ cao xuống thấp</div>
                </div>
                <span class="badge bg-indigo-lt px-3 py-2 font-weight-bold" id="badge-table-count">
                    {{ count($stats['ranking']) }} đối tác
                </span>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="w-1 text-center font-weight-bold"># Hạng</th>
                            <th class="font-weight-bold" style="min-width: 200px;">Đối tác</th>
                            <th class="font-weight-bold text-center">Cấp bậc</th>
                            <th class="text-center font-weight-bold">Giới thiệu (Kỳ / Tổng)</th>
                            <th class="text-center font-weight-bold">Đơn mua gói</th>
                            <th class="text-end font-weight-bold" style="min-width: 150px;">Doanh thu trong kỳ</th>
                            <th class="text-end font-weight-bold" style="min-width: 150px;">Doanh số tích lũy</th>
                            <th class="text-end font-weight-bold">Ví hoa hồng</th>
                            <th class="w-1 text-center font-weight-bold">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody id="table-ranking-body">
                        @forelse($stats['ranking'] as $index => $item)
                            <tr>
                                <td class="text-center">
                                    <span class="badge-rank {{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : 'rank-other')) }}">
                                        @if($index === 0)
                                            👑
                                        @elseif($index === 1)
                                            🥈
                                        @elseif($index === 2)
                                            🥉
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if(!empty($item['avatar']))
                                            <img src="{{ asset($item['avatar']) }}" alt="{{ $item['fullname'] }}" class="avatar-partner">
                                        @else
                                            <div class="avatar-fallback">
                                                {{ mb_strtoupper(mb_substr($item['fullname'], 0, 1)) }}
                                            </div>
                                        @endif
                                        <div>
                                            <div class="font-weight-bold text-dark fs-14">{{ $item['fullname'] }}</div>
                                            <div class="text-muted fs-12 d-flex align-items-center gap-2">
                                                <span class="badge bg-secondary-lt px-1.5 py-0.5 rounded font-monospace">{{ $item['affiliate_code'] }}</span>
                                                <span><i class="ti ti-phone fs-11"></i> {{ $item['phone'] }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge {{ $item['rank_badge'] }} px-2 py-1 font-weight-bold d-inline-flex align-items-center gap-1">
                                        <i class="{{ $item['rank_icon'] }}"></i> {{ $item['rank_name'] }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="font-weight-bold text-primary">{{ $item['period_new_f1'] }}</span>
                                    <span class="text-muted"> / {{ $item['total_f1'] }}</span>
                                </td>
                                <td class="text-center font-weight-bold text-indigo">
                                    {{ number_format($item['period_orders']) }}
                                </td>
                                <td class="text-end">
                                    <span class="font-weight-bold text-success fs-14">
                                        {{ number_format($item['period_sales'], 0, ',', '.') }}đ
                                    </span>
                                </td>
                                <td class="text-end text-muted font-weight-medium">
                                    {{ number_format($item['total_sales'], 0, ',', '.') }}đ
                                </td>
                                <td class="text-end font-weight-bold text-teal">
                                    {{ number_format($item['wallet_balance'], 0, ',', '.') }}đ
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-view-f1 rounded-pill px-2.5" 
                                            data-partner-id="{{ $item['id'] }}" 
                                            title="Xem chi tiết đơn hàng & thành viên giới thiệu">
                                        <i class="ti ti-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="ti ti-inbox fs-1 mb-2 d-block"></i>
                                    Không có dữ liệu đối tác nào trong khoảng thời gian đã chọn.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal Xem Chi Tiết Đối Tác -->
<div class="modal modal-blur fade" id="modal-partner-details" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header border-bottom bg-light">
                <h5 class="modal-title font-weight-bold d-flex align-items-center gap-2">
                    <i class="ti ti-user-check text-primary"></i>
                    <span>Chi Tiết Đơn Hàng Của Đối Tác</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="modal-partner-content">
                <!-- Nội dung được nạp qua AJAX -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status"></div>
                    <div class="text-muted mt-2 fs-13">Đang tải chi tiết đối tác...</div>
                </div>
            </div>
            <div class="modal-footer bg-light border-top py-2">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

@push('libs-js')
    <!-- amCharts 5 Resources -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/xy.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>
    <!-- Tabler Litepicker Bundle -->
    <script src="{{ asset('public/libs/tabler/dist/litepicker/dist/bundle.js') }}"></script>
@endpush

@push('custom-js')
<script>
    // Tham chiếu biểu đồ amCharts 5
    var trendRoot, trendChart, trendXAxis, trendYAxis, trendSeriesSales, trendSeriesOrders;
    var donutRoot, donutChart, donutSeries;
    var top10Root, top10Chart, top10XAxis, top10YAxis, top10Series;

    var currentPeriod = '{{ $currentPeriod }}';
    var currentRank = '{{ $currentRank }}';
    var customFrom = '{{ $from ?? "" }}';
    var customTo = '{{ $to ?? "" }}';
    var currentSearch = '{{ $search ?? "" }}';
    var picker;

    var initialStats = {!! json_encode($stats) !!};

    am5.ready(function() {
        initTrendChart(initialStats.trend_data);
        initDonutChart(initialStats.donut_data);
        initTop10Chart(initialStats.top10_data);
    });

    // 1. Biểu đồ Xu hướng Doanh thu (amCharts 5 XY Line & Column)
    function initTrendChart(data) {
        if (trendRoot) {
            trendRoot.dispose();
        }

        trendRoot = am5.Root.new("chart-trend");
        trendRoot._logo.dispose();
        trendRoot.setThemes([am5themes_Animated.new(trendRoot)]);

        trendChart = trendRoot.container.children.push(am5xy.XYChart.new(trendRoot, {
            panX: true,
            panY: true,
            wheelX: "panX",
            wheelY: "zoomX",
            pinchZoomX: true
        }));

        var cursor = trendChart.set("cursor", am5xy.XYCursor.new(trendRoot, {
            behavior: "none"
        }));
        cursor.lineY.set("visible", false);

        trendXAxis = trendChart.xAxes.push(am5xy.CategoryAxis.new(trendRoot, {
            categoryField: "date",
            renderer: am5xy.AxisRendererX.new(trendRoot, { minGridDistance: 35 }),
            tooltip: am5.Tooltip.new(trendRoot, {})
        }));

        // Trục Y 1: Doanh số (VNĐ)
        trendYAxis = trendChart.yAxes.push(am5xy.ValueAxis.new(trendRoot, {
            min: 0,
            renderer: am5xy.AxisRendererY.new(trendRoot, {})
        }));

        // Trục Y 2: Số đơn hàng
        var yAxisOrders = trendChart.yAxes.push(am5xy.ValueAxis.new(trendRoot, {
            min: 0,
            syncWithAxis: trendYAxis,
            renderer: am5xy.AxisRendererY.new(trendRoot, {
                opposite: true
            })
        }));
        yAxisOrders.get("renderer").grid.template.set("forceHidden", true);

        // Chuỗi 1: Cột Doanh số (Columns)
        trendSeriesSales = trendChart.series.push(am5xy.ColumnSeries.new(trendRoot, {
            name: "Doanh thu (VNĐ)",
            xAxis: trendXAxis,
            yAxis: trendYAxis,
            valueYField: "sales",
            categoryXField: "date",
            tooltip: am5.Tooltip.new(trendRoot, {
                labelText: "{name}: [bold]{valueY.formatNumber('#,###')}đ[/]"
            })
        }));

        trendSeriesSales.columns.template.setAll({
            cornerRadiusTL: 6,
            cornerRadiusTR: 6,
            fill: am5.color(0x10b981),
            stroke: am5.color(0x10b981),
            width: am5.percent(60)
        });

        // Chuỗi 2: Đường Đơn hàng (Line)
        trendSeriesOrders = trendChart.series.push(am5xy.LineSeries.new(trendRoot, {
            name: "Số đơn mua gói",
            xAxis: trendXAxis,
            yAxis: yAxisOrders,
            valueYField: "orders",
            categoryXField: "date",
            stroke: am5.color(0x3b82f6),
            tooltip: am5.Tooltip.new(trendRoot, {
                labelText: "{name}: [bold]{valueY}[/] đơn"
            })
        }));

        trendSeriesOrders.strokes.template.setAll({
            strokeWidth: 3
        });

        trendSeriesOrders.bullets.push(function() {
            return am5.Bullet.new(trendRoot, {
                sprite: am5.Circle.new(trendRoot, {
                    radius: 5,
                    fill: am5.color(0x3b82f6),
                    stroke: am5.color(0xffffff),
                    strokeWidth: 2
                })
            });
        });

        // Legend
        var legend = trendChart.children.push(am5.Legend.new(trendRoot, {
            centerX: am5.p50,
            x: am5.p50,
            marginTop: 10
        }));
        legend.data.setAll(trendChart.series.values);

        trendXAxis.data.setAll(data || []);
        trendSeriesSales.data.setAll(data || []);
        trendSeriesOrders.data.setAll(data || []);
        trendChart.appear(800, 100);
    }

    // 2. Biểu đồ Cơ cấu Cấp bậc (amCharts 5 Donut Chart)
    function initDonutChart(data) {
        if (donutRoot) {
            donutRoot.dispose();
        }

        donutRoot = am5.Root.new("chart-donut");
        donutRoot._logo.dispose();
        donutRoot.setThemes([am5themes_Animated.new(donutRoot)]);

        donutChart = donutRoot.container.children.push(am5percent.PieChart.new(donutRoot, {
            innerRadius: am5.percent(62),
            layout: donutRoot.verticalLayout
        }));

        donutSeries = donutChart.series.push(am5percent.PieSeries.new(donutRoot, {
            valueField: "value",
            categoryField: "category",
            alignLabels: false
        }));

        donutSeries.slices.template.setAll({
            templateField: "sliceSettings",
            stroke: am5.color(0xffffff),
            strokeWidth: 2,
            tooltipText: "{category}: [bold]{value.formatNumber('#,###')}đ[/] ({count} đối tác)"
        });

        donutSeries.slices.template.adapters.add("fill", function(fill, target) {
            if (target.dataItem && target.dataItem.dataContext && target.dataItem.dataContext.color) {
                return am5.color(target.dataItem.dataContext.color);
            }
            return fill;
        });

        donutSeries.labels.template.set("forceHidden", true);
        donutSeries.ticks.template.set("forceHidden", true);

        // Label tổng tiền ở tâm Donut
        var total = (data || []).reduce(function(sum, item) { return sum + Number(item.value || 0); }, 0);
        donutChart.seriesContainer.children.push(am5.Label.new(donutRoot, {
            textAlign: "center",
            centerY: am5.percent(50),
            centerX: am5.percent(50),
            text: "[#64748b;fontSize:11px;fontWeight:600]TỔNG DOANH THU[/]\n[bold;fontSize:17px;color:#0f172a]" + Number(total).toLocaleString('vi-VN') + "đ[/]"
        }));

        // Legend
        var legend = donutChart.children.push(am5.Legend.new(donutRoot, {
            centerX: am5.percent(50),
            x: am5.percent(50),
            marginTop: 15,
            marginBottom: 5,
            layout: donutRoot.gridLayout,
            maxColumns: 2
        }));

        legend.labels.template.setAll({
            fontSize: 12,
            fontWeight: "500",
            maxWidth: 110,
            oversizedBehavior: "truncate"
        });

        legend.valueLabels.template.setAll({
            fontSize: 12,
            fontWeight: "bold",
            text: "{valuePercentTotal.formatNumber('0.0')}%"
        });

        donutSeries.data.setAll(data || []);
        legend.data.setAll(donutSeries.dataItems);
        donutChart.appear(800, 100);
    }

    // 3. Biểu đồ Top 10 Đối tác Doanh thu Khủng (amCharts 5 Bar Chart)
    function initTop10Chart(data) {
        if (top10Root) {
            top10Root.dispose();
        }

        top10Root = am5.Root.new("chart-top10");
        top10Root._logo.dispose();
        top10Root.setThemes([am5themes_Animated.new(top10Root)]);

        top10Chart = top10Root.container.children.push(am5xy.XYChart.new(top10Root, {
            panX: false,
            panY: false,
            layout: top10Root.verticalLayout
        }));

        top10YAxis = top10Chart.yAxes.push(am5xy.CategoryAxis.new(top10Root, {
            categoryField: "partner",
            renderer: am5xy.AxisRendererY.new(top10Root, {
                inversed: false,
                cellStartLocation: 0.1,
                cellEndLocation: 0.9
            })
        }));

        top10YAxis.get("renderer").labels.template.setAll({
            fontSize: 12,
            fontWeight: "600",
            maxWidth: 180,
            oversizedBehavior: "truncate"
        });

        top10XAxis = top10Chart.xAxes.push(am5xy.ValueAxis.new(top10Root, {
            min: 0,
            renderer: am5xy.AxisRendererX.new(top10Root, {
                strokeOpacity: 0.1
            })
        }));

        top10Series = top10Chart.series.push(am5xy.ColumnSeries.new(top10Root, {
            xAxis: top10XAxis,
            yAxis: top10YAxis,
            valueXField: "sales",
            categoryYField: "partner",
            tooltip: am5.Tooltip.new(top10Root, {
                labelText: "{full_name} ({rank}): [bold]{valueX.formatNumber('#,###')}đ[/] ({orders} đơn)"
            })
        }));

        top10Series.columns.template.setAll({
            cornerRadiusTR: 6,
            cornerRadiusBR: 6,
            height: am5.percent(70)
        });

        top10Series.columns.template.adapters.add("fill", function(fill, target) {
            if (target.dataItem && target.dataItem.dataContext && target.dataItem.dataContext.color) {
                return am5.color(target.dataItem.dataContext.color);
            }
            return am5.color(0x3b82f6);
        });

        top10YAxis.data.setAll(data || []);
        top10Series.data.setAll(data || []);
        top10Chart.appear(800, 100);
    }

    // 4. Hàm Gọi AJAX Cập Nhật Dữ Liệu Toàn Trang
    function refreshData() {
        $('#chart-loading-trend, #chart-loading-donut, #chart-loading-top10').removeClass('d-none');

        $.ajax({
            url: "{{ route(RouteAdminSystem::AFFILIATE_STATISTICS_INDEX) }}",
            type: "GET",
            data: {
                period: currentPeriod,
                rank: currentRank,
                from: customFrom,
                to: customTo,
                search: currentSearch
            },
            success: function(res) {
                if (res.status === 'success') {
                    var data = res.data;

                    // Cập nhật Badge Label
                    $('#badge-date-range').html('<i class="ti ti-calendar me-1"></i>' + data.date_range_label);
                    $('#badge-table-count').text(data.ranking.length + ' đối tác');

                    // Cập nhật KPI Cards
                    $('#kpi-total-revenue').text(data.kpis.total_revenue_formatted);
                    $('#kpi-active-partners').text(Number(data.kpis.active_partners).toLocaleString('vi-VN'));
                    $('#kpi-total-partners').text(Number(data.total_partners).toLocaleString('vi-VN'));
                    $('#kpi-total-orders').text(Number(data.kpis.total_orders).toLocaleString('vi-VN'));
                    $('#kpi-aov').text(data.kpis.aov_formatted);

                    var growthHtml = '';
                    if (data.kpis.growth >= 0) {
                        growthHtml = '<span class="text-success font-weight-bold"><i class="ti ti-arrow-up-right"></i> +' + data.kpis.growth + '%</span>';
                    } else {
                        growthHtml = '<span class="text-danger font-weight-bold"><i class="ti ti-arrow-down-right"></i> ' + data.kpis.growth + '%</span>';
                    }
                    growthHtml += '<span class="text-muted ms-1">so với kỳ trước</span>';
                    $('#kpi-growth-container').html(growthHtml);

                    // Vẽ lại 3 Biểu đồ
                    initTrendChart(data.trend_data);
                    initDonutChart(data.donut_data);
                    initTop10Chart(data.top10_data);

                    // Render lại Bảng Xếp Hạng
                    renderRankingTable(data.ranking);
                }
            },
            error: function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi nạp dữ liệu',
                    text: err.responseJSON ? err.responseJSON.message : 'Không thể kết nối máy chủ'
                });
            },
            complete: function() {
                $('#chart-loading-trend, #chart-loading-donut, #chart-loading-top10').addClass('d-none');
            }
        });
    }

    // 5. Render lại HTML Bảng Xếp Hạng
    function renderRankingTable(items) {
        var tbody = $('#table-ranking-body');
        tbody.empty();

        if (!items || items.length === 0) {
            tbody.html('<tr><td colspan="9" class="text-center py-5 text-muted"><i class="ti ti-inbox fs-1 mb-2 d-block"></i>Không có dữ liệu đối tác nào phù hợp với bộ lọc.</td></tr>');
            return;
        }

        items.forEach(function(item, idx) {
            var rankBadgeClass = idx === 0 ? 'rank-1' : (idx === 1 ? 'rank-2' : (idx === 2 ? 'rank-3' : 'rank-other'));
            var rankIcon = idx === 0 ? '👑' : (idx === 1 ? '🥈' : (idx === 2 ? '🥉' : (idx + 1)));

            var avatarHtml = '';
            if (item.avatar) {
                avatarHtml = '<img src="' + item.avatar + '" alt="' + item.fullname + '" class="avatar-partner">';
            } else {
                var firstChar = (item.fullname || 'K').charAt(0).toUpperCase();
                avatarHtml = '<div class="avatar-fallback">' + firstChar + '</div>';
            }

            var tr = '<tr>' +
                '<td class="text-center"><span class="badge-rank ' + rankBadgeClass + '">' + rankIcon + '</span></td>' +
                '<td>' +
                    '<div class="d-flex align-items-center gap-2">' +
                        avatarHtml +
                        '<div>' +
                            '<div class="font-weight-bold text-dark fs-14">' + item.fullname + '</div>' +
                            '<div class="text-muted fs-12 d-flex align-items-center gap-2">' +
                                '<span class="badge bg-secondary-lt px-1.5 py-0.5 rounded font-monospace">' + item.affiliate_code + '</span>' +
                                '<span><i class="ti ti-phone fs-11"></i> ' + item.phone + '</span>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</td>' +
                '<td class="text-center">' +
                    '<span class="badge ' + item.rank_badge + ' px-2 py-1 font-weight-bold d-inline-flex align-items-center gap-1">' +
                        '<i class="' + item.rank_icon + '"></i> ' + item.rank_name +
                    '</span>' +
                '</td>' +
                '<td class="text-center">' +
                    '<span class="font-weight-bold text-primary">' + item.period_new_f1 + '</span>' +
                    '<span class="text-muted"> / ' + item.total_f1 + '</span>' +
                '</td>' +
                '<td class="text-center font-weight-bold text-indigo">' + Number(item.period_orders).toLocaleString('vi-VN') + '</td>' +
                '<td class="text-end font-weight-bold text-success fs-14">' + Number(item.period_sales).toLocaleString('vi-VN') + 'đ</td>' +
                '<td class="text-end text-muted font-weight-medium">' + Number(item.total_sales).toLocaleString('vi-VN') + 'đ</td>' +
                '<td class="text-end font-weight-bold text-teal">' + Number(item.wallet_balance).toLocaleString('vi-VN') + 'đ</td>' +
                '<td class="text-center">' +
                    '<button type="button" class="btn btn-sm btn-outline-primary btn-view-f1 rounded-pill px-2.5" data-partner-id="' + item.id + '" title="Xem chi tiết đơn hàng & thành viên giới thiệu">' +
                        '<i class="ti ti-eye"></i>' +
                    '</button>' +
                '</td>' +
            '</tr>';

            tbody.append(tr);
        });
    }

    // 6. Xử lý các Event Lọc Nhanh & Lọc Cấp Bậc
    $(document).ready(function() {
        // Event nút thời gian
        $(document).on('click', '.btn-period', function() {
            $('.btn-period').removeClass('active');
            $(this).addClass('active');
            currentPeriod = $(this).data('period');

            if (currentPeriod !== 'custom') {
                customFrom = '';
                customTo = '';
                $('#btn-clear-custom-date').hide();
                if (picker) {
                    picker.clearSelection();
                }
            }
            refreshData();
        });

        // Event nút Cấp bậc
        $(document).on('click', '.btn-rank', function() {
            $('.btn-rank').removeClass('active');
            $(this).addClass('active');
            currentRank = $(this).data('rank');
            refreshData();
        });

        // Event tìm kiếm đối tác với Debounce
        var searchTimeout = null;
        $('#input-search-partner').on('input', function() {
            var val = $(this).val();
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                currentSearch = val;
                refreshData();
            }, 400);
        });

        // Event Litepicker Custom Date Range
        if (window.Litepicker) {
            picker = new Litepicker({
                element: document.getElementById('datepicker-range'),
                singleMode: false,
                numberOfMonths: 2,
                numberOfColumns: 2,
                format: 'DD/MM/YYYY',
                autoApply: true,
                lang: 'vi-VN',
                setup: function(p) {
                    p.on('selected', function(date1, date2) {
                        if (date1 && date2) {
                            customFrom = date1.format('DD/MM/YYYY');
                            customTo = date2.format('DD/MM/YYYY');
                            currentPeriod = 'custom';
                            $('.btn-period').removeClass('active');
                            $('#btn-clear-custom-date').show();
                            refreshData();
                        }
                    });
                }
            });
        }

        // Event xóa lọc ngày
        $('#btn-clear-custom-date').on('click', function() {
            customFrom = '';
            customTo = '';
            currentPeriod = '30d';
            $('.btn-period').removeClass('active');
            $('.btn-period[data-period="30d"]').addClass('active');
            $(this).hide();
            if (picker) {
                picker.clearSelection();
            }
            refreshData();
        });

        // Event Xem Chi Tiết Đối Tác Modal
        $(document).on('click', '.btn-view-f1', function() {
            var partnerId = $(this).data('partner-id');
            var modal = $('#modal-partner-details');
            var container = $('#modal-partner-content');

            container.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><div class="text-muted mt-2 fs-13">Đang nạp chi tiết đối tác...</div></div>');
            modal.modal('show');

            $.ajax({
                url: "{{ url('admin/thong-ke-doi-tac/partner-details') }}/" + partnerId,
                type: "GET",
                data: {
                    period: currentPeriod,
                    from: customFrom,
                    to: customTo
                },
                success: function(res) {
                    if (res.status === 'success') {
                        var p = res.data.partner;
                        var orders = res.data.orders || [];
                        var referrals = res.data.referrals || [];
                        var periodSalesFormatted = res.data.period_sales_formatted || '0đ';
                        var dateRangeLabel = res.data.date_range_label || 'Kỳ đang chọn';
                        var totalReferrals = p.total_referrals_count !== undefined ? p.total_referrals_count : (p.total_f1_count || referrals.length);

                        // Card thông tin đối tác & Chỉ số doanh thu
                        var html = '<div class="card p-3 bg-light border mb-4">' +
                            '<div class="row align-items-center g-3">' +
                                '<div class="col-12 col-md-7">' +
                                    '<div class="d-flex align-items-center gap-2 mb-1 flex-wrap">' +
                                        '<h4 class="mb-0 font-weight-bold text-dark">' + p.fullname + '</h4>' +
                                        '<span class="badge bg-secondary-lt font-monospace">' + p.affiliate_code + '</span>' +
                                        '<span class="badge ' + p.rank_badge + ' font-weight-bold">' + p.rank_name + '</span>' +
                                    '</div>' +
                                    '<div class="text-muted fs-13 d-flex align-items-center flex-wrap gap-3 mt-1">' +
                                        '<span><i class="ti ti-phone text-muted me-1"></i>' + p.phone + '</span>' +
                                        '<span><i class="ti ti-mail text-muted me-1"></i>' + p.email + '</span>' +
                                        '<span><i class="ti ti-users text-muted me-1"></i>Tổng ' + totalReferrals + ' thành viên</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="col-12 col-md-5">' +
                                    '<div class="row g-2 text-center">' +
                                        '<div class="col-6">' +
                                            '<div class="bg-white border rounded p-2">' +
                                                '<div class="text-muted fs-11 text-uppercase font-weight-bold">Trong kỳ</div>' +
                                                '<div class="font-weight-bold text-success fs-14 mt-1">' + periodSalesFormatted + '</div>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="col-6">' +
                                            '<div class="bg-white border rounded p-2">' +
                                                '<div class="text-muted fs-11 text-uppercase font-weight-bold">Tổng tích lũy</div>' +
                                                '<div class="font-weight-bold text-primary fs-14 mt-1">' + p.total_sales_formatted + '</div>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';

                        // Tabs Navigation
                        html += '<ul class="nav nav-tabs nav-fill mb-3" role="tablist">' +
                            '<li class="nav-item" role="presentation">' +
                                '<a class="nav-link active font-weight-bold" data-bs-toggle="tab" href="#tab-modal-orders" role="tab">' +
                                    '<i class="ti ti-shopping-cart me-1"></i>Đơn hàng mua gói <span class="badge bg-blue-lt ms-1">' + orders.length + '</span>' +
                                '</a>' +
                            '</li>' +
                            '<li class="nav-item" role="presentation">' +
                                '<a class="nav-link font-weight-bold" data-bs-toggle="tab" href="#tab-modal-referrals" role="tab">' +
                                    '<i class="ti ti-users me-1"></i>Thành viên giới thiệu <span class="badge bg-secondary-lt ms-1">' + referrals.length + '</span>' +
                                '</a>' +
                            '</li>' +
                        '</ul>';

                        html += '<div class="tab-content">';

                        // TAB 1: DANH SÁCH ĐƠN HÀNG
                        html += '<div class="tab-pane fade show active" id="tab-modal-orders" role="tabpanel">';
                        if (orders.length === 0) {
                            html += '<div class="alert alert-info border-0 rounded-3 text-center py-4">' +
                                '<i class="ti ti-info-circle fs-2 d-block mb-1"></i>Chưa có giao dịch mua gói nào từ các thành viên được đối tác này giới thiệu.' +
                            '</div>';
                        } else {
                            html += '<div class="table-responsive border rounded-3"><table class="table table-vcenter table-hover mb-0"><thead class="bg-light">' +
                                '<tr>' +
                                    '<th class="font-weight-bold">Người mua gói</th>' +
                                    '<th class="font-weight-bold">Gói dịch vụ</th>' +
                                    '<th class="font-weight-bold text-end">Số tiền</th>' +
                                    '<th class="font-weight-bold text-center">Cổng</th>' +
                                    '<th class="font-weight-bold text-center">Kỳ lọc</th>' +
                                    '<th class="font-weight-bold text-end">Thời gian mua</th>' +
                                '</tr>' +
                            '</thead><tbody>';

                            orders.forEach(function(o) {
                                var buyerName = o.buyer_name || o.f1_name || 'Khách hàng';
                                var buyerPhone = o.buyer_phone || o.f1_phone || '-';
                                var periodBadge = o.is_in_period 
                                    ? '<span class="badge bg-success-lt font-weight-bold">Trong kỳ</span>' 
                                    : '<span class="badge bg-secondary-lt">Kỳ khác</span>';

                                html += '<tr>' +
                                    '<td>' +
                                        '<div class="font-weight-bold text-dark fs-13">' + buyerName + '</div>' +
                                        '<div class="text-muted fs-11">' + buyerPhone + '</div>' +
                                    '</td>' +
                                    '<td><span class="badge bg-blue-lt font-weight-bold">' + o.package_name + '</span></td>' +
                                    '<td class="text-end font-weight-bold text-success">' + o.amount_formatted + '</td>' +
                                    '<td class="text-center"><span class="badge bg-secondary-lt">' + o.service + '</span></td>' +
                                    '<td class="text-center">' + periodBadge + '</td>' +
                                    '<td class="text-end text-muted fs-12">' + o.created_at + '</td>' +
                                '</tr>';
                            });

                            html += '</tbody></table></div>';
                        }
                        html += '</div>';

                        // TAB 2: DANH SÁCH THÀNH VIÊN GIỚI THIỆU
                        html += '<div class="tab-pane fade" id="tab-modal-referrals" role="tabpanel">';
                        if (referrals.length === 0) {
                            html += '<div class="alert alert-info border-0 rounded-3 text-center py-4">' +
                                '<i class="ti ti-info-circle fs-2 d-block mb-1"></i>Chưa có thành viên nào đăng ký qua mã giới thiệu của đối tác này.' +
                            '</div>';
                        } else {
                            html += '<div class="table-responsive border rounded-3"><table class="table table-vcenter table-hover mb-0"><thead class="bg-light">' +
                                '<tr>' +
                                    '<th class="font-weight-bold">Thành viên</th>' +
                                    '<th class="font-weight-bold">Số điện thoại</th>' +
                                    '<th class="font-weight-bold">Email</th>' +
                                    '<th class="font-weight-bold text-end">Ngày đăng ký</th>' +
                                '</tr>' +
                            '</thead><tbody>';

                            referrals.forEach(function(r) {
                                var avatarHtml = r.avatar 
                                    ? '<img src="' + r.avatar + '" alt="' + r.fullname + '" class="avatar-partner" style="width: 32px; height: 32px;">'
                                    : '<div class="avatar-fallback" style="width: 32px; height: 32px; font-size: 12px;">' + (r.fullname ? r.fullname.charAt(0).toUpperCase() : 'K') + '</div>';

                                html += '<tr>' +
                                    '<td>' +
                                        '<div class="d-flex align-items-center gap-2">' +
                                            avatarHtml +
                                            '<span class="font-weight-bold text-dark fs-13">' + r.fullname + '</span>' +
                                        '</div>' +
                                    '</td>' +
                                    '<td class="fs-13 text-muted">' + r.phone + '</td>' +
                                    '<td class="fs-13 text-muted">' + r.email + '</td>' +
                                    '<td class="text-end text-muted fs-12">' + r.created_at + '</td>' +
                                '</tr>';
                            });

                            html += '</tbody></table></div>';
                        }
                        html += '</div>'; // End Tab 2
                        html += '</div>'; // End tab-content

                        container.html(html);
                    }
                },
                error: function(err) {
                    container.html('<div class="alert alert-danger text-center">Không thể tải dữ liệu chi tiết đối tác: ' + (err.responseJSON ? err.responseJSON.message : '') + '</div>');
                }
            });
        });

        // Event Đồng bộ cấp bậc realtime
        $('#btn-sync-ranks').on('click', function() {
            var btn = $(this);
            var icon = $('#icon-sync-ranks');
            btn.prop('disabled', true);
            icon.addClass('ti-spin');

            $.ajax({
                url: "{{ route(RouteAdminSystem::AFFILIATE_STATISTICS_SYNC) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: res.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    refreshData();
                },
                error: function(err) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi đồng bộ',
                        text: err.responseJSON ? err.responseJSON.message : 'Lỗi kết nối'
                    });
                },
                complete: function() {
                    btn.prop('disabled', false);
                    icon.removeClass('ti-spin');
                }
            });
        });
    });
</script>
@endpush
@endsection
