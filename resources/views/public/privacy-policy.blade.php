@extends('public.layouts.master')

@section('title', ($settings['site_name'] ?? 'Chăm Con 360') . ' - Chính Sách Bảo Mật Quyền Riêng Tư & An Toàn Cho Trẻ')

@section('meta_description', 'Chính sách bảo mật ứng dụng Chăm Con 360 (Kids360): Cam kết bảo vệ tối đa dữ liệu sức khỏe, thể chất và học tập của trẻ em. Minh bạch và tôn trọng quyền kiểm soát của cha mẹ.')

@push('styles')
<style>
    /* Hero Banner */
    .policy-hero {
        background: radial-gradient(100% 100% at 50% 0%, #E6F7F7 0%, #F8FAFC 100%);
        padding: 56px 0 32px;
        text-align: center;
        border-bottom: 1px solid var(--border-color);
        position: relative;
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(19, 122, 127, 0.1);
        color: var(--primary);
        font-weight: 700;
        font-size: 13px;
        padding: 6px 16px;
        border-radius: var(--radius-full);
        margin-bottom: 16px;
        border: 1px solid var(--primary-border);
    }
    .hero-title {
        font-size: 38px;
        font-weight: 800;
        color: var(--text-title);
        letter-spacing: -1px;
        line-height: 1.25;
        margin-bottom: 14px;
    }
    .hero-subtitle {
        font-size: 17px;
        color: var(--text-muted);
        max-width: 760px;
        margin: 0 auto 24px;
        line-height: 1.6;
    }
    .last-updated {
        font-size: 13px;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #fff;
        padding: 6px 14px;
        border-radius: var(--radius-full);
        border: 1px solid var(--border-color);
        box-shadow: var(--shadow-sm);
    }

    /* Trust Badges Strip */
    .trust-badges-strip {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
        margin-top: 28px;
    }
    .trust-badge-item {
        display: flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        border: 1px solid var(--border-color);
        padding: 8px 16px;
        border-radius: var(--radius-md);
        font-size: 13px;
        font-weight: 600;
        color: var(--text-body);
        box-shadow: var(--shadow-sm);
    }
    .trust-badge-item svg {
        color: var(--accent);
        flex-shrink: 0;
    }

    /* Tab Switcher */
    .policy-tabs-nav {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-top: 36px;
        background: #E2E8F0;
        padding: 6px;
        border-radius: var(--radius-full);
        max-width: 640px;
        margin-left: auto;
        margin-right: auto;
    }
    .tab-btn {
        flex: 1;
        padding: 10px 18px;
        font-size: 14px;
        font-weight: 700;
        color: var(--text-muted);
        border-radius: var(--radius-full);
        cursor: pointer;
        text-align: center;
        border: none;
        background: transparent;
        transition: all 0.2s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .tab-btn:hover {
        color: var(--text-title);
    }
    .tab-btn.active {
        background: #fff;
        color: var(--primary);
        box-shadow: var(--shadow-md);
    }

    /* Highlights Section */
    .highlights-section {
        padding: 40px 0 20px;
    }
    .highlights-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }
    .highlight-card {
        background: var(--bg-card);
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
        transition: transform 0.2s, box-shadow 0.2s;
        display: flex;
        flex-direction: column;
    }
    .highlight-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--primary-border);
    }
    .highlight-icon-wrap {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        background: var(--primary-light);
        color: var(--primary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
    }
    .highlight-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--text-title);
        margin-bottom: 8px;
    }
    .highlight-desc {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.5;
    }

    /* Main Two-Column Layout */
    .policy-layout {
        display: grid;
        grid-template-columns: 300px 1fr;
        gap: 48px;
        align-items: start;
        padding: 40px 0 60px;
    }

    /* Sticky Sidebar */
    .policy-sidebar {
        position: sticky;
        top: 96px;
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 24px;
        box-shadow: var(--shadow-sm);
    }
    .sidebar-title {
        font-size: 15px;
        font-weight: 700;
        color: var(--text-title);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .toc-list {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .toc-link {
        display: block;
        padding: 8px 12px;
        border-radius: var(--radius-sm);
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 500;
        transition: all 0.2s;
        line-height: 1.4;
    }
    .toc-link:hover {
        background: var(--primary-light);
        color: var(--primary);
    }
    .toc-link.active {
        background: var(--primary-light);
        color: var(--primary);
        font-weight: 700;
        border-left: 3px solid var(--primary);
    }

    /* Content Area */
    .policy-content-body {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 48px;
        box-shadow: var(--shadow-sm);
    }
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }

    /* Section Styling */
    .content-section {
        margin-bottom: 48px;
        scroll-margin-top: 110px;
    }
    .content-section:last-child {
        margin-bottom: 0;
    }
    .section-title {
        font-size: 22px;
        font-weight: 800;
        color: var(--text-title);
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 12px;
        letter-spacing: -0.3px;
        padding-bottom: 10px;
        border-bottom: 1.5px solid var(--border-color);
    }
    .section-title-num {
        width: 32px;
        height: 32px;
        background: var(--primary);
        color: #fff;
        border-radius: var(--radius-full);
        font-size: 15px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .section-text {
        font-size: 15px;
        line-height: 1.75;
        color: var(--text-body);
        margin-bottom: 16px;
    }

    /* Callout Boxes */
    .callout {
        border-radius: var(--radius-md);
        padding: 20px;
        margin: 20px 0;
        display: flex;
        gap: 16px;
        align-items: flex-start;
        font-size: 14.5px;
        line-height: 1.6;
    }
    .callout-info {
        background: var(--primary-light);
        border: 1px solid var(--primary-border);
        color: var(--primary-dark);
    }
    .callout-success {
        background: var(--accent-light);
        border: 1px solid #A7F3D0;
        color: #065F46;
    }
    .callout-warning {
        background: var(--warning-light);
        border: 1px solid #FDE68A;
        color: #92400E;
    }
    .callout-danger {
        background: var(--danger-light);
        border: 1px solid #FECACA;
        color: #991B1B;
    }

    /* List styling */
    .policy-list {
        list-style: none;
        margin: 16px 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .policy-list li {
        position: relative;
        padding-left: 26px;
        font-size: 15px;
        line-height: 1.6;
        color: var(--text-body);
    }
    .policy-list li::before {
        content: "";
        position: absolute;
        left: 6px;
        top: 10px;
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: var(--primary);
    }

    /* Tables */
    .data-table-wrap {
        overflow-x: auto;
        margin: 20px 0;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-color);
    }
    .data-table {
        width: 100%;
        border-collapse: collapse;
        text-align: left;
        font-size: 14px;
    }
    .data-table th {
        background: #F1F5F9;
        padding: 12px 16px;
        font-weight: 700;
        color: var(--text-title);
        border-bottom: 1px solid var(--border-color);
    }
    .data-table td {
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        color: var(--text-body);
        vertical-align: top;
    }
    .data-table tr:last-child td {
        border-bottom: none;
    }

    /* Account Deletion Box */
    .deletion-box {
        background: #FFFBEB;
        border: 2px dashed #F59E0B;
        border-radius: var(--radius-lg);
        padding: 30px;
        margin: 30px 0;
    }
    .deletion-box-title {
        color: #B45309;
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* App Promotion Banner */
    .app-promo-banner {
        background: linear-gradient(135deg, #137A7F 0%, #0B4B4E 100%);
        border-radius: var(--radius-lg);
        padding: 48px;
        color: #fff;
        margin-top: 60px;
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 36px;
        align-items: center;
        box-shadow: var(--shadow-xl);
    }
    .promo-title {
        font-size: 28px;
        font-weight: 800;
        line-height: 1.3;
        margin-bottom: 14px;
        letter-spacing: -0.5px;
    }
    .promo-desc {
        font-size: 15px;
        color: #E2E8F0;
        margin-bottom: 24px;
        line-height: 1.6;
    }
    .download-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
    }
    .download-store-btn {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #fff;
        padding: 10px 18px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        transition: all 0.2s;
    }
    .download-store-btn:hover {
        background: #fff;
        color: var(--primary-dark) !important;
        transform: translateY(-2px);
    }
    .download-store-btn svg {
        flex-shrink: 0;
    }
    .store-text-small {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.85;
    }
    .store-text-large {
        font-size: 15px;
        font-weight: 700;
    }
    .promo-qr-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.1);
        padding: 24px;
        border-radius: var(--radius-md);
        border: 1px solid rgba(255, 255, 255, 0.2);
        text-align: center;
    }

    @media (max-width: 960px) {
        .policy-layout {
            grid-template-columns: 1fr;
            gap: 30px;
        }
        .policy-sidebar {
            display: none; /* Hide TOC on mobile for simple reading flow */
        }
        .highlights-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        .policy-content-body {
            padding: 24px;
        }
        .hero-title {
            font-size: 28px;
        }
        .app-promo-banner {
            grid-template-columns: 1fr;
            padding: 30px 24px;
            text-align: center;
        }
        .download-buttons {
            justify-content: center;
        }
    }
    @media (max-width: 600px) {
        .highlights-grid {
            grid-template-columns: 1fr;
        }
        .policy-tabs-nav {
            flex-direction: column;
            border-radius: var(--radius-md);
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="policy-hero">
    <div class="container">
        <div class="hero-badge">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <span>Quyền Riêng Tư & Bảo Vệ Trẻ Em</span>
        </div>

        <h1 class="hero-title">Chính Sách Bảo Mật & Điều Khoản Sử Dụng</h1>
        <p class="hero-subtitle">
            Hệ sinh thái <strong>{{ $settings['site_name'] ?? 'Chăm Con 360' }}</strong> cam kết bảo vệ an toàn tối đa cho dữ liệu của con và quyền riêng tư của phụ huynh. Minh bạch, an toàn và đồng hành bền vững.
        </p>

        <div class="last-updated">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span>Hiệu lực từ: <strong>01/01/2026</strong> • Cập nhật phiên bản: <strong>2.1 (Tháng 09/2026)</strong></span>
        </div>

        <!-- Trust Badges -->
        <div class="trust-badges-strip">
            <div class="trust-badge-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Google Play Families Policy</span>
            </div>
            <div class="trust-badge-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                <span>Mã hóa SSL/TLS 256-bit</span>
            </div>
            <div class="trust-badge-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                <span>Tuân thủ COPPA & Luật Trẻ em VN</span>
            </div>
            <div class="trust-badge-item">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>100% Không bán dữ liệu</span>
            </div>
        </div>

        <!-- Tab Switcher -->
        <div class="policy-tabs-nav">
            <button class="tab-btn {{ $activeTab == 'privacy' ? 'active' : '' }}" onclick="switchTab('privacy')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Chính Sách Bảo Mật</span>
            </button>
            <button class="tab-btn {{ $activeTab == 'terms' ? 'active' : '' }}" onclick="switchTab('terms')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                <span>Điều Khoản Sử Dụng</span>
            </button>
            <button class="tab-btn {{ $activeTab == 'delete' ? 'active' : '' }}" onclick="switchTab('delete')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                <span>Xóa Dữ Liệu</span>
            </button>
        </div>
    </div>
</section>

<!-- Highlights Section -->
<section class="highlights-section">
    <div class="container">
        <div class="highlights-grid">
            <div class="highlight-card">
                <div class="highlight-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <h3 class="highlight-title">An Toàn Cho Con</h3>
                <p class="highlight-desc">Mọi hồ sơ của bé được bảo vệ nghiêm ngặt. Trẻ dưới 16 tuổi không tạo tài khoản độc lập, toàn quyền do cha mẹ quản trị.</p>
            </div>

            <div class="highlight-card">
                <div class="highlight-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"></line></svg>
                </div>
                <h3 class="highlight-title">Không Thương Mại Dữ Liệu</h3>
                <p class="highlight-desc">Cam kết không bán, chia sẻ hoặc thương mại hóa thông tin sức khỏe và hành vi của trẻ cho bất kỳ đơn vị quảng cáo nào.</p>
            </div>

            <div class="highlight-card">
                <div class="highlight-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="8.5" cy="7" r="4"></circle><polyline points="17 11 19 13 23 9"></polyline></svg>
                </div>
                <h3 class="highlight-title">Cha Mẹ Toàn Quyền</h3>
                <p class="highlight-desc">Phụ huynh có thể xem, chỉnh sửa, trích xuất hoặc yêu cầu xóa vĩnh viễn hồ sơ và tài khoản bất kỳ lúc nào.</p>
            </div>

            <div class="highlight-card">
                <div class="highlight-icon-wrap">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <h3 class="highlight-title">Mã Hóa Tiêu Chuẩn</h3>
                <p class="highlight-desc">Toàn bộ dữ liệu kết nối được truyền tải qua HTTPS/TLS và lưu trữ bảo mật với khóa mã hóa AES an toàn chuẩn quốc tế.</p>
            </div>
        </div>
    </div>
</section>

<!-- Main Two-Column Layout -->
<section>
    <div class="container policy-layout">
        <!-- Sticky Sidebar (TOC) -->
        <aside class="policy-sidebar">
            <div class="sidebar-title">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg>
                <span>Mục Lục Điều Hướng</span>
            </div>
            <nav id="privacy-toc" class="toc-list">
                <a href="#sec-gioi-thieu" class="toc-link">1. Mục đích thu thập dữ liệu</a>
                <a href="#sec-du-lieu" class="toc-link">2. Phạm vi dữ liệu thu thập</a>
                <a href="#sec-tieu-chuan-tre-em" class="toc-link">3. Bảo vệ dữ liệu trẻ em</a>
                <a href="#sec-bao-mat" class="toc-link">4. Cam kết bảo mật & Mã hóa</a>
                <a href="#sec-quyen-loi" class="toc-link">5. Quyền của phụ huynh</a>
                <a href="#sec-xoa-du-lieu" class="toc-link">6. Quy trình xóa dữ liệu</a>
                <a href="#sec-lien-he" class="toc-link">7. Thông tin đơn vị chủ quản</a>
            </nav>
        </aside>

        <!-- Main Content Body -->
        <div class="policy-content-body">
            
            <!-- TAB 1: PRIVACY POLICY -->
            <div id="tab-privacy" class="tab-content {{ $activeTab == 'privacy' ? 'active' : '' }}">
                
                <section id="sec-gioi-thieu" class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">1</span>
                        <span>Mục Đích Thu Thập Dữ Liệu</span>
                    </h2>
                    <p class="section-text">
                        Ứng dụng <strong>{{ $settings['site_name'] ?? 'Chăm Con 360' }}</strong> được thiết kế như một trợ lý đồng hành tin cậy cho cha mẹ Việt. Chúng tôi chỉ thu thập các dữ liệu thực sự cần thiết nhằm phục vụ các mục đích sau:
                    </p>
                    <ul class="policy-list">
                        <li><strong>Theo dõi thể chất chuẩn WHO:</strong> Ghi nhận chiều cao, cân nặng, chỉ số BMI và dự đoán tốc độ tăng trưởng của trẻ theo từng giai đoạn.</li>
                        <li><strong>Đánh giá đa trí thông minh:</strong> Thực hiện các bài kiểm tra khoa học về chỉ số cảm xúc (EQ), chỉ số thông minh (IQ), chỉ số vượt khó (AQ), chỉ số xã hội (SQ).</li>
                        <li><strong>Nhật ký sức khỏe & Tiêm chủng:</strong> Lưu trữ lịch tiêm phòng vắc-xin, nhắc lịch khám định kỳ, ghi chép đơn thuốc và triệu chứng bệnh.</li>
                        <li><strong>Nhật ký thai kỳ & Đồng hành cùng mẹ:</strong> Hướng dẫn chăm sóc thai nhi và trẻ sơ sinh theo tuần tuổi.</li>
                        <li><strong>Cá nhân hóa lời khuyên chuyên gia:</strong> Đưa ra các gợi ý dinh dưỡng, bài tập vận động phù hợp nhất với thể trạng của từng bé.</li>
                    </ul>

                    <div class="callout callout-info">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <div>
                            <strong>Khuyến nghị bảo mật từ chuyên gia:</strong> Phụ huynh hoàn toàn có thể sử dụng <em>biệt danh (nickname / tên ở nhà)</em> cho bé thay vì tên khai sinh để tăng cường tính riêng tư tuyệt đối trên hệ thống.
                        </div>
                    </div>
                </section>

                <section id="sec-du-lieu" class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">2</span>
                        <span>Phạm Vi & Danh Mục Dữ Liệu Thu Thập</span>
                    </h2>
                    <p class="section-text">
                        Chúng tôi phân loại dữ liệu thu thập thành hai nhóm rõ ràng, chỉ thu thập khi được phụ huynh chủ động nhập vào hoặc cấp quyền:
                    </p>

                    <div class="data-table-wrap">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 28%;">Nhóm dữ liệu</th>
                                    <th>Chi tiết dữ liệu</th>
                                    <th style="width: 25%;">Mục đích sử dụng</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>Thông tin phụ huynh</strong></td>
                                    <td>Họ tên, Email, Số điện thoại, Chiều cao bố mẹ (để tính tiềm năng chiều cao cho con).</td>
                                    <td>Tạo tài khoản cha mẹ, xác thực đăng nhập bảo mật qua OTP, liên hệ khẩn cấp.</td>
                                </tr>
                                <tr>
                                    <td><strong>Hồ sơ của trẻ</strong></td>
                                    <td>Tên/biệt danh, ngày dự sinh/ngày sinh, giới tính, ảnh đại diện (tùy chọn hoặc chọn avatar hoạt hình có sẵn).</td>
                                    <td>Thiết lập hồ sơ bé, tính toán tuổi chính xác theo tháng tuổi để đối chiếu chuẩn phát triển.</td>
                                </tr>
                                <tr>
                                    <td><strong>Dữ liệu thể chất & Y tế</strong></td>
                                    <td>Chỉ số chiều cao, cân nặng, lịch tiêm chủng, nhật ký thuốc, tiền sử bệnh án do cha mẹ nhập.</td>
                                    <td>Vẽ biểu đồ tăng trưởng, phân tích chỉ số BMI, cảnh báo suy dinh dưỡng/thừa cân, nhắc lịch tiêm.</td>
                                </tr>
                                <tr>
                                    <td><strong>Kết quả đánh giá</strong></td>
                                    <td>Câu trả lời các bài test AQ, EQ, IQ, năng khiếu và kỹ năng xã hội.</td>
                                    <td>Phân tích thế mạnh tâm sinh lý, gợi ý định hướng giáo dục và phát triển kỹ năng phù hợp.</td>
                                </tr>
                                <tr>
                                    <td><strong>Dữ liệu thiết bị</strong></td>
                                    <td>FCM Device Token, model thiết bị, hệ điều hành (Android/iOS).</td>
                                    <td>Gửi thông báo nhắc lịch tiêm, kiểm tra đăng nhập trên thiết bị lạ để bảo vệ tài khoản.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="sec-tieu-chuan-tre-em" class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">3</span>
                        <span>Chính Sách Bảo Vệ Dữ Liệu Trẻ Em (COPPA & Families Policy)</span>
                    </h2>
                    <p class="section-text">
                        Đối tượng phục vụ trọng tâm của Chăm Con 360 là trẻ em, do đó chúng tôi tuân thủ nghiêm ngặt <strong>Chính sách bảo vệ gia đình của Google Play (Families Policy)</strong>, đạo luật <strong>COPPA (Children’s Online Privacy Protection Act)</strong> và <strong>Luật Trẻ em Việt Nam 2016</strong>:
                    </p>
                    
                    <div class="callout callout-success">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                        <div>
                            <strong>Cam kết vàng 3 KHÔNG:</strong><br>
                            1. <strong>KHÔNG</strong> cho phép trẻ em dưới 16 tuổi đăng ký tài khoản độc lập.<br>
                            2. <strong>KHÔNG</strong> thu thập dữ liệu định vị GPS theo thời gian thực của trẻ.<br>
                            3. <strong>KHÔNG</strong> gắn mã theo dõi quảng cáo nhắm mục tiêu (No Behavioral Advertising) trong ứng dụng.
                        </div>
                    </div>
                    <p class="section-text">
                        Mọi thao tác tạo hồ sơ cho trẻ, chỉnh sửa chỉ số hay xóa hồ sơ bắt buộc phải được thực hiện bởi người giám hộ hợp pháp đã đăng nhập tài khoản.
                    </p>
                </section>

                <section id="sec-bao-mat" class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">4</span>
                        <span>Biện Pháp An Toàn & Mã Hóa Dữ Liệu</span>
                    </h2>
                    <p class="section-text">
                        Hạ tầng máy chủ và hệ thống cơ sở dữ liệu của chúng tôi được bảo vệ bởi nhiều lớp an ninh thông tin:
                    </p>
                    <ul class="policy-list">
                        <li><strong>Mã hóa đường truyền:</strong> 100% kết nối API giữa ứng dụng di động và máy chủ đều được mã hóa bằng giao thức HTTPS/TLS với chứng chỉ SSL 256-bit cao cấp.</li>
                        <li><strong>Mã hóa dữ liệu nhạy cảm:</strong> Mật khẩu tài khoản được băm một chiều bằng thuật toán an toàn (Bcrypt). Khóa bảo mật AES được áp dụng cho dữ liệu giao dịch.</li>
                        <li><strong>Phân quyền truy cập nghiêm ngặt:</strong> Chỉ những kỹ sư được ủy quyền bằng xác thực 2 yếu tố mới có quyền truy cập hạ tầng máy chủ cho mục đích bảo trì kỹ thuật.</li>
                        <li><strong>Sao lưu định kỳ (Backups):</strong> Dữ liệu được sao lưu tự động hàng ngày nhằm đề phòng thảm họa phần cứng và đảm bảo tính liên tục của dữ liệu.</li>
                    </ul>
                </section>

                <section id="sec-quyen-loi" class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">5</span>
                        <span>Quyền Kiểm Soát Của Phụ Huynh</span>
                    </h2>
                    <p class="section-text">
                        Cha mẹ là người sở hữu duy nhất đối với dữ liệu của con cái mình. Bạn có đầy đủ các quyền sau bất kỳ lúc nào:
                    </p>
                    <ul class="policy-list">
                        <li><strong>Quyền xem & chỉnh sửa:</strong> Bạn có thể kiểm tra và cập nhật bất kỳ thông tin nào của con (ngày sinh, chỉ số chiều cao, đổi ảnh đại diện) ngay trong app.</li>
                        <li><strong>Quyền xóa hồ sơ trẻ:</strong> Bạn có thể xóa riêng từng hồ sơ trẻ em khi không còn nhu cầu theo dõi mà vẫn giữ lại tài khoản cha mẹ.</li>
                        <li><strong>Quyền xuất dữ liệu:</strong> Bạn có quyền yêu cầu xuất bản sao các biểu đồ tăng trưởng và kết quả đánh giá để tham khảo ý kiến bác sĩ hoặc chuyên gia dinh dưỡng.</li>
                        <li><strong>Quyền xóa vĩnh viễn tài khoản:</strong> Bạn có quyền yêu cầu hủy tài khoản và xóa toàn bộ dữ liệu khỏi hệ sinh thái Chăm Con 360.</li>
                    </ul>
                </section>

                <section id="sec-xoa-du-lieu" class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">6</span>
                        <span>Quy Trình Yêu Cầu Xóa Dữ Liệu & Tài Khoản</span>
                    </h2>
                    <p class="section-text">
                        Nhằm tuân thủ quy định mới nhất của Google Play Store về <em>Tính minh bạch xóa tài khoản (Account Deletion Policy)</em>, chúng tôi cung cấp cả hai phương thức xóa dữ liệu dễ dàng:
                    </p>

                    <div class="deletion-box">
                        <div class="deletion-box-title">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            <span>Hai Cách Yêu Cầu Xóa Tài Khoản & Dữ Liệu:</span>
                        </div>
                        
                        <div style="margin-bottom: 20px;">
                            <h4 style="color:#B45309; font-size:15px; margin-bottom: 6px;">Cách 1: Xóa trực tiếp trong ứng dụng di động</h4>
                            <p style="font-size:14px; color:#78350F; line-height: 1.6;">
                                Mở app <strong>Chăm Con 360</strong> → Vào tab <strong>Tài khoản</strong> → Chọn <strong>Yêu cầu xóa tài khoản</strong> → Nhấn <strong>Xác nhận</strong>. Hệ thống sẽ tiếp nhận và tiến hành vô hiệu hóa tài khoản của bạn.
                            </p>
                        </div>

                        <div>
                            <h4 style="color:#B45309; font-size:15px; margin-bottom: 6px;">Cách 2: Gửi yêu cầu trực tuyến qua Email (Không cần cài app)</h4>
                            <p style="font-size:14px; color:#78350F; line-height: 1.6; margin-bottom: 14px;">
                                Nếu bạn đã gỡ ứng dụng hoặc muốn xóa dữ liệu từ xa, vui lòng gửi email về hòm thư hỗ trợ chính thức của chúng tôi:
                            </p>
                            <a href="mailto:{{ $settings['email'] ?? 'Chamcon360@gmail.com' }}?subject=Yêu cầu xóa tài khoản và dữ liệu cá nhân - Chăm Con 360&body=Kính gửi Ban Quản Trị Chăm Con 360,%0D%0A%0D%0ATôi muốn yêu cầu xóa hoàn toàn tài khoản và toàn bộ dữ liệu liên quan khỏi hệ thống.%0D%0A- Email đăng ký: %0D%0A- Số điện thoại đăng ký: %0D%0A- Lý do (tùy chọn): %0D%0A%0D%0AXin cảm ơn!" 
                               class="btn btn-primary" style="background:#B45309; box-shadow:none;">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                                <span>Bấm vào đây để gửi email yêu cầu xóa tài khoản</span>
                            </a>
                        </div>
                    </div>

                    <p class="section-text">
                        <strong>Thời gian xử lý:</strong> Yêu cầu sẽ được xử lý trong vòng <strong>48 giờ làm việc</strong>. Sau khi xóa thành công, toàn bộ hồ sơ của bé, các bài đánh giá và thông tin đăng nhập sẽ được xóa vĩnh viễn khỏi cơ sở dữ liệu hoạt động.
                    </p>
                </section>

                <section id="sec-lien-he" class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">7</span>
                        <span>Đơn Vị Chủ Quản & Kênh Tiếp Nhận Khiếu Nại</span>
                    </h2>
                    <p class="section-text">
                        Mọi thắc mắc, đóng góp ý kiến hoặc khiếu nại liên quan đến quyền riêng tư và bảo vệ thông tin trẻ em, xin vui lòng liên hệ trực tiếp với cơ quan chủ quản:
                    </p>
                    
                    <div style="background: #F8FAFC; border: 1.5px solid var(--border-color); border-radius: var(--radius-md); padding: 24px; margin-top: 16px;">
                        <h3 style="font-size: 17px; font-weight: 800; color: var(--text-title); margin-bottom: 12px;">
                            {{ $settings['company_name'] ?? 'Công ty TNHH Thân Tâm Trí Việt Nam' }}
                        </h3>
                        <p style="font-size: 14px; margin-bottom: 8px;">
                            <strong>📍 Trụ sở chính:</strong> {{ $settings['address'] ?? 'CF Tower, 70 Phạm Ngọc Thạch Q3, TP.HCM' }}
                        </p>
                        <p style="font-size: 14px; margin-bottom: 8px;">
                            <strong>✉️ Email bảo vệ dữ liệu:</strong> <a href="mailto:{{ $settings['email'] ?? 'Chamcon360@gmail.com' }}">{{ $settings['email'] ?? 'Chamcon360@gmail.com' }}</a>
                        </p>
                        <p style="font-size: 14px; margin-bottom: 8px;">
                            <strong>📞 Hotline hỗ trợ phụ huynh:</strong> <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline'] ?? '0985367464') }}">{{ $settings['hotline'] ?? '0985367464' }}</a>
                        </p>
                        <p style="font-size: 14px; margin-bottom: 0;">
                            <strong>🌐 Cổng thông tin điện tử:</strong> <a href="{{ $settings['website'] ?? 'https://kids360growth.com' }}" target="_blank">{{ $settings['website'] ?? 'https://kids360growth.com' }}</a>
                        </p>
                    </div>
                </section>
            </div>

            <!-- TAB 2: TERMS OF SERVICE -->
            <div id="tab-terms" class="tab-content {{ $activeTab == 'terms' ? 'active' : '' }}">
                <section class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">§</span>
                        <span>Điều Khoản Sử Dụng Dịch Vụ</span>
                    </h2>
                    <p class="section-text">
                        Chào mừng bạn đến với <strong>{{ $settings['site_name'] ?? 'Chăm Con 360' }}</strong>. Bằng việc tải về, đăng ký tài khoản hoặc sử dụng bất kỳ tính năng nào trên ứng dụng, bạn đồng ý tuân thủ các điều khoản sau:
                    </p>

                    @if(!empty($settings['policy']))
                        <div class="custom-admin-content" style="font-size: 15px; line-height: 1.8;">
                            {!! $settings['policy'] !!}
                        </div>
                    @else
                        <h3 style="font-size: 17px; font-weight: 700; margin: 20px 0 10px;">1. Quy định chung</h3>
                        <p class="section-text">
                            Ứng dụng Chăm Con 360 là công cụ công nghệ hỗ trợ cha mẹ theo dõi, đánh giá và lưu trữ nhật ký phát triển của con. Ứng dụng cung cấp các dữ liệu tham khảo dựa trên chuẩn của WHO và không thay thế cho các chẩn đoán y khoa chính thức từ bác sĩ chuyên khoa.
                        </p>
                        <h3 style="font-size: 17px; font-weight: 700; margin: 20px 0 10px;">2. Điều kiện người dùng</h3>
                        <p class="section-text">
                            Người đăng ký và tạo tài khoản phải từ đủ 18 tuổi trở lên, là cha mẹ hoặc người giám hộ hợp pháp của trẻ.
                        </p>
                        <h3 style="font-size: 17px; font-weight: 700; margin: 20px 0 10px;">3. Quyền sở hữu trí tuệ</h3>
                        <p class="section-text">
                            Mọi nhãn hiệu, logo, bộ câu hỏi đánh giá IQ/EQ/AQ, giao diện và phần mềm thuộc bản quyền sở hữu của Công ty TNHH Thân Tâm Trí Việt Nam.
                        </p>
                    @endif
                </section>
            </div>

            <!-- TAB 3: ACCOUNT & DATA DELETION -->
            <div id="tab-delete" class="tab-content {{ $activeTab == 'delete' ? 'active' : '' }}">
                <section class="content-section">
                    <h2 class="section-title">
                        <span class="section-title-num">✕</span>
                        <span>Cổng Hướng Dẫn & Tiếp Nhận Yêu Cầu Xóa Dữ Liệu</span>
                    </h2>
                    <p class="section-text">
                        Tại <strong>{{ $settings['site_name'] ?? 'Chăm Con 360' }}</strong>, chúng tôi tôn trọng tuyệt đối quyền kiểm soát thông tin cá nhân của bạn. Dưới đây là quy trình chi tiết để yêu cầu xóa vĩnh viễn tài khoản và mọi dữ liệu liên quan:
                    </p>

                    <div class="callout callout-warning">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        <div>
                            <strong>Lưu ý quan trọng:</strong> Hành động xóa tài khoản là <em>vĩnh viễn và không thể khôi phục</em>. Toàn bộ hồ sơ của các bé, biểu đồ tăng trưởng, lịch tiêm chủng và gói thành viên sẽ bị xóa hoàn toàn.
                        </div>
                    </div>

                    <h3 style="font-size: 17px; font-weight: 700; margin: 24px 0 12px; color: var(--text-title);">Các dữ liệu sẽ được xóa hoàn toàn:</h3>
                    <ul class="policy-list">
                        <li>Thông tin tài khoản phụ huynh: Họ tên, email, số điện thoại, mật khẩu mã hóa.</li>
                        <li>Toàn bộ hồ sơ của các bé: Tên/biệt danh, ngày sinh, giới tính, ảnh đại diện.</li>
                        <li>Toàn bộ chỉ số thể chất (chiều cao, cân nặng, BMI) và các bài test EQ/AQ/IQ.</li>
                        <li>Lịch tiêm phòng, nhật ký đơn thuốc và ghi chép phát triển.</li>
                        <li>Dữ liệu token thiết bị dùng cho thông báo đẩy.</li>
                    </ul>

                    <div class="deletion-box" style="margin-top: 30px;">
                        <h3 style="color:#B45309; font-size: 18px; font-weight: 800; margin-bottom: 12px;">Gửi yêu cầu xóa trực tuyến ngay bây giờ</h3>
                        <p style="font-size: 14px; color:#78350F; line-height: 1.6; margin-bottom: 20px;">
                            Nếu bạn không tiện thực hiện trong app, hãy nhấn nút dưới đây để gửi email trực tiếp tới bộ phận quản trị dữ liệu. Đội ngũ kỹ thuật sẽ xác nhận và tiến hành xóa trong vòng 48 giờ.
                        </p>
                        <a href="mailto:{{ $settings['email'] ?? 'Chamcon360@gmail.com' }}?subject=Yêu cầu xóa tài khoản và dữ liệu cá nhân - Chăm Con 360&body=Kính gửi Ban Quản Trị Chăm Con 360,%0D%0A%0D%0ATôi muốn yêu cầu xóa hoàn toàn tài khoản và toàn bộ dữ liệu liên quan khỏi hệ thống.%0D%0A- Email tài khoản: %0D%0A- Số điện thoại: %0D%0A%0D%0AThao tác này do tôi tự nguyện xác nhận." 
                           class="btn btn-primary" style="background:#B45309; box-shadow:none;">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <span>Gửi Email Yêu Cầu Xóa Dữ Liệu</span>
                        </a>
                    </div>
                </section>
            </div>

        </div>
    </div>
</section>

<!-- App Download Promotion Banner -->
<section id="app-download-section" class="container">
    <div class="app-promo-banner">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.2); padding: 4px 12px; border-radius: var(--radius-full); font-size: 12px; font-weight: 700; margin-bottom: 14px;">
                <span>✨ Ứng Dụng Hàng Đầu Dành Cho Cha Mẹ Việt</span>
            </div>
            <h2 class="promo-title">Tải Ứng Dụng {{ $settings['site_name'] ?? 'Chăm Con 360' }} Ngay Hôm Nay</h2>
            <p class="promo-desc">
                Đồng hành cùng hàng nghìn cha mẹ thông thái theo dõi chuẩn xác hành trình lớn khôn của con mỗi ngày. Miễn phí cài đặt trên cả hai nền tảng Android và iOS.
            </p>

            <div class="download-buttons">
                <a href="https://play.google.com/store/apps" target="_blank" class="download-store-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M3 20.5V3.5C3 2.91 3.34 2.39 3.84 2.15L13.69 12L3.84 21.85C3.34 21.6 3 21.09 3 20.5ZM16.81 15.12L15.11 13.42L18.3 10.23C18.77 10.7 18.77 11.45 18.3 11.92L16.81 15.12ZM14.41 12.71L4.71 22.41C4.89 22.47 5.09 22.5 5.3 22.5C5.7 22.5 6.09 22.33 6.38 22.04L14.41 14L14.41 12.71ZM14.41 11.29L6.38 3.26C6.09 2.97 5.7 2.8 5.3 2.8C5.09 2.8 4.89 2.83 4.71 2.89L14.41 11.29Z"/></svg>
                    <div>
                        <div class="store-text-small">Tải về trên</div>
                        <div class="store-text-large">Google Play</div>
                    </div>
                </a>

                <a href="https://www.apple.com/app-store/" target="_blank" class="download-store-btn">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M18.71 19.5C17.88 20.74 17 21.95 15.66 21.97C14.32 22 13.89 21.18 12.37 21.18C10.84 21.18 10.37 21.95 9.09 22C7.79 22.05 6.8 20.68 5.96 19.47C4.25 17 2.94 12.45 4.7 9.39C5.57 7.87 7.13 6.91 8.82 6.88C10.1 6.86 11.32 7.75 12.11 7.75C12.89 7.75 14.37 6.68 15.92 6.84C16.57 6.87 18.39 7.1 19.56 8.82C19.47 8.88 17.39 10.1 17.41 12.63C17.44 15.65 20.06 16.66 20.09 16.67C20.06 16.74 19.67 18.11 18.71 19.5ZM15.97 4.54C16.65 3.71 17.11 2.56 16.98 1.41C15.98 1.45 14.77 2.08 14.05 2.91C13.41 3.65 12.85 4.82 13.01 5.95C14.12 6.04 15.29 5.37 15.97 4.54Z"/></svg>
                    <div>
                        <div class="store-text-small">Tải về trên</div>
                        <div class="store-text-large">App Store</div>
                    </div>
                </a>
            </div>
        </div>

        <div class="promo-qr-wrap">
            <div style="width: 140px; height: 140px; background: #fff; border-radius: 12px; padding: 10px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center;">
                <img src="{{ asset($settings['site_logo'] ?? 'public/assets/images/logo.png') }}" 
                     alt="Chăm Con 360" style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <span style="font-size: 13px; font-weight: 600;">Quét mã tải ứng dụng</span>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    // Tab switching function
    function switchTab(tabId) {
        // Update URL parameter without reload
        const url = new URL(window.location);
        url.searchParams.set('tab', tabId);
        window.history.replaceState({}, '', url);

        // Update tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        event.currentTarget.classList.add('active');

        // Update tab contents
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));
        const target = document.getElementById('tab-' + tabId);
        if (target) {
            target.classList.add('active');
        }

        // Show/hide sidebar TOC depending on tab
        const sidebar = document.querySelector('.policy-sidebar');
        if (sidebar) {
            sidebar.style.display = (tabId === 'privacy') ? 'block' : 'none';
        }
    }

    // ScrollSpy for TOC highlight
    window.addEventListener('DOMContentLoaded', () => {
        const sections = document.querySelectorAll('.content-section');
        const navLinks = document.querySelectorAll('.toc-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop - 130;
                if (pageYOffset >= sectionTop) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === '#' + current) {
                    link.classList.add('active');
                }
            });
        });
    });
</script>
@endpush
