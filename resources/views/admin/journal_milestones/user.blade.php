@extends('admin.layouts.master')

@push('libs-css')
    <link rel="stylesheet" href="https://cdn.datatables.net/select/1.6.2/css/select.bootstrap5.min.css">
@endpush

@push('custom-css')
<style>
    /* Bento Grid KPI Cards */
    .milestone-kpi-card {
        border-radius: 14px;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
        border: 1px solid rgba(0, 0, 0, 0.06);
        background: #ffffff;
        position: relative;
        overflow: hidden;
        text-decoration: none !important;
        display: block;
    }
    .milestone-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 24px -6px rgba(0, 0, 0, 0.08);
        border-color: rgba(var(--tblr-primary-rgb), 0.3);
    }
    .milestone-kpi-card.active-kpi {
        border-width: 2px;
        box-shadow: 0 8px 20px -4px rgba(var(--tblr-primary-rgb), 0.2);
    }
    .milestone-kpi-card.active-kpi-all {
        border-color: var(--tblr-primary);
        background: linear-gradient(135deg, rgba(var(--tblr-primary-rgb), 0.04) 0%, #ffffff 100%);
    }
    .milestone-kpi-card.active-kpi-newborn {
        border-color: #0ca678;
        background: linear-gradient(135deg, rgba(12, 166, 120, 0.04) 0%, #ffffff 100%);
    }
    .milestone-kpi-card.active-kpi-toddler {
        border-color: #2fb344;
        background: linear-gradient(135deg, rgba(47, 179, 68, 0.04) 0%, #ffffff 100%);
    }
    .milestone-kpi-card.active-kpi-preschool {
        border-color: #7c3aed;
        background: linear-gradient(135deg, rgba(124, 58, 237, 0.04) 0%, #ffffff 100%);
    }
    .milestone-kpi-card.active-kpi-primary_school {
        border-color: #f97316;
        background: linear-gradient(135deg, rgba(249, 115, 22, 0.04) 0%, #ffffff 100%);
    }
    .milestone-kpi-card.active-kpi-secondary_school {
        border-color: #6366f1;
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.04) 0%, #ffffff 100%);
    }
    .kpi-icon-avatar {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .kpi-value {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -0.02em;
    }
    .kpi-sub-title {
        font-size: 0.8125rem;
        letter-spacing: 0.03em;
    }

    /* Segmented Control Filter */
    .milestone-segmented-control {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
    }
    .segmented-pill-item {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.45rem 0.9rem;
        border-radius: 8px;
        font-size: 0.845rem;
        font-weight: 600;
        color: #475569;
        text-decoration: none !important;
        transition: all 0.18s ease-in-out;
    }
    .segmented-pill-item:hover {
        color: #0f172a;
        background: rgba(255, 255, 255, 0.7);
    }
    .segmented-pill-item.active {
        background: #ffffff;
        color: var(--tblr-primary);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }
    .segmented-pill-item.active-azure {
        color: #0ca678;
    }
    .segmented-pill-item.active-green {
        color: #2fb344;
    }
    .segmented-pill-item.active-purple {
        color: #7c3aed;
    }
    .segmented-pill-item.active-orange {
        color: #ea580c;
    }
    .segmented-pill-item.active-indigo {
        color: #4f46e5;
    }
    .pill-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.1rem 0.45rem;
        border-radius: 999px;
        font-size: 0.75rem;
        background: #e2e8f0;
        color: #475569;
        font-weight: 700;
    }
    .segmented-pill-item.active .pill-counter {
        background: var(--tblr-primary);
        color: #ffffff;
    }
    .segmented-pill-item.active-azure .pill-counter {
        background: #0ca678;
        color: #ffffff;
    }
    .segmented-pill-item.active-green .pill-counter {
        background: #2fb344;
        color: #ffffff;
    }
    .segmented-pill-item.active-purple .pill-counter {
        background: #7c3aed;
        color: #ffffff;
    }
    .segmented-pill-item.active-orange .pill-counter {
        background: #ea580c;
        color: #ffffff;
    }
    .segmented-pill-item.active-indigo .pill-counter {
        background: #4f46e5;
        color: #ffffff;
    }
</style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <!-- 1. Top KPI Summary Cards (Bento Grid) -->
            <div class="row g-3 mb-4">
                <!-- Card 1: Tất cả cột mốc -->
                <div class="col-6 col-md-4 col-xl-2">
                    <a href="{{ route('admin.journal_milestone.user') }}" 
                       class="card milestone-kpi-card p-3 h-100 {{ empty($currentStage) ? 'active-kpi active-kpi-all' : '' }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="kpi-sub-title text-muted text-uppercase fw-bold">{{ __('Tất cả') }}</span>
                                <div class="kpi-value text-dark fw-extrabold mt-1">{{ number_format($totalCount) }}</div>
                                <div class="kpi-meta text-muted mt-1 fs-12">
                                    <i class="ti ti-users me-1"></i>{{ count($stages) }} giai đoạn
                                </div>
                            </div>
                            <div class="kpi-icon-avatar bg-primary-lt text-primary">
                                <i class="ti ti-flag-3 fs-20"></i>
                            </div>
                        </div>
                        @if(empty($currentStage))
                            <span class="badge bg-primary text-white position-absolute top-0 end-0 m-2 px-2 py-0.5 fs-11 rounded-pill">
                                <i class="ti ti-check me-0.5"></i>
                            </span>
                        @endif
                    </a>
                </div>

                @foreach($stages as $stage)
                    @php
                        $isActive = ($currentStage === $stage->value);
                        $stageColor = $stage->color();
                        $stageIcon = $stage->icon();
                        $ageRange = $stage->ageRangeMonths();
                    @endphp
                    <div class="col-6 col-md-4 col-xl-2">
                        <a href="{{ route('admin.journal_milestone.user', ['stage' => $stage->value]) }}" 
                           class="card milestone-kpi-card p-3 h-100 {{ $isActive ? 'active-kpi active-kpi-' . $stage->value : '' }}">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <span class="kpi-sub-title text-{{ $stageColor }} text-uppercase fw-bold">{{ $stage->shortLabel() }}</span>
                                    <div class="kpi-value text-dark fw-extrabold mt-1">{{ number_format($stageCounts[$stage->value] ?? 0) }}</div>
                                    <div class="kpi-meta text-muted mt-1 fs-12">
                                        <i class="ti ti-clock me-1"></i>{{ $ageRange[0] }}–{{ $ageRange[1] }} tháng
                                    </div>
                                </div>
                                <div class="kpi-icon-avatar bg-{{ $stageColor }}-lt text-{{ $stageColor }}">
                                    <i class="ti {{ $stageIcon }} fs-20"></i>
                                </div>
                            </div>
                            @if($isActive)
                                <span class="badge bg-{{ $stageColor }} text-white position-absolute top-0 end-0 m-2 px-2 py-0.5 fs-11 rounded-pill">
                                    <i class="ti ti-check me-0.5"></i>
                                </span>
                            @endif
                        </a>
                    </div>
                @endforeach
            </div>

            <!-- 2. Main Data Table Card -->
            <div class="card custom-shadow border-0 rounded-3">
                <x-admin.page-header :title="__('Cột mốc người dùng')"
                                     :subtitle="__('Danh sách các cột mốc do phụ huynh tự thêm theo từng bé')"
                                     icon="ti ti-users" />

                <div class="card-body pt-3">
                    <!-- Segmented Control Pills Filter -->
                    <div class="milestone-segmented-control mb-3 p-1.5 rounded-3 d-flex flex-wrap align-items-center gap-1.5">
                        <span class="text-muted fw-bold px-2 py-1.5 d-inline-flex align-items-center gap-1 fs-13">
                            <i class="ti ti-filter text-primary fs-15"></i>
                            <span>{{ __('Lọc giai đoạn:') }}</span>
                        </span>

                        <a href="{{ route('admin.journal_milestone.user') }}" 
                           class="segmented-pill-item {{ empty($currentStage) ? 'active' : '' }}">
                            <i class="ti ti-apps"></i>
                            <span>{{ __('Tất cả') }}</span>
                            <span class="pill-counter">{{ $totalCount }}</span>
                        </a>

                        @foreach($stages as $stage)
                            @php
                                $isActive = ($currentStage === $stage->value);
                                $stageIcon = $stage->icon();
                                $stageColor = $stage->color();
                            @endphp
                            <a href="{{ route('admin.journal_milestone.user', ['stage' => $stage->value]) }}"
                               class="segmented-pill-item {{ $isActive ? 'active active-' . $stageColor : '' }}">
                                <i class="ti {{ $stageIcon }}"></i>
                                <span>{{ $stage->shortLabel() }}</span>
                                <span class="pill-counter">{{ $stageCounts[$stage->value] ?? 0 }}</span>
                            </a>
                        @endforeach
                    </div>

                    <!-- DataTable Table with Column Toggle -->
                    <div class="table-responsive position-relative mt-2">
                        <x-admin.partials.toggle-column-datatable/>
                        {{ $dataTable->table(['class' => 'table table-bordered table-striped align-middle table-hover'], true) }}
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

    @include('admin.scripts.datatable-toggle-columns', [
        'id_table' => $dataTable->getTableAttribute('id')
    ])
@endpush
