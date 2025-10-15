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
    /* ===== JQUERY TOAST PLUGIN CUSTOM STYLES ===== */

    /* Toast Container */
    .jq-toast-wrap {
        position: fixed;
        z-index: 9999;
        pointer-events: none;
    }

    .jq-toast-wrap * {
        pointer-events: auto;
    }

    /* Base Toast Styles */
    .jq-toast-single {
        position: relative !important;
        left: -35% !important;
        color: #333;
        font-size: 15px;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(20px) saturate(180%);
        border-radius: 16px !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1),
        0 8px 16px rgba(0, 0, 0, 0.06),
        inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
        padding: 20px 56px 20px 20px !important;
        margin-bottom: 16px !important;
        min-width: 320px !important;
        max-width: 480px !important;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        overflow: hidden !important;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif !important;
        animation: toastSlideIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
    }

    .jq-toast-single:hover {
        transform: translateY(-4px) scale(1.02) !important;
        box-shadow: 0 32px 64px rgba(0, 0, 0, 0.15),
        0 16px 32px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
    }

    /* Toast Entrance Animation */
    @keyframes toastSlideIn {
        0% {
            transform: translateX(400px) rotate(10deg);
            opacity: 0;
        }
        60% {
            transform: translateX(-20px) rotate(-2deg);
            opacity: 0.8;
        }
        100% {
            transform: translateX(0) rotate(0deg);
            opacity: 1;
        }
    }

    /* Toast Exit Animation */
    @keyframes toastSlideOut {
        0% {
            transform: translateX(0) scale(1);
            opacity: 1;
        }
        100% {
            transform: translateX(400px) scale(0.8);
            opacity: 0;
        }
    }

    /* Toast Heading */
    .jq-toast-single h2 {
        font-size: 16px !important;
        font-weight: 700 !important;
        margin: 0 0 8px 0 !important;
        line-height: 1.4 !important;
        color: #1f2937 !important;
        letter-spacing: -0.025em !important;
        padding: 0 !important;
    }

    /* Toast Message Text */
    .jq-toast-single .jq-toast-message {
        font-size: 14px !important;
        line-height: 1.5 !important;
        margin: 0 !important;
        color: #6b7280 !important;
        font-weight: 400 !important;
        padding: 0 !important;
    }

    /* Close Button */
    .jq-toast-single .close-jq-toast-single {
        position: absolute !important;
        top: 12px !important;
        right: 12px !important;
        width: 28px !important;
        height: 28px !important;
        background: rgba(0, 0, 0, 0.05) !important;
        border-radius: 50% !important;
        border: none !important;
        cursor: pointer !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        transition: all 0.2s ease !important;
        font-size: 18px !important;
        color: #9ca3af !important;
        text-decoration: none !important;
        line-height: 1 !important;
    }

    .jq-toast-single .close-jq-toast-single:hover {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
        transform: scale(1.1) !important;
    }

    /* SUCCESS TOAST */
    .jq-toast-single.jq-has-icon.jq-icon-success {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.05), rgba(5, 150, 105, 0.05)) !important;
        border-left: 4px solid #10b981 !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-success h2 {
        color: #047857 !important;
        padding-left: 40px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-success .jq-toast-message {
        padding-left: 40px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-success::before {
        content: '✓';
        position: absolute;
        top: 18px;
        left: 16px;
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        z-index: 1;
    }

    /* ERROR TOAST */
    .jq-toast-single.jq-has-icon.jq-icon-error {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.05), rgba(220, 38, 38, 0.05)) !important;
        border-left: 4px solid #ef4444 !important;
        animation: toastSlideIn 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275), toastPulse 2s infinite !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-error h2 {
        color: #dc2626 !important;
        padding-left: 40px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-error .jq-toast-message {
        padding-left: 40px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-error::before {
        content: '✕';
        position: absolute;
        top: 18px;
        left: 16px;
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
        z-index: 1;
    }

    /* WARNING TOAST */
    .jq-toast-single.jq-has-icon.jq-icon-warning {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.05), rgba(217, 119, 6, 0.05)) !important;
        border-left: 4px solid #f59e0b !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-warning h2 {
        color: #d97706 !important;
        padding-left: 40px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-warning .jq-toast-message {
        padding-left: 40px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-warning::before {
        content: '!';
        position: absolute;
        top: 18px;
        left: 16px;
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: bold;
        z-index: 1;
    }

    /* INFO TOAST */
    .jq-toast-single.jq-has-icon.jq-icon-info {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05), rgba(37, 99, 235, 0.05)) !important;
        border-left: 4px solid #3b82f6 !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-info h2 {
        color: #2563eb !important;
        padding-left: 40px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-info .jq-toast-message {
        padding-left: 40px !important;
    }

    .jq-toast-single.jq-has-icon.jq-icon-info::before {
        content: 'i';
        position: absolute;
        top: 18px;
        left: 16px;
        width: 24px;
        height: 24px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: bold;
        font-style: italic;
        z-index: 1;
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

    /* Progress Bar for Long Toasts */
    .jq-toast-single::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(0, 0, 0, 0.1));
        border-radius: 0 0 16px 16px;
        animation: toastProgress linear;
    }

    .jq-toast-single.jq-has-icon.jq-icon-success::after {
        background: linear-gradient(90deg, #10b981, #059669);
    }

    .jq-toast-single.jq-has-icon.jq-icon-error::after {
        background: linear-gradient(90deg, #ef4444, #dc2626);
    }

    .jq-toast-single.jq-has-icon.jq-icon-warning::after {
        background: linear-gradient(90deg, #f59e0b, #d97706);
    }

    .jq-toast-single.jq-has-icon.jq-icon-info::after {
        background: linear-gradient(90deg, #3b82f6, #2563eb);
    }

    @keyframes toastProgress {
        0% {
            width: 0%;
        }
        100% {
            width: 100%;
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
            color: #f9fafb !important;
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
