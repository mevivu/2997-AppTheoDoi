@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
<style>
/* ==============================================================
   TOURNAMENT LEADERBOARD ENHANCED STYLING
   ============================================================== */
:root {
    --gold-glow: rgba(245, 158, 11, 0.25);
    --silver-glow: rgba(148, 163, 184, 0.25);
    --bronze-glow: rgba(234, 88, 12, 0.25);
}

/* 1. Hero Tournament Info Banner */
.tournament-hero-card {
    background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);
    border: 1px solid #bae6fd;
    border-radius: 18px;
    padding: 20px 24px;
    position: relative;
    box-shadow: 0 8px 24px rgba(2, 132, 199, 0.08);
}
.tournament-hero-banner {
    width: 140px;
    aspect-ratio: 16 / 9;
    border-radius: 12px;
    object-fit: cover;
    border: 2px solid #ffffff;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}
.theme-mini-chip {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 50px;
    padding: 4px 10px 4px 5px;
    font-size: 11px;
    font-weight: 600;
    color: #1e293b;
    box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    transition: all 0.2s ease;
}
.theme-mini-chip:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.08);
    border-color: #0284c7;
}
.theme-mini-img {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    object-fit: contain;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
}

/* 2. Modern Stat Cards */
.stat-kpi-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 18px 20px;
    border: 1px solid #e2e8f0;
    position: relative;
    overflow: hidden;
    transition: all 0.25s ease;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    height: 100%;
}
.stat-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.06);
    border-color: #cbd5e1;
}
.stat-kpi-card::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
}
.stat-kpi-card.kpi-blue::after { background: linear-gradient(90deg, #0284c7, #38bdf8); }
.stat-kpi-card.kpi-green::after { background: linear-gradient(90deg, #16a34a, #4ade80); }
.stat-kpi-card.kpi-amber::after { background: linear-gradient(90deg, #d97706, #fbbf24); }
.stat-kpi-card.kpi-purple::after { background: linear-gradient(90deg, #7c3aed, #c084fc); }

.stat-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}
.kpi-blue .stat-icon-wrapper { background: #e0f2fe; color: #0284c7; }
.kpi-green .stat-icon-wrapper { background: #dcfce7; color: #16a34a; }
.kpi-amber .stat-icon-wrapper { background: #fef3c7; color: #d97706; }
.kpi-purple .stat-icon-wrapper { background: #f3e8ff; color: #7c3aed; }

/* 3. Champions Podium */
.podium-section {
    position: relative;
    padding: 10px 0 20px;
}
.podium-container {
    display: flex;
    justify-content: center;
    align-items: flex-end;
    gap: 20px;
    margin: 15px auto 30px;
    max-width: 900px;
}
.podium-item {
    flex: 1;
    background: #ffffff;
    border-radius: 20px;
    padding: 24px 18px 20px;
    text-align: center;
    position: relative;
    border: 2px solid transparent;
    box-shadow: 0 12px 30px rgba(0,0,0,0.06);
    transition: all 0.3s ease;
}
.podium-item:hover {
    transform: translateY(-6px);
}
.podium-first {
    order: 2;
    border-color: #f59e0b;
    background: linear-gradient(180deg, #fffbeb 0%, #ffffff 80%);
    transform: scale(1.06);
    box-shadow: 0 16px 36px var(--gold-glow);
    z-index: 2;
}
.podium-first:hover {
    transform: scale(1.06) translateY(-6px);
}
.podium-second {
    order: 1;
    border-color: #94a3b8;
    background: linear-gradient(180deg, #f8fafc 0%, #ffffff 80%);
    box-shadow: 0 12px 28px var(--silver-glow);
}
.podium-third {
    order: 3;
    border-color: #ea580c;
    background: linear-gradient(180deg, #fff7ed 0%, #ffffff 80%);
    box-shadow: 0 12px 28px var(--bronze-glow);
}
.podium-crown {
    position: absolute;
    top: -18px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 26px;
    filter: drop-shadow(0 2px 4px rgba(245, 158, 11, 0.4));
}
.podium-avatar-wrapper {
    position: relative;
    width: 80px;
    height: 80px;
    margin: 4px auto 12px;
}
.podium-first .podium-avatar-wrapper {
    width: 92px;
    height: 92px;
}
.podium-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #ffffff;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.podium-medal-badge {
    position: absolute;
    bottom: -4px;
    right: -4px;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 6px rgba(0,0,0,0.2);
}
.badge-gold { background: linear-gradient(135deg, #fde68a, #f59e0b); color: #78350f; }
.badge-silver { background: linear-gradient(135deg, #f1f5f9, #94a3b8); color: #1e293b; }
.badge-bronze { background: linear-gradient(135deg, #fed7aa, #ea580c); color: #7c2d12; }

.podium-name {
    font-weight: 800;
    font-size: 16px;
    color: #0f172a;
    margin-bottom: 2px;
}
.podium-time {
    font-size: 22px;
    font-weight: 900;
    color: #0284c7;
    line-height: 1.2;
    margin-bottom: 4px;
}
.podium-first .podium-time {
    font-size: 26px;
    color: #d97706;
}
.podium-stat-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    background: rgba(0,0,0,0.04);
    padding: 3px 10px;
    border-radius: 50px;
}

/* 4. Table Ranking Badges & Leaderboard Table */
.table-leaderboard {
    margin-bottom: 0;
}
.table-leaderboard th {
    white-space: nowrap;
    vertical-align: middle;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #475569;
    padding: 12px 14px;
    background-color: #f8fafc;
    border-bottom: 2px solid #e2e8f0;
}
.table-leaderboard td {
    vertical-align: middle;
    padding: 12px 14px;
}
.phone-badge {
    background: #ecfdf5;
    color: #059669;
    border: 1px solid #a7f3d0;
    border-radius: 50px;
    padding: 2.5px 8px;
    font-size: 12px;
    font-family: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    transition: all 0.15s ease;
}
.phone-badge:hover {
    background: #d1fae5;
    color: #047857;
    border-color: #6ee7b7;
    text-decoration: none;
}
.btn-copy-phone {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background: #ffffff;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-copy-phone:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #cbd5e1;
}
.btn-copy-phone.copied {
    background: #dcfce7 !important;
    color: #16a34a !important;
    border-color: #86efac !important;
}
.rank-badge {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 14px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.06);
}
.rank-1 { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #b45309; border: 2px solid #f59e0b; }
.rank-2 { background: linear-gradient(135deg, #f8fafc, #e2e8f0); color: #475569; border: 2px solid #94a3b8; }
.rank-3 { background: linear-gradient(135deg, #ffedd5, #fed7aa); color: #c2410c; border: 2px solid #f97316; }
.rank-other { background: #f8fafc; color: #475569; border: 1.5px solid #cbd5e1; font-size: 13px; }

/* 5. Mini Round Indicators */
.mini-rounds-strip {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}
.mini-round-chip {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
    transition: transform 0.15s ease;
}
.mini-round-chip:hover {
    transform: scale(1.2);
    z-index: 2;
}
.round-won { background: #dcfce7; color: #15803d; border: 1px solid #86efac; }
.round-lost { background: #fee2e2; color: #b91c1c; border: 1px solid #fca5a5; }
.round-pending { background: #f1f5f9; color: #94a3b8; border: 1px solid #cbd5e1; }

/* 6. Filter Tabs */
.filter-tab-btn {
    border-radius: 50px !important;
    padding: 6px 16px;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    color: #475569;
}
.filter-tab-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}
.filter-tab-btn.active {
    background: #0284c7 !important;
    color: #ffffff !important;
    border-color: #0284c7 !important;
    box-shadow: 0 4px 10px rgba(2, 132, 199, 0.25);
}

/* 7. Modal Round Cards */
.round-detail-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 14px;
    padding: 14px;
    background: #ffffff;
    transition: all 0.2s ease;
}
.round-detail-card:hover {
    border-color: #0284c7;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);
}
.round-detail-card.won {
    border-left: 4px solid #16a34a;
}
.round-detail-card.lost {
    border-left: 4px solid #ef4444;
}

/* Print Styles */
@media print {
    .btn, .navbar, .page-header, .filter-tab-btn, #leaderboardSearch { display: none !important; }
    .card { border: none !important; box-shadow: none !important; }
}
</style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <!-- Header chuẩn CMS -->
            <x-admin.page-header
                :title="__('Bảng Xếp Hạng: ') . $competition->name"
                :subtitle="__('Tiêu chí xếp hạng: ① Thắng đủ 4/4 ván -> ② Tổng thời gian ngắn nhất -> ③ Tiebreaker: Số lượt lật ít nhất')"
                icon="trophy"
                :back-route="route(RouteAdminSystem::MEMO_COMPETITION_INDEX)"
            >
                <x-slot:actions>
                    <!-- Nút Tính & Chốt Thứ Hạng (Pill Xanh Lá Nổi Bật) -->
                    <form action="{{ route('admin.memo-game.competition.calculate', $competition->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('{{ __('Bạn có chắc muốn tính toán và chốt thứ hạng chính thức cho toàn bộ giải đấu này?') }}')">
                        @csrf
                        <button type="submit" class="btn btn-success rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1.5" style="border-radius: 50px !important;">
                            <i class="ti ti-calculator fs-5"></i>
                            <span class="fw-semibold">{{ __('Tính & Chốt Thứ Hạng') }}</span>
                        </button>
                    </form>

                    <!-- Nút Sửa giải đấu đồng bộ dạng pill -->
                    <a href="{{ route(RouteAdminSystem::MEMO_COMPETITION_EDIT, $competition->id) }}" class="btn btn-outline-primary rounded-pill px-3 d-inline-flex align-items-center gap-1.5" style="border-radius: 50px !important;">
                        <i class="ti ti-edit"></i>
                        <span>{{ __('Sửa giải đấu') }}</span>
                    </a>

                    <!-- Nút In / Xuất nhanh -->
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1.5" onclick="window.print()" style="border-radius: 50px !important;" title="{{ __('In bảng xếp hạng') }}">
                        <i class="ti ti-printer"></i>
                        <span>{{ __('In BXH') }}</span>
                    </button>
                </x-slot:actions>
            </x-admin.page-header>

            <!-- 1. Hero Tournament Info Banner -->
            <div class="tournament-hero-card mb-4">
                <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        @if ($competition->banner_image)
                            <img src="{{ asset($competition->banner_image) }}" alt="Banner" class="tournament-hero-banner d-none d-sm-block">
                        @else
                            <div class="tournament-hero-banner d-none d-sm-flex align-items-center justify-content-center bg-primary-lt text-primary">
                                <i class="ti ti-trophy fs-1"></i>
                            </div>
                        @endif

                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h4 class="fw-extrabold text-dark mb-0 fs-18">{{ $competition->name }}</h4>
                                
                                <!-- Trạng thái giải đấu -->
                                @php
                                    $now = now();
                                    $isUpcoming = $competition->start_at && $now < $competition->start_at;
                                    $isFinished = $competition->end_at && $now > $competition->end_at;
                                @endphp

                                @if ($isFinal)
                                    <span class="badge bg-success shadow-2xs fs-11">
                                        <i class="ti ti-circle-check me-1"></i>{{ __('Đã chốt chính thức') }}
                                    </span>
                                @elseif ($isFinished)
                                    <span class="badge bg-danger shadow-2xs fs-11">
                                        <i class="ti ti-clock-off me-1"></i>{{ __('Đã kết thúc') }}
                                    </span>
                                @elseif ($isUpcoming)
                                    <span class="badge bg-warning shadow-2xs fs-11">
                                        <i class="ti ti-calendar-time me-1"></i>{{ __('Sắp diễn ra') }}
                                    </span>
                                @else
                                    <span class="badge bg-primary shadow-2xs fs-11">
                                        <i class="ti ti-flame me-1"></i>{{ __('Đang diễn ra (Live)') }}
                                    </span>
                                @endif

                                <span class="badge bg-light text-muted border fs-11">
                                    <i class="ti ti-grid-dots me-1"></i>Lưới 5×6 (30 thẻ)
                                </span>
                                <span class="badge bg-light text-muted border fs-11">
                                    <i class="ti ti-rotate me-1"></i>{{ $competition->max_attempts > 1 ? "Tối đa {$competition->max_attempts} lượt/bé" : "Mỗi bé 1 lượt" }}
                                </span>
                            </div>

                            <div class="text-muted fs-12 mb-2 d-flex align-items-center gap-3 flex-wrap">
                                <span><i class="ti ti-calendar me-1 text-primary"></i><strong>{{ $competition->start_at?->format('d/m/Y H:i') }}</strong> — <strong>{{ $competition->end_at?->format('d/m/Y H:i') }}</strong></span>
                                @if ($competition->end_at && $now < $competition->end_at && !$isFinished)
                                    <span class="text-primary fw-medium"><i class="ti ti-hourglass-low me-1"></i>Còn {{ $now->diffForHumans($competition->end_at, ['parts' => 2, 'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE]) }}</span>
                                @endif
                            </div>

                            <!-- 4 Chủ đề ván thi với ảnh thực tế -->
                            <div class="d-flex align-items-center gap-1.5 flex-wrap">
                                <span class="fs-11 fw-bold text-uppercase text-muted me-1">{{ __('4 Ván thi:') }}</span>
                                @foreach ($competition->competitionThemes->sortBy('game_order') as $ct)
                                    @php
                                        $t = $ct->theme;
                                        $tImg = !empty($t?->icon) && $t?->icon !== \App\Traits\ImageSystem::DEFAULT_IMAGE ? asset($t->icon) : asset($t?->card_back);
                                    @endphp
                                    <div class="theme-mini-chip" title="Ván {{ $ct->game_order }}: {{ $t?->name }}">
                                        @if ($tImg)
                                            <img src="{{ $tImg }}" alt="{{ $t?->name }}" class="theme-mini-img">
                                        @else
                                            <i class="ti ti-cards text-primary"></i>
                                        @endif
                                        <span>Ván {{ $ct->game_order }}: {{ $t?->name ? Str::limit($t->name, 16) : 'Chủ đề' }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Khối 4 Thẻ KPI Thông Minh -->
            <div class="row g-3 mb-4">
                <!-- Thẻ 1: Tổng Thí Sinh -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-kpi-card kpi-blue">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted fs-11 fw-bold text-uppercase tracking-wider mb-1">{{ __('Tổng Thí Sinh') }}</div>
                                <div class="fs-24 fw-extrabold text-dark">{{ $totalParticipants }} <span class="fs-14 fw-medium text-muted">bé</span></div>
                                <div class="fs-12 text-muted mt-1">
                                    <i class="ti ti-activity me-1 text-primary"></i>{{ $totalAttempts }} lượt thi đấu
                                </div>
                            </div>
                            <div class="stat-icon-wrapper shadow-xs">
                                <i class="ti ti-users"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thẻ 2: Hoàn Thành Đủ 4/4 Ván -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-kpi-card kpi-green">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted fs-11 fw-bold text-uppercase tracking-wider mb-1">{{ __('Hoàn Thành 4/4 Ván') }}</div>
                                <div class="fs-24 fw-extrabold text-success">{{ $completedCount }} <span class="fs-14 fw-medium text-muted">lượt</span></div>
                                <div class="fs-12 text-muted mt-1">
                                    <i class="ti ti-percentage me-1 text-success"></i>
                                    <strong>{{ $totalAttempts > 0 ? round(($completedCount / $totalAttempts) * 100) : 0 }}%</strong> tỷ lệ đạt chuẩn
                                </div>
                            </div>
                            <div class="stat-icon-wrapper shadow-xs">
                                <i class="ti ti-trophy"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thẻ 3: Kỷ Lục Nhanh Nhất (Top 1 Record) -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-kpi-card kpi-amber">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted fs-11 fw-bold text-uppercase tracking-wider mb-1">{{ __('Kỷ Lục Best Time') }}</div>
                                <div class="fs-24 fw-extrabold text-warning">
                                    @if ($bestRecord)
                                        {{ $bestRecord->total_time }}s
                                    @else
                                        <span class="text-muted fs-20">--:--</span>
                                    @endif
                                </div>
                                <div class="fs-12 text-muted mt-1 text-truncate" style="max-width: 170px;">
                                    @if ($bestRecord)
                                        <i class="ti ti-medal me-1 text-warning"></i>{{ $bestRecord->child?->fullname }} ({{ gmdate("i:s", $bestRecord->total_time) }})
                                    @else
                                        <i class="ti ti-clock me-1"></i>{{ __('Chờ đón kỷ lục đầu tiên') }}
                                    @endif
                                </div>
                            </div>
                            <div class="stat-icon-wrapper shadow-xs">
                                <i class="ti ti-bolt"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thẻ 4: Trạng Thái BXH & Lần Chốt -->
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-kpi-card kpi-purple">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <div class="text-muted fs-11 fw-bold text-uppercase tracking-wider mb-1">{{ __('Trạng Thái BXH') }}</div>
                                <div class="fs-18 fw-extrabold {{ $isFinal ? 'text-purple' : 'text-primary' }}">
                                    {{ $isFinal ? __('Đã chốt chính thức') : __('Tạm thời (Thời gian thực)') }}
                                </div>
                                <div class="fs-12 text-muted mt-1">
                                    @if ($competition->ranking_calculated_at)
                                        <i class="ti ti-check me-1 text-purple"></i>Chốt: {{ $competition->ranking_calculated_at->format('H:i d/m/Y') }}
                                    @else
                                        <i class="ti ti-refresh me-1 text-primary"></i>Tự động cập nhật tức thì
                                    @endif
                                </div>
                            </div>
                            <div class="stat-icon-wrapper shadow-xs">
                                <i class="ti {{ $isFinal ? 'ti-shield-check' : 'ti-device-analytics' }}"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Bục Vinh Quang Top 3 (Champions Podium) -->
            @if (count($leaderboard) > 0)
                <div class="card custom-shadow mb-4 overflow-hidden border-0">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 d-flex align-items-center gap-2 fs-15 text-dark fw-bold">
                            <i class="ti ti-crown text-warning fs-4"></i> {{ __('Bục Vinh Quang — Top 3 Thí Sinh Xuất Sắc Nhất') }}
                        </h5>
                        <span class="badge bg-warning-lt fw-bold fs-11">
                            <i class="ti ti-sparkles me-1"></i>Champions Hall
                        </span>
                    </div>
                    <div class="card-body bg-light py-4">
                        <div class="podium-container">
                            <!-- Hạng 2 (Á Quân - Bạc) -->
                            @if (isset($leaderboard[1]))
                                @php $second = $leaderboard[1]; @endphp
                                <div class="podium-item podium-second">
                                    <div class="podium-avatar-wrapper">
                                        <img src="{{ $second->child?->avatar ? asset($second->child->avatar) : asset('images/avatar-default.png') }}" 
                                             class="podium-avatar" alt="Hạng 2">
                                        <div class="podium-medal-badge badge-silver">🥈</div>
                                    </div>
                                    <div class="text-uppercase fw-extrabold text-muted fs-11 tracking-wider mb-1">{{ __('Á Quân 2') }}</div>
                                    <div class="podium-name text-truncate">{{ $second->child?->fullname }}</div>
                                    <div class="podium-time">{{ $second->total_time }}s</div>
                                    <div class="text-muted fs-11 mb-2">({{ gmdate("i:s", $second->total_time) }})</div>
                                    <div class="podium-stat-pill">
                                        <i class="ti ti-hand-click"></i>{{ $second->total_moves }} lần lật
                                    </div>
                                    @if ($second->child?->user)
                                        <div class="text-muted fs-11 mt-1 text-truncate" style="max-width: 190px;" title="PH: {{ $second->child->user->fullname }} ({{ $second->child->user->decrypted_phone }})">
                                            <i class="ti ti-user me-0.5"></i>{{ $second->child->user->fullname }}
                                            @if ($second->child->user->decrypted_phone)
                                                • <span class="text-success font-monospace">{{ $second->child->user->decrypted_phone }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Hạng 1 (Quán Quân - Vàng) -->
                            @if (isset($leaderboard[0]))
                                @php $first = $leaderboard[0]; @endphp
                                <div class="podium-item podium-first">
                                    <div class="podium-crown">👑</div>
                                    <div class="podium-avatar-wrapper">
                                        <img src="{{ $first->child?->avatar ? asset($first->child->avatar) : asset('images/avatar-default.png') }}" 
                                             class="podium-avatar" alt="Hạng 1">
                                        <div class="podium-medal-badge badge-gold">🥇</div>
                                    </div>
                                    <div class="text-uppercase fw-extrabold text-warning fs-12 tracking-wider mb-1">
                                        <i class="ti ti-trophy me-1"></i>{{ __('Quán Quân 1') }}
                                    </div>
                                    <div class="podium-name fs-18 text-truncate">{{ $first->child?->fullname }}</div>
                                    <div class="podium-time">{{ $first->total_time }}s</div>
                                    <div class="text-muted fs-12 mb-2">({{ gmdate("i:s", $first->total_time) }})</div>
                                    <div class="podium-stat-pill bg-warning-lt text-warning fw-bold">
                                        <i class="ti ti-hand-click"></i>{{ $first->total_moves }} lần lật
                                    </div>
                                    @if ($first->child?->user)
                                        <div class="text-dark fs-11 mt-1 text-truncate fw-medium" style="max-width: 200px;" title="PH: {{ $first->child->user->fullname }} ({{ $first->child->user->decrypted_phone }})">
                                            <i class="ti ti-user me-0.5 text-warning"></i>{{ $first->child->user->fullname }}
                                            @if ($first->child->user->decrypted_phone)
                                                • <span class="text-success font-monospace fw-bold">{{ $first->child->user->decrypted_phone }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Hạng 3 (Quý Quân - Đồng) -->
                            @if (isset($leaderboard[2]))
                                @php $third = $leaderboard[2]; @endphp
                                <div class="podium-item podium-third">
                                    <div class="podium-avatar-wrapper">
                                        <img src="{{ $third->child?->avatar ? asset($third->child->avatar) : asset('images/avatar-default.png') }}" 
                                             class="podium-avatar" alt="Hạng 3">
                                        <div class="podium-medal-badge badge-bronze">🥉</div>
                                    </div>
                                    <div class="text-uppercase fw-extrabold text-muted fs-11 tracking-wider mb-1">{{ __('Quý Quân 3') }}</div>
                                    <div class="podium-name text-truncate">{{ $third->child?->fullname }}</div>
                                    <div class="podium-time">{{ $third->total_time }}s</div>
                                    <div class="text-muted fs-11 mb-2">({{ gmdate("i:s", $third->total_time) }})</div>
                                    <div class="podium-stat-pill">
                                        <i class="ti ti-hand-click"></i>{{ $third->total_moves }} lần lật
                                    </div>
                                    @if ($third->child?->user)
                                        <div class="text-muted fs-11 mt-1 text-truncate" style="max-width: 190px;" title="PH: {{ $third->child->user->fullname }} ({{ $third->child->user->decrypted_phone }})">
                                            <i class="ti ti-user me-0.5"></i>{{ $third->child->user->fullname }}
                                            @if ($third->child->user->decrypted_phone)
                                                • <span class="text-success font-monospace">{{ $third->child->user->decrypted_phone }}</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- 4. Danh Sách Toàn Bộ Lượt Thi & Bảng Xếp Hạng -->
            <div class="card custom-shadow border-0">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 w-100">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ti ti-list-numbers fs-3 text-primary"></i>
                            <div>
                                <h5 class="card-title mb-0 fw-bold">{{ __('Danh Sách Kết Quả Toàn Bộ Lượt Thi') }}</h5>
                                <div class="text-muted fs-11">{{ __('Cập nhật tự động theo tiêu chí xếp hạng chính thức') }}</div>
                            </div>
                        </div>

                        <!-- Bộ lọc Tab (Pills) -->
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <button type="button" class="filter-tab-btn active" data-filter="all">
                                {{ __('Tất cả') }} <span class="badge bg-light text-dark ms-1 rounded-pill" id="countAll">{{ $totalAttempts }}</span>
                            </button>
                            <button type="button" class="filter-tab-btn" data-filter="valid">
                                <i class="ti ti-check me-1 text-success"></i>{{ __('Đủ điều kiện (4/4 ván)') }} <span class="badge bg-light text-dark ms-1 rounded-pill" id="countValid">{{ $completedCount }}</span>
                            </button>
                            <button type="button" class="filter-tab-btn" data-filter="invalid">
                                <i class="ti ti-x me-1 text-danger"></i>{{ __('Chưa đạt') }} <span class="badge bg-light text-dark ms-1 rounded-pill" id="countInvalid">{{ max(0, $totalAttempts - $completedCount) }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Thanh tìm kiếm nhanh thời gian thực -->
                    <div class="mt-3 pt-3 border-top">
                        <div class="row g-2 align-items-center">
                            <div class="col-12 col-md-5">
                                <div class="input-icon">
                                    <span class="input-icon-addon"><i class="ti ti-search text-muted"></i></span>
                                    <input type="text" id="leaderboardSearch" class="form-control rounded-pill" placeholder="{{ __('Tìm theo tên bé, phụ huynh, số điện thoại...') }}" style="border-radius: 50px !important;">
                                </div>
                            </div>
                            <div class="col-12 col-md-7 text-md-end text-muted fs-12">
                                <i class="ti ti-info-circle me-1"></i>{{ __('Bấm vào nút "Chi tiết" ở mỗi dòng để xem số giây và lượt lật của từng ván') }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive position-relative">
                        <x-admin.partials.toggle-column-datatable />
                        {{ $dataTable->table(['class' => 'table table-bordered align-middle'], true) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Modal Popup Chi Tiết 4 Ván Thi Của Thí Sinh -->
    <div class="modal modal-blur fade" id="modalRoundDetails" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div class="modal-header py-3 px-4 bg-light border-bottom d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <img id="mChildAvatar" src="" class="avatar avatar-md rounded-circle border shadow-xs" alt="" style="width: 44px; height: 44px; object-fit: cover;">
                        <div>
                            <h5 class="modal-title fw-bold mb-0 text-dark" id="mChildName">Thí sinh</h5>
                            <div class="text-muted fs-11" id="mChildMeta">Lần thi • Hạng #--</div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-3 me-2">
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-bold text-dark fs-12" id="mParentName"><i class="ti ti-user me-1 text-muted"></i>--</div>
                            <div class="fs-11 mt-0.5" id="mParentPhone">--</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                
                <div class="modal-body p-4 bg-light">
                    <!-- Tổng quan lượt thi -->
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <div class="p-2.5 bg-white rounded-3 border text-center">
                                <div class="text-muted fs-11">{{ __('Tổng thời gian') }}</div>
                                <div class="fw-extrabold text-primary fs-16" id="mTotalTime">0s</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2.5 bg-white rounded-3 border text-center">
                                <div class="text-muted fs-11">{{ __('Tổng lượt lật') }}</div>
                                <div class="fw-extrabold text-dark fs-16" id="mTotalMoves">0</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-2.5 bg-white rounded-3 border text-center">
                                <div class="text-muted fs-11">{{ __('Thứ hạng BXH') }}</div>
                                <div class="fw-extrabold text-warning fs-16" id="mRanking">#--</div>
                            </div>
                        </div>
                    </div>

                    <h6 class="fw-bold text-dark mb-2 fs-13 d-flex align-items-center gap-1.5">
                        <i class="ti ti-list-check text-primary"></i> {{ __('Kết Quả Chi Tiết Từng Ván (4 Ván Liên Tục)') }}
                    </h6>

                    <!-- Lưới 4 ván thi -->
                    <div class="row g-3" id="mRoundsList">
                        <!-- Đổ tự động bằng JS -->
                    </div>
                </div>

                <div class="modal-footer py-2 px-3 bg-white border-top d-flex justify-content-between">
                    <span class="fs-12 text-muted"><i class="ti ti-lock me-1"></i>Lưới 5×6 (30 thẻ = 15 cặp thẻ) • Peek Time 0s</span>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal" style="border-radius: 50px !important;">
                        {{ __('Đóng') }}
                    </button>
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
        'id_table' => $dataTable->getTableAttribute('id'),
    ])

<script>
$(document).ready(function () {
    const tableId = '{{ $dataTable->getTableAttribute("id") }}';

    // 1. Bộ lọc Tab (Tất cả / Đủ điều kiện / Chưa đạt)
    $('.filter-tab-btn').on('click', function () {
        $('.filter-tab-btn').removeClass('active');
        $(this).addClass('active');

        const filter = $(this).data('filter');
        const table = window.LaravelDataTables[tableId];
        if (table) {
            let currentUrl = new URL(table.ajax.url() || window.location.href, window.location.origin);
            if (filter && filter !== 'all') {
                currentUrl.searchParams.set('filter_status', filter);
            } else {
                currentUrl.searchParams.delete('filter_status');
            }
            table.ajax.url(currentUrl.toString()).load();
        }
    });

    // 2. Tìm kiếm nhanh thời gian thực theo tên, SĐT đồng bộ với DataTable
    $('#leaderboardSearch').on('keyup input', function () {
        const table = window.LaravelDataTables[tableId];
        if (table) {
            table.search($(this).val().trim()).draw();
        }
    });

    // 3. Modal xem chi tiết 4 ván thi của thí sinh (Sự kiện uỷ quyền cho Ajax DataTable)
    $(document).on('click', '.btn-view-rounds', function () {
        const childName = $(this).data('child');
        const childAvatar = $(this).data('avatar');
        const parentName = $(this).data('parent-name') || '--';
        const parentPhone = $(this).data('parent-phone') || '--';
        const rank = $(this).data('rank');
        const time = $(this).data('time');
        const moves = $(this).data('moves');
        const attempt = $(this).data('attempt');
        const rounds = $(this).data('rounds') || [];

        $('#mChildName').text(childName);
        $('#mChildAvatar').attr('src', childAvatar);
        $('#mChildMeta').text('Lần thi ' + attempt + ' • Hạng: ' + (rank !== '--' ? '#' + rank : 'Chưa xếp hạng'));
        
        $('#mParentName').html('<i class="ti ti-user me-1 text-muted"></i>' + parentName);
        if (parentPhone && parentPhone !== '--') {
            const cleanPhone = parentPhone.replace(/\s+/g, '');
            $('#mParentPhone').html('<a href="tel:' + cleanPhone + '" class="phone-badge" style="font-size: 11px;"><i class="ti ti-phone-call fs-11"></i>' + parentPhone + '</a>');
        } else {
            $('#mParentPhone').html('<span class="text-muted fs-11">Chưa có SĐT</span>');
        }

        $('#mTotalTime').text(time);
        $('#mTotalMoves').text(moves + ' lượt');
        $('#mRanking').text(rank !== '--' ? '#' + rank : 'Chưa xếp hạng');

        const $roundsContainer = $('#mRoundsList');
        $roundsContainer.empty();

        if (rounds.length === 0) {
            $roundsContainer.html(
                '<div class="col-12 text-center py-4 text-muted">' +
                '<i class="ti ti-info-circle fs-2 d-block mb-1"></i>' +
                '<div>Chưa có dữ liệu ván thi chi tiết</div>' +
                '</div>'
            );
        } else {
            rounds.forEach(function (rnd) {
                const won = rnd.is_won;
                const statusBadge = won 
                    ? '<span class="badge bg-success shadow-2xs fs-11"><i class="ti ti-check me-1"></i>Thắng</span>'
                    : '<span class="badge bg-danger shadow-2xs fs-11"><i class="ti ti-x me-1"></i>Thua / Hết giờ</span>';

                const themeImgHtml = rnd.theme_icon 
                    ? '<img src="' + rnd.theme_icon + '" alt="" class="rounded border p-0.5 bg-white" style="width: 38px; height: 38px; object-fit: contain;">'
                    : '<div class="rounded bg-light border d-flex align-items-center justify-content-center text-primary" style="width: 38px; height: 38px;"><i class="ti ti-cards fs-4"></i></div>';

                const cardClass = won ? 'won' : 'lost';
                $roundsContainer.append(
                    '<div class="col-12 col-sm-6">' +
                        '<div class="round-detail-card ' + cardClass + ' h-100">' +
                            '<div class="d-flex align-items-center justify-content-between mb-2">' +
                                '<span class="badge bg-light text-dark border fw-bold fs-11">Ván ' + rnd.game_number + '</span>' +
                                statusBadge +
                            '</div>' +
                            '<div class="d-flex align-items-center gap-2.5 mb-2.5">' +
                                themeImgHtml +
                                '<div class="min-w-0">' +
                                    '<div class="fw-bold text-dark fs-13 text-truncate">' + rnd.theme_name + '</div>' +
                                    '<div class="text-muted fs-11">Thời gian: <strong class="text-primary">' + rnd.duration_spent + 's</strong></div>' +
                                    (rnd.mistakes > 0 ? '<div class="text-muted fs-11">Lật sai: <strong class="text-danger">' + rnd.mistakes + '</strong></div>' : '') +
                                '</div>' +
                            '</div>' +
                            '<div class="d-flex align-items-center justify-content-between pt-2 border-top fs-11 text-muted">' +
                                '<span><i class="ti ti-hand-click me-1"></i>Lượt lật: <strong>' + rnd.total_moves + '</strong></span>' +
                                '<span><i class="ti ti-alert-triangle me-1 text-danger"></i>Lật sai: <strong>' + rnd.mistakes + '</strong></span>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
            });
        }

        const modal = new bootstrap.Modal(document.getElementById('modalRoundDetails'));
        modal.show();
    });

    // 4. Sao chép số điện thoại nhanh 1-chạm
    $(document).on('click', '.btn-copy-phone', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const phone = $(this).data('phone');
        if (!phone) return;

        const $btn = $(this);
        const originalHtml = $btn.html();

        function showCopied() {
            $btn.addClass('copied').html('<i class="ti ti-check fs-12"></i>');
            setTimeout(function () {
                $btn.removeClass('copied').html(originalHtml);
            }, 1600);
        }

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(phone).then(showCopied).catch(function () {
                fallbackCopy(phone);
            });
        } else {
            fallbackCopy(phone);
        }

        function fallbackCopy(text) {
            const $temp = $('<input>');
            $('body').append($temp);
            $temp.val(text).select();
            document.execCommand('copy');
            $temp.remove();
            showCopied();
        }
    });
});
</script>
@endpush
