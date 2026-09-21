@extends('admin.layouts.master')
@php use App\Traits\RouteAdminSystem; @endphp

@section('content')
    <style>
        .page-body {
            background: #F8FAFC;
            min-height: 100vh;
            padding: 1.75rem 0;
        }

        .device-card-custom {
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            background: #FFFFFF;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .device-card-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
        }

        /* Platform Icon Circle */
        .platform-icon-circle {
            width: 54px;
            height: 54px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.85rem;
        }

        .platform-icon-ios {
            background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.25);
        }

        .platform-icon-android {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        }

        /* Highlight winner banner */
        .leader-banner {
            border-radius: 14px;
            padding: 1.15rem 1.35rem;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            border: 1px solid #C7D2FE;
        }

        /* Custom progress bar */
        .stacked-ratio-bar {
            height: 18px;
            border-radius: 10px;
            overflow: hidden;
            display: flex;
            background: #E2E8F0;
        }

        .stacked-seg-ios {
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .stacked-seg-android {
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>

    <div class="page-body">
        <div class="container-fluid">
            <!-- PAGE HEADER -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h1 class="h2 fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                                    <i class="ti ti-devices text-primary"></i>
                                    <span>{{ __('Thống kê Thiết bị & Hệ điều hành') }}</span>
                                </h1>
                                <span class="badge bg-success-lt fw-semibold ms-2">
                                    <i class="ti ti-database me-1"></i>{{ __('Toàn bộ hệ thống (Cơ sở dữ liệu)') }}
                                </span>
                            </div>
                            <p class="text-muted mb-0">
                                {{ __('Thống kê so sánh tỷ trọng thị phần toàn bộ thiết bị đang liên kết trong hệ thống giữa 2 nền tảng: Apple iOS và Google Android.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- LEADER INSIGHT BANNER -->
            @php
                $totalDevices = $deviceStats['total'] ?? 0;
                $iosPercent = $deviceStats['ios']['percent'] ?? 0;
                $androidPercent = $deviceStats['android']['percent'] ?? 0;
                $topPlatform = $deviceStats['top_platform'] ?? 'iOS';
                $topPercent = $deviceStats['top_percent'] ?? max($iosPercent, $androidPercent);
            @endphp

            <div class="leader-banner mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white p-2 rounded-circle text-primary shadow-sm d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="ti ti-trophy fs-2 text-warning"></i>
                    </div>
                    <div>
                        <div class="text-uppercase fw-bold fs-11 text-indigo mb-1" style="letter-spacing: 0.5px;">
                            {{ __('Kết quả dẫn đầu toàn hệ thống') }}
                        </div>
                        <div class="fw-bold text-dark fs-3" id="leader-text">
                            @if($totalDevices > 0)
                                {{ $topPlatform === 'iOS' ? 'Apple iOS' : 'Google Android' }}
                                {{ __('đang được sử dụng nhiều nhất, chiếm') }}
                                <span class="text-primary">{{ $topPercent }}%</span>
                                {{ __('thị phần người dùng toàn hệ thống') }}
                            @else
                                {{ __('Chưa có dữ liệu thiết bị liên kết trong hệ thống') }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2 PLATFORM COMPARISON CARDS (IOS VS ANDROID) -->
            <div class="row g-3 mb-4">
                <!-- 🍏 APPLE IOS CARD -->
                <div class="col-12 col-lg-6">
                    <div class="card device-card-custom h-100 p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="platform-icon-circle platform-icon-ios">
                                    <i class="ti ti-brand-apple"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold text-dark mb-0 fs-2">Apple iOS</h3>
                                    <span class="text-muted fs-12">{{ __('iPhone & iPad') }}</span>
                                </div>
                            </div>
                            @if($totalDevices > 0 && ($deviceStats['ios']['is_top'] ?? false))
                                <span class="badge bg-primary-lt px-3 py-2 fw-bold fs-12">
                                    <i class="ti ti-crown text-warning me-1"></i>{{ __('Sử dụng nhiều nhất') }}
                                </span>
                            @endif
                        </div>

                        <div class="my-3">
                            <div class="d-flex align-items-baseline justify-content-between mb-2">
                                <span class="text-muted fw-semibold fs-13">{{ __('Thị phần nền tảng') }}</span>
                                <span class="fw-bold text-primary fs-1">{{ $iosPercent }}%</span>
                            </div>

                            <div class="progress progress-sm" style="height: 10px; border-radius: 6px; background: #E2E8F0;">
                                <div class="progress-bar bg-primary" style="width: {{ $iosPercent }}%" role="progressbar" aria-valuenow="{{ $iosPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between text-muted fs-12">
                            <span><i class="ti ti-device-mobile me-1"></i>{{ __('Hệ điều hành iOS') }}</span>
                            <span class="badge bg-blue-lt">{{ __('App Store') }}</span>
                        </div>
                    </div>
                </div>

                <!-- 🤖 GOOGLE ANDROID CARD -->
                <div class="col-12 col-lg-6">
                    <div class="card device-card-custom h-100 p-4">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="platform-icon-circle platform-icon-android">
                                    <i class="ti ti-brand-android"></i>
                                </div>
                                <div>
                                    <h3 class="fw-bold text-dark mb-0 fs-2">Google Android</h3>
                                    <span class="text-muted fs-12">{{ __('Samsung, Xiaomi, Oppo, v.v.') }}</span>
                                </div>
                            </div>
                            @if($totalDevices > 0 && ($deviceStats['android']['is_top'] ?? false))
                                <span class="badge bg-success-lt px-3 py-2 fw-bold fs-12">
                                    <i class="ti ti-crown text-warning me-1"></i>{{ __('Sử dụng nhiều nhất') }}
                                </span>
                            @endif
                        </div>

                        <div class="my-3">
                            <div class="d-flex align-items-baseline justify-content-between mb-2">
                                <span class="text-muted fw-semibold fs-13">{{ __('Thị phần nền tảng') }}</span>
                                <span class="fw-bold text-success fs-1">{{ $androidPercent }}%</span>
                            </div>

                            <div class="progress progress-sm" style="height: 10px; border-radius: 6px; background: #E2E8F0;">
                                <div class="progress-bar bg-success" style="width: {{ $androidPercent }}%" role="progressbar" aria-valuenow="{{ $androidPercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>

                        <div class="mt-auto pt-3 border-top d-flex align-items-center justify-content-between text-muted fs-12">
                            <span><i class="ti ti-device-mobile me-1"></i>{{ __('Hệ điều hành Android') }}</span>
                            <span class="badge bg-green-lt">{{ __('Google Play') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- OVERALL MARKET SHARE VISUAL BAR -->
            <div class="card device-card-custom mb-4">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h3 class="card-title fw-bold text-dark mb-1">
                                <i class="ti ti-chart-pie text-indigo me-2"></i>{{ __('Tương quan Thị phần Toàn hệ thống (Apple iOS vs Google Android)') }}
                            </h3>
                            <div class="text-muted fs-12">{{ __('Biểu đồ so sánh tỷ trọng trực quan theo tỷ lệ phần trăm thị phần') }}</div>
                        </div>
                        <span class="badge bg-secondary-lt fw-bold px-3 py-1 fs-12">
                            {{ __('Toàn bộ dữ liệu') }}
                        </span>
                    </div>
                </div>
                <div class="card-body p-4">
                    <!-- Visual Stacked Bar -->
                    <div class="mb-3">
                        <div class="stacked-ratio-bar shadow-sm">
                            <div class="stacked-seg-ios" style="width: {{ $iosPercent }}%" title="iOS: {{ $iosPercent }}%"></div>
                            <div class="stacked-seg-android" style="width: {{ $androidPercent }}%" title="Android: {{ $androidPercent }}%"></div>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div class="d-flex align-items-center justify-content-center gap-4 text-muted fs-13 py-2 flex-wrap">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background: #3b82f6;"></span>
                            <span class="fw-semibold text-dark">Apple iOS:</span>
                            <strong class="text-primary">{{ $iosPercent }}%</strong>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded-circle" style="width: 12px; height: 12px; background: #10b981;"></span>
                            <span class="fw-semibold text-dark">Google Android:</span>
                            <strong class="text-success">{{ $androidPercent }}%</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- QUICK LINKS -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="{{ route(RouteAdminSystem::ADMIN_DASHBOARD) }}" class="btn btn-outline-secondary d-flex align-items-center gap-2">
                    <i class="ti ti-arrow-left"></i>
                    <span>{{ __('Quay lại Dashboard') }}</span>
                </a>
                <a href="{{ route(RouteAdminSystem::USER_INDEX) }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="ti ti-users"></i>
                    <span>{{ __('Quản lý Người dùng') }}</span>
                </a>
            </div>
        </div>
    </div>
@endsection
