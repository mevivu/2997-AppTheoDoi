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
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .kpi-value {
        font-size: 1.85rem;
        font-weight: 800;
        line-height: 1.2;
        color: #0f172a;
    }

    .kpi-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 600;
    }

    /* Segmented Filter Pills */
    .filter-pill-container {
        background: #E2E8F0;
        padding: 4px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 2px;
        flex-wrap: wrap;
    }

    .btn-filter-pill {
        background: transparent;
        border: none;
        color: #475569;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 30px;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }

    .btn-filter-pill:hover {
        color: #0f172a;
    }

    .btn-filter-pill.active {
        background: #FFFFFF;
        color: #206bc4;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
    }

    .btn-filter-pill.disabled {
        opacity: 0.6;
        pointer-events: none;
    }

    .category-tab-btn {
        font-weight: 600;
        font-size: 0.85rem;
        padding: 6px 16px;
        border-radius: 10px;
        cursor: pointer;
        border: 1px solid #e2e8f0;
        background: #fff;
        color: #475569;
        transition: all 0.2s;
    }

    .category-tab-btn.active {
        background: #206bc4;
        color: #fff;
        border-color: #206bc4;
    }

    .badge-rank {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .rank-1 { background: #fef08a; color: #854d0e; }
    .rank-2 { background: #e2e8f0; color: #475569; }
    .rank-3 { background: #fed7aa; color: #9a3412; }
    .rank-other { background: #f1f5f9; color: #64748b; }

    .chart-card {
        border: none;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }
</style>

<div class="page-body">
    <div class="container-fluid px-4">

        <!-- Header: Title & Actions -->
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
            <div>
                <h1 class="report-title mb-1">
                    <i class="ti ti-chart-pie text-primary me-2"></i>Thống Kê Chức Năng
                </h1>
                <p class="text-muted mb-0 font-weight-medium">
                    Theo dõi tần suất và mức độ sử dụng của phụ huynh đối với các chức năng Đánh giá toàn diện & Tiện ích
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-primary d-flex align-items-center gap-1 font-weight-semibold shadow-sm px-3" data-bs-toggle="modal" data-bs-target="#modal-statistics-logic">
                    <i class="ti ti-help-circle fs-3"></i>
                    <span>Giải thích logic thống kê</span>
                </button>
                <span class="badge bg-blue-lt px-3 py-2 font-weight-bold" id="badge-date-range" style="font-size: 0.85rem;">
                    <i class="ti ti-calendar me-1"></i>{{ $stats['date_range_label'] }}
                </span>
                <a href="{{ route(RouteAdminSystem::FEATURE_STATISTICS_EXPORT, ['period' => $currentPeriod, 'category' => $currentCategory]) }}"
                   id="btn-export-csv" class="btn btn-outline-success font-weight-semibold">
                    <i class="ti ti-download me-1"></i> Xuất Excel / CSV
                </a>
            </div>
        </div>

        <!-- Filter Control Bar -->
        <div class="card mb-4 border-0 shadow-sm rounded-3">
            <div class="card-body p-3">
                <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                    <!-- Category Switcher -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="text-muted font-weight-semibold me-1 fs-13">Phân loại:</span>
                        <button type="button" class="category-tab-btn btn-category active" data-category="evaluation">
                            <i class="ti ti-brain me-1"></i>Đánh giá toàn diện
                        </button>
                        <button type="button" class="category-tab-btn btn-category" data-category="utility">
                            <i class="ti ti-tool me-1"></i>Tiện ích
                        </button>
                        <button type="button" class="category-tab-btn btn-category" data-category="all">
                            <i class="ti ti-apps me-1"></i>Tất cả
                        </button>
                    </div>

                    <!-- Time Period Quick Filters -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <span class="text-muted font-weight-semibold me-1 fs-13">Thời gian:</span>
                        <div class="filter-pill-container">
                            <button type="button" class="btn-filter-pill btn-period" data-period="1d">Hôm nay</button>
                            <button type="button" class="btn-filter-pill btn-period" data-period="7d">7 Ngày</button>
                            <button type="button" class="btn-filter-pill btn-period active" data-period="30d">30 Ngày</button>
                            <button type="button" class="btn-filter-pill btn-period" data-period="this_month">Tháng này</button>
                            <button type="button" class="btn-filter-pill btn-period" data-period="last_month">Tháng trước</button>
                            <button type="button" class="btn-filter-pill btn-period" data-period="all">Tất cả</button>
                        </div>
                    </div>
                </div>

                <!-- Modern Date Range Selector (Litepicker) -->
                <div class="mt-3 pt-3 border-top d-flex align-items-center gap-3 flex-wrap">
                    <span class="text-muted fs-13 font-weight-medium d-flex align-items-center gap-1">
                        <i class="ti ti-calendar-event text-primary fs-3"></i> Hoặc chọn khoảng ngày tùy chỉnh:
                    </span>
                    <div class="input-icon" style="min-width: 270px;">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar text-muted"></i>
                        </span>
                        <input type="text" id="datepicker-range" class="form-control form-control-sm bg-white shadow-none rounded-pill" 
                               placeholder="Chọn khoảng ngày (dd/mm/yyyy - dd/mm/yyyy)" 
                               value="{{ ($from && $to) ? \Carbon\Carbon::parse($from)->format('d/m/Y') . ' - ' . \Carbon\Carbon::parse($to)->format('d/m/Y') : '' }}" 
                               readonly style="cursor: pointer; font-size: 0.85rem; font-weight: 500;">
                    </div>
                    <button type="button" id="btn-clear-custom-date" class="btn btn-sm btn-ghost-danger rounded-pill px-2 d-flex align-items-center gap-1" style="display: {{ ($from && $to) ? 'inline-flex' : 'none' }}; font-size: 0.8rem;">
                        <i class="ti ti-x"></i> Xóa lọc ngày
                    </button>
                </div>
            </div>
        </div>

        <!-- KPI Metric Cards -->
        <div class="row g-3 mb-4">
            <!-- KPI 1: Tổng lượt sử dụng -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-kpi p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">TỔNG LƯỢT SỬ DỤNG</div>
                            <div class="kpi-value mt-1" id="kpi-total-usages">
                                {{ number_format($stats['kpis']['total_usages']) }}
                            </div>
                            <div class="mt-2 fs-12" id="kpi-growth-container">
                                @if($stats['kpis']['growth'] >= 0)
                                    <span class="text-success font-weight-bold">
                                        <i class="ti ti-arrow-up-right"></i> +{{ $stats['kpis']['growth'] }}%
                                    </span>
                                @else
                                    <span class="text-danger font-weight-bold">
                                        <i class="ti ti-arrow-down-right"></i> {{ $stats['kpis']['growth'] }}%
                                    </span>
                                @endif
                                <span class="text-muted ms-1">so với kỳ trước</span>
                            </div>
                        </div>
                        <div class="kpi-icon-box bg-blue-lt text-primary">
                            <i class="ti ti-click"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI 2: Chức năng Top 1 -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-kpi p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="min-w-0">
                            <div class="kpi-label">CHỨC NĂNG DÙNG NHIỀU NHẤT</div>
                            <div class="kpi-value mt-1 text-truncate" id="kpi-top-feature-name" style="font-size: 1.35rem;" title="{{ $stats['kpis']['top_feature']['name'] ?? 'Chưa có' }}">
                                {{ $stats['kpis']['top_feature']['short_name'] ?? 'Chưa có' }}
                            </div>
                            <div class="mt-2 fs-12" id="kpi-top-feature-count">
                                <span class="badge bg-yellow-lt font-weight-bold px-2 py-1">
                                    <i class="ti ti-trophy text-warning me-1"></i>{{ number_format($stats['kpis']['top_feature']['count'] ?? 0) }} lượt ({{ $stats['kpis']['top_feature']['percentage'] ?? 0 }}%)
                                </span>
                            </div>
                        </div>
                        <div class="kpi-icon-box bg-yellow-lt text-warning">
                            <i class="ti ti-trophy"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI 3: Phụ huynh tham gia -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-kpi p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">PHỤ HUYNH THAM GIA</div>
                            <div class="kpi-value mt-1" id="kpi-unique-users">
                                {{ number_format($stats['kpis']['unique_users_count']) }}
                            </div>
                            <div class="mt-2 fs-12 text-muted">
                                <i class="ti ti-user-check text-success me-1"></i>Phụ huynh có hoạt động
                            </div>
                        </div>
                        <div class="kpi-icon-box bg-green-lt text-success">
                            <i class="ti ti-users"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- KPI 4: Trẻ em được đánh giá -->
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card card-kpi p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="kpi-label">TRẺ ĐƯỢC ĐÁNH GIÁ</div>
                            <div class="kpi-value mt-1" id="kpi-unique-children">
                                {{ number_format($stats['kpis']['unique_children_count']) }}
                            </div>
                            <div class="mt-2 fs-12 text-muted">
                                <i class="ti ti-heart text-danger me-1"></i>Hồ sơ trẻ có dữ liệu
                            </div>
                        </div>
                        <div class="kpi-icon-box bg-purple-lt text-purple">
                            <i class="ti ti-mood-smile"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section (amCharts 5) -->
        <div class="row g-3 mb-4">
            <!-- Chart 1: Xu hướng sử dụng theo thời gian -->
            <div class="col-12 col-xl-8">
                <div class="card chart-card h-100">
                    <div class="card-header border-0 bg-transparent pt-3 pb-2 d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title font-weight-bold mb-0">
                                <i class="ti ti-chart-line text-primary me-2"></i>Xu hướng sử dụng theo thời gian
                            </h3>
                            <div class="text-muted fs-12 mt-1">Biểu đồ thể hiện lượt sử dụng của từng chức năng theo các mốc thời gian</div>
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

            <!-- Chart 2: Tỷ trọng phân bổ các chức năng -->
            <div class="col-12 col-xl-4">
                <div class="card chart-card h-100">
                    <div class="card-header border-0 bg-transparent pt-3 pb-2">
                        <div>
                            <h3 class="card-title font-weight-bold mb-0">
                                <i class="ti ti-chart-donut text-success me-2"></i>Tỷ trọng sử dụng
                            </h3>
                            <div class="text-muted fs-12 mt-1">Cơ cấu phần trăm giữa các chức năng</div>
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

        <!-- Detailed Ranking & Statistics Table -->
        <div class="card chart-card">
            <div class="card-header border-bottom bg-white py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h3 class="card-title font-weight-bold mb-0">
                        <i class="ti ti-list-numbers text-indigo me-2"></i>Bảng xếp hạng mức độ sử dụng chức năng
                    </h3>
                    <div class="text-muted fs-12 mt-1">Danh sách sắp xếp theo số lượt người dùng sử dụng từ cao xuống thấp</div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-vcenter table-hover card-table mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="w-1 text-center font-weight-bold"># Hạng</th>
                            <th class="font-weight-bold">Chức năng</th>
                            <th class="font-weight-bold">Nhóm chức năng</th>
                            <th class="font-weight-bold" style="min-width: 180px;">Lượt sử dụng</th>
                            <th class="text-center font-weight-bold">Tỷ trọng</th>
                            <th class="text-center font-weight-bold">Số phụ huynh</th>
                            <th class="text-center font-weight-bold">Số trẻ em</th>
                            <th class="text-center font-weight-bold">Tăng trưởng</th>
                        </tr>
                    </thead>
                    <tbody id="table-ranking-body">
                        @forelse($stats['ranking'] as $index => $item)
                            <tr>
                                <td class="text-center">
                                    <span class="badge-rank {{ $index === 0 ? 'rank-1' : ($index === 1 ? 'rank-2' : ($index === 2 ? 'rank-3' : 'rank-other')) }}">
                                        {{ $index + 1 }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-xs rounded-circle text-white d-flex align-items-center justify-content-center"
                                             style="background-color: {{ $item['color'] }}; width: 32px; height: 32px; font-size: 15px;">
                                            <i class="{{ $item['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <div class="font-weight-bold text-dark">{{ $item['name'] }}</div>
                                            <div class="text-muted fs-12">Mã: <code>{{ $item['code'] }}</code></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $item['badge_class'] }}">
                                        {{ $item['category_name'] }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="font-weight-bold text-dark fs-14" style="min-width: 45px;">
                                            {{ number_format($item['count']) }}
                                        </span>
                                        <div class="progress progress-sm flex-grow-1" style="height: 6px; background: #f1f5f9;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: {{ $item['percentage'] }}%; background-color: {{ $item['color'] }};"
                                                 aria-valuenow="{{ $item['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center font-weight-bold text-muted">
                                    {{ $item['percentage'] }}%
                                </td>
                                <td class="text-center font-weight-bold text-dark">
                                    {{ number_format($item['unique_users']) }}
                                </td>
                                <td class="text-center font-weight-bold text-dark">
                                    {{ number_format($item['unique_children']) }}
                                </td>
                                <td class="text-center">
                                    @if($item['growth'] > 0)
                                        <span class="text-success font-weight-semibold">
                                            <i class="ti ti-trending-up me-1"></i>+{{ $item['growth'] }}%
                                        </span>
                                    @elseif($item['growth'] < 0)
                                        <span class="text-danger font-weight-semibold">
                                            <i class="ti ti-trending-down me-1"></i>{{ $item['growth'] }}%
                                        </span>
                                    @else
                                        <span class="text-muted font-weight-semibold">0%</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="ti ti-database-off fs-1 d-block mb-2"></i>
                                    Không có dữ liệu trong khoảng thời gian này
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Giải thích Logic Thống Kê & Nguồn Dữ Liệu -->
<div class="modal modal-blur fade" id="modal-statistics-logic" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="ti ti-info-circle fs-1"></i>
                    <div>
                        <h4 class="modal-title font-weight-bold mb-0 text-white">Giải Thích Logic Tính Toán & Nguồn Dữ Liệu Thống Kê</h4>
                        <div class="fs-12 text-white-50">Minh bạch cơ chế thu thập và phương pháp tổng hợp số liệu trên hệ thống</div>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background: #F8FAFC;">
                
                <!-- 1. Mô hình tổng hợp 2 tầng -->
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h4 class="font-weight-bold text-dark d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-blue-lt p-2 rounded-circle"><i class="ti ti-layers-intersect fs-3 text-primary"></i></span>
                            Mô Hình Dữ Liệu Kết Hợp 2 Tầng (Two-Tier Hybrid Architecture)
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="font-weight-bold text-primary mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-database"></i> Tầng 1: Dữ liệu nghiệp vụ lịch sử (Actual Records)
                                    </div>
                                    <p class="text-muted fs-13 mb-0">
                                        Hệ thống kết nối trực tiếp với các bảng cơ sở dữ liệu đã có từ trước (kết quả bài kiểm tra IQ, EQ, AQ, PQ, điểm học bạ GPA, hồ sơ tiêm chủng, thai kỳ, nhật ký...). 
                                        Giúp trang thống kê <strong>ngay lập tức có sẵn dữ liệu lịch sử đầy đủ</strong> của hàng chục ngàn lượt đánh giá trước đây mà không bị trống.
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="font-weight-bold text-success mb-1 d-flex align-items-center gap-1">
                                        <i class="ti ti-device-mobile"></i> Tầng 2: Ghi nhận sự kiện thời gian thực (Mobile Event Tracking)
                                    </div>
                                    <p class="text-muted fs-13 mb-0">
                                        Mỗi khi phụ huynh nhấn vào một chức năng trên ứng dụng di động <code>App-User</code>, ứng dụng sẽ gửi ngầm một gói tin tracking qua API <code>/api/v1/tracking/feature-usage</code> lưu vào bảng <code>feature_usages</code>. 
                                        Quá trình này chạy bất đồng bộ trong nền, hoàn toàn <strong>không gây chậm hay gián đoạn trải nghiệm</strong> của người dùng.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Bảng quy chuẩn nguồn dữ liệu chi tiết -->
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h4 class="font-weight-bold text-dark d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-green-lt p-2 rounded-circle"><i class="ti ti-table fs-3 text-success"></i></span>
                            Nguồn Dữ Liệu & Quy Tắc Tính Toán Theo Từng Chức Năng
                        </h4>
                        <div class="table-responsive">
                            <table class="table table-vcenter table-bordered bg-white fs-13 mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="font-weight-bold" style="width: 220px;">Chức năng</th>
                                        <th class="font-weight-bold text-center" style="width: 120px;">Nhóm</th>
                                        <th class="font-weight-bold" style="width: 220px;">Bảng dữ liệu nguồn</th>
                                        <th class="font-weight-bold">Cách tính Lượt sử dụng</th>
                                        <th class="font-weight-bold">Quy đổi Phụ huynh & Trẻ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-bulb text-warning me-1"></i>Thông minh (IQ)</div>
                                            <span class="text-muted fs-11">Mã: <code>iq</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-blue-lt">Đánh giá</span></td>
                                        <td><code>ratings</code> (type='iq')<br>+ <code>feature_usages</code></td>
                                        <td>Đếm số lượt nộp bài đánh giá chỉ số IQ hoàn thành trong kỳ.</td>
                                        <td><code>child_id</code> từ kết quả đánh giá &rarr; tra cứu <code>user_id</code> từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-heart-handshake text-danger me-1"></i>Cảm xúc (EQ)</div>
                                            <span class="text-muted fs-11">Mã: <code>eq</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-blue-lt">Đánh giá</span></td>
                                        <td><code>ratings</code> (type='eq')<br>+ <code>feature_usages</code></td>
                                        <td>Đếm số lượt nộp bài đánh giá chỉ số EQ hoàn thành trong kỳ.</td>
                                        <td><code>child_id</code> từ kết quả đánh giá &rarr; tra cứu <code>user_id</code> từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-shield-check text-success me-1"></i>Vượt khó (AQ)</div>
                                            <span class="text-muted fs-11">Mã: <code>aq</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-blue-lt">Đánh giá</span></td>
                                        <td><code>ratings</code> (type='aq')<br>+ <code>feature_usages</code></td>
                                        <td>Đếm số lượt nộp bài đánh giá chỉ số AQ hoàn thành trong kỳ.</td>
                                        <td><code>child_id</code> từ kết quả đánh giá &rarr; tra cứu <code>user_id</code> từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-run text-info me-1"></i>Thể chất (PQ)</div>
                                            <span class="text-muted fs-11">Mã: <code>pq</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-blue-lt">Đánh giá</span></td>
                                        <td><code>ratings_pqs</code><br>+ <code>feature_usages</code></td>
                                        <td>Đếm số bản ghi đo lường chỉ số thể chất của trẻ trong kỳ.</td>
                                        <td><code>child_id</code> từ kết quả đo &rarr; tra cứu <code>user_id</code> từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-school text-purple me-1"></i>Học bạ điện tử (GPA)</div>
                                            <span class="text-muted fs-11">Mã: <code>gpa</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-blue-lt">Đánh giá</span></td>
                                        <td><code>class_grades</code><br>+ <code>feature_usages</code></td>
                                        <td>Đếm số bản ghi điểm số các môn học được lưu trong sổ học bạ.</td>
                                        <td><code>child_id</code> của học sinh &rarr; tra cứu <code>user_id</code> từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-vaccine text-teal me-1"></i>Theo dõi lịch tiêm chủng</div>
                                            <span class="text-muted fs-11">Mã: <code>vaccine</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-azure-lt">Tiện ích</span></td>
                                        <td><code>vaccination_schedules</code><br>+ <code>feature_usages</code></td>
                                        <td>Đếm số mũi tiêm/lịch tiêm chủng được tạo hoặc cập nhật trong kỳ.</td>
                                        <td><code>child_id</code> của bé &rarr; tra cứu <code>user_id</code> phụ huynh từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-woman text-pink me-1"></i>Theo dõi thai kỳ</div>
                                            <span class="text-muted fs-11">Mã: <code>pregnancy</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-azure-lt">Tiện ích</span></td>
                                        <td><code>pregnancies</code><br>+ <code>feature_usages</code></td>
                                        <td>Đếm số lần cập nhật chỉ số thai kỳ (cân nặng, chiều dài, tuần thai).</td>
                                        <td><code>child_id</code> thai nhi &rarr; tra cứu <code>user_id</code> người mẹ từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-file-medical text-red me-1"></i>Hồ sơ y tế / Đơn thuốc</div>
                                            <span class="text-muted fs-11">Mã: <code>diary_prescription</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-azure-lt">Tiện ích</span></td>
                                        <td><code>journals</code> (type='prescription')<br>+ <code>feature_usages</code></td>
                                        <td>Đếm số lần lưu đơn thuốc, toa khám bệnh trong sổ khám y tế của bé.</td>
                                        <td><code>child_id</code> của bé &rarr; tra cứu <code>user_id</code> phụ huynh từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-photo-heart text-orange me-1"></i>Nhật ký khoảnh khắc</div>
                                            <span class="text-muted fs-11">Mã: <code>diary_moment</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-azure-lt">Tiện ích</span></td>
                                        <td><code>journals</code> (type='moment')<br>+ <code>feature_usages</code></td>
                                        <td>Đếm số bài viết khoảnh khắc, hình ảnh nhật ký được phụ huynh chia sẻ.</td>
                                        <td><code>child_id</code> của bé &rarr; tra cứu <code>user_id</code> phụ huynh từ bảng <code>children</code>.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-ruler-measure text-lime me-1"></i>Dự đoán chiều cao</div>
                                            <span class="text-muted fs-11">Mã: <code>predict_height</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-azure-lt">Tiện ích</span></td>
                                        <td><code>feature_usages</code> (từ App)</td>
                                        <td>Đếm số lượt phụ huynh mở công cụ tính toán dự đoán chiều cao tương lai.</td>
                                        <td>Ghi nhận trực tiếp <code>user_id</code> và <code>child_id</code> lúc mở chức năng trên App.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-timeline text-indigo me-1"></i>Quá trình phát triển</div>
                                            <span class="text-muted fs-11">Mã: <code>develop</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-azure-lt">Tiện ích</span></td>
                                        <td><code>feature_usages</code> (từ App)</td>
                                        <td>Đếm số lượt phụ huynh xem các mốc phát triển theo chuẩn WHO.</td>
                                        <td>Ghi nhận trực tiếp <code>user_id</code> và <code>child_id</code> lúc mở chức năng trên App.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-shopping-cart text-cyan me-1"></i>Cửa hàng / Sản phẩm</div>
                                            <span class="text-muted fs-11">Mã: <code>store</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-azure-lt">Tiện ích</span></td>
                                        <td><code>feature_usages</code> (từ App)</td>
                                        <td>Đếm số lượt phụ huynh truy cập xem danh mục cửa hàng/sản phẩm chăm con.</td>
                                        <td>Ghi nhận trực tiếp <code>user_id</code> của tài khoản đăng nhập.</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="font-weight-bold text-dark"><i class="ti ti-building-hospital text-secondary me-1"></i>Tìm phòng khám</div>
                                            <span class="text-muted fs-11">Mã: <code>clinic</code></span>
                                        </td>
                                        <td class="text-center"><span class="badge bg-azure-lt">Tiện ích</span></td>
                                        <td><code>feature_usages</code> (từ App)</td>
                                        <td>Đếm số lượt phụ huynh tra cứu bản đồ phòng khám & cơ sở y tế.</td>
                                        <td>Ghi nhận trực tiếp <code>user_id</code> của tài khoản đăng nhập.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 3. Công thức tính toán các chỉ số KPI -->
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-4">
                        <h4 class="font-weight-bold text-dark d-flex align-items-center gap-2 mb-3">
                            <span class="badge bg-purple-lt p-2 rounded-circle"><i class="ti ti-math-function fs-3 text-purple"></i></span>
                            Phương Pháp Tính Toán Các Chỉ Số KPI & Tăng Trưởng
                        </h4>
                        <div class="row g-3">
                            <div class="col-md-6 col-lg-3">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="font-weight-bold text-dark fs-14 mb-1">1. Tổng Lượt Sử Dụng</div>
                                    <div class="text-muted fs-12 mb-2">Đo lường tần suất hoạt động tổng thể</div>
                                    <span class="badge bg-blue-lt">COUNT(*)</span>
                                    <div class="fs-12 text-secondary mt-2">Tổng toàn bộ lượt phát sinh từ tất cả chức năng trong khoảng ngày được lọc.</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="font-weight-bold text-dark fs-14 mb-1">2. Tỷ Lệ Tăng Trưởng</div>
                                    <div class="text-muted fs-12 mb-2">So sánh cùng kỳ trước đó</div>
                                    <span class="badge bg-green-lt text-wrap">((Kỳ này - Kỳ trước) / Kỳ trước) * 100%</span>
                                    <div class="fs-12 text-secondary mt-2">Kỳ trước có độ dài ngày bằng kỳ đang chọn (ví dụ: 30 ngày so với 30 ngày liền kề trước đó).</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="font-weight-bold text-dark fs-14 mb-1">3. Phụ Huynh Tham Gia</div>
                                    <div class="text-muted fs-12 mb-2">Số người dùng duy nhất</div>
                                    <span class="badge bg-orange-lt">COUNT(DISTINCT user_id)</span>
                                    <div class="fs-12 text-secondary mt-2">Một phụ huynh thao tác nhiều lần hay trên nhiều trẻ chỉ tính là 1 phụ huynh tham gia.</div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-3">
                                <div class="p-3 bg-light rounded-3 border h-100">
                                    <div class="font-weight-bold text-dark fs-14 mb-1">4. Trẻ Được Đánh Giá</div>
                                    <div class="text-muted fs-12 mb-2">Số hồ sơ trẻ em duy nhất</div>
                                    <span class="badge bg-pink-lt">COUNT(DISTINCT child_id)</span>
                                    <div class="fs-12 text-secondary mt-2">Đếm số lượng hồ sơ trẻ em không trùng lặp có dữ liệu đánh giá/tiện ích trong kỳ.</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-white border-top py-2 px-4 d-flex justify-content-between">
                <span class="text-muted fs-12"><i class="ti ti-shield-check text-success me-1"></i>Hệ thống tự động đồng bộ và bảo mật dữ liệu trẻ em theo tiêu chuẩn ISO</span>
                <button type="button" class="btn btn-primary px-4 fw-bold" data-bs-dismiss="modal">
                    <i class="ti ti-check me-1"></i> Đã hiểu
                </button>
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
    // Global Chart references for dynamic updates
    var trendRoot, trendChart, trendXAxis, trendYAxis;
    var trendSeriesList = {};
    var donutRoot, donutChart, donutSeries;

    var currentPeriod = '{{ $currentPeriod }}';
    var currentCategory = '{{ $currentCategory }}';
    var initialStats = {!! json_encode($stats) !!};

    // 1. Initialize Trend XY Chart
    am5.ready(function() {
        initTrendChart(initialStats.trend_data, initialStats.trend_series);
        initDonutChart(initialStats.donut_data);
    });

    function initTrendChart(data, seriesList) {
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
            renderer: am5xy.AxisRendererX.new(trendRoot, { minGridDistance: 40 }),
            tooltip: am5.Tooltip.new(trendRoot, {})
        }));

        trendYAxis = trendChart.yAxes.push(am5xy.ValueAxis.new(trendRoot, {
            min: 0,
            renderer: am5xy.AxisRendererY.new(trendRoot, {})
        }));

        // Legend
        var legend = trendChart.children.push(am5.Legend.new(trendRoot, {
            centerX: am5.p50,
            x: am5.p50,
            marginTop: 10
        }));

        trendSeriesList = {};

        seriesList.forEach(function(s) {
            var series = trendChart.series.push(am5xy.SmoothedXLineSeries.new(trendRoot, {
                name: s.name,
                xAxis: trendXAxis,
                yAxis: trendYAxis,
                valueYField: s.field,
                categoryXField: "date",
                stroke: am5.color(s.color),
                fill: am5.color(s.color),
                tooltip: am5.Tooltip.new(trendRoot, {
                    labelText: "{name}: [bold]{valueY}[/] lượt"
                })
            }));

            series.strokes.template.setAll({ strokeWidth: 2.5 });
            series.fills.template.setAll({
                fillOpacity: 0.1,
                visible: true
            });

            series.data.setAll(data);
            legend.data.push(series);
            trendSeriesList[s.field] = series;
        });

        trendXAxis.data.setAll(data);
        trendChart.appear(800, 100);
    }

    // 2. Initialize Donut Chart
    function initDonutChart(data) {
        if (donutRoot) {
            donutRoot.dispose();
        }

        donutRoot = am5.Root.new("chart-donut");
        donutRoot._logo.dispose();
        donutRoot.setThemes([am5themes_Animated.new(donutRoot)]);

        if (!data || data.length === 0) {
            donutRoot.container.children.push(am5.Label.new(donutRoot, {
                text: "Không có dữ liệu trong khoảng thời gian này",
                fontSize: 13,
                fill: am5.color(0x94a3b8),
                centerX: am5.p50,
                centerY: am5.p50,
                x: am5.p50,
                y: am5.p50
            }));
            return;
        }

        donutChart = donutRoot.container.children.push(am5percent.PieChart.new(donutRoot, {
            layout: donutRoot.verticalLayout,
            innerRadius: am5.percent(65)
        }));

        donutSeries = donutChart.series.push(am5percent.PieSeries.new(donutRoot, {
            valueField: "value",
            categoryField: "category",
            alignLabels: false
        }));

        // Hide cluttered slice labels and ticks completely to prevent overlapping
        donutSeries.labels.template.set("forceHidden", true);
        donutSeries.ticks.template.set("forceHidden", true);

        donutSeries.slices.template.setAll({
            templateField: "sliceSettings",
            stroke: am5.color(0xffffff),
            strokeWidth: 2,
            tooltipText: "{category}: [bold]{value}[/] lượt ({percentage})"
        });

        donutSeries.slices.template.adapters.add("fill", function(fill, target) {
            var dataItem = target.dataItem;
            if (dataItem && dataItem.dataContext && dataItem.dataContext.color) {
                return am5.color(dataItem.dataContext.color);
            }
            return fill;
        });

        var total = (data || []).reduce(function(sum, item) { return sum + Number(item.value || 0); }, 0);

        // Center Label in the Donut Hole
        donutChart.seriesContainer.children.push(am5.Label.new(donutRoot, {
            textAlign: "center",
            centerY: am5.percent(50),
            centerX: am5.percent(50),
            text: "[#64748b;fontSize:11px;fontWeight:600]TỔNG LƯỢT[/]\n[bold;fontSize:20px;color:#0f172a]" + Number(total).toLocaleString('vi-VN') + "[/]"
        }));

        // Clean interactive Legend below the Donut
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
            maxWidth: 120,
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

    // 3. AJAX Data Refresh Handler
    function refreshData(params) {
        $('#chart-loading-trend, #chart-loading-donut').removeClass('d-none');
        $('.btn-filter-pill, .category-tab-btn').addClass('disabled');

        $.ajax({
            url: "{{ route(RouteAdminSystem::FEATURE_STATISTICS_INDEX) }}",
            type: 'GET',
            data: params,
            success: function(response) {
                if (response.status === 'success') {
                    var data = response.data;

                    // 1. Update Date range label
                    $('#badge-date-range').html('<i class="ti ti-calendar me-1"></i>' + data.date_range_label);

                    // 2. Update Export CSV link
                    var exportUrl = "{{ route(RouteAdminSystem::FEATURE_STATISTICS_EXPORT) }}?" + $.param(params);
                    $('#btn-export-csv').attr('href', exportUrl);

                    // 3. Update KPIs
                    $('#kpi-total-usages').text(Number(data.kpis.total_usages).toLocaleString('vi-VN'));
                    $('#kpi-unique-users').text(Number(data.kpis.unique_users_count).toLocaleString('vi-VN'));
                    $('#kpi-unique-children').text(Number(data.kpis.unique_children_count).toLocaleString('vi-VN'));

                    // Growth badge
                    var growthHtml = '';
                    if (data.kpis.growth >= 0) {
                        growthHtml = '<span class="text-success font-weight-bold"><i class="ti ti-arrow-up-right"></i> +' + data.kpis.growth + '%</span>';
                    } else {
                        growthHtml = '<span class="text-danger font-weight-bold"><i class="ti ti-arrow-down-right"></i> ' + data.kpis.growth + '%</span>';
                    }
                    growthHtml += '<span class="text-muted ms-1">so với kỳ trước</span>';
                    $('#kpi-growth-container').html(growthHtml);

                    // Top Feature
                    if (data.kpis.top_feature) {
                        $('#kpi-top-feature-name').text(data.kpis.top_feature.short_name).attr('title', data.kpis.top_feature.name);
                        $('#kpi-top-feature-count').html(
                            '<span class="badge bg-yellow-lt font-weight-bold px-2 py-1"><i class="ti ti-trophy text-warning me-1"></i>' +
                            Number(data.kpis.top_feature.count).toLocaleString('vi-VN') + ' lượt (' + data.kpis.top_feature.percentage + '%)</span>'
                        );
                    } else {
                        $('#kpi-top-feature-name').text('Chưa có');
                        $('#kpi-top-feature-count').html('<span class="badge bg-light text-muted">0 lượt</span>');
                    }

                    // 4. Re-render Charts
                    initTrendChart(data.trend_data, data.trend_series);
                    initDonutChart(data.donut_data);

                    // 5. Update Ranking Table
                    var tableHtml = '';
                    if (data.ranking && data.ranking.length > 0) {
                        data.ranking.forEach(function(item, idx) {
                            var rankClass = idx === 0 ? 'rank-1' : (idx === 1 ? 'rank-2' : (idx === 2 ? 'rank-3' : 'rank-other'));
                            var growthText = '';
                            if (item.growth > 0) {
                                growthText = '<span class="text-success font-weight-semibold"><i class="ti ti-trending-up me-1"></i>+' + item.growth + '%</span>';
                            } else if (item.growth < 0) {
                                growthText = '<span class="text-danger font-weight-semibold"><i class="ti ti-trending-down me-1"></i>' + item.growth + '%</span>';
                            } else {
                                growthText = '<span class="text-muted font-weight-semibold">0%</span>';
                            }

                            tableHtml += '<tr>' +
                                '<td class="text-center"><span class="badge-rank ' + rankClass + '">' + (idx + 1) + '</span></td>' +
                                '<td><div class="d-flex align-items-center gap-2">' +
                                '<div class="avatar avatar-xs rounded-circle text-white d-flex align-items-center justify-content-center" style="background-color: ' + item.color + '; width: 32px; height: 32px; font-size: 15px;"><i class="' + item.icon + '"></i></div>' +
                                '<div><div class="font-weight-bold text-dark">' + item.name + '</div><div class="text-muted fs-12">Mã: <code>' + item.code + '</code></div></div>' +
                                '</div></td>' +
                                '<td><span class="badge ' + item.badge_class + '">' + item.category_name + '</span></td>' +
                                '<td><div class="d-flex align-items-center gap-2">' +
                                '<span class="font-weight-bold text-dark fs-14" style="min-width: 45px;">' + Number(item.count).toLocaleString('vi-VN') + '</span>' +
                                '<div class="progress progress-sm flex-grow-1" style="height: 6px; background: #f1f5f9;"><div class="progress-bar" style="width: ' + item.percentage + '%; background-color: ' + item.color + ';"></div></div>' +
                                '</div></td>' +
                                '<td class="text-center font-weight-bold text-muted">' + item.percentage + '%</td>' +
                                '<td class="text-center font-weight-bold text-dark">' + Number(item.unique_users).toLocaleString('vi-VN') + '</td>' +
                                '<td class="text-center font-weight-bold text-dark">' + Number(item.unique_children).toLocaleString('vi-VN') + '</td>' +
                                '<td class="text-center">' + growthText + '</td>' +
                                '</tr>';
                        });
                    } else {
                        tableHtml = '<tr><td colspan="8" class="text-center py-4 text-muted"><i class="ti ti-database-off fs-1 d-block mb-2"></i>Không có dữ liệu trong khoảng thời gian này</td></tr>';
                    }
                    $('#table-ranking-body').html(tableHtml);
                }
            },
            error: function(err) {
                alert('Có lỗi xảy ra khi tải dữ liệu thống kê.');
            },
            complete: function() {
                $('#chart-loading-trend, #chart-loading-donut').addClass('d-none');
                $('.btn-filter-pill, .category-tab-btn').removeClass('disabled');
            }
        });
    }

    // Global DateRange Picker reference
    var dateRangePicker = null;

    // Event Listeners
    $(document).ready(function() {
        // Initialize Litepicker Date Range
        if (window.Litepicker && document.getElementById('datepicker-range')) {
            dateRangePicker = new Litepicker({
                element: document.getElementById('datepicker-range'),
                singleMode: false,
                numberOfMonths: 2,
                numberOfColumns: 2,
                format: "DD/MM/YYYY",
                delimiter: " - ",
                autoApply: true,
                allowRepick: true,
                buttonText: {
                    previousMonth: '<i class="ti ti-chevron-left"></i>',
                    nextMonth: '<i class="ti ti-chevron-right"></i>'
                },
                setup: function(picker) {
                    picker.on('selected', function(d1, d2) {
                        if (d1 && d2) {
                            var from = d1.format('YYYY-MM-DD');
                            var to = d2.format('YYYY-MM-DD');
                            currentPeriod = 'custom';
                            $('.btn-period').removeClass('active');
                            $('#btn-clear-custom-date').css('display', 'inline-flex');

                            refreshData({
                                period: 'custom',
                                category: currentCategory,
                                from: from,
                                to: to
                            });
                        }
                    });
                }
            });
        }

        // 1. Period Button Click
        $('.btn-period').on('click', function() {
            var btn = $(this);
            if (btn.hasClass('active')) return;

            $('.btn-period').removeClass('active');
            btn.addClass('active');
            currentPeriod = btn.data('period');

            // Reset Litepicker input when preset period is chosen
            if (dateRangePicker) {
                dateRangePicker.clearSelection();
            }
            $('#datepicker-range').val('');
            $('#btn-clear-custom-date').hide();

            refreshData({
                period: currentPeriod,
                category: currentCategory
            });
        });

        // 2. Category Tab Click
        $('.btn-category').on('click', function() {
            var btn = $(this);
            if (btn.hasClass('active')) return;

            $('.btn-category').removeClass('active');
            btn.addClass('active');
            currentCategory = btn.data('category');

            var params = {
                period: currentPeriod,
                category: currentCategory
            };

            if (currentPeriod === 'custom' && dateRangePicker && dateRangePicker.getStartDate() && dateRangePicker.getEndDate()) {
                params.from = dateRangePicker.getStartDate().format('YYYY-MM-DD');
                params.to = dateRangePicker.getEndDate().format('YYYY-MM-DD');
            }

            refreshData(params);
        });

        // 3. Clear Custom Date Range Filter
        $('#btn-clear-custom-date').on('click', function() {
            if (dateRangePicker) {
                dateRangePicker.clearSelection();
            }
            $('#datepicker-range').val('');
            $(this).hide();

            currentPeriod = '30d';
            $('.btn-period').removeClass('active');
            $('.btn-period[data-period="30d"]').addClass('active');

            refreshData({
                period: '30d',
                category: currentCategory
            });
        });
    });
</script>
@endpush
@endsection
