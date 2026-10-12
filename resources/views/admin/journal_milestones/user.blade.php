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
</style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <!-- 1. Top KPI Summary Cards (Bento Grid) -->
            <div class="row g-3 mb-4">
                <!-- Card 1: Tất cả cột mốc -->
                <div class="col-6 col-lg-3">
                    <a href="{{ route('admin.journal_milestone.user') }}" 
                       class="card milestone-kpi-card p-3 {{ empty($currentStage) ? 'active-kpi active-kpi-all' : '' }}">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="kpi-sub-title text-muted text-uppercase fw-bold">{{ __('Tất cả mốc người dùng') }}</span>
                                <div class="kpi-value text-dark fw-extrabold mt-1">{{ number_format($totalCount) }}</div>
                                <div class="kpi-meta text-muted mt-1 fs-12">
                                    <i class="ti ti-users me-1"></i>{{ __('Do phụ huynh tự thêm') }}
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
                    <a href="{{ route('admin.journal_milestone.user', ['stage' => 'newborn']) }}" 
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
                    <a href="{{ route('admin.journal_milestone.user', ['stage' => 'toddler']) }}" 
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
                    <a href="{{ route('admin.journal_milestone.user', ['stage' => 'preschool']) }}" 
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
                            <a href="{{ route('admin.journal_milestone.user', ['stage' => $stage->value]) }}"
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
