@extends('admin.layouts.guest.master')

@section('content')
    @php
        $settingRepository = app()->make(App\Admin\Repositories\Setting\SettingRepository::class);
        $settings = $settingRepository->getAll();
        $siteLogo = $settings->where('setting_key', 'site_logo')->first()?->plain_value 
            ?? config('custom.images.default.logo', '/public/assets/images/logo.png');
        $siteName = $settings->where('setting_key', 'site_name')->first()?->plain_value ?? 'CHĂM CON';
    @endphp

    <div class="auth-wrapper">
        <div class="auth-split">
            <!-- Left Side: Branding & Features Hero -->
            <div class="auth-hero">
                <div class="hero-bg-overlay"></div>
                <div class="hero-content">
                    <!-- Brand Header -->
                    <div class="hero-brand">
                        <div class="brand-logo-wrapper">
                            <img src="{{ asset($siteLogo) }}" alt="{{ $siteName }}" class="brand-logo">
                        </div>
                        <div class="brand-info">
                            <span class="brand-title">{{ $siteName }}</span>
                            <span class="brand-tagline">Hệ thống Quản trị & Đồng hành cùng trẻ</span>
                        </div>
                    </div>

                    <!-- Visual Illustration Art -->
                    <div class="hero-illustration">
                        <div class="growth-graphic-container">
                            <div class="glow-sphere"></div>
                            <div class="graphic-art">
                                <svg class="art-svg" viewBox="0 0 260 260" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="primaryGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#3B82F6" />
                                            <stop offset="100%" stop-color="#1D4ED8" />
                                        </linearGradient>
                                        <linearGradient id="cardGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#1E293B" />
                                            <stop offset="100%" stop-color="#0F172A" />
                                        </linearGradient>
                                        <linearGradient id="accentGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                                            <stop offset="0%" stop-color="#06B6D4" />
                                            <stop offset="100%" stop-color="#3B82F6" />
                                        </linearGradient>
                                        <linearGradient id="warmGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" stop-color="#F59E0B" />
                                            <stop offset="100%" stop-color="#EF4444" />
                                        </linearGradient>
                                        <filter id="shadow" x="-10%" y="-10%" width="120%" height="120%">
                                            <feDropShadow dx="0" dy="12" stdDeviation="15" flood-color="#000000" flood-opacity="0.45"/>
                                        </filter>
                                    </defs>

                                    <!-- Background decorative grid circles -->
                                    <circle cx="130" cy="130" r="105" stroke="rgba(255,255,255,0.06)" stroke-width="1.5" stroke-dasharray="6 6" />
                                    <circle cx="130" cy="130" r="82" stroke="rgba(59,130,246,0.2)" stroke-width="1.5" />

                                    <!-- Main Floating Center Card -->
                                    <g class="floating-main-card" filter="url(#shadow)">
                                        <rect x="45" y="70" width="170" height="115" rx="16" fill="url(#cardGrad)" stroke="rgba(255,255,255,0.12)" stroke-width="1.5" />
                                        
                                        <!-- Chart lines / Growth bars inside card -->
                                        <rect x="65" y="145" width="16" height="25" rx="4" fill="url(#accentGrad)" opacity="0.8" />
                                        <rect x="90" y="125" width="16" height="45" rx="4" fill="url(#primaryGrad)" />
                                        <rect x="115" y="110" width="16" height="60" rx="4" fill="url(#accentGrad)" />
                                        <rect x="140" y="95" width="16" height="75" rx="4" fill="url(#primaryGrad)" />
                                        <rect x="165" y="85" width="16" height="85" rx="4" fill="url(#accentGrad)" />
                                        
                                        <!-- Growth curve line -->
                                        <path d="M73 140 Q 115 105, 173 80" stroke="#60A5FA" stroke-width="3" stroke-linecap="round" />
                                        <circle cx="173" cy="80" r="4" fill="#FFFFFF" stroke="#3B82F6" stroke-width="2" />
                                    </g>

                                    <!-- Top Left Floating Badge (Security) -->
                                    <g class="floating-badge-top">
                                        <rect x="25" y="45" width="76" height="28" rx="14" fill="#0F172A" stroke="rgba(16,185,129,0.5)" stroke-width="1.5" />
                                        <circle cx="39" cy="59" r="4" fill="#10B981" />
                                        <text x="65" y="63" fill="#E2E8F0" font-size="10" font-weight="700" text-anchor="middle" font-family="sans-serif">SECURE</text>
                                    </g>

                                    <!-- Top Right Floating Badge (Heart / Care) -->
                                    <g class="floating-badge-right">
                                        <rect x="175" y="40" width="70" height="28" rx="14" fill="url(#warmGrad)" />
                                        <path d="M195 54 C193 51, 189 52, 189 55 C189 58, 195 62, 195 62 C195 62, 201 58, 201 55 C201 52, 197 51, 195 54 Z" fill="#FFFFFF" />
                                        <text x="220" y="58" fill="#FFFFFF" font-size="10" font-weight="700" text-anchor="middle" font-family="sans-serif">CARE</text>
                                    </g>

                                    <!-- Bottom Floating Badge (Analytics) -->
                                    <g class="floating-badge-bottom">
                                        <rect x="145" y="195" width="85" height="28" rx="14" fill="#1E293B" stroke="rgba(59,130,246,0.5)" stroke-width="1.5" />
                                        <circle cx="160" cy="209" r="4" fill="#3B82F6" />
                                        <text x="195" y="213" fill="#E2E8F0" font-size="10" font-weight="700" text-anchor="middle" font-family="sans-serif">WHO & GPA</text>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </div>

                    <!-- Headline & Features List -->
                    <div class="hero-text-block">
                        <h1 class="hero-title">Quản Trị Hệ Thống Chăm Con</h1>
                        <p class="hero-subtitle">Nền tảng số quản lý chỉ số phát triển, y tế, giáo dục và hỗ trợ cha mẹ toàn diện.</p>

                        <div class="hero-features">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                </div>
                                <div class="feature-desc">
                                    <strong>Bảo mật & Phân quyền đa cấp</strong>
                                    <span>An toàn tuyệt đối dữ liệu trẻ và hồ sơ sức khỏe phụ huynh</span>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                                </div>
                                <div class="feature-desc">
                                    <strong>Theo dõi & Đánh giá khoa học</strong>
                                    <span>Chuẩn hóa chỉ số phát triển GPA, PQ, EQ, IQ, WHO & Lịch tiêm</span>
                                </div>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="m13 2-2 10h7l-9 10 2-10H4z"/></svg>
                                </div>
                                <div class="feature-desc">
                                    <strong>Tương tác & Thông báo tức thì</strong>
                                    <span>Đồng bộ dữ liệu App Khách hàng và Firebase realtime</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Note -->
                    <div class="hero-footer">
                        <span>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</span>
                    </div>
                </div>
            </div>

            <!-- Right Side: Auth Form Container -->
            <div class="auth-form-side">
                <div class="form-container-card">

                    <!-- Mobile Brand Header (Visible only on mobile) -->
                    <div class="mobile-brand-header text-center mb-4">
                        <div class="mobile-logo-wrapper mx-auto mb-2">
                            <img class="img-fluid" src="{{ asset($siteLogo) }}" alt="{{ $siteName }}">
                        </div>
                        <h2 class="mobile-site-name mb-1">{{ $siteName }}</h2>
                        <span class="badge bg-primary text-white px-3 py-1 rounded-pill fs-11 fw-semibold">CỔNG QUẢN TRỊ VIÊN</span>
                    </div>

                    <!-- Desktop Form Header -->
                    <div class="form-header text-center mb-4">
                        <div class="admin-badge mb-2">
                            <span class="badge bg-blue-subtle text-primary border border-primary-subtle px-3 py-1 rounded-pill">
                                <i class="ti ti-shield-lock me-1"></i> CMS ADMIN PORTAL
                            </span>
                        </div>
                        <h2 class="auth-title">Đăng nhập Hệ thống</h2>
                        <p class="auth-subtitle">Vui lòng nhập thông tin tài khoản của bạn để tiếp tục</p>
                    </div>

                    <!-- Login Form -->
@php
    use App\Traits\RouteAdminSystem;
@endphp
                    <x-form :action="route(RouteAdminSystem::ADMIN_LOGIN_POST)" class="login-form-element" type="post" :validate="true">
                        
                        <!-- Email Input Group -->
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Tài khoản Email <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                </span>
                                <x-input-email name="email" :required="true"
                                               class="form-control-custom"
                                               placeholder="admin@example.com" />
                            </div>
                        </div>

                        <!-- Password Input Group -->
                        <div class="form-group mb-3">
                            <label class="form-label-custom">Mật khẩu <span class="text-danger">*</span></label>
                            <div class="input-icon-wrapper">
                                <span class="input-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                                </span>
                                <x-input-password name="password" :required="true" id="password_field"
                                                  class="form-control-custom pr-10"
                                                  placeholder="••••••••" />
                                <button type="button" class="btn-toggle-password" id="togglePasswordBtn" title="Hiện/Ẩn mật khẩu" tabindex="-1">
                                    <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
                                    <svg id="eyeOffIcon" class="d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.88 9.88a3 3 0 1 0 4.24 4.24"/><path d="M10.73 5.08A10.43 10.43 0 0 1 12 5c7 0 10 7 10 7a13.16 13.16 0 0 1-1.67 2.68"/><path d="M6.61 6.61A13.52 13.52 0 0 0 2 12s3 7 10 7a9.74 9.74 0 0 0 5.39-1.61"/><line x1="2" y1="2" x2="22" y2="22"/></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Remember Option -->
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <label class="form-check m-0">
                                <input type="checkbox" name="remember" class="form-check-input" value="1" checked>
                                <span class="form-check-label text-sm text-secondary">Ghi nhớ đăng nhập</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-footer">
                            <button type="submit" class="btn-auth-submit w-100">
                                <span>ĐĂNG NHẬP HỆ THỐNG</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                            </button>
                        </div>
                    </x-form>

                    <!-- Trust & Security Note -->
                    <div class="security-badge-footer text-center mt-4 pt-3 border-top">
                        <div class="d-inline-flex align-items-center gap-2 text-xs text-muted">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                            <span>Hệ thống bảo vệ với mã hóa dữ liệu an toàn</span>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('custom-css')
<style>
    /* Reset & Master Layout for Auth */
    body {
        margin: 0;
        padding: 0;
        overflow-x: hidden;
        background: radial-gradient(circle at 50% 30%, #1e293b 0%, #0f172a 70%, #080d1a 100%);
        min-height: 100vh;
        font-family: var(--tblr-font-sans-serif, 'Inter', -apple-system, sans-serif);
    }

    .auth-wrapper {
        width: 100vw;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem 1.5rem;
        box-sizing: border-box;
        position: relative;
    }

    .auth-wrapper::before {
        content: '';
        position: absolute;
        width: 600px;
        height: 600px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.22) 0%, rgba(6, 182, 212, 0.05) 60%, transparent 100%);
        top: -150px;
        left: -100px;
        filter: blur(70px);
        pointer-events: none;
    }

    .auth-wrapper::after {
        content: '';
        position: absolute;
        width: 500px;
        height: 500px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(139, 92, 246, 0.18) 0%, rgba(37, 99, 235, 0.03) 60%, transparent 100%);
        bottom: -100px;
        right: -100px;
        filter: blur(70px);
        pointer-events: none;
    }

    .auth-split {
        display: flex;
        width: 100%;
        max-width: 1020px;
        min-height: 600px;
        background: #ffffff;
        border-radius: 24px;
        box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.5), 0 0 1px rgba(255, 255, 255, 0.2);
        overflow: hidden;
        position: relative;
        z-index: 10;
    }

    /* Left Side - Hero Branding */
    .auth-hero {
        flex: 1.15;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #092552 100%);
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 2.5rem 3rem;
        color: #ffffff;
        overflow: hidden;
    }

    .hero-bg-overlay {
        position: absolute;
        inset: 0;
        background-image: 
            radial-gradient(circle at 15% 20%, rgba(59, 130, 246, 0.18) 0%, transparent 40%),
            radial-gradient(circle at 85% 80%, rgba(6, 182, 212, 0.15) 0%, transparent 40%),
            linear-gradient(rgba(255, 255, 255, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
        background-size: 100% 100%, 100% 100%, 40px 40px, 40px 40px;
        z-index: 1;
    }

    .hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        flex-direction: column;
        height: 100%;
        justify-content: space-between;
    }

    .hero-brand {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .brand-logo-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .brand-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .brand-info {
        display: flex;
        flex-direction: column;
    }

    .brand-title {
        font-size: 1.35rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: #ffffff;
        text-transform: uppercase;
        line-height: 1.2;
    }

    .brand-tagline {
        font-size: 0.775rem;
        color: #60a5fa;
        font-weight: 500;
        letter-spacing: 0.2px;
    }

    /* Hero Graphic Art */
    .hero-illustration {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 1.5rem 0;
    }

    .growth-graphic-container {
        position: relative;
        width: 240px;
        height: 240px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .glow-sphere {
        position: absolute;
        width: 170px;
        height: 170px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(37, 99, 235, 0.4) 0%, rgba(6, 182, 212, 0.1) 70%, transparent 100%);
        filter: blur(25px);
        animation: pulseGlow 4s ease-in-out infinite alternate;
    }

    .art-svg {
        width: 100%;
        height: 100%;
    }

    .floating-main-card {
        animation: floatMain 4s ease-in-out infinite alternate;
    }

    .floating-badge-top {
        animation: floatBadge 3.2s ease-in-out infinite alternate 0.2s;
    }

    .floating-badge-right {
        animation: floatBadge 3.6s ease-in-out infinite alternate 0.6s;
    }

    .floating-badge-bottom {
        animation: floatBadge 3s ease-in-out infinite alternate 0.4s;
    }

    @keyframes floatMain {
        0% { transform: translateY(0px); }
        100% { transform: translateY(-10px); }
    }

    @keyframes floatBadge {
        0% { transform: translateY(0px) scale(1); }
        100% { transform: translateY(-6px) scale(1.04); }
    }

    @keyframes pulseGlow {
        0% { opacity: 0.6; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1.1); }
    }

    /* Hero Text & Features */
    .hero-text-block {
        max-width: 520px;
    }

    .hero-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 0.4rem;
        line-height: 1.25;
    }

    .hero-subtitle {
        font-size: 0.9rem;
        color: #94a3b8;
        margin-bottom: 1.25rem;
        line-height: 1.5;
    }

    .hero-features {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .feature-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        padding: 0.7rem 1rem;
        border-radius: 12px;
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }

    .feature-item:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(59, 130, 246, 0.4);
        transform: translateX(4px);
    }

    .feature-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(37, 99, 235, 0.2);
        color: #60a5fa;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .feature-desc {
        display: flex;
        flex-direction: column;
    }

    .feature-desc strong {
        font-size: 0.85rem;
        color: #f1f5f9;
        margin-bottom: 2px;
    }

    .feature-desc span {
        font-size: 0.75rem;
        color: #94a3b8;
    }

    .hero-footer {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 1.25rem;
    }

    /* Right Side - Auth Form Side */
    .auth-form-side {
        flex: 0.85;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2.5rem;
        position: relative;
    }

    .form-container-card {
        width: 100%;
        max-width: 380px;
        background: #ffffff;
        border-radius: 0;
        padding: 0;
        box-shadow: none;
        border: none;
        animation: cardFadeUp 0.6s ease-out;
    }

    @keyframes cardFadeUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mobile-brand-header {
        display: none;
    }

    .mobile-logo-wrapper {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 6px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .mobile-site-name {
        color: #ffffff;
        font-size: 1.4rem;
        font-weight: 700;
    }

    .auth-title {
        font-size: 1.65rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 0.35rem;
    }

    .auth-subtitle {
        font-size: 0.875rem;
        color: #64748b;
        margin: 0;
    }

    .bg-blue-subtle {
        background-color: #eff6ff !important;
    }

    .border-primary-subtle {
        border-color: #bfdbfe !important;
    }

    .form-label-custom {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }

    /* Custom Input with Icons & Parsley Validation */
    .input-icon-wrapper {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }

    .input-icon {
        position: absolute;
        left: 14px;
        top: 24px;
        transform: translateY(-50%);
        color: #94a3b8;
        display: flex;
        align-items: center;
        pointer-events: none;
        transition: color 0.2s ease;
        z-index: 2;
    }

    .form-control-custom,
    input.form-control-custom {
        width: 100%;
        height: 48px;
        padding-left: 44px !important;
        padding-right: 16px;
        border-radius: 10px !important;
        border: 1.5px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        font-size: 0.95rem !important;
        transition: all 0.25s ease !important;
        box-shadow: none !important;
    }

    .form-control-custom.pr-10 {
        padding-right: 46px !important;
    }

    .form-control-custom:focus {
        border-color: #2563eb !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12) !important;
    }

    .input-icon-wrapper:focus-within .input-icon {
        color: #2563eb;
    }

    .btn-toggle-password {
        position: absolute;
        right: 12px;
        top: 24px;
        transform: translateY(-50%);
        background: none;
        border: none;
        color: #94a3b8;
        cursor: pointer;
        padding: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        transition: color 0.2s ease;
        z-index: 2;
    }

    .btn-toggle-password:hover {
        color: #1e293b;
    }

    /* Parsley Validation Styling Fix */
    .parsley-errors-list {
        width: 100%;
        flex-basis: 100%;
        margin: 6px 0 0 0 !important;
        padding: 0 !important;
        list-style-type: none !important;
        font-size: 0.825rem !important;
        color: #dc2626 !important;
        font-weight: 500;
        line-height: 1.4;
    }

    .parsley-errors-list li {
        color: #dc2626 !important;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .form-control-custom.parsley-error {
        border-color: #ef4444 !important;
        background-color: #fef2f2 !important;
    }

    .form-control-custom.parsley-error:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15) !important;
    }

    /* Submit Button */
    .btn-auth-submit {
        height: 50px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.4);
    }

    .btn-auth-submit:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 14px 26px -4px rgba(37, 99, 235, 0.5);
    }

    .btn-auth-submit:active {
        transform: translateY(0);
    }

    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .auth-hero {
            display: none;
        }

        .auth-form-side {
            flex: 1;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 1.5rem;
        }

        .form-container-card {
            max-width: 420px;
            padding: 2.25rem 1.75rem;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
        }

        .mobile-brand-header {
            display: block;
        }

        .form-header .admin-badge,
        .form-header .auth-title,
        .form-header .auth-subtitle {
            display: none;
        }

        .form-label-custom {
            color: #f1f5f9 !important;
        }

        .form-check-label {
            color: #94a3b8 !important;
        }

        .security-badge-footer {
            border-top-color: rgba(255, 255, 255, 0.1) !important;
        }

        .security-badge-footer span {
            color: #94a3b8 !important;
        }
    }
</style>
@endpush

@push('custom-js')
<script>
    $(document).ready(function () {
        // Toggle password visibility
        $('#togglePasswordBtn').on('click', function (e) {
            e.preventDefault();
            const passwordInput = $('#password_field');
            const eyeIcon = $('#eyeIcon');
            const eyeOffIcon = $('#eyeOffIcon');

            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                eyeIcon.addClass('d-none');
                eyeOffIcon.removeClass('d-none');
            } else {
                passwordInput.attr('type', 'password');
                eyeIcon.removeClass('d-none');
                eyeOffIcon.addClass('d-none');
            }
        });
    });
</script>
@endpush
