<style>
    /* =========================================================
       MEMO GAME CMS PLAY SCREEN - FRESH BLUE & EMERALD PALETTE (NO PURPLE)
       ========================================================= */

    :root {
        --memo-primary: #0284c7;          /* Fresh Ocean Blue */
        --memo-primary-hover: #0369a1;    /* Deep Ocean Blue */
        --memo-royal: #2563eb;            /* Royal Blue */
        --memo-emerald: #10b981;          /* Mint Emerald */
        --memo-emerald-hover: #059669;    /* Dark Emerald */
        --memo-warning: #f59e0b;
        --memo-danger: #ef4444;
        --memo-card-radius: 14px;
    }

    .memo-play-container {
        max-width: 1280px;
        margin: 0 auto;
    }

    /* Override header icon box from indigo to fresh blue */
    .page-header-custom .ph-icon-box {
        background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%) !important;
        box-shadow: 0 6px 18px rgba(2, 132, 199, 0.35) !important;
    }

    /* Control Panel Cards */
    .memo-control-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        padding: 22px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
    }

    .memo-control-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, #0284c7 0%, #10b981 100%);
    }

    .memo-section-title {
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #475569;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    /* Selection Chips */
    .memo-choice-group {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .memo-choice-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        user-select: none;
    }

    .memo-choice-btn:hover {
        background: #f0f9ff;
        border-color: #bae6fd;
        color: #0369a1;
        transform: translateY(-1px);
    }

    /* Active Theme Chip Styles (Color-coded by theme, NO PURPLE) */
    .memo-choice-btn.active {
        background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
        border-color: #0284c7;
        color: #ffffff;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.28);
        transform: translateY(-2px);
    }

    .memo-choice-btn.active.theme-vehicles {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        border-color: #0284c7;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.32);
    }

    .memo-choice-btn.active.theme-flowers {
        background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
        border-color: #f43f5e;
        box-shadow: 0 4px 14px rgba(244, 63, 94, 0.32);
    }

    .memo-choice-btn.active.theme-numbers {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
        border-color: #0d9488;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.32);
    }

    .memo-choice-btn.active.theme-flags {
        background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%);
        border-color: #ea580c;
        box-shadow: 0 4px 14px rgba(234, 88, 12, 0.32);
    }

    .memo-choice-btn.active i {
        color: #ffffff !important;
    }

    .memo-choice-badge {
        font-size: 11px;
        padding: 2px 8px;
        border-radius: 20px;
        background: rgba(0, 0, 0, 0.06);
        color: #475569;
    }

    .memo-choice-btn.active .memo-choice-badge {
        background: rgba(255, 255, 255, 0.25);
        color: #ffffff;
    }

    /* Active Age Chip Style */
    .memo-age-btn.active {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
        border-color: #059669 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.32) !important;
        transform: translateY(-2px);
    }

    /* Action Buttons (Fresh & Standout) */
    .btn-memo-start {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: none !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        padding: 9px 24px !important;
        border-radius: 12px !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        transition: all 0.2s ease !important;
        cursor: pointer;
    }

    .btn-memo-start:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.45) !important;
        transform: translateY(-1px);
    }

    /* Synchronized Header Action Buttons (Matching Pill Border-Radius 50px of 'Quay lại') */
    .btn-memo-library-header {
        background: #ffffff !important;
        color: #0284c7 !important;
        border: 1.5px solid #0284c7 !important;
        padding: 8px 18px !important;
        border-radius: 50px !important;
        font-size: 0.88rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        box-shadow: 0 2px 6px rgba(2, 132, 199, 0.12) !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }

    .btn-memo-library-header:hover {
        background: #f0f9ff !important;
        border-color: #0369a1 !important;
        color: #0369a1 !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.22) !important;
    }

    .btn-memo-bulk-header {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        color: #ffffff !important;
        border: 1.5px solid transparent !important;
        padding: 8px 18px !important;
        border-radius: 50px !important;
        font-size: 0.88rem !important;
        font-weight: 600 !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        box-shadow: 0 4px 12px rgba(2, 132, 199, 0.28) !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
    }

    .btn-memo-bulk-header:hover {
        background: linear-gradient(135deg, #0369a1 0%, #075985 100%) !important;
        color: #ffffff !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 6px 16px rgba(2, 132, 199, 0.4) !important;
    }

    /* Mode Buttons (Blue active) */
    .memo-mode-btn.active {
        background: #0284c7 !important;
        border-color: #0284c7 !important;
        color: #ffffff !important;
    }

    /* Game HUD */
    .memo-hud {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 16px;
        padding: 16px 24px;
        color: #ffffff;
        box-shadow: 0 10px 25px rgba(15, 23, 42, 0.15);
        margin-bottom: 24px;
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .memo-hud-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .memo-hud-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    .memo-hud-value {
        font-size: 24px;
        font-weight: 800;
        font-family: monospace;
        letter-spacing: 0.02em;
    }

    .memo-timer-bar-wrap {
        width: 100%;
        height: 6px;
        background: rgba(255, 255, 255, 0.12);
        border-radius: 4px;
        margin-top: 12px;
        overflow: hidden;
    }

    .memo-timer-bar {
        height: 100%;
        width: 100%;
        background: linear-gradient(90deg, #10b981 0%, #38bdf8 100%);
        border-radius: 4px;
        transition: width 1s linear, background-color 0.5s ease;
    }

    .memo-timer-bar.warning {
        background: #f59e0b !important;
    }

    .memo-timer-bar.danger {
        background: #ef4444 !important;
    }

    /* Game Board Grid Wrapper */
    .memo-board-wrapper {
        position: relative;
        background: #ffffff;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 28px 20px;
        min-height: 480px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    /* Loading State: Centered, Full Width, Clean Layout (Fixes Vỡ Giao Diện) */
    .memo-loading-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
        min-height: 280px;
        padding: 50px 20px;
    }

    .memo-spinner-ring {
        width: 52px;
        height: 52px;
        border: 4px solid #e0f2fe;
        border-top-color: #0284c7;
        border-radius: 50%;
        animation: spinRing 0.85s linear infinite;
        margin-bottom: 16px;
    }

    .memo-loading-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 6px;
        white-space: nowrap;
    }

    .memo-loading-sub {
        font-size: 13px;
        color: #64748b;
        max-width: 440px;
        line-height: 1.5;
    }

    /* Error State */
    .memo-error-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        width: 100%;
        padding: 40px 20px;
    }

    .memo-error-icon {
        font-size: 48px;
        color: #ef4444;
        margin-bottom: 12px;
    }

    .memo-error-title {
        font-size: 15px;
        font-weight: 700;
        color: #1e293b;
        max-width: 480px;
    }

    /* Game Board Grid */
    .memo-grid {
        display: grid;
        gap: 16px;
        width: 100%;
        max-width: 900px;
        margin: 0 auto;
        justify-content: center;
        perspective: 1200px;
        animation: fadeInGrid 0.4s ease;
    }

    /* Responsive Grid setups */
    .memo-grid-2x3 {
        grid-template-columns: repeat(3, minmax(110px, 180px));
    }
    .memo-grid-3x4 {
        grid-template-columns: repeat(4, minmax(90px, 150px));
    }
    .memo-grid-4x4 {
        grid-template-columns: repeat(4, minmax(85px, 140px));
    }
    .memo-grid-4x5 {
        grid-template-columns: repeat(5, minmax(75px, 125px));
    }

    /* 3D Card Architecture */
    .memo-card-wrapper {
        position: relative;
        aspect-ratio: 1 / 1.18;
        perspective: 1000px;
        cursor: pointer;
        user-select: none;
        outline: none;
    }

    .memo-card-inner {
        position: relative;
        width: 100%;
        height: 100%;
        text-align: center;
        transition: transform 0.45s cubic-bezier(0.34, 1.56, 0.64, 1);
        transform-style: preserve-3d;
        border-radius: var(--memo-card-radius);
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.07);
    }

    .memo-card-wrapper:hover:not(.flipped):not(.matched) .memo-card-inner {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 10px 22px rgba(2, 132, 199, 0.18);
    }

    .memo-card-wrapper.flipped .memo-card-inner {
        transform: rotateY(180deg);
    }

    .memo-card-wrapper.matched .memo-card-inner {
        transform: rotateY(180deg);
        box-shadow: 0 0 0 3px #10b981, 0 8px 20px rgba(16, 185, 129, 0.25);
        cursor: default;
    }

    .memo-card-wrapper.matched {
        animation: pulseMatched 0.5s ease-out;
    }

    .memo-card-wrapper.wrong {
        animation: shakeWrong 0.5s ease;
    }

    /* Card Faces */
    .memo-card-face {
        position: absolute;
        width: 100%;
        height: 100%;
        backface-visibility: hidden;
        -webkit-backface-visibility: hidden;
        border-radius: var(--memo-card-radius);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 10px;
        box-sizing: border-box;
        overflow: hidden;
    }

    /* Card Back (Facedown - Fresh Ocean Blue & Starry Pattern) */
    .memo-card-back {
        background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%);
        border: 3px solid #ffffff;
        color: #ffffff;
    }

    .memo-card-back-pattern {
        width: 100%;
        height: 100%;
        border: 2px dashed rgba(255, 255, 255, 0.45);
        border-radius: calc(var(--memo-card-radius) - 4px);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.18) 12%, transparent 12%);
        background-size: 16px 16px;
    }

    .memo-card-back-icon {
        font-size: 28px;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
        color: #ffffff;
        opacity: 0.95;
    }

    /* Card Front (Faceup) */
    .memo-card-front {
        background: #ffffff;
        transform: rotateY(180deg);
        border: 2px solid #e2e8f0;
    }

    .memo-card-front img {
        width: 82%;
        height: 72%;
        object-fit: contain;
        transition: transform 0.3s ease;
    }

    .memo-card-front-icon {
        font-size: 44px;
        margin-bottom: 4px;
        transition: transform 0.3s ease;
    }

    .memo-card-name {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        margin-top: 6px;
        text-align: center;
        line-height: 1.2;
        max-width: 95%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .memo-matched-badge {
        position: absolute;
        top: 6px;
        right: 6px;
        background: #10b981;
        color: #ffffff;
        border-radius: 50%;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.4);
    }

    /* Peek Countdown Banner - Floats at top without blocking cards view */
    .memo-peek-overlay {
        position: absolute;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 41, 59, 0.92) 100%);
        backdrop-filter: blur(10px);
        border: 2px solid rgba(251, 191, 36, 0.6);
        border-radius: 50px;
        padding: 8px 24px;
        z-index: 100;
        display: flex;
        align-items: center;
        gap: 14px;
        color: #ffffff;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25);
        pointer-events: none;
        animation: fadeIn 0.3s ease;
    }

    .memo-peek-counter {
        font-size: 28px;
        font-weight: 900;
        color: #fbbf24;
        text-shadow: 0 2px 10px rgba(251, 191, 36, 0.5);
        min-width: 32px;
        text-align: center;
        animation: pulseNumber 1s infinite;
    }

    .memo-peek-text {
        font-size: 14px;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-transform: uppercase;
        color: #f8fafc;
        display: flex;
        align-items: center;
    }

    /* Smart Fallback Notice */
    .memo-fallback-notice {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 4px solid #10b981;
        border-radius: 12px;
        padding: 12px 18px;
        font-size: 13px;
        color: #166534;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
    }

    /* Animations */
    @keyframes spinRing {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes pulseMatched {
        0% { transform: scale(1); }
        50% { transform: scale(1.08); }
        100% { transform: scale(1); }
    }

    @keyframes shakeWrong {
        0%, 100% { transform: translateX(0); }
        20%, 60% { transform: translateX(-6px); }
        40%, 80% { transform: translateX(6px); }
    }

    @keyframes pulseNumber {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.15); opacity: 0.85; }
    }

    @keyframes fadeInGrid {
        from { opacity: 0; transform: scale(0.98); }
        to { opacity: 1; transform: scale(1); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Victory Modal Enhancements */
    .memo-score-circle {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35);
    }

    .memo-score-number {
        font-size: 34px;
        font-weight: 900;
        line-height: 1;
    }

    .memo-score-max {
        font-size: 11px;
        opacity: 0.85;
    }
</style>
