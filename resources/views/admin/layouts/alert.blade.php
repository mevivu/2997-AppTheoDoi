<script>
    $(document).ready(function () {
        // Toast cho các message thông thường
        @foreach($type as $value)
        @if($message = Session::get($value))
        $.toast({
            heading: '{{ $title }}',
            text: '{{ $message }}',
            position: '{{ $position }}',
            icon: '{{ $value }}'
        });
        @endif
        @endforeach

        // Toast cho validation errors thông thường
        @if (isset($errors) && $errors->any())
        @foreach($errors->all() as $val)
        $.toast({
            heading: '{{ $title }}',
            text: '{{ $val }}',
            position: '{{ $position }}',
            icon: 'warning',
            hideAfter: 10000
        });
        @endforeach
        @endif

        @if(session('import_errors'))
        const importErrors = @json(session('import_errors'));
        const errorCount = {{ session('import_error_count') }};

        // Toast tổng quan về số lỗi
        $.toast({
            heading: 'Import Error',
            text: `Có ${errorCount} lỗi trong quá trình import. Vui lòng kiểm tra chi tiết bên dưới.`,
            position: '{{ $position }}',
            icon: 'error',
            hideAfter: 8000
        });

        importErrors.forEach((error, index) => {
            setTimeout(() => {
                $.toast({
                    heading: 'Lỗi Import dữ liệu Exel',
                    text: error,
                    position: '{{ $position }}',
                    icon: 'warning',
                    hideAfter: 12000
                });
            }, (index + 1) * 1500);
        });
        @endif
    });
</script>

<style>
    /* ===== JQUERY TOAST PLUGIN MODERN STYLES ===== */

    .jq-toast-wrap {
        position: fixed !important;
        top: 24px !important;
        right: 24px !important;
        left: auto !important;
        bottom: auto !important;
        width: auto !important;
        max-width: calc(100vw - 48px) !important;
        z-index: 99999 !important;
        pointer-events: none;
    }

    .jq-toast-wrap * {
        pointer-events: auto;
    }

    /* Base Toast Styles */
    .jq-toast-single {
        position: relative !important;
        left: auto !important;
        right: 0 !important;
        margin-left: auto !important;
        margin-right: 0 !important;
        box-sizing: border-box !important;
        background: #ffffff !important;
        backdrop-filter: blur(16px) !important;
        -webkit-backdrop-filter: blur(16px) !important;
        border-radius: 14px !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.16), 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        padding: 14px 44px 14px 50px !important;
        margin-bottom: 12px !important;
        width: 350px !important;
        max-width: calc(100vw - 48px) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        overflow: hidden !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        animation: toastSlideInRight 0.4s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        color: #1e293b !important;
    }

    .jq-toast-single:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 20px 40px -4px rgba(15, 23, 42, 0.2), 0 6px 16px rgba(0, 0, 0, 0.1) !important;
    }

    /* Toast Entrance Animation */
    @keyframes toastSlideInRight {
        0% {
            transform: translateX(120%) scale(0.9);
            opacity: 0;
        }
        100% {
            transform: translateX(0) scale(1);
            opacity: 1;
        }
    }

    /* Toast Heading */
    .jq-toast-single h2,
    .jq-toast-single .jq-toast-heading {
        font-size: 0.95rem !important;
        font-weight: 700 !important;
        margin: 0 0 4px 0 !important;
        line-height: 1.3 !important;
        color: #0f172a !important;
        letter-spacing: -0.01em !important;
        padding: 0 !important;
    }

    /* Toast Message Text - Enforce High Contrast Dark Text */
    .jq-toast-single .jq-toast-message,
    .jq-toast-single .jq-toast-message *,
    .jq-toast-single p,
    .jq-toast-single span,
    .jq-toast-single div {
        font-size: 0.88rem !important;
        line-height: 1.45 !important;
        margin: 0 !important;
        color: #334155 !important;
        font-weight: 600 !important;
        opacity: 1 !important;
        padding: 0 !important;
    }

    /* Close Button */
    .jq-toast-single .close-jq-toast-single {
        position: absolute !important;
        top: 14px !important;
        right: 14px !important;
        width: 24px !important;
        height: 24px !important;
        background: #f1f5f9 !important;
        border-radius: 50% !important;
        border: 1px solid #e2e8f0 !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        font-size: 13px !important;
        color: #64748b !important;
        text-decoration: none !important;
        line-height: 1 !important;
    }

    .jq-toast-single .close-jq-toast-single:hover {
        background: #fee2e2 !important;
        border-color: #fca5a5 !important;
        color: #ef4444 !important;
        transform: rotate(90deg) !important;
    }

    /* Status Accent Border */
    .jq-toast-single.jq-has-icon {
        border-left-width: 5px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-success {
        border-left-color: #10b981 !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-success h2 {
        color: #047857 !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-success::before {
        content: '✓';
        position: absolute;
        top: 16px;
        left: 14px;
        width: 24px;
        height: 24px;
        background: #10b981;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
        box-shadow: 0 0 10px rgba(16, 185, 129, 0.4);
    }

    /* ERROR TOAST */
    .jq-toast-single.jq-has-icon.jq-icon-error {
        border-left-color: #ef4444 !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-error h2 {
        color: #b91c1c !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-error::before {
        content: '✕';
        position: absolute;
        top: 16px;
        left: 14px;
        width: 24px;
        height: 24px;
        background: #ef4444;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.4);
    }

    /* WARNING TOAST */
    .jq-toast-single.jq-has-icon.jq-icon-warning {
        border-left-color: #f59e0b !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-warning h2 {
        color: #b45309 !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-warning::before {
        content: '!';
        position: absolute;
        top: 16px;
        left: 14px;
        width: 24px;
        height: 24px;
        background: #f59e0b;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
        font-weight: 800;
        box-shadow: 0 0 10px rgba(245, 158, 11, 0.4);
    }

    /* INFO TOAST */
    .jq-toast-single.jq-has-icon.jq-icon-info {
        border-left-color: #3b82f6 !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-info h2 {
        color: #1d4ed8 !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-info::before {
        content: 'i';
        position: absolute;
        top: 16px;
        left: 14px;
        width: 24px;
        height: 24px;
        background: #3b82f6;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 700;
        font-style: italic;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.4);
    }

    /* Pulse Animation for Errors */
    @keyframes toastPulse {
        0%, 100% {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1),
            0 8px 16px rgba(0, 0, 0, 0.06),
            0 0 0 0 rgba(239, 68, 68, 0.4);
        }
        50% {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1),
            0 8px 16px rgba(0, 0, 0, 0.06),
            0 0 0 10px rgba(239, 68, 68, 0.1);
        }
    }

    /* Position-specific styles */
    .jq-toast-wrap.top-right {
        top: 20px;
        right: 20px;
    }

    .jq-toast-wrap.top-left {
        top: 20px;
        left: 20px;
    }

    .jq-toast-wrap.bottom-right {
        bottom: 20px;
        right: 20px;
    }

    .jq-toast-wrap.bottom-left {
        bottom: 20px;
        left: 20px;
    }

    /* Responsive Design */
    @media (max-width: 480px) {
        .jq-toast-single {
            min-width: 280px !important;
            max-width: calc(100vw - 40px) !important;
            margin: 0 0 16px 0 !important;
        }

        .jq-toast-wrap.top-right,
        .jq-toast-wrap.bottom-right {
            right: 10px !important;
        }

        .jq-toast-wrap.top-left,
        .jq-toast-wrap.bottom-left {
            left: 10px !important;
        }

        .jq-toast-wrap.top-right,
        .jq-toast-wrap.top-left {
            top: 10px !important;
        }

        .jq-toast-wrap.bottom-right,
        .jq-toast-wrap.bottom-left {
            bottom: 10px !important;
        }
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        .jq-toast-single {
            background: rgba(31, 41, 55, 0.95) !important;
            border: 1px solid rgba(75, 85, 99, 0.3) !important;
            color: #000000 !important;
        }

        .jq-toast-single h2 {
            color: #f9fafb !important;
        }

        .jq-toast-single .jq-toast-message {
            color: #d1d5db !important;
        }

        .jq-toast-single .close-jq-toast-single {
            background: rgba(255, 255, 255, 0.1) !important;
            color: #d1d5db !important;
        }

        .jq-toast-single .close-jq-toast-single:hover {
            background: rgba(239, 68, 68, 0.2) !important;
            color: #fca5a5 !important;
        }
    }

    /* Stack Management */
    .jq-toast-wrap .jq-toast-single:nth-child(n+4) {
        opacity: 0.8;
        transform: scale(0.95);
    }

    .jq-toast-wrap .jq-toast-single:nth-child(n+6) {
        display: none;
    }
</style>
