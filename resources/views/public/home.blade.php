@extends('public.layouts.master')

@section('title', ($settings['site_name'] ?? 'Chăm Con 360') . ' - Trợ Lý Thông Minh Đồng Hành Cùng Sự Phát Triển Của Con')

@section('meta_description', 'Ứng dụng Chăm Con 360 (Kids360): Theo dõi chiều cao chuẩn WHO, đánh giá đa trí tuệ AQ - EQ - IQ, sổ tiêm chủng và nhật ký dinh dưỡng toàn diện cho trẻ.')

@push('styles')
<style>
    /* Gradient text */
    .gradient-text {
        background: linear-gradient(135deg, var(--primary) 0%, #21A179 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Hero Section */
    .home-hero {
        position: relative;
        background: radial-gradient(120% 120% at 50% -10%, #E6F7F7 0%, #FFFFFF 60%, #F8FAFC 100%);
        padding: 70px 0 60px;
        overflow: hidden;
    }
    .hero-grid {
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 48px;
        align-items: center;
    }
    .hero-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(19, 122, 127, 0.08);
        border: 1px solid var(--primary-border);
        color: var(--primary);
        font-weight: 700;
        font-size: 13px;
        padding: 6px 16px;
        border-radius: var(--radius-full);
        margin-bottom: 20px;
    }
    .hero-heading {
        font-size: 46px;
        font-weight: 800;
        color: var(--text-title);
        line-height: 1.2;
        letter-spacing: -1.2px;
        margin-bottom: 20px;
    }
    .hero-lead {
        font-size: 17px;
        color: var(--text-muted);
        line-height: 1.65;
        margin-bottom: 32px;
        max-width: 580px;
    }
    .hero-cta-group {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
        margin-bottom: 36px;
    }
    .btn-hero-primary {
        background: linear-gradient(135deg, var(--primary) 0%, #0D5A5E 100%);
        color: #fff !important;
        font-size: 15px;
        font-weight: 700;
        padding: 14px 28px;
        border-radius: var(--radius-full);
        box-shadow: 0 10px 20px rgba(19, 122, 127, 0.25);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.25s ease;
    }
    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(19, 122, 127, 0.35);
    }
    .btn-hero-secondary {
        background: #fff;
        color: var(--text-title) !important;
        border: 1.5px solid var(--border-color);
        font-size: 15px;
        font-weight: 600;
        padding: 14px 24px;
        border-radius: var(--radius-full);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
    }
    .btn-hero-secondary:hover {
        border-color: var(--primary);
        color: var(--primary) !important;
        background: var(--primary-light);
    }

    /* Hero Visual Card Stack */
    .hero-visual-wrap {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .hero-main-img-card {
        background: #fff;
        border-radius: 28px;
        padding: 20px;
        box-shadow: 0 25px 50px -12px rgba(19, 122, 127, 0.15);
        border: 1.5px solid var(--border-color);
        position: relative;
        z-index: 2;
        max-width: 420px;
        width: 100%;
        text-align: center;
    }
    .hero-main-img {
        width: 100%;
        height: auto;
        max-height: 400px;
        object-fit: contain;
        border-radius: 16px;
    }

    /* Floating Stat Badges */
    .floating-badge {
        position: absolute;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1.5px solid var(--border-color);
        padding: 12px 18px;
        border-radius: 16px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.08);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 3;
        animation: float 4s ease-in-out infinite;
    }
    .floating-badge-1 {
        top: -15px;
        left: -20px;
    }
    .floating-badge-2 {
        bottom: 20px;
        right: -20px;
        animation-delay: 2s;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-8px); }
    }
    .badge-icon-box {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .badge-label {
        font-size: 11px;
        color: var(--text-muted);
        text-transform: uppercase;
        font-weight: 600;
    }
    .badge-value {
        font-size: 14px;
        font-weight: 800;
        color: var(--text-title);
    }

    /* Trust Stats Section */
    .trust-stats-section {
        background: #fff;
        border-top: 1px solid var(--border-color);
        border-bottom: 1px solid var(--border-color);
        padding: 36px 0;
    }
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 24px;
        text-align: center;
    }
    .stat-number {
        font-size: 36px;
        font-weight: 800;
        color: var(--primary);
        letter-spacing: -1px;
        line-height: 1.1;
        margin-bottom: 6px;
    }
    .stat-label {
        font-size: 14px;
        color: var(--text-muted);
        font-weight: 600;
    }

    /* Features Section */
    .features-section {
        padding: 80px 0;
    }
    .section-header-center {
        text-align: center;
        max-width: 700px;
        margin: 0 auto 56px;
    }
    .section-tag {
        font-size: 13px;
        font-weight: 700;
        color: var(--primary);
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        display: inline-block;
    }
    .section-main-title {
        font-size: 34px;
        font-weight: 800;
        color: var(--text-title);
        letter-spacing: -0.8px;
        line-height: 1.3;
        margin-bottom: 14px;
    }
    .section-subtitle {
        font-size: 16px;
        color: var(--text-muted);
        line-height: 1.6;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
    }
    .feature-card {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 32px 28px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
    }
    .feature-card:hover {
        transform: translateY(-6px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary-border);
    }
    .feature-icon-wrap {
        width: 54px;
        height: 54px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }
    .feature-title {
        font-size: 19px;
        font-weight: 800;
        color: var(--text-title);
        margin-bottom: 12px;
        letter-spacing: -0.3px;
    }
    .feature-desc {
        font-size: 14.5px;
        color: var(--text-body);
        line-height: 1.65;
        margin-bottom: 18px;
        flex-grow: 1;
    }
    .feature-points {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 13.5px;
        color: var(--text-muted);
        border-top: 1px solid var(--border-color);
        padding-top: 16px;
    }
    .feature-points li {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .feature-points li svg {
        color: var(--accent);
        flex-shrink: 0;
    }

    /* Trust & Security Highlight Section */
    .trust-section {
        background: #F0FDF4;
        border: 1px solid #BBF7D0;
        border-radius: 24px;
        padding: 48px;
        margin: 40px 0;
        display: grid;
        grid-template-columns: 1.2fr 0.8fr;
        gap: 36px;
        align-items: center;
    }
    .trust-title {
        font-size: 26px;
        font-weight: 800;
        color: #14532D;
        margin-bottom: 14px;
    }
    .trust-desc {
        font-size: 15px;
        color: #166534;
        line-height: 1.65;
        margin-bottom: 24px;
    }

    /* Download Banner Section */
    .home-download-section {
        background: linear-gradient(135deg, #137A7F 0%, #0A4346 100%);
        border-radius: 28px;
        padding: 56px;
        color: #fff;
        margin: 60px 0;
        box-shadow: 0 25px 50px -12px rgba(19, 122, 127, 0.35);
        display: grid;
        grid-template-columns: 1.3fr 0.7fr;
        gap: 40px;
        align-items: center;
    }

    @media (max-width: 960px) {
        .hero-grid {
            grid-template-columns: 1fr;
            text-align: center;
            gap: 40px;
        }
        .hero-heading {
            font-size: 34px;
        }
        .hero-lead {
            margin-left: auto;
            margin-right: auto;
        }
        .hero-cta-group {
            justify-content: center;
        }
        .floating-badge {
            display: none;
        }
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        .features-grid {
            grid-template-columns: 1fr;
        }
        .trust-section {
            grid-template-columns: 1fr;
            padding: 30px;
            text-align: center;
        }
        .home-download-section {
            grid-template-columns: 1fr;
            padding: 36px 24px;
            text-align: center;
        }
        .download-buttons {
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<!-- Hero Section -->
<section class="home-hero">
    <div class="container hero-grid">
        <div>
            <div class="hero-chip">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                <span>Ứng Dụng Hàng Đầu Cho Cha Mẹ Việt</span>
            </div>

            <h1 class="hero-heading">
                Nuôi Dạy Con Khoa Học,<br>
                <span class="gradient-text">Thấu Hiểu Từng Bước Con Lớn Khôn</span>
            </h1>

            <p class="hero-lead">
                <strong>{{ $settings['site_name'] ?? 'Chăm Con 360' }}</strong> là nền tảng theo dõi thể chất chuẩn WHO, đánh giá đa trí thông minh (IQ, EQ, AQ), nhắc lịch tiêm chủng tự động và đồng hành dinh dưỡng toàn diện cho trẻ từ sơ sinh đến tuổi trưởng thành.
            </p>

            <div class="hero-cta-group">
                <a href="#download-section" class="btn-hero-primary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Tải Ứng Dụng Miễn Phí</span>
                </a>

                <a href="{{ route('privacy-policy') }}" class="btn-hero-secondary">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                    <span>Chính Sách Bảo Mật</span>
                </a>
            </div>

            <div style="display: flex; align-items: center; gap: 14px; color: var(--text-muted); font-size: 13.5px;">
                <div style="display: flex; color: #F59E0B;">
                    ★★★★★
                </div>
                <span>Được hơn <strong>50.000+ phụ huynh</strong> tin cậy sử dụng mỗi ngày</span>
            </div>
        </div>

        <!-- Hero Visual Showcase -->
        <div class="hero-visual-wrap">
            <div class="hero-main-img-card">
                <img src="{{ asset('public/assets/images/get_started_img.png') }}" 
                     alt="Ứng dụng Chăm Con 360" 
                     class="hero-main-img"
                     onerror="this.src='{{ asset('public/assets/images/logo.png') }}'">
                <div style="margin-top: 14px;">
                    <span style="font-weight: 800; font-size: 17px; color: var(--text-title);">Kids360 Growth & Development</span>
                    <p style="font-size: 13px; color: var(--text-muted); margin-top: 2px;">Trợ lý sức khỏe & trí tuệ thế hệ mới</p>
                </div>
            </div>

            <!-- Floating Badge 1 -->
            <div class="floating-badge floating-badge-1">
                <div class="badge-icon-box" style="background: #E8F8F3; color: #21A179;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                </div>
                <div>
                    <div class="badge-label">Chiều Cao Chuẩn WHO</div>
                    <div class="badge-value">Tăng trưởng tối ưu</div>
                </div>
            </div>

            <!-- Floating Badge 2 -->
            <div class="floating-badge floating-badge-2">
                <div class="badge-icon-box" style="background: #FEF3C7; color: #D97706;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><path d="M12 6v6l4 2"></path></svg>
                </div>
                <div>
                    <div class="badge-label">Nhắc Tiêm Chủng</div>
                    <div class="badge-value">Đúng lịch 100%</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Counter -->
<section class="trust-stats-section">
    <div class="container stats-grid">
        <div>
            <div class="stat-number">50K+</div>
            <div class="stat-label">Hồ Sơ Bé Được Chăm Sóc</div>
        </div>
        <div>
            <div class="stat-number">100%</div>
            <div class="stat-label">Tiêu Chuẩn WHO & Y Tế</div>
        </div>
        <div>
            <div class="stat-number">4.9 ★</div>
            <div class="stat-label">Đánh Giá Từ Phụ Huynh</div>
        </div>
        <div>
            <div class="stat-number">0đ</div>
            <div class="stat-label">Miễn Phí Cài Đặt Trọn Đời</div>
        </div>
    </div>
</section>

<!-- Core Features Section -->
<section class="features-section" id="features">
    <div class="container">
        <div class="section-header-center">
            <span class="section-tag">Tính Năng Cốt Lõi</span>
            <h2 class="section-main-title">Giải Pháp Đồng Hành Toàn Diện Cho Bé Yêu</h2>
            <p class="section-subtitle">
                Tích hợp đầy đủ các công cụ khoa học giúp cha mẹ không còn bỡ ngỡ, tự tin nuôi con khỏe mạnh, thông minh và hạnh phúc.
            </p>
        </div>

        <div class="features-grid">
            <!-- Feature 1 -->
            <div class="feature-card">
                <div class="feature-icon-wrap" style="background: #EBF8F8; color: #137A7F;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 20V10"></path><path d="M12 20V4"></path><path d="M6 20v-6"></path></svg>
                </div>
                <h3 class="feature-title">Theo Dõi Thể Chất & Chiều Cao</h3>
                <p class="feature-desc">
                    Đối chiếu chỉ số chiều cao, cân nặng của con theo chuẩn WHO, dự báo chiều cao khi trưởng thành và cảnh báo sớm nguy cơ thừa cân/suy dinh dưỡng.
                </p>
                <ul class="feature-points">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Biểu đồ tăng trưởng trực quan</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Tính tiềm năng di truyền bố mẹ</li>
                </ul>
            </div>

            <!-- Feature 2 -->
            <div class="feature-card">
                <div class="feature-icon-wrap" style="background: #FEF3C7; color: #D97706;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                </div>
                <h3 class="feature-title">Đánh Giá Đa Trí Thông Minh</h3>
                <p class="feature-desc">
                    Hệ thống trắc nghiệm khoa học đánh giá chỉ số trí tuệ (IQ), cảm xúc (EQ), vượt khó (AQ), và kỹ năng xã hội (SQ) giúp cha mẹ thấu hiểu tâm lý trẻ.
                </p>
                <ul class="feature-points">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Phân tích thế mạnh nổi trội</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Lời khuyên định hướng giáo dục</li>
                </ul>
            </div>

            <!-- Feature 3 -->
            <div class="feature-card">
                <div class="feature-icon-wrap" style="background: #E8F8F3; color: #21A179;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                </div>
                <h3 class="feature-title">Sổ Tiêm Chủng Điện Tử</h3>
                <p class="feature-desc">
                    Tự động thiết lập lộ trình tiêm phòng chuẩn Bộ Y Tế, nhắc lịch thông minh trước ngày tiêm và lưu trữ lịch sử phản ứng sau tiêm an toàn.
                </p>
                <ul class="feature-points">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Nhắc lịch tiêm qua thông báo đẩy</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Lưu đơn thuốc và triệu chứng</li>
                </ul>
            </div>

            <!-- Feature 4 -->
            <div class="feature-card">
                <div class="feature-icon-wrap" style="background: #FEE2E2; color: #DC2626;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                </div>
                <h3 class="feature-title">Nhật Ký Thai Kỳ & Sơ Sinh</h3>
                <p class="feature-desc">
                    Theo dõi thai kỳ từng tuần tuổi, đếm ngày dự sinh, hướng dẫn chăm sóc mẹ bầu và lưu lại những mốc phát triển đầu đời thiêng liêng của con.
                </p>
                <ul class="feature-points">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Cẩm nang mẹ bầu theo tuần</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Khoảnh khắc đáng nhớ của bé</li>
                </ul>
            </div>

            <!-- Feature 5 -->
            <div class="feature-card">
                <div class="feature-icon-wrap" style="background: #EDE9FE; color: #7C3AED;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>
                </div>
                <h3 class="feature-title">Cẩm Nang Dinh Dưỡng Khoa Học</h3>
                <p class="feature-desc">
                    Hàng trăm công thức ăn dặm, thực đơn dinh dưỡng theo độ tuổi được biên soạn bởi các chuyên gia dinh dưỡng và bác sĩ nhi khoa uy tín.
                </p>
                <ul class="feature-points">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Thực đơn ăn dặm đa dạng</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Kiến thức nuôi con chuẩn y khoa</li>
                </ul>
            </div>

            <!-- Feature 6 -->
            <div class="feature-card">
                <div class="feature-icon-wrap" style="background: #E0F2FE; color: #0284C7;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                </div>
                <h3 class="feature-title">Tra Cứu Phòng Khám & Tiện Ích</h3>
                <p class="feature-desc">
                    Dễ dàng tìm kiếm phòng khám nhi, trung tâm tiêm chủng và nhà thuốc uy tín gần bạn nhất với thông tin liên hệ và chỉ đường tiện lợi.
                </p>
                <ul class="feature-points">
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Cơ sở nhi khoa uy tín</li>
                    <li><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg> Đánh giá chất lượng dịch vụ</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Trust & Privacy Banner -->
<section class="container">
    <div class="trust-section">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 700; color: #16A34A; text-transform: uppercase; margin-bottom: 10px;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                <span>Bảo Mật Quyền Riêng Tư 100%</span>
            </div>
            <h3 class="trust-title">An Toàn Cho Con Là Ưu Tiên Số 1</h3>
            <p class="trust-desc">
                Chúng tôi hiểu dữ liệu sức khỏe và thông tin con cái là vô giá. Chăm Con 360 cam kết tuân thủ chính sách bảo vệ gia đình của Google Play, tiêu chuẩn COPPA và Luật Trẻ em Việt Nam. Tuyệt đối không thương mại hóa dữ liệu của bé.
            </p>
            <a href="{{ route('privacy-policy') }}" class="btn btn-primary" style="background:#15803D;">
                <span>Xem chi tiết Chính sách bảo mật</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
            </a>
        </div>

        <div style="display: flex; flex-direction: column; gap: 14px;">
            <div style="background:#fff; border-radius: 14px; padding: 16px; display: flex; align-items: center; gap: 14px; border: 1px solid #BBF7D0;">
                <div style="color: #16A34A; font-size: 20px;">🛡️</div>
                <div style="font-size: 14px; font-weight: 700; color: #14532D;">Mã hóa đa tầng SSL/TLS 256-bit an toàn</div>
            </div>
            <div style="background:#fff; border-radius: 14px; padding: 16px; display: flex; align-items: center; gap: 14px; border: 1px solid #BBF7D0;">
                <div style="color: #16A34A; font-size: 20px;">👨‍👩‍👧</div>
                <div style="font-size: 14px; font-weight: 700; color: #14532D;">Cha mẹ toàn quyền trích xuất hoặc xóa dữ liệu</div>
            </div>
            <div style="background:#fff; border-radius: 14px; padding: 16px; display: flex; align-items: center; gap: 14px; border: 1px solid #BBF7D0;">
                <div style="color: #16A34A; font-size: 20px;">🚫</div>
                <div style="font-size: 14px; font-weight: 700; color: #14532D;">Không theo dõi hành vi hoặc quảng cáo trẻ em</div>
            </div>
        </div>
    </div>
</section>

<!-- App Download CTA Banner -->
<section id="download-section" class="container">
    <div class="home-download-section">
        <div>
            <div style="display: inline-flex; align-items: center; gap: 8px; background: rgba(255, 255, 255, 0.2); padding: 4px 14px; border-radius: var(--radius-full); font-size: 12px; font-weight: 700; margin-bottom: 14px;">
                <span>📱 Trải Nghiệm Hoàn Toàn Miễn Phí</span>
            </div>
            <h2 style="font-size: 32px; font-weight: 800; line-height: 1.3; margin-bottom: 14px; letter-spacing: -0.5px;">
                Tải Ứng Dụng {{ $settings['site_name'] ?? 'Chăm Con 360' }} Ngay
            </h2>
            <p style="font-size: 15.5px; color: #E2E8F0; margin-bottom: 28px; line-height: 1.6;">
                Bắt đầu hành trình chăm sóc và phát triển toàn diện cho con cùng chuyên gia ngay hôm nay. Hỗ trợ đầy đủ thiết bị Android và iOS.
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
            <div style="width: 150px; height: 150px; background: #fff; border-radius: 16px; padding: 12px; margin-bottom: 12px; display: flex; align-items: center; justify-content: center; box-shadow: var(--shadow-md);">
                <img src="{{ asset($settings['site_logo'] ?? 'public/assets/images/logo.png') }}" 
                     alt="{{ $settings['site_name'] ?? 'Chăm Con 360' }}" 
                     style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </div>
            <span style="font-size: 13.5px; font-weight: 700;">Quét mã tải ứng dụng</span>
            <span style="font-size: 12px; color: #CBD5E1; margin-top: 2px;">Kids360 Ecosystem</span>
        </div>
    </div>
</section>
@endsection
