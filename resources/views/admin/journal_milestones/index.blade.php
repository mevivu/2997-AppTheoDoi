@extends('admin.layouts.master')

@push('libs-css')
<style>
    /* Milestone KPI Cards */
    .milestone-kpi-card {
        border-radius: 14px;
        border: 1.5px solid #e2e8f0;
        background: #ffffff;
        text-decoration: none !important;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }
    .milestone-kpi-card::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }
    .milestone-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
        border-color: #cbd5e1;
    }
    .milestone-kpi-card:hover::after {
        opacity: 0.6;
    }

    /* Active KPI Card States */
    .milestone-kpi-card.active-kpi {
        box-shadow: 0 8px 20px -4px rgba(15, 23, 42, 0.12);
    }
    .milestone-kpi-card.active-kpi::after {
        opacity: 1;
    }

    .active-kpi-all {
        border-color: #3b82f6 !important;
        background: linear-gradient(180deg, #ffffff 0%, #f0f7ff 100%) !important;
    }
    .active-kpi-all::after { background: #3b82f6; }

    .active-kpi-newborn {
        border-color: #0ea5e9 !important;
        background: linear-gradient(180deg, #ffffff 0%, #f0f9ff 100%) !important;
    }
    .active-kpi-newborn::after { background: #0ea5e9; }

    .active-kpi-toddler {
        border-color: #10b981 !important;
        background: linear-gradient(180deg, #ffffff 0%, #f0fdf4 100%) !important;
    }
    .active-kpi-toddler::after { background: #10b981; }

    .active-kpi-preschool {
        border-color: #8b5cf6 !important;
        background: linear-gradient(180deg, #ffffff 0%, #faf5ff 100%) !important;
    }
    .active-kpi-preschool::after { background: #8b5cf6; }

    .kpi-icon-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }
    .milestone-kpi-card:hover .kpi-icon-avatar {
        transform: scale(1.08);
    }
    .kpi-sub-title {
        font-size: 11.5px;
        letter-spacing: 0.04em;
    }
    .kpi-value {
        font-size: 26px;
        line-height: 1.1;
    }

    /* Segmented Control Bar */
    .milestone-segmented-control {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }
    .segmented-pill-item {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 8px;
        text-decoration: none !important;
        color: #64748b;
        transition: all 0.16s ease;
    }
    .segmented-pill-item:hover {
        background: #e2e8f0;
        color: #1e293b;
    }
    .segmented-pill-item.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06), 0 1px 2px rgba(0, 0, 0, 0.04);
        font-weight: 700;
    }
    .segmented-pill-item.active-azure {
        color: #0284c7;
    }
    .segmented-pill-item.active-green {
        color: #16a34a;
    }
    .segmented-pill-item.active-purple {
        color: #7c3aed;
    }
    .pill-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        font-size: 11px;
        font-weight: 700;
        border-radius: 999px;
        background: #e2e8f0;
        color: #475569;
    }
    .segmented-pill-item.active .pill-counter {
        background: #0f172a;
        color: #ffffff;
    }
    .segmented-pill-item.active-azure .pill-counter {
        background: #0284c7;
        color: #ffffff;
    }
    .segmented-pill-item.active-green .pill-counter {
        background: #16a34a;
        color: #ffffff;
    }
    .segmented-pill-item.active-purple .pill-counter {
        background: #7c3aed;
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
                <div class="col-6 col-lg-3">
                    <a href="{{ route('admin.journal_milestone.index') }}" 
                       class="card milestone-kpi-card p-3 {{ empty($currentStage) ? 'active-kpi active-kpi-all' : '' }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="kpi-sub-title text-muted text-uppercase fw-bold">{{ __('Tất cả cột mốc') }}</span>
                                <div class="kpi-value text-dark fw-extrabold mt-1">{{ number_format($totalCount) }}</div>
                                <div class="kpi-meta text-muted mt-1 fs-12">
                                    <i class="ti ti-layers-intersect me-1"></i>{{ __('3 giai đoạn phát triển') }}
                                </div>
                            </div>
                            <div class="kpi-icon-avatar bg-primary-lt text-primary">
                                <i class="ti ti-flag-3 fs-22"></i>
                            </div>
                        </div>
                        @if(empty($currentStage))
                            <span class="badge bg-primary text-white position-absolute top-0 end-0 m-2 px-2 py-0.5 fs-11 rounded-pill">
                                <i class="ti ti-check me-0.5"></i>{{ __('Đang lọc') }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Card 2: Sơ sinh (0–1 tuổi) -->
                <div class="col-6 col-lg-3">
                    <a href="{{ route('admin.journal_milestone.index', ['stage' => 'newborn']) }}" 
                       class="card milestone-kpi-card p-3 {{ $currentStage === 'newborn' ? 'active-kpi active-kpi-newborn' : '' }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="kpi-sub-title text-azure text-uppercase fw-bold">{{ __('Sơ sinh (0–1 tuổi)') }}</span>
                                <div class="kpi-value text-dark fw-extrabold mt-1">{{ number_format($stageCounts['newborn'] ?? 0) }}</div>
                                <div class="kpi-meta text-muted mt-1 fs-12">
                                    <i class="ti ti-clock me-1"></i>{{ __('0 – 12 tháng tuổi') }}
                                </div>
                            </div>
                            <div class="kpi-icon-avatar bg-azure-lt text-azure">
                                <i class="ti ti-baby-bottle fs-22"></i>
                            </div>
                        </div>
                        @if($currentStage === 'newborn')
                            <span class="badge bg-azure text-white position-absolute top-0 end-0 m-2 px-2 py-0.5 fs-11 rounded-pill">
                                <i class="ti ti-check me-0.5"></i>{{ __('Đang lọc') }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Card 3: Tập đi (1–3 tuổi) -->
                <div class="col-6 col-lg-3">
                    <a href="{{ route('admin.journal_milestone.index', ['stage' => 'toddler']) }}" 
                       class="card milestone-kpi-card p-3 {{ $currentStage === 'toddler' ? 'active-kpi active-kpi-toddler' : '' }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="kpi-sub-title text-success text-uppercase fw-bold">{{ __('Tập đi (1–3 tuổi)') }}</span>
                                <div class="kpi-value text-dark fw-extrabold mt-1">{{ number_format($stageCounts['toddler'] ?? 0) }}</div>
                                <div class="kpi-meta text-muted mt-1 fs-12">
                                    <i class="ti ti-clock me-1"></i>{{ __('12 – 36 tháng tuổi') }}
                                </div>
                            </div>
                            <div class="kpi-icon-avatar bg-success-lt text-success">
                                <i class="ti ti-walk fs-22"></i>
                            </div>
                        </div>
                        @if($currentStage === 'toddler')
                            <span class="badge bg-success text-white position-absolute top-0 end-0 m-2 px-2 py-0.5 fs-11 rounded-pill">
                                <i class="ti ti-check me-0.5"></i>{{ __('Đang lọc') }}
                            </span>
                        @endif
                    </a>
                </div>

                <!-- Card 4: Mẫu giáo (3–6 tuổi) -->
                <div class="col-6 col-lg-3">
                    <a href="{{ route('admin.journal_milestone.index', ['stage' => 'preschool']) }}" 
                       class="card milestone-kpi-card p-3 {{ $currentStage === 'preschool' ? 'active-kpi active-kpi-preschool' : '' }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="kpi-sub-title text-purple text-uppercase fw-bold">{{ __('Mẫu giáo (3–6 tuổi)') }}</span>
                                <div class="kpi-value text-dark fw-extrabold mt-1">{{ number_format($stageCounts['preschool'] ?? 0) }}</div>
                                <div class="kpi-meta text-muted mt-1 fs-12">
                                    <i class="ti ti-clock me-1"></i>{{ __('36 – 72 tháng tuổi') }}
                                </div>
                            </div>
                            <div class="kpi-icon-avatar bg-purple-lt text-purple">
                                <i class="ti ti-palette fs-22"></i>
                            </div>
                        </div>
                        @if($currentStage === 'preschool')
                            <span class="badge bg-purple text-white position-absolute top-0 end-0 m-2 px-2 py-0.5 fs-11 rounded-pill">
                                <i class="ti ti-check me-0.5"></i>{{ __('Đang lọc') }}
                            </span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- 2. Main Data Table Card -->
            <div class="card custom-shadow border-0 rounded-3">
                <x-admin.page-header :title="__('Cột mốc nhật ký')"
                                     :subtitle="__('Danh mục cột mốc gợi ý theo giai đoạn phát triển của trẻ')"
                                     icon="ti ti-flag-3"
                                     :addRoute="route('admin.journal_milestone.create')"
                                     :addText="__('Thêm cột mốc')" />

                <div class="card-body pt-3">
                    <!-- Segmented Control Pills Filter -->
                    <div class="milestone-segmented-control mb-3 p-1.5 rounded-3 d-flex flex-wrap align-items-center gap-1.5">
                        <span class="text-muted fw-bold px-2 py-1.5 d-inline-flex align-items-center gap-1 fs-13">
                            <i class="ti ti-filter text-primary fs-15"></i>
                            <span>{{ __('Lọc giai đoạn:') }}</span>
                        </span>

                        <a href="{{ route('admin.journal_milestone.index') }}" 
                           class="segmented-pill-item {{ empty($currentStage) ? 'active' : '' }}">
                            <i class="ti ti-apps"></i>
                            <span>{{ __('Tất cả') }}</span>
                            <span class="pill-counter">{{ $totalCount }}</span>
                        </a>

                        @foreach($stages as $stage)
                            @php
                                $isActive = ($currentStage === $stage->value);
                                $stageIcon = match($stage) {
                                    \App\Enums\Journal\MilestoneStage::Newborn => 'ti-baby-bottle',
                                    \App\Enums\Journal\MilestoneStage::Toddler => 'ti-walk',
                                    \App\Enums\Journal\MilestoneStage::Preschool => 'ti-palette',
                                };
                                $stageColor = match($stage) {
                                    \App\Enums\Journal\MilestoneStage::Newborn => 'azure',
                                    \App\Enums\Journal\MilestoneStage::Toddler => 'green',
                                    \App\Enums\Journal\MilestoneStage::Preschool => 'purple',
                                };
                            @endphp
                            <a href="{{ route('admin.journal_milestone.index', ['stage' => $stage->value]) }}"
                               class="segmented-pill-item {{ $isActive ? 'active active-' . $stageColor : '' }}">
                                <i class="ti {{ $stageIcon }}"></i>
                                <span>{{ $stage->label() }}</span>
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

