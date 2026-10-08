<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="robots" content="noindex, nofollow">
    <meta name="theme-color" content="#137A7F">
    <title>Lời mời tham gia Chăm Con 360</title>
    <meta name="description" content="Mở ứng dụng Chăm Con 360 để đăng ký với mã giới thiệu và nhận ưu đãi.">
    <link rel="icon" href="{{ asset('public/assets/images/logo.png') }}">
    <style>
        :root {
            --primary: #137A7F;
            --primary-dark: #0D5A5E;
            --accent: #21A179;
            --text: #0F172A;
            --muted: #64748B;
            --border: #E2E8F0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, "SF Pro Text", "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: var(--text);
            background: radial-gradient(120% 80% at 50% -10%, #DDF4F2 0%, #F4FBFA 45%, #FFFFFF 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: calc(24px + env(safe-area-inset-top)) 20px calc(24px + env(safe-area-inset-bottom));
        }
        main {
            width: 100%;
            max-width: 420px;
            text-align: center;
            animation: rise .45s ease-out both;
        }
        @keyframes rise { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: none; } }
        .logo {
            width: 84px; height: 84px; border-radius: 22px;
            box-shadow: 0 12px 28px rgba(19, 122, 127, .22);
            background: #fff; object-fit: contain;
        }
        h1 { font-size: 22px; font-weight: 800; letter-spacing: -.3px; margin: 18px 0 8px; line-height: 1.3; }
        .lead { font-size: 15px; color: var(--muted); line-height: 1.55; margin-bottom: 22px; }
        .code-card {
            background: #fff;
            border: 1px dashed rgba(19, 122, 127, .45);
            border-radius: 18px;
            padding: 16px 18px;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
            margin-bottom: 22px;
            box-shadow: 0 6px 18px rgba(15, 23, 42, .05);
        }
        .code-label { font-size: 12px; color: var(--muted); text-align: left; }
        .code-card > div:first-child { min-width: 0; }
        .code-value { font-size: 24px; font-weight: 800; letter-spacing: 2px; color: var(--primary); text-align: left; overflow-wrap: anywhere; }
        html, body { overflow-x: hidden; }
        h1, .lead, .btn { overflow-wrap: anywhere; }
        @media (max-width: 360px) { h1 { font-size: 20px; } .code-value { font-size: 20px; } }
        .btn {
            display: flex; align-items: center; justify-content: center; gap: 10px;
            width: 100%; border: 0; border-radius: 999px; cursor: pointer;
            font-size: 16px; font-weight: 700; text-decoration: none;
            padding: 15px 22px; transition: transform .15s ease, box-shadow .2s ease, background .2s ease;
            -webkit-tap-highlight-color: transparent;
        }
        .btn:active { transform: scale(.98); }
        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            box-shadow: 0 12px 24px rgba(19, 122, 127, .28);
        }
        .btn-secondary { color: var(--primary); background: #fff; border: 1.5px solid rgba(19, 122, 127, .3); margin-top: 12px; }
        .btn-copy {
            width: auto; flex-shrink: 0; font-size: 14px; padding: 10px 16px;
            color: var(--primary); background: rgba(19, 122, 127, .08);
        }
        .btn-copy.copied { color: #fff; background: var(--accent); }
        .hint {
            margin-top: 22px; text-align: left;
            background: #FFF8E6; border: 1px solid #FCE3A6; border-radius: 14px;
            padding: 14px 16px; font-size: 14px; line-height: 1.55; color: #7A5A00;
        }
        .hint strong { color: #5C4300; }
        .hint.highlight { animation: pulse 1.2s ease-in-out 2; }
        @keyframes pulse { 50% { box-shadow: 0 0 0 6px rgba(252, 227, 166, .6); } }
        .steps { margin: 6px 0 0 18px; }
        .footer { margin-top: 26px; font-size: 12px; color: #94A3B8; }
    </style>
</head>
<body>
<main>
    <img class="logo" src="{{ asset('public/assets/images/logo.png') }}" alt="Chăm Con 360" width="84" height="84">

    <h1>Bạn được mời tham gia Chăm Con 360</h1>
    <p class="lead">Đồng hành chăm sóc và theo dõi phát triển toàn diện cho bé. Đăng ký bằng mã giới thiệu để nhận nhiều ưu đãi hấp dẫn.</p>

    @if($code)
        <div class="code-card">
            <div>
                <div class="code-label">Mã giới thiệu</div>
                <div class="code-value" id="referral-code">{{ $code }}</div>
            </div>
            <button type="button" class="btn btn-copy" id="copy-code-btn" data-code="{{ $code }}">Sao chép</button>
        </div>
    @endif

    <a class="btn btn-primary" id="open-app-btn" href="{{ $isInApp ? $appSchemeUrl : $oneLinkUrl }}">
        Mở ứng dụng Chăm Con 360
    </a>

    <a class="btn btn-secondary" id="store-btn" href="{{ $storeUrl }}">
        Chưa có ứng dụng? Tải trên App Store
    </a>

    @unless($isIos)
        <a class="btn btn-secondary" id="play-store-btn" href="{{ $playStoreUrl }}">Tải trên Google Play</a>
    @endunless

    @if($isInApp)
        <div class="hint" id="in-app-hint">
            <strong>Không mở được ứng dụng?</strong>
            <ol class="steps">
                <li>Bấm <strong>⋯</strong> ở góc trên bên phải</li>
                <li>Chọn <strong>Mở bằng trình duyệt</strong> (Safari)</li>
                <li>Bấm lại <strong>Mở ứng dụng Chăm Con 360</strong></li>
            </ol>
            @if($code)
                <div style="margin-top:8px">Hoặc sao chép mã ở trên và dán vào ô <strong>Mã giới thiệu</strong> khi đăng ký.</div>
            @endif
        </div>
    @endif

    <p class="footer">© {{ date('Y') }} Chăm Con 360</p>
</main>

<script>
    (function () {
        var isInApp = @json($isInApp);

        // Khi người dùng chọn "Mở bằng trình duyệt" trong Zalo/Facebook, trình duyệt
        // ngoài mở lại URL hiện tại. Đánh dấu via=inapp để server hiển thị trang này
        // (thay vì chuyển thẳng App Store) và nút mở app sẽ dùng Universal Link.
        if (isInApp && window.history && history.replaceState) {
            try {
                var url = new URL(window.location.href);
                if (url.searchParams.get('via') !== 'inapp') {
                    url.searchParams.set('via', 'inapp');
                    history.replaceState(null, '', url.toString());
                }
            } catch (e) { /* URL API không khả dụng: bỏ qua */ }
        }

        var copyBtn = document.getElementById('copy-code-btn');
        if (copyBtn) {
            copyBtn.addEventListener('click', function () {
                var code = copyBtn.getAttribute('data-code');
                var done = function () {
                    copyBtn.textContent = 'Đã sao chép';
                    copyBtn.classList.add('copied');
                    setTimeout(function () {
                        copyBtn.textContent = 'Sao chép';
                        copyBtn.classList.remove('copied');
                    }, 2000);
                };
                var fallback = function () {
                    var input = document.createElement('textarea');
                    input.value = code;
                    input.setAttribute('readonly', '');
                    input.style.position = 'fixed';
                    input.style.opacity = '0';
                    document.body.appendChild(input);
                    input.select();
                    input.setSelectionRange(0, code.length);
                    try { document.execCommand('copy'); done(); } catch (e) { /* ignore */ }
                    document.body.removeChild(input);
                };
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(code).then(done, fallback);
                } else {
                    fallback();
                }
            });
        }

        // Nếu bấm "Mở ứng dụng" mà trang vẫn hiển thị sau 2,5 giây (app không mở
        // được trong trình duyệt nhúng), làm nổi bật hướng dẫn.
        var openBtn = document.getElementById('open-app-btn');
        var hint = document.getElementById('in-app-hint');
        if (openBtn && hint) {
            openBtn.addEventListener('click', function () {
                setTimeout(function () {
                    if (!document.hidden) {
                        hint.classList.remove('highlight');
                        void hint.offsetWidth;
                        hint.classList.add('highlight');
                        hint.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 2500);
            });
        }
    })();
</script>
</body>
</html>
