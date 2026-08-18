<style>
    /* ============================================
    Desktop - Style mặc định
    ============================================ */
    .button-container {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .action-button {
        flex: 1;
        min-width: 120px;
        padding: 10px 15px;
        border-radius: 6px;
        text-align: center;
        font-weight: 600;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    /* NÚT LƯU */
    .save-button {
        background: linear-gradient(135deg, #86efac 0%, #4ade80 100%) !important;
        color: #fff !important;
        border: 1px solid #6ee7b7 !important;
        box-shadow: 0 2px 8px rgba(74, 222, 128, 0.25) !important;
    }

    .save-button:hover {
        background: linear-gradient(135deg, #4ade80 0%, #22c55e 100%) !important;
        color: #065f46 !important;
        box-shadow: 0 4px 12px rgba(74, 222, 128, 0.35) !important;
        transform: translateY(-1px);
    }

    /* NÚT QUAY LẠI */
    .back-button {
        background: linear-gradient(135deg, #fde68a 0%, #fbbf24 100%);
        color: #fff !important;
        border: 1px solid #fcd34d;
        box-shadow: 0 2px 8px rgba(251, 191, 36, 0.25);
        text-decoration: none;
    }

    .back-button:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: #78350f;
        box-shadow: 0 4px 12px rgba(251, 191, 36, 0.35);
        transform: translateY(-1px);
    }

    /* Style cho các button khác (btn-success, btn-primary, etc) */
    .btn-success {
        background: linear-gradient(135deg, #93c5fd 0%, #60a5fa 100%);
        color: #1e3a8a;
        border: 1px solid #93c5fd;
        box-shadow: 0 2px 8px rgba(96, 165, 250, 0.25);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(96, 165, 250, 0.35);
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #c4b5fd 0%, #a78bfa 100%);
        color: #4c1d95;
        border: 1px solid #c4b5fd;
        box-shadow: 0 2px 8px rgba(167, 139, 250, 0.25);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #a78bfa 0%, #8b5cf6 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(167, 139, 250, 0.35);
        transform: translateY(-1px);
    }

    /* ============================================
       Floating Bottom Action Bar (Cố định góc dưới bên phải)
       ============================================ */
    .floating-bottom-actions {
        position: fixed !important;
        bottom: 20px !important;
        right: 85px !important;
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(12px) !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 50px !important;
        padding: 8px 16px 8px 22px !important;
        box-shadow: 0 10px 35px -5px rgba(15, 23, 42, 0.18), 0 4px 12px rgba(37, 99, 235, 0.1) !important;
        z-index: 1040 !important;
        display: flex !important;
        align-items: center !important;
        gap: 16px !important;
    }

    .btn-save-settings {
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%) !important;
        color: #ffffff !important;
        border: none !important;
        border-radius: 50px !important;
        font-weight: 600 !important;
        font-size: 0.88rem !important;
        padding: 9px 20px !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3) !important;
        white-space: nowrap !important;
    }

    .btn-save-settings:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4) !important;
        transform: translateY(-1px);
    }

    .btn-save-exit-settings {
        background: #ffffff !important;
        color: #334155 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 50px !important;
        font-weight: 500 !important;
        font-size: 0.88rem !important;
        padding: 8.5px 18px !important;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04) !important;
        white-space: nowrap !important;
        text-decoration: none !important;
    }

    .btn-save-exit-settings:hover {
        background: #f8fafc !important;
        color: #0f172a !important;
        border-color: #94a3b8 !important;
    }

    /* ============================================
       MOBILE - Enhanced Bottom Sheet
       ============================================ */
    @media (max-width: 768px) {
        .floating-bottom-actions {
            position: fixed !important;
            bottom: 12px !important;
            left: 50% !important;
            right: auto !important;
            transform: translateX(-50%) !important;
            width: min(calc(100vw - 20px), 480px) !important;
            padding: 6px 10px !important;
            border-radius: 30px !important;
            justify-content: center !important;
            gap: 6px !important;
            box-sizing: border-box !important;
            z-index: 99999 !important;
        }

        .floating-bottom-actions > .d-flex {
            width: 100% !important;
            justify-content: space-between !important;
            gap: 6px !important;
        }

        .floating-bottom-actions .btn-save-settings,
        .floating-bottom-actions .btn-save-exit-settings,
        .floating-bottom-actions .btn {
            flex: 1 1 0px !important;
            min-width: 0 !important;
            padding: 7.5px 8px !important;
            font-size: 0.75rem !important;
            text-align: center !important;
            justify-content: center !important;
            gap: 4px !important;
            border-radius: 30px !important;
        }

        .floating-bottom-actions .btn-save-settings i,
        .floating-bottom-actions .btn-save-exit-settings i,
        .floating-bottom-actions .btn i {
            font-size: 0.95rem !important;
        }

        .floating-bottom-actions .btn-save-settings span,
        .floating-bottom-actions .btn-save-exit-settings span,
        .floating-bottom-actions .btn span {
            white-space: nowrap !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
        }

        /* Padding bottom cho body để nội dung không bị che */
        body {
            padding-bottom: 90px;
        }
    }

    @media (max-width: 480px) {
        .floating-bottom-actions {
            width: calc(100vw - 16px) !important;
            bottom: 8px !important;
            padding: 5px 8px !important;
        }
    }

    /* iPhone safe area */
    @supports (padding: max(0px)) {
        @media (max-width: 768px) {
            .floating-bottom-actions {
                bottom: max(12px, env(safe-area-inset-bottom)) !important;
            }
        }
    }
</style>
