@extends('admin.layouts.master')

@push('libs-css')
    <style>
        .report-card-hero { background: linear-gradient(135deg, #206bc4 0%, #5b5bd6 55%, #7c3aed 100%); border: 0; overflow: hidden; }
        .report-card-hero:after { content: ''; position: absolute; width: 240px; height: 240px; right: -60px; top: -110px; border-radius: 50%; background: rgba(255,255,255,.1); }
        .report-card-hero .hero-icon { width: 54px; height: 54px; border-radius: 16px; background: rgba(255,255,255,.16); display: grid; place-items: center; font-size: 28px; }
        .report-stat { border: 0; box-shadow: 0 4px 18px rgba(30,41,59,.07); transition: transform .2s ease, box-shadow .2s ease; }
        .report-stat:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(30,41,59,.11); }
        .report-stat .stat-icon { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; font-size: 21px; flex: 0 0 auto; }
        .report-table-card { border: 0; box-shadow: 0 6px 24px rgba(30,41,59,.07); }
        .report-table-card .dataTables_filter input { border-radius: 10px; min-width: 240px; }
        #GPATable thead th { white-space: nowrap; background: #f8fafc; color: #475569; font-size: 12px; text-transform: uppercase; letter-spacing: .02em; }
        #GPATable tbody tr { transition: background .15s ease; }
        #GPATable tbody tr:hover { background: #f6f9ff; }
        @media (max-width: 767.98px) { .report-card-hero .hero-copy { max-width: 82%; } .report-table-card .dataTables_filter input { min-width: 150px; } }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <div class="card report-card-hero text-white position-relative mb-4">
                <div class="card-body p-4 position-relative" style="z-index:1">
                    <div class="d-flex align-items-center gap-3">
                        <div class="hero-icon"><i class="ti ti-report"></i></div>
                        <div class="hero-copy">
                            <h2 class="mb-1 text-white">Học bạ điện tử</h2>
                            <div class="text-white-75">Theo dõi kết quả học tập, tiến độ cập nhật và mở hồ sơ chi tiết của từng trẻ.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row row-cards mb-4">
                <div class="col-6 col-lg-3"><div class="card report-stat h-100"><div class="card-body d-flex align-items-center gap-3"><span class="stat-icon bg-blue-lt text-blue"><i class="ti ti-books"></i></span><div><div class="text-muted small">Tổng học bạ</div><div class="h2 mb-0">{{ number_format($reportCardStats['total']) }}</div></div></div></div></div>
                <div class="col-6 col-lg-3"><div class="card report-stat h-100"><div class="card-body d-flex align-items-center gap-3"><span class="stat-icon bg-green-lt text-green"><i class="ti ti-circle-check"></i></span><div><div class="text-muted small">Đã hoàn tất</div><div class="h2 mb-0">{{ $reportCardStats['completion_rate'] }}%</div><small class="text-muted">{{ $reportCardStats['completed'] }} hồ sơ</small></div></div></div></div>
                <div class="col-6 col-lg-3"><div class="card report-stat h-100"><div class="card-body d-flex align-items-center gap-3"><span class="stat-icon bg-purple-lt text-purple"><i class="ti ti-chart-bar"></i></span><div><div class="text-muted small">Điểm TB cả năm</div><div class="h2 mb-0">{{ $reportCardStats['average_score'] ?? '—' }}</div></div></div></div></div>
                <div class="col-6 col-lg-3"><div class="card report-stat h-100"><div class="card-body d-flex align-items-center gap-3"><span class="stat-icon {{ $reportCardStats['needs_review'] ? 'bg-orange-lt text-orange' : 'bg-secondary-lt text-secondary' }}"><i class="ti ti-alert-triangle"></i></span><div><div class="text-muted small">Cần kiểm tra</div><div class="h2 mb-0">{{ number_format($reportCardStats['needs_review']) }}</div></div></div></div></div>
            </div>

            <div class="card report-table-card">
                <div class="card-header border-0 pb-0">
                    <div><h3 class="card-title mb-1"><i class="ti ti-list-details text-primary me-2"></i>Danh sách học bạ</h3><div class="text-muted small">Tìm theo mã trẻ, mã phụ huynh, họ tên hoặc lớp học.</div></div>
                </div>
                <div class="card-body">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable />
                        {{ $dataTable->table(['class' => 'table table-vcenter'], true) }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush

@push('custom-js')
    {{ $dataTable->scripts() }}
    @include('admin.scripts.datatable-toggle-columns', ['id_table' => $dataTable->getTableAttribute('id')])
    @include('admin.common.copy')
@endpush
