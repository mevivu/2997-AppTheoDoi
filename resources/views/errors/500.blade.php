<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi máy chủ nội bộ | {{ config('app.name', 'Kids360') }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: radial-gradient(circle at 15% 20%, #450a0a 0%, #0f172a 45%, #020617 100%);
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            overflow-x: hidden;
            position: relative;
        }

        /* Ambient Crimson Glow */
        .ambient-glow {
            position: absolute;
            width: 480px;
            height: 480px;
            background: radial-gradient(circle, rgba(225, 29, 72, 0.22) 0%, rgba(159, 18, 57, 0.06) 50%, transparent 70%);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            filter: blur(55px);
            pointer-events: none;
            animation: pulseGlow 4s ease-in-out infinite alternate;
        }

        @keyframes pulseGlow {
            0% { transform: translate(-50%, -50%) scale(0.9); opacity: 0.75; }
            100% { transform: translate(-50%, -50%) scale(1.18); opacity: 1; }
        }

        .error-card {
            background: rgba(15, 23, 42, 0.78);
            backdrop-filter: blur(22px);
            -webkit-backdrop-filter: blur(22px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.55), 0 0 35px rgba(225, 29, 72, 0.12);
            border-radius: 28px;
            padding: 48px 36px;
            max-width: 540px;
            width: 100%;
            text-align: center;
            position: relative;
            z-index: 10;
        }

        .badge-error {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 18px;
            background: rgba(225, 29, 72, 0.14);
            border: 1px solid rgba(225, 29, 72, 0.32);
            border-radius: 999px;
            color: #fda4af;
            font-size: 0.85rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            margin-bottom: 24px;
        }

        .icon-box {
            width: 96px;
            height: 96px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, rgba(225, 29, 72, 0.22), rgba(159, 18, 57, 0.35));
            border: 2px solid rgba(244, 63, 94, 0.35);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: #fb7185;
            box-shadow: 0 12px 24px rgba(225, 29, 72, 0.25);
            animation: floatIcon 3s ease-in-out infinite;
        }

        @keyframes floatIcon {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-8px) rotate(-3deg); }
        }

        .error-code {
            font-size: 4.2rem;
            font-weight: 800;
            line-height: 1;
            background: linear-gradient(135deg, #ffffff 30%, #fb7185 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 12px;
            letter-spacing: -0.03em;
        }

        .error-title {
            font-size: 1.55rem;
            font-weight: 700;
            color: #f8fafc;
            margin-bottom: 12px;
        }

        .error-message {
            font-size: 0.95rem;
            color: #94a3b8;
            line-height: 1.65;
            margin-bottom: 36px;
        }

        /* Action Buttons */
        .actions-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 20px;
            border-radius: 14px;
            font-size: 0.9rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #e11d48 0%, #be123c 100%);
            color: #ffffff;
            box-shadow: 0 8px 20px rgba(225, 29, 72, 0.35);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #be123c 0%, #9f1239 100%);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(225, 29, 72, 0.45);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.08);
            color: #e2e8f0;
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
            transform: translateY(-2px);
        }

        @media (max-width: 480px) {
            .error-card {
                padding: 32px 20px;
            }
            .actions-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <div class="ambient-glow"></div>

    <div class="error-card">
        <div class="badge-error">
            <i class="ti ti-server-off"></i> Sự cố máy chủ
        </div>

        <div class="icon-box">
            <i class="ti ti-cpu"></i>
        </div>

        <div class="error-code">500</div>
        <h1 class="error-title">Lỗi Máy Chủ Nội Bộ</h1>
        <p class="error-message">
            Hệ thống đang gặp sự cố kỹ thuật gián đoạn tạm thời. Đội ngũ kỹ thuật đã ghi nhận thông tin sự cố và đang tiến hành xử lý khắc phục.
        </p>

        <div class="actions-grid">
            <button type="button" class="btn-action btn-primary" onclick="location.reload()">
                <i class="ti ti-refresh"></i> Tải lại trang
            </button>
            <a href="{{ request()->is('admin*') ? (Route::has('admin.dashboard') ? route('admin.dashboard') : url('/admin')) : (Route::has('user.index') ? route('user.index') : url('/')) }}" class="btn-action btn-secondary">
                <i class="ti ti-home"></i> Trang chủ
            </a>
        </div>
    </div>
</body>

</html>
