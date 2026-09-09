<!DOCTYPE html>
<html lang="vi" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    
    <title>@yield('title', ($settings['site_name'] ?? 'Chăm Con 360') . ' - Chính Sách Bảo Mật & Quyền Riêng Tư')</title>
    <meta name="description" content="@yield('meta_description', 'Chính sách bảo mật thông tin và quyền riêng tư của ứng dụng Chăm Con 360 (Kids360). Cam kết bảo vệ dữ liệu trẻ em, mã hóa an toàn và tôn trọng quyền riêng tư của phụ huynh.')">
    <meta name="keywords" content="Chăm Con 360, Kids360, Chính sách bảo mật, Privacy Policy, Bảo vệ trẻ em, Điều khoản sử dụng">
    <meta name="author" content="{{ $settings['company_name'] ?? 'Công ty TNHH Thân Tâm Trí Việt Nam' }}">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Social Media Meta -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="@yield('title', ($settings['site_name'] ?? 'Chăm Con 360') . ' - Chính Sách Bảo Mật & Quyền Riêng Tư')">
    <meta property="og:description" content="@yield('meta_description', 'Cam kết bảo vệ dữ liệu trẻ em, mã hóa an toàn và tôn trọng quyền riêng tư của phụ huynh trên ứng dụng Chăm Con 360.')">
    <meta property="og:image" content="{{ asset($settings['site_logo'] ?? 'public/assets/images/logo.png') }}">
    <meta property="og:site_name" content="{{ $settings['site_name'] ?? 'Chăm Con 360' }}">

    <link rel="icon" type="image/x-icon" href="{{ asset('public/favicon.ico') }}">
    
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Design System CSS -->
    <style>
        :root {
            --primary: #137A7F;
            --primary-dark: #0D5A5E;
            --primary-light: #EBF8F8;
            --primary-border: #BCE3E5;
            --accent: #21A179;
            --accent-light: #E8F8F3;
            --warning: #F59E0B;
            --warning-light: #FEF3C7;
            --danger: #EF4444;
            --danger-light: #FEE2E2;
            --text-title: #0F172A;
            --text-body: #334155;
            --text-muted: #64748B;
            --bg-page: #F8FAFC;
            --bg-card: #FFFFFF;
            --border-color: #E2E8F0;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.07), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -4px rgba(0, 0, 0, 0.04);
            --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.04);
            --radius-sm: 8px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --radius-full: 9999px;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: var(--bg-page);
            color: var(--text-body);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        a {
            color: var(--primary);
            text-decoration: none;
            transition: color 0.2s ease;
        }
        a:hover {
            color: var(--primary-dark);
        }

        /* Reading Progress Bar */
        #reading-progress-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: transparent;
            z-index: 1001;
        }
        #reading-progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--primary) 0%, var(--accent) 100%);
            width: 0%;
            transition: width 0.1s ease;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        /* Header Navbar */
        .site-header {
            position: sticky;
            top: 0;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-color);
            z-index: 1000;
            transition: all 0.3s ease;
        }
        .header-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 76px;
        }
        .brand-logo-link {
            display: flex;
            align-items: center;
            gap: 14px;
            text-decoration: none;
        }
        .brand-logo-img {
            height: 48px;
            width: auto;
            object-fit: contain;
            border-radius: 10px;
            background: #fff;
        }
        .brand-name-wrap {
            display: flex;
            flex-direction: column;
        }
        .brand-name {
            font-size: 20px;
            font-weight: 800;
            color: var(--text-title);
            letter-spacing: -0.5px;
            line-height: 1.2;
        }
        .brand-tagline {
            font-size: 12px;
            color: var(--primary);
            font-weight: 600;
            letter-spacing: 0.2px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-weight: 600;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: var(--radius-full);
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            border: none;
        }
        .btn-outline {
            border: 1.5px solid var(--border-color);
            color: var(--text-body);
            background: #fff;
        }
        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: var(--primary-light);
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, #0F6367 100%);
            color: #fff !important;
            box-shadow: 0 4px 12px rgba(19, 122, 127, 0.25);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(19, 122, 127, 0.35);
        }

        /* App Store Download Badges - High Contrast & Crisp */
        .download-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
        }
        .download-store-btn {
            background: #FFFFFF !important;
            color: #0F172A !important;
            border: 2px solid #FFFFFF;
            border-radius: 14px;
            padding: 10px 20px;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            text-decoration: none !important;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
            min-width: 195px;
            box-sizing: border-box;
        }
        .download-store-btn:hover {
            background: #F8FAFC !important;
            transform: translateY(-3px);
            box-shadow: 0 16px 32px rgba(0, 0, 0, 0.28);
            border-color: #E2E8F0;
            color: #0F172A !important;
        }
        .download-store-btn .store-icon-wrap {
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .download-store-btn .store-text-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }
        .download-store-btn .store-text-small {
            color: #64748B !important;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            line-height: 1.2;
            margin-bottom: 2px;
        }
        .download-store-btn .store-text-large {
            color: #0F172A !important;
            font-size: 16px;
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.2px;
        }

        /* Footer */
        .site-footer {
            background: #0B132B;
            color: #94A3B8;
            padding: 60px 0 30px;
            margin-top: 80px;
            border-top: 1px solid #1E293B;
        }
        .footer-grid {
            display: grid;
            grid-template-columns: 1.8fr 1fr 1.2fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .footer-brand-title {
            color: #fff;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .footer-desc {
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
            color: #94A3B8;
        }
        .footer-links-title {
            color: #fff;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 16px;
        }
        .footer-nav {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .footer-nav a {
            color: #94A3B8;
            font-size: 14px;
            transition: color 0.2s;
        }
        .footer-nav a:hover {
            color: #fff;
        }
        .footer-contact-item {
            display: flex;
            gap: 12px;
            font-size: 14px;
            margin-bottom: 12px;
            align-items: flex-start;
        }
        .footer-bottom {
            padding-top: 24px;
            border-top: 1px solid #1E293B;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            flex-wrap: wrap;
            gap: 12px;
        }

        /* Back to top button */
        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 46px;
            height: 46px;
            border-radius: var(--radius-full);
            background: #fff;
            color: var(--primary);
            border: 1.5px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: var(--shadow-lg);
            cursor: pointer;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 999;
        }
        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }
        .back-to-top:hover {
            background: var(--primary);
            color: #fff;
            border-color: var(--primary);
            transform: translateY(-3px);
        }

        .header-nav-menu {
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .nav-item-link {
            font-size: 14.5px;
            font-weight: 600;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
            position: relative;
        }
        .nav-item-link:hover,
        .nav-item-link.active {
            color: var(--primary);
        }
        .nav-item-link.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--primary);
            border-radius: var(--radius-full);
        }

        @media (max-width: 900px) {
            .header-nav-menu {
                display: none;
            }
            .footer-grid {
                grid-template-columns: 1fr;
                gap: 30px;
            }
            .header-inner {
                height: 68px;
            }
            .brand-tagline {
                display: none;
            }
            .header-actions .btn-outline {
                display: none;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="reading-progress-container">
        <div id="reading-progress-bar"></div>
    </div>

    <!-- Header Navigation -->
    <header class="site-header">
        <div class="container header-inner">
            <a href="{{ route('home') }}" class="brand-logo-link">
                <img src="{{ asset($settings['site_logo'] ?? 'public/assets/images/logo.png') }}" 
                     alt="{{ $settings['site_name'] ?? 'Chăm Con 360' }}" 
                     class="brand-logo-img"
                     onerror="this.src='{{ asset('public/assets/images/logo.png') }}'">
                <div class="brand-name-wrap">
                    <span class="brand-name">{{ $settings['site_name'] ?? 'Chăm Con 360' }}</span>
                    <span class="brand-tagline">Phát triển toàn diện cho con</span>
                </div>
            </a>

            <!-- Central Nav Menu -->
            <nav class="header-nav-menu">
                <a href="{{ route('home') }}" class="nav-item-link {{ request()->routeIs('home') ? 'active' : '' }}">Trang Chủ</a>
                <a href="{{ route('home') }}#features" class="nav-item-link">Tính Năng</a>
                <a href="{{ route('privacy-policy') }}" class="nav-item-link {{ request()->routeIs('privacy-policy') ? 'active' : '' }}">Chính Sách Bảo Mật</a>
            </nav>

            <div class="header-actions">
                @if(!empty($settings['hotline']))
                    <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline']) }}" class="btn btn-outline">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                        <span>Hotline: {{ $settings['hotline'] }}</span>
                    </a>
                @endif
                <a href="{{ request()->routeIs('home') ? '#download-section' : route('home') . '#download-section' }}" class="btn btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Tải Ứng Dụng</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="footer-brand-title">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#21A179" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
                        <span>{{ $settings['site_name'] ?? 'Chăm Con 360' }}</span>
                    </div>
                    <p class="footer-desc">
                        Nền tảng công nghệ toàn diện hỗ trợ phụ huynh theo dõi thể chất (chiều cao, cân nặng), đánh giá đa trí thông minh (IQ, EQ, AQ), nhật ký thai kỳ và hành trình phát triển toàn diện của trẻ từ sơ sinh đến trưởng thành.
                    </p>
                    <p style="font-size: 13px; color: #64748B;">
                        Chủ quản: <strong>{{ $settings['company_name'] ?? 'Công ty TNHH Thân Tâm Trí Việt Nam' }}</strong>
                    </p>
                </div>

                <div>
                    <div class="footer-links-title">Chính sách & Pháp lý</div>
                    <ul class="footer-nav">
                        <li><a href="{{ route('privacy-policy') }}">Chính sách bảo mật dữ liệu</a></li>
                        <li><a href="{{ route('privacy-policy', ['tab' => 'terms']) }}">Điều khoản sử dụng dịch vụ</a></li>
                        <li><a href="{{ route('privacy-policy', ['tab' => 'delete']) }}">Yêu cầu xóa dữ liệu & Tài khoản</a></li>
                        <li><a href="{{ route('privacy-policy') }}#sec-tieu-chuan-tre-em">Chính sách an toàn cho trẻ</a></li>
                    </ul>
                </div>

                <div>
                    <div class="footer-links-title">Liên hệ hỗ trợ</div>
                    @if(!empty($settings['address']))
                        <div class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#21A179" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            <span>{{ $settings['address'] }}</span>
                        </div>
                    @endif
                    @if(!empty($settings['email']))
                        <div class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#21A179" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <a href="mailto:{{ $settings['email'] }}" style="color:#94A3B8;">{{ $settings['email'] }}</a>
                        </div>
                    @endif
                    @if(!empty($settings['hotline']))
                        <div class="footer-contact-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#21A179" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            <a href="tel:{{ preg_replace('/[^0-9]/', '', $settings['hotline']) }}" style="color:#94A3B8;">{{ $settings['hotline'] }}</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="footer-bottom">
                <div>
                    © {{ date('Y') }} <strong>{{ $settings['site_name'] ?? 'Chăm Con 360' }}</strong>. Tất cả quyền được bảo lưu.
                </div>
                <div style="display: flex; gap: 18px;">
                    <a href="{{ route('home') }}" style="color: #64748B;">Trang chủ</a>
                    <a href="{{ route('privacy-policy') }}" style="color: #64748B;">Chính sách bảo mật</a>
                    <a href="{{ route('privacy-policy', ['tab' => 'terms']) }}" style="color: #64748B;">Điều khoản</a>
                    <a href="{{ route('privacy-policy', ['tab' => 'delete']) }}" style="color: #64748B;">Xóa dữ liệu</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to top button -->
    <button class="back-to-top" id="backToTopBtn" title="Lên đầu trang" aria-label="Lên đầu trang">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"></line><polyline points="5 12 12 5 19 12"></polyline></svg>
    </button>

    <script>
        // Reading Progress Indicator
        window.addEventListener('scroll', () => {
            const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
            const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
            const scrolled = (winScroll / height) * 100;
            const progressBar = document.getElementById('reading-progress-bar');
            if (progressBar) progressBar.style.width = scrolled + '%';

            // Back to top button visibility
            const backBtn = document.getElementById('backToTopBtn');
            if (backBtn) {
                if (winScroll > 350) {
                    backBtn.classList.add('visible');
                } else {
                    backBtn.classList.remove('visible');
                }
            }
        });

        // Smooth scroll to top
        document.getElementById('backToTopBtn')?.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
    @stack('scripts')
</body>
</html>
