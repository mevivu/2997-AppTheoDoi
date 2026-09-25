@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
<style>
/* 4-Game Pipeline Cards */
.pipeline-step-card {
    background: #ffffff;
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px;
    position: relative;
    transition: all 0.25s ease;
}
.pipeline-step-card:hover {
    border-color: #0284c7;
    box-shadow: 0 8px 20px rgba(2, 132, 199, 0.08);
}
.pipeline-step-card.has-duplicate {
    border-color: #ef4444 !important;
    background: #fff5f5 !important;
}
.pipeline-step-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 12px;
}
.pipeline-badge {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 13px;
    box-shadow: 0 2px 6px rgba(2, 132, 199, 0.3);
}
.theme-preview-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 10px 12px;
    margin-top: 10px;
    border: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s ease;
}
.theme-preview-card:hover {
    background: #ffffff;
    border-color: #cbd5e1;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
}
.theme-icon-box {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
    overflow: hidden;
    padding: 3px;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
}
.theme-icon-box img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    border-radius: 7px;
    display: block;
    transition: transform 0.2s ease;
}
.theme-preview-card:hover .theme-icon-box img {
    transform: scale(1.1);
}
.theme-meta-name {
    font-weight: 700;
    font-size: 13px;
    color: #0f172a;
    line-height: 1.2;
}
.theme-meta-count {
    font-size: 11px;
    margin-top: 2px;
}
.theme-sample-stack {
    display: inline-flex;
    align-items: center;
}
.theme-sample-img {
    width: 24px;
    height: 24px;
    border-radius: 6px;
    border: 1.5px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12);
    object-fit: contain;
    background: #ffffff;
    margin-left: -6px;
    display: inline-block;
    transition: transform 0.15s ease;
}
.theme-sample-img:first-child {
    margin-left: 0;
}
.theme-sample-img:hover {
    transform: translateY(-2px) scale(1.2);
    z-index: 5;
    position: relative;
}

/* Radio Cards for Max Attempts */
.attempt-radio-card {
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    padding: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    background: #ffffff;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.attempt-radio-card:hover {
    border-color: #0284c7;
    background: #f8fafc;
}
.attempt-radio-card.active {
    border-color: #0284c7;
    background: #f0f9ff;
    box-shadow: 0 4px 12px rgba(2, 132, 199, 0.12);
}
.attempt-radio-card input[type="radio"] {
    display: none;
}

/* Phone Mockup Simulation (Mini Live Preview) */
.phone-mockup-wrapper {
    max-width: 320px;
    margin: 0 auto;
}
.phone-mockup-screen {
    background: linear-gradient(180deg, #e0f2fe 0%, #bae6fd 25%, #ffffff 100%);
    border: 4px solid #0284c7;
    border-radius: 28px;
    padding: 12px;
    box-shadow: 0 12px 30px rgba(2, 132, 199, 0.25);
    position: relative;
    user-select: none;
}
.phone-mockup-speaker {
    width: 44px;
    height: 4px;
    background: #0284c7;
    border-radius: 4px;
    margin: 0 auto 10px;
    opacity: 0.5;
}
.phone-preview-banner {
    width: 100%;
    height: 110px;
    border-radius: 14px;
    object-fit: cover;
    background: #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #64748b;
    font-size: 12px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    overflow: hidden;
}
.phone-preview-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 3px;
    margin-top: 10px;
    padding: 6px;
    background: rgba(255, 255, 255, 0.8);
    border-radius: 10px;
    border: 1px solid rgba(2, 132, 199, 0.2);
}
.phone-mini-card {
    aspect-ratio: 3/4;
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #ffffff;
    font-size: 7px;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

/* Modern Banner Uploader UI */
.banner-uploader-card {
    border: 2px dashed #cbd5e1 !important;
    background: #ffffff;
    transition: all 0.25s ease;
    box-sizing: border-box !important;
    width: 100% !important;
    max-width: 100% !important;
    overflow: hidden !important;
}
.banner-uploader-card:hover {
    border-color: #94a3b8 !important;
}
.banner-uploader-card.dragover {
    border-color: #0284c7 !important;
    background: #f0f9ff !important;
    box-shadow: 0 0 0 4px rgba(2, 132, 199, 0.1);
}
.banner-preview-box {
    aspect-ratio: 16 / 9;
    width: 100%;
    max-height: 200px;
    background: #f8fafc;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    overflow: hidden;
}
.banner-preview-box img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}
.banner-preview-box:hover .banner-hover-overlay {
    opacity: 1 !important;
}
.banner-hover-overlay {
    background: rgba(15, 23, 42, 0.55);
    backdrop-filter: blur(3px);
    opacity: 0;
    transition: opacity 0.2s ease;
    z-index: 5;
}
.banner-upload-icon-circle {
    width: 48px;
    height: 48px;
    background: #eff6ff;
    color: #0284c7;
    transition: transform 0.2s ease;
}
.banner-uploader-card:hover .banner-upload-icon-circle {
    transform: translateY(-2px);
}
</style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-3"
                icon="trophy"
                :title="__('Chỉnh Sửa Giải Đấu: ') . $competition->name"
                :subtitle="__('Cập nhật thể lệ, thời gian, 4 chủ đề và quy định số lượt thi')"
                :back-route="route(RouteAdminSystem::MEMO_COMPETITION_INDEX)"
            >
                <x-slot:actions>
                    <a href="{{ route(RouteAdminSystem::MEMO_COMPETITION_LEADERBOARD, $competition->id) }}" class="btn btn-warning">
                        <i class="ti ti-trophy me-1"></i>{{ __('Xem Bảng Xếp Hạng') }}
                    </a>
                </x-slot:actions>
            </x-admin.page-header>

            <!-- Cảnh báo nếu chọn trùng chủ đề -->
            <div id="duplicateThemeAlert" class="alert alert-danger py-2 px-3 fs-13 mb-3 d-none align-items-center gap-2">
                <i class="ti ti-alert-triangle fs-16 flex-shrink-0"></i>
                <div id="duplicateThemeText"></div>
            </div>

            <form id="formEditCompetition" action="{{ route('admin.memo-game.competition.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $competition->id }}">

                <div class="row g-4">
                    <!-- Cột Trái (8 phần): Form nhập & 4 Chủ đề thi -->
                    <div class="col-12 col-xl-8">
                        <!-- 1. Thông Tin Cơ Bản -->
                        <div class="card custom-shadow mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-info-circle text-primary"></i> {{ __('1. Thông Tin Cơ Bản Giải Đấu') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required fw-bold">{{ __('Tên giải đấu') }}</label>
                                    <input type="text" id="inputName" name="name" class="form-control @error('name') is-invalid @enderror" 
                                           value="{{ old('name', $competition->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row g-3 mb-3">
                                    <div class="col-12 col-md-6">
                                        <label class="form-label required fw-bold">{{ __('Thời gian bắt đầu') }}</label>
                                        <input type="datetime-local" id="inputStartAt" name="start_at" class="form-control @error('start_at') is-invalid @enderror" 
                                               value="{{ old('start_at', $competition->start_at?->format('Y-m-d\TH:i')) }}" required>
                                        @error('start_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-12 col-md-6">
                                        <label class="form-label required fw-bold">{{ __('Thời gian kết thúc') }}</label>
                                        <input type="datetime-local" id="inputEndAt" name="end_at" class="form-control @error('end_at') is-invalid @enderror" 
                                               value="{{ old('end_at', $competition->end_at?->format('Y-m-d\TH:i')) }}" required>
                                        @error('end_at')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-0">
                                    <label class="form-label fw-bold">{{ __('Mô tả & Quy định giải đấu') }}</label>
                                    <textarea id="inputDescription" name="description" rows="3" class="form-control">{{ old('description', $competition->description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Trực Quan Hóa 4 Chặng Thi Đấu Liên Hoàn (Interactive Pipeline) -->
                        <div class="card custom-shadow mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-cards text-warning"></i> {{ __('2. Cấu Hình 4 Ván Thi Liên Hoàn (Thứ Tự 1 → 4)') }}
                                </h5>
                                <span class="badge bg-primary-lt fw-bold">4 ván × 15 cặp = 60 cặp</span>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info py-2 px-3 fs-12 mb-3">
                                    <i class="ti ti-info-circle me-1"></i>
                                    {{ __('Mỗi bé sẽ thi liên tục 4 ván tương ứng với 4 chủ đề bên dưới (lưới 5×6, không cho xem trước). Bạn có thể bấm nút mũi tên để hoán đổi nhanh thứ tự các ván.') }}
                                </div>

                                <div class="row g-3" id="pipelineThemeContainer">
                                    @for ($i = 1; $i <= 4; $i++)
                                        @php
                                            $currentThemeId = $selectedThemes[$i - 1] ?? ($themes->skip($i - 1)->first()?->id);
                                        @endphp
                                        <div class="col-12 col-md-6">
                                            <div class="pipeline-step-card" data-step="{{ $i }}">
                                                <div class="pipeline-step-header">
                                                    <div class="d-flex align-items-center gap-2">
                                                        <span class="pipeline-badge">{{ $i }}</span>
                                                        <span class="fw-bold text-dark fs-13">{{ __("Ván {$i}") }}</span>
                                                    </div>
                                                    <div class="d-flex gap-1">
                                                        @if ($i > 1)
                                                            <button type="button" class="btn btn-sm btn-icon btn-light btn-swap-left" 
                                                                    data-index="{{ $i }}" title="{{ __('Đổi vị trí lên trước') }}">
                                                                <i class="ti ti-arrow-left fs-12"></i>
                                                            </button>
                                                        @endif
                                                        @if ($i < 4)
                                                            <button type="button" class="btn btn-sm btn-icon btn-light btn-swap-right" 
                                                                    data-index="{{ $i }}" title="{{ __('Đổi vị trí ra sau') }}">
                                                                <i class="ti ti-arrow-right fs-12"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>

                                                <select name="theme_ids[]" class="form-select theme-select-input" data-index="{{ $i }}" required>
                                                    @foreach ($themes as $theme)
                                                        @php
                                                            $cardsCount = $theme->cards_count ?? 0;
                                                            $code = strtolower($theme->code);
                                                            $iconClass = 'ti ti-cards';
                                                            if (str_contains($code, 'veh') || str_contains($code, 'xe')) $iconClass = 'ti ti-car';
                                                            elseif (str_contains($code, 'flow') || str_contains($code, 'hoa')) $iconClass = 'ti ti-flower';
                                                            elseif (str_contains($code, 'numb') || str_contains($code, 'so')) $iconClass = 'ti ti-numbers';
                                                            elseif (str_contains($code, 'flag') || str_contains($code, 'co')) $iconClass = 'ti ti-flag';

                                                            $themeImage = '';
                                                            if (!empty($theme->icon) && $theme->icon !== \App\Traits\ImageSystem::DEFAULT_IMAGE) {
                                                                $themeImage = asset($theme->icon);
                                                            } elseif (!empty($theme->card_back) && $theme->card_back !== \App\Traits\ImageSystem::DEFAULT_IMAGE) {
                                                                $themeImage = asset($theme->card_back);
                                                            }

                                                            $sampleCards = $theme->activeCards ? $theme->activeCards->take(3)->pluck('image')->map(fn($img) => asset($img))->toArray() : [];
                                                        @endphp
                                                        <option value="{{ $theme->id }}" 
                                                                data-name="{{ $theme->name }}"
                                                                data-code="{{ $theme->code }}"
                                                                data-icon="{{ $iconClass }}"
                                                                data-image="{{ $themeImage }}"
                                                                data-samples='@json($sampleCards)'
                                                                data-cards="{{ $cardsCount }}"
                                                                {{ old("theme_ids." . ($i-1), $currentThemeId) == $theme->id ? 'selected' : '' }}>
                                                            {{ $theme->name }} ({{ $cardsCount }} thẻ)
                                                        </option>
                                                    @endforeach
                                                </select>

                                                <!-- Mini preview card của chủ đề -->
                                                <div class="theme-preview-card" id="themePreviewBox_{{ $i }}">
                                                    <div class="theme-icon-box" id="themeIcon_{{ $i }}">
                                                        <i class="ti ti-cards text-primary"></i>
                                                    </div>
                                                    <div class="flex-grow-1 min-w-0">
                                                        <div class="d-flex align-items-center justify-content-between gap-1">
                                                            <div class="theme-meta-name text-truncate" id="themeName_{{ $i }}">--</div>
                                                            <div class="theme-sample-stack d-none d-sm-inline-flex" id="themeSamples_{{ $i }}"></div>
                                                        </div>
                                                        <div class="theme-meta-count mt-1" id="themeCountBadge_{{ $i }}">--</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <!-- 3. Cấu Hình Số Lượt Thi (Smart Radio Cards) -->
                        @php
                            $currentAttempts = (int) old('max_attempts', $competition->max_attempts);
                            $attemptType = '1';
                            if ($currentAttempts === 0) {
                                $attemptType = '0';
                            } elseif ($currentAttempts > 1) {
                                $attemptType = 'custom';
                            }
                        @endphp
                        <div class="card custom-shadow mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-refresh text-success"></i> {{ __('3. Quy Định Số Lượt Thi (max_attempts)') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-3">
                                    <!-- Option 1: Chỉ 1 lần -->
                                    <div class="col-12 col-md-4">
                                        <label class="attempt-radio-card {{ $attemptType === '1' ? 'active' : '' }}" id="cardOptSingle">
                                            <input type="radio" name="attempt_type" value="1" {{ $attemptType === '1' ? 'checked' : '' }}>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fs-4">🥇</span>
                                                <span class="badge bg-success-lt fw-bold">{{ __('Khuyến nghị') }}</span>
                                            </div>
                                            <div class="fw-bold text-dark fs-14">{{ __('Chỉ 1 Lần Duy Nhất') }}</div>
                                            <small class="text-muted fs-11 mt-1 flex-grow-1">
                                                {{ __('Tạo áp lực thi đấu công bằng và chuẩn xác. Bé không được thi lại.') }}
                                            </small>
                                        </label>
                                    </div>

                                    <!-- Option 2: Cho phép thi lại N lần -->
                                    <div class="col-12 col-md-4">
                                        <label class="attempt-radio-card {{ $attemptType === 'custom' ? 'active' : '' }}" id="cardOptMultiple">
                                            <input type="radio" name="attempt_type" value="custom" {{ $attemptType === 'custom' ? 'checked' : '' }}>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fs-4">🔁</span>
                                                <span class="badge bg-primary-lt fw-bold">{{ __('Cải thiện điểm') }}</span>
                                            </div>
                                            <div class="fw-bold text-dark fs-14">{{ __('Cho Phép Thi Lại') }}</div>
                                            <small class="text-muted fs-11 mt-1 mb-2">
                                                {{ __('Bé được thi tối đa N lần. Hệ thống tự lấy lượt thi tốt nhất để xếp hạng.') }}
                                            </small>
                                            <div class="mt-auto" id="customAttemptsBox" style="{{ $attemptType === 'custom' ? '' : 'display: none;' }}">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-white">{{ __('Tối đa:') }}</span>
                                                    <input type="number" id="inputCustomAttempts" class="form-control" 
                                                           value="{{ $attemptType === 'custom' ? $currentAttempts : 3 }}" min="2" max="50">
                                                    <span class="input-group-text bg-white">{{ __('lượt') }}</span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                    <!-- Option 3: Không giới hạn -->
                                    <div class="col-12 col-md-4">
                                        <label class="attempt-radio-card {{ $attemptType === '0' ? 'active' : '' }}" id="cardOptUnlimited">
                                            <input type="radio" name="attempt_type" value="0" {{ $attemptType === '0' ? 'checked' : '' }}>
                                            <div class="d-flex align-items-center justify-content-between mb-2">
                                                <span class="fs-4">♾️</span>
                                                <span class="badge bg-secondary-lt fw-bold">{{ __('Tập luyện') }}</span>
                                            </div>
                                            <div class="fw-bold text-dark fs-14">{{ __('Không Giới Hạn') }}</div>
                                            <small class="text-muted fs-11 mt-1 flex-grow-1">
                                                {{ __('Bé thi bao nhiêu lần tùy thích, khuyến khích luyện tập liên tục.') }}
                                            </small>
                                        </label>
                                    </div>
                                </div>

                                <!-- Input ẩn gửi lên server -->
                                <input type="hidden" name="max_attempts" id="finalMaxAttempts" value="{{ $currentAttempts }}">
                            </div>
                        </div>
                    </div>

                    <!-- Cột Phải (4 phần): Cấu hình lưới, Banner & Live Phone Simulator -->
                    <div class="col-12 col-xl-4">
                        <!-- Cấu hình lưới & Trạng thái -->
                        <div class="card custom-shadow mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-settings text-primary"></i> {{ __('Cấu Hình & Trạng Thái') }}
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label required fw-bold">{{ __('Cấu hình lưới bài thi') }}</label>
                                    <select name="memo_age_config_id" id="selectAgeConfig" class="form-select" required>
                                        @foreach ($ageConfigs as $cfg)
                                            <option value="{{ $cfg->id }}" 
                                                    data-rows="{{ $cfg->rows }}" 
                                                    data-cols="{{ $cfg->columns }}"
                                                    data-cards="{{ $cfg->total_cards }}"
                                                    {{ old('memo_age_config_id', $competition->memo_age_config_id) == $cfg->id ? 'selected' : '' }}>
                                                {{ $cfg->name }} ({{ $cfg->rows }}×{{ $cfg->columns }} - {{ $cfg->total_cards }} thẻ)
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted fs-11 mt-1 d-block">
                                        <i class="ti ti-check text-success me-1"></i>{{ __('Đã khóa: Lưới 5×6 (30 thẻ = 15 cặp thẻ, peek_time = 0s).') }}
                                    </small>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label required fw-bold">{{ __('Trạng thái mở giải') }}</label>
                                    <select name="status" class="form-select" required>
                                        <option value="draft" {{ old('status', $competition->status) === 'draft' ? 'selected' : '' }}>⚪ {{ __('Bản nháp (Draft)') }}</option>
                                        <option value="upcoming" {{ old('status', $competition->status) === 'upcoming' ? 'selected' : '' }}>🟡 {{ __('Sắp diễn ra (Upcoming)') }}</option>
                                        <option value="active" {{ old('status', $competition->status) === 'active' ? 'selected' : '' }}>🟢 {{ __('Đang mở (Active)') }}</option>
                                        <option value="ended" {{ old('status', $competition->status) === 'ended' ? 'selected' : '' }}>🔴 {{ __('Đã kết thúc (Ended)') }}</option>
                                        <option value="cancelled" {{ old('status', $competition->status) === 'cancelled' ? 'selected' : '' }}>⚫ {{ __('Đã hủy (Cancelled)') }}</option>
                                    </select>
                                </div>

                                <!-- Khu vực Tải & Quản lý Ảnh Banner Giải Đấu -->
                                <div class="mb-0">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <label class="form-label fw-bold mb-0 d-flex align-items-center gap-1.5">
                                            <i class="ti ti-photo text-primary fs-5"></i>
                                            <span>{{ __('Ảnh Banner Giải Đấu') }}</span>
                                        </label>
                                        <span class="badge bg-blue-lt fs-11" id="bannerAspectBadge">
                                            <i class="ti ti-aspect-ratio me-1"></i>16:9 • Tối đa 5MB
                                        </span>
                                    </div>

                                    <div class="banner-uploader-card p-3 rounded-3 border bg-white position-relative" id="bannerUploaderCard" style="box-sizing: border-box; overflow: hidden; width: 100%;">
                                        <!-- Khung Xem Trước Banner Tỷ Lệ 16:9 -->
                                        <div class="banner-preview-box rounded-3 position-relative overflow-hidden mb-3 border shadow-xs" 
                                             id="bannerPreviewBox"
                                             role="button"
                                             tabindex="0"
                                             title="{{ __('Nhấp để chọn hoặc kéo thả ảnh Banner mới') }}">
                                            
                                            <img id="bannerImgPreview" 
                                                 src="{{ $competition->banner_image ? asset($competition->banner_image) : '' }}" 
                                                 alt="{{ $competition->name }}" 
                                                 class="w-100 h-100" 
                                                 style="object-fit: cover; display: {{ $competition->banner_image ? 'block' : 'none' }};"
                                                 data-original-src="{{ $competition->banner_image ? asset($competition->banner_image) : '' }}">

                                            <!-- Trạng thái trống (khi chưa có ảnh nào) -->
                                            <div id="bannerEmptyState" class="text-center p-3 text-muted w-100" style="display: {{ $competition->banner_image ? 'none' : 'block' }}; pointer-events: none;">
                                                <div class="banner-upload-icon-circle mx-auto mb-2 d-flex align-items-center justify-content-center rounded-circle">
                                                    <i class="ti ti-cloud-upload fs-2"></i>
                                                </div>
                                                <div class="fw-bold text-dark fs-13 mb-1">{{ __('Kéo thả ảnh hoặc nhấp để tải Banner') }}</div>
                                                <div class="fs-11 text-muted">{{ __('Định dạng hỗ trợ: JPG, PNG, WEBP (Khuyến nghị 1200×675px)') }}</div>
                                            </div>

                                            <!-- Overlay khi hover chuột lên ảnh (đã có ảnh) -->
                                            <div class="banner-hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center gap-2" id="bannerHoverOverlay">
                                                <button type="button" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" id="btnOverlayChange">
                                                    <i class="ti ti-camera"></i>
                                                    <span>{{ __('Đổi ảnh') }}</span>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-3 shadow-sm d-flex align-items-center gap-1" id="btnOverlayZoom">
                                                    <i class="ti ti-zoom-in"></i>
                                                    <span>{{ __('Phóng to') }}</span>
                                                </button>
                                            </div>

                                            <!-- Badge góc thông báo trạng thái -->
                                            <div class="position-absolute top-0 end-0 m-2" id="bannerBadgeContainer">
                                                @if ($competition->banner_image)
                                                    <span class="badge bg-success shadow-sm" id="bannerCurrentBadge" style="font-size: 10px;">
                                                        <i class="ti ti-check me-1"></i>{{ __('Banner hiện tại') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Hộp thông tin tệp mới chọn (Chỉ hiện khi người dùng chọn ảnh mới từ máy) -->
                                        <div id="bannerFileInfo" class="d-none align-items-center justify-content-between p-2 mb-2 rounded-2 bg-light border" style="min-width: 0; width: 100%; overflow: hidden;">
                                            <div class="d-flex align-items-center gap-2" style="min-width: 0; overflow: hidden; flex: 1 1 auto;">
                                                <i class="ti ti-file-check text-success fs-5 flex-shrink-0"></i>
                                                <div style="min-width: 0; overflow: hidden; flex: 1 1 auto;">
                                                    <div class="fs-12 fw-semibold text-dark text-truncate" id="bannerFileName"></div>
                                                    <div class="fs-11 text-muted text-truncate" id="bannerFileSize"></div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 p-1 ms-1 flex-shrink-0 rounded-circle" id="btnCancelNewBanner" title="{{ __('Hủy chọn tệp này') }}" style="width: 26px; height: 26px; display: inline-flex; align-items: center; justify-content: center;">
                                                <i class="ti ti-x fs-14"></i>
                                            </button>
                                        </div>

                                        <!-- Nhóm nút bấm thao tác chuẩn dạng pill -->
                                        <div class="d-flex align-items-center gap-2" style="width: 100%;">
                                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1.5 flex-grow-1 d-inline-flex align-items-center justify-content-center gap-1.5" id="btnTriggerUpload" style="border-radius: 50px !important;">
                                                <i class="ti ti-photo-up fs-5"></i>
                                                <span id="btnTriggerUploadText" class="fw-medium">{{ $competition->banner_image ? __('Thay đổi ảnh') : __('Tải ảnh lên') }}</span>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 py-1.5 d-none align-items-center justify-content-center gap-1 flex-shrink-0" id="btnResetBanner" title="{{ __('Hủy chọn và khôi phục ảnh ban đầu') }}" style="border-radius: 50px !important;">
                                                <i class="ti ti-rotate-clockwise"></i>
                                                <span>{{ __('Hoàn tác') }}</span>
                                            </button>
                                        </div>

                                        <!-- Input file ẩn chuẩn của HTML -->
                                        <input type="file" id="inputBannerFile" name="banner_image" class="d-none" accept="image/png,image/jpeg,image/webp,image/jpg">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Live Mobile Phone Simulator (Mô phỏng tức thì) -->
                        <div class="card custom-shadow mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="card-title mb-0 d-flex align-items-center gap-1 fs-13">
                                    <i class="ti ti-device-mobile text-primary"></i> {{ __('Mô Phỏng Trên Ứng Dụng Bé') }}
                                </h6>
                                <span class="badge bg-success-lt fs-10">Live Preview</span>
                            </div>
                            <div class="card-body p-3">
                                <div class="phone-mockup-wrapper">
                                    <div class="phone-mockup-screen">
                                        <div class="phone-mockup-speaker"></div>
                                        
                                        <!-- Banner Preview -->
                                        <div class="phone-preview-banner" id="simBannerContainer">
                                            @if ($competition->banner_image)
                                                <img id="simBannerImg" src="{{ asset($competition->banner_image) }}" alt="Banner" style="width: 100%; height: 100%; object-fit: cover;">
                                                <div id="simBannerPlaceholder" class="text-center px-2" style="display: none;">
                                                    <i class="ti ti-photo fs-2 d-block mb-1 opacity-50"></i>
                                                    <span class="fs-10">{{ __('Chưa có ảnh banner') }}</span>
                                                </div>
                                            @else
                                                <img id="simBannerImg" src="" alt="Banner" style="width: 100%; height: 100%; object-fit: cover; display: none;">
                                                <div id="simBannerPlaceholder" class="text-center px-2">
                                                    <i class="ti ti-photo fs-2 d-block mb-1 opacity-50"></i>
                                                    <span class="fs-10">{{ __('Chưa có ảnh banner') }}</span>
                                                </div>
                                            @endif
                                        </div>

                                        <!-- Title Preview -->
                                        <div class="mt-2 text-center">
                                            <div class="fw-bold text-dark fs-12 text-truncate" id="simTitle">{{ $competition->name }}</div>
                                            <div class="text-primary fs-10 fw-bold mt-1" id="simDates">
                                                {{ $competition->start_at?->format('d/m') }} - {{ $competition->end_at?->format('d/m/Y') }}
                                            </div>
                                        </div>

                                        <!-- 4 Themes Badges -->
                                        <div class="d-flex justify-content-center gap-1 mt-2 flex-wrap" id="simThemePills"></div>

                                        <!-- 5x6 Board Simulation -->
                                        <div class="phone-preview-grid">
                                            @for ($c = 1; $c <= 30; $c++)
                                                <div class="phone-mini-card">★</div>
                                            @endfor
                                        </div>

                                        <div class="mt-2 text-center text-muted" style="font-size: 9px;">
                                            <i class="ti ti-lock me-1"></i>{{ __('Lưới 5×6 (30 thẻ) • Peek Time 0s') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Thanh thao tác chung (Floating Form Actions) --}}
                        <x-admin.form-actions
                            :submit-title="__('Lưu Cập Nhật')"
                            submit-icon="ti ti-device-floppy"
                            :back-route="route(RouteAdminSystem::MEMO_COMPETITION_INDEX)"
                            :back-title="__('Quay lại')"
                        >
                            {{-- Nút Xem Bảng Xếp Hạng đồng bộ dạng pill --}}
                            <a href="{{ route(RouteAdminSystem::MEMO_COMPETITION_LEADERBOARD, $competition->id) }}" class="btn-leaderboard-settings">
                                <i class="ti ti-trophy fs-5"></i>
                                <span>{{ __('Xem BXH') }}</span>
                            </a>

                            {{-- Nút Xóa đồng bộ dạng pill và truyền nội dung chi tiết vào popup --}}
                            <x-button.modal-delete
                                class="btn-delete-settings"
                                data-route="{{ route(RouteAdminSystem::MEMO_COMPETITION_DELETE, $competition->id) }}"
                                data-modal-title="{{ __('Xác nhận xóa giải đấu?') }}"
                                data-modal-desc="Khi xóa giải đấu <strong>&quot;{{ e($competition->name) }}&quot;</strong>, các dữ liệu sau sẽ bị xóa vĩnh viễn khỏi hệ thống:<ul class='text-start mt-2 mb-0 ps-3 fs-12 text-muted' style='line-height: 1.6;'><li>Toàn bộ cấu hình 4 ván thi và chủ đề của giải đấu</li><li>Toàn bộ <strong>{{ $competition->entries()->count() }} lượt thi</strong> của các bé đã tham gia</li><li>Toàn bộ kết quả chi tiết từng ván chơi &amp; bảng xếp hạng</li><li>File ảnh banner đại diện giải đấu trên server</li></ul><div class='alert alert-warning py-1 px-2 fs-11 mt-2 mb-0 text-start'><i class='ti ti-alert-triangle me-1 text-warning'></i><strong>Lưu ý:</strong> Hành động này không thể hoàn tác!</div>"
                                :title="__('Xóa giải đấu')"
                            />
                        </x-admin.form-actions>
                    </div>
                </div>
            </form>
    <!-- Modal Xem Phóng To Banner -->
    <div class="modal modal-blur fade" id="modalBannerZoom" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content border-0 shadow-lg overflow-hidden">
                <div class="modal-header py-2 px-3 bg-dark text-white border-0">
                    <h6 class="modal-title d-flex align-items-center gap-2 mb-0 fs-13">
                        <i class="ti ti-photo text-warning"></i>
                        <span>{{ __('Xem Chi Tiết Banner Giải Đấu') }}</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0 bg-dark text-center">
                    <img id="modalBannerZoomImg" src="" alt="Banner Preview Full" class="img-fluid" style="max-height: 80vh; width: auto; object-fit: contain;">
                </div>
                <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between">
                    <span class="fs-12 text-muted" id="modalBannerZoomInfo">16:9 HD</span>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-3" data-bs-dismiss="modal">{{ __('Đóng') }}</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-js')
<script>
$(document).ready(function () {
    // 1. Cập nhật preview của 4 chủ đề và Live Phone Preview
    function updateThemePreviews() {
        const selectedValues = [];
        let hasDuplicate = false;
        let duplicateName = '';

        $('#simThemePills').empty();

        $('.theme-select-input').each(function () {
            const index = $(this).data('index');
            const $selectedOpt = $(this).find('option:selected');
            const themeId = $(this).val();
            const themeName = $selectedOpt.data('name') || '--';
            const iconClass = $selectedOpt.data('icon') || 'ti ti-cards';
            const themeImage = $selectedOpt.data('image') || '';
            const sampleCards = $selectedOpt.data('samples') || [];
            const cardsCount = parseInt($selectedOpt.data('cards') || 0, 10);

            // Kiểm tra trùng lặp
            if (selectedValues.includes(themeId)) {
                hasDuplicate = true;
                duplicateName = themeName;
                $(this).closest('.pipeline-step-card').addClass('has-duplicate');
            } else {
                $(this).closest('.pipeline-step-card').removeClass('has-duplicate');
                selectedValues.push(themeId);
            }

            // Cập nhật card preview bên dưới dropdown (ảnh thực tế)
            $('#themeName_' + index).text(themeName);

            if (themeImage) {
                $('#themeIcon_' + index).html(
                    '<img src="' + themeImage + '" alt="' + themeName + '" class="w-100 h-100" onerror="this.onerror=null;this.parentElement.innerHTML=\'<i class=\\\'ti ti-cards text-primary\\\'></i>\';">'
                );
            } else {
                $('#themeIcon_' + index).html('<i class="' + iconClass + ' text-primary"></i>');
            }

            // Cập nhật sample cards mini
            const $sampleContainer = $('#themeSamples_' + index);
            $sampleContainer.empty();
            if (sampleCards && sampleCards.length > 0) {
                sampleCards.forEach(function (cardImg) {
                    $sampleContainer.append(
                        '<img src="' + cardImg + '" alt="Thẻ" class="theme-sample-img" title="' + themeName + '">'
                    );
                });
            }

            if (cardsCount >= 15) {
                $('#themeCountBadge_' + index).html('<span class="badge bg-success-lt" style="font-size: 10px;"><i class="ti ti-check me-1"></i>' + cardsCount + ' thẻ thật</span>');
            } else {
                $('#themeCountBadge_' + index).html('<span class="badge bg-warning-lt" style="font-size: 10px;"><i class="ti ti-sparkles me-1"></i>' + cardsCount + ' thẻ + thẻ ảo</span>');
            }

            // Cập nhật vào màn hình mô phỏng
            const shortName = themeName ? themeName.split(' ')[0] : '';
            if (themeImage) {
                $('#simThemePills').append(
                    '<span class="badge bg-white text-dark border p-1 d-inline-flex align-items-center gap-1 shadow-2xs" style="font-size: 8px;">' +
                    '<img src="' + themeImage + '" alt="" style="width: 12px; height: 12px; object-fit: contain;" class="rounded-circle">' +
                    '<span>' + shortName + '</span>' +
                    '</span>'
                );
            } else {
                $('#simThemePills').append(
                    '<span class="badge bg-white text-dark border px-1" style="font-size: 8px;">' +
                    '<i class="' + iconClass + ' text-primary me-1"></i>' + shortName +
                    '</span>'
                );
            }
        });

        // Hiển thị cảnh báo trùng lặp
        if (hasDuplicate) {
            $('#duplicateThemeText').text('Cảnh báo: Có ván đang chọn trùng chủ đề "' + duplicateName + '". Vui lòng chọn 4 chủ đề khác nhau để giải đấu công bằng và hấp dẫn!');
            $('#duplicateThemeAlert').removeClass('d-none').addClass('d-flex');
        } else {
            $('#duplicateThemeAlert').addClass('d-none').removeClass('d-flex');
        }
    }

    $('.theme-select-input').on('change', updateThemePreviews);
    updateThemePreviews();

    // 2. Hoán đổi nhanh thứ tự chủ đề giữa các ván (Swap buttons)
    $('.btn-swap-left').on('click', function () {
        const curIdx = $(this).data('index');
        const prevIdx = curIdx - 1;
        swapThemes(curIdx, prevIdx);
    });

    $('.btn-swap-right').on('click', function () {
        const curIdx = $(this).data('index');
        const nextIdx = curIdx + 1;
        swapThemes(curIdx, nextIdx);
    });

    function swapThemes(idx1, idx2) {
        const $select1 = $('.theme-select-input[data-index="' + idx1 + '"]');
        const $select2 = $('.theme-select-input[data-index="' + idx2 + '"]');
        const val1 = $select1.val();
        const val2 = $select2.val();

        $select1.val(val2);
        $select2.val(val1);
        updateThemePreviews();
    }

    // 3. Xử lý Radio Cards cho Số lượt thi (max_attempts)
    $('.attempt-radio-card').on('click', function () {
        $('.attempt-radio-card').removeClass('active');
        $(this).addClass('active');
        $(this).find('input[type="radio"]').prop('checked', true);

        const val = $(this).find('input[type="radio"]').val();
        if (val === '1') {
            $('#customAttemptsBox').slideUp(150);
            $('#finalMaxAttempts').val(1);
        } else if (val === 'custom') {
            $('#customAttemptsBox').slideDown(150);
            $('#finalMaxAttempts').val($('#inputCustomAttempts').val());
        } else {
            $('#customAttemptsBox').slideUp(150);
            $('#finalMaxAttempts').val(0); // 0 = Không giới hạn
        }
    });

    $('#inputCustomAttempts').on('input change', function () {
        if ($('input[name="attempt_type"]:checked').val() === 'custom') {
            $('#finalMaxAttempts').val($(this).val());
        }
    });

    // 4. Live update title trên phone mockup
    $('#inputName').on('input', function () {
        const val = $.trim($(this).val());
        $('#simTitle').text(val || 'Tên Giải Đấu');
    });

    // ============================================
    // 5. CẢI THIỆN UI BANNER UPLOAD & LIVE PREVIEW
    // ============================================
    const $uploaderCard = $('#bannerUploaderCard');
    const $previewBox = $('#bannerPreviewBox');
    const $bannerInput = $('#inputBannerFile');
    const $bannerImgPreview = $('#bannerImgPreview');
    const $bannerEmptyState = $('#bannerEmptyState');
    const $bannerFileInfo = $('#bannerFileInfo');
    const $bannerFileName = $('#bannerFileName');
    const $bannerFileSize = $('#bannerFileSize');
    const $bannerStatusBadge = $('#bannerBadgeContainer');
    const $btnReset = $('#btnResetBanner');
    const $btnCancelNew = $('#btnCancelNewBanner');
    const $btnUploadText = $('#btnTriggerUploadText');
    const originalBannerSrc = $bannerImgPreview.data('original-src') || '';

    function formatBytes(bytes) {
        if (!bytes || bytes === 0) return '0 B';
        const k = 1024;
        const sizes = ['B', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
    }

    function handleBannerFile(file) {
        if (!file || !file.type.startsWith('image/')) {
            alert('Vui lòng chọn tệp hình ảnh hợp lệ (PNG, JPG, WEBP).');
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Dung lượng ảnh vượt quá 5MB. Vui lòng chọn ảnh nhẹ hơn.');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (evt) {
            const dataUrl = evt.target.result;
            // 1. Cập nhật preview trong form
            $bannerImgPreview.attr('src', dataUrl).show();
            $bannerEmptyState.hide();
            $bannerFileName.text(file.name);
            $bannerFileSize.text('Dung lượng: ' + formatBytes(file.size));
            $bannerFileInfo.removeClass('d-none').addClass('d-flex');
            $bannerStatusBadge.html('<span class="badge bg-success shadow-sm" style="font-size: 10px;"><i class="ti ti-sparkles me-1"></i>Ảnh mới chọn</span>');
            $btnReset.removeClass('d-none').addClass('d-inline-flex');
            $btnUploadText.text('Đổi ảnh khác');

            // 2. Đồng bộ tức thì lên Live Phone Mockup Simulator
            $('#simBannerImg').attr('src', dataUrl).show();
            $('#simBannerPlaceholder').hide();
        };
        reader.readAsDataURL(file);
    }

    function resetBannerToOriginal() {
        $bannerInput.val('');
        $bannerFileInfo.addClass('d-none').removeClass('d-flex');
        $btnReset.addClass('d-none').removeClass('d-inline-flex');

        if (originalBannerSrc) {
            $bannerImgPreview.attr('src', originalBannerSrc).show();
            $bannerEmptyState.hide();
            $bannerFileName.text('');
            $bannerFileSize.text('');
            $bannerStatusBadge.html('<span class="badge bg-success shadow-sm" style="font-size: 10px;"><i class="ti ti-check me-1"></i>Banner hiện tại</span>');
            $btnUploadText.text('Thay đổi ảnh banner');
            $('#simBannerImg').attr('src', originalBannerSrc).show();
            $('#simBannerPlaceholder').hide();
        } else {
            $bannerImgPreview.attr('src', '').hide();
            $bannerEmptyState.show();
            $bannerFileName.text('');
            $bannerFileSize.text('');
            $bannerStatusBadge.empty();
            $btnUploadText.text('Tải ảnh lên');
            $('#simBannerImg').attr('src', '').hide();
            $('#simBannerPlaceholder').show();
        }
    }

    // Click triggers
    $('#btnTriggerUpload, #btnOverlayChange').on('click', function (e) {
        e.stopPropagation();
        $bannerInput.trigger('click');
    });

    $previewBox.on('click', function (e) {
        if ($(e.target).closest('#btnOverlayZoom, #btnOverlayChange').length === 0) {
            $bannerInput.trigger('click');
        }
    });

    // Native file change
    $bannerInput.on('change', function (e) {
        if (e.target.files && e.target.files[0]) {
            handleBannerFile(e.target.files[0]);
        }
    });

    // Reset buttons
    $btnReset.on('click', function (e) {
        e.stopPropagation();
        resetBannerToOriginal();
    });

    $btnCancelNew.on('click', function (e) {
        e.stopPropagation();
        resetBannerToOriginal();
    });

    // Zoom modal
    $('#btnOverlayZoom').on('click', function (e) {
        e.stopPropagation();
        const currentSrc = $bannerImgPreview.attr('src');
        if (currentSrc) {
            $('#modalBannerZoomImg').attr('src', currentSrc);
            const zoomText = $bannerFileName.text() ? $bannerFileName.text() + ' (' + $bannerFileSize.text() + ')' : 'Banner 16:9 HD';
            $('#modalBannerZoomInfo').text(zoomText);
            const zoomModal = new bootstrap.Modal(document.getElementById('modalBannerZoom'));
            zoomModal.show();
        }
    });

    // Drag and Drop
    $uploaderCard.on('dragover dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).addClass('dragover');
    });

    $uploaderCard.on('dragleave dragend drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        $(this).removeClass('dragover');
    });

    $uploaderCard.on('drop', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const dt = e.originalEvent.dataTransfer;
        if (dt && dt.files && dt.files[0]) {
            $bannerInput[0].files = dt.files;
            handleBannerFile(dt.files[0]);
        }
    });
});
</script>
@endpush
