<style>
    /* ==========================================================
       Memo Game Card Management - Premium UI / UX Styling
       ========================================================== */

    /* Emerald Bulk Upload Header Button */
    .btn-memo-bulk {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 9px 20px !important;
        border-radius: 50px !important;
        font-weight: 600 !important;
        font-size: 0.88rem !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.28) !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        text-decoration: none !important;
        cursor: pointer;
    }

    .btn-memo-bulk:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 6px 18px rgba(16, 185, 129, 0.42) !important;
        transform: translateY(-1px);
    }

    .btn-memo-bulk:active {
        transform: translateY(0);
    }

    /* Filter Bar Container */
    .memo-filter-bar {
        background: #ffffff;
        padding: 12px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }

    .memo-filter-label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    /* Base Memo Chip Pill */
    .memo-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1.5px solid transparent;
        white-space: nowrap;
    }

    .memo-chip-count {
        font-size: 0.72rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 20px;
        line-height: 1.2;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 20px;
        transition: background 0.2s ease, color 0.2s ease;
    }

    /* 1. Tất cả (All) */
    .memo-chip-all {
        background: #f8fafc;
        border-color: #e2e8f0;
        color: #475569;
    }
    .memo-chip-all .memo-chip-count {
        background: #e2e8f0;
        color: #475569;
    }
    .memo-chip-all:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #1e293b;
        transform: translateY(-1.5px);
    }
    .memo-chip-all.active {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.35) !important;
    }
    .memo-chip-all.active .memo-chip-count {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* 2. Xe (Vehicles - Amber/Warm) */
    .memo-chip-vehicles {
        background: #fffbeb;
        border-color: #fde68a;
        color: #b45309;
    }
    .memo-chip-vehicles .memo-chip-count {
        background: #fde68a;
        color: #92400e;
    }
    .memo-chip-vehicles:hover {
        background: #fef3c7;
        border-color: #fcd34d;
        color: #92400e;
        transform: translateY(-1.5px);
    }
    .memo-chip-vehicles.active {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.35) !important;
    }
    .memo-chip-vehicles.active .memo-chip-count {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* 3. Hoa (Flowers - Rose/Pink) */
    .memo-chip-flowers {
        background: #fff1f2;
        border-color: #fecdd3;
        color: #e11d48;
    }
    .memo-chip-flowers .memo-chip-count {
        background: #fecdd3;
        color: #be123c;
    }
    .memo-chip-flowers:hover {
        background: #ffe4e6;
        border-color: #fda4af;
        color: #be123c;
        transform: translateY(-1.5px);
    }
    .memo-chip-flowers.active {
        background: linear-gradient(135deg, #f43f5e 0%, #be123c 100%) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(244, 63, 94, 0.35) !important;
    }
    .memo-chip-flowers.active .memo-chip-count {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* 4. Số (Numbers - Indigo/Violet) */
    .memo-chip-numbers {
        background: #eef2ff;
        border-color: #c7d2fe;
        color: #4f46e5;
    }
    .memo-chip-numbers .memo-chip-count {
        background: #c7d2fe;
        color: #3730a3;
    }
    .memo-chip-numbers:hover {
        background: #e0e7ff;
        border-color: #a5b4fc;
        color: #3730a3;
        transform: translateY(-1.5px);
    }
    .memo-chip-numbers.active {
        background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(99, 102, 241, 0.35) !important;
    }
    .memo-chip-numbers.active .memo-chip-count {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* 5. Cờ (Flags - Teal/Cyan) */
    .memo-chip-flags {
        background: #f0fdfa;
        border-color: #99f6e4;
        color: #0d9488;
    }
    .memo-chip-flags .memo-chip-count {
        background: #99f6e4;
        color: #0f766e;
    }
    .memo-chip-flags:hover {
        background: #ccfbf1;
        border-color: #5eead4;
        color: #0f766e;
        transform: translateY(-1.5px);
    }
    .memo-chip-flags.active {
        background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35) !important;
    }
    .memo-chip-flags.active .memo-chip-count {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* 6. Default Fallback Theme */
    .memo-chip-default {
        background: #f0f9ff;
        border-color: #bae6fd;
        color: #0284c7;
    }
    .memo-chip-default .memo-chip-count {
        background: #bae6fd;
        color: #0369a1;
    }
    .memo-chip-default:hover {
        background: #e0f2fe;
        border-color: #7dd3fc;
        color: #0369a1;
        transform: translateY(-1.5px);
    }
    .memo-chip-default.active {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%) !important;
        border-color: transparent !important;
        color: #ffffff !important;
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35) !important;
    }
    .memo-chip-default.active .memo-chip-count {
        background: rgba(255, 255, 255, 0.25) !important;
        color: #ffffff !important;
    }

    /* ==========================================================
       DataTable Theme Badges
       ========================================================== */
    .badge-memo-theme-vehicles {
        background: #fef3c7 !important;
        color: #92400e !important;
        border: 1px solid #fde68a !important;
        font-weight: 600 !important;
    }

    .badge-memo-theme-flowers {
        background: #ffe4e6 !important;
        color: #be123c !important;
        border: 1px solid #fecdd3 !important;
        font-weight: 600 !important;
    }

    .badge-memo-theme-numbers {
        background: #e0e7ff !important;
        color: #3730a3 !important;
        border: 1px solid #c7d2fe !important;
        font-weight: 600 !important;
    }

    .badge-memo-theme-flags {
        background: #ccfbf1 !important;
        color: #0f766e !important;
        border: 1px solid #99f6e4 !important;
        font-weight: 600 !important;
    }

    .badge-memo-theme-default {
        background: #e0f2fe !important;
        color: #0369a1 !important;
        border: 1px solid #bae6fd !important;
        font-weight: 600 !important;
    }

    /* ==========================================================
       DataTable Card Image Thumbnail
       ========================================================== */
    .memo-card-thumb-link {
        display: inline-block;
        position: relative;
        border-radius: 8px;
    }

    .memo-card-thumb {
        width: 48px;
        height: 48px;
        object-fit: cover;
        border-radius: 8px;
        border: 1.5px solid #e2e8f0;
        transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease, border-color 0.25s ease;
        display: block;
    }

    .memo-card-thumb:hover {
        transform: scale(1.18);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.16) !important;
        border-color: #3b82f6 !important;
        z-index: 10;
    }

    .memo-card-thumb-placeholder {
        width: 48px;
        height: 48px;
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }

    /* ==========================================================
       Bulk Add (Upload hàng loạt thẻ) - Modern UI / UX
       ========================================================== */

    /* 1. Visual Theme Selector Grid */
    .memo-theme-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
        gap: 12px;
    }

    .memo-theme-card-option {
        position: relative;
        border: 2px solid #e2e8f0;
        border-radius: 14px;
        padding: 14px 16px;
        background: #ffffff;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        gap: 12px;
        user-select: none;
    }

    .memo-theme-card-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.06);
    }

    .memo-theme-card-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        transition: all 0.25s ease;
    }

    .memo-theme-card-info {
        min-width: 0;
        flex-grow: 1;
    }

    .memo-theme-card-title {
        font-weight: 700;
        font-size: 0.88rem;
        margin-bottom: 2px;
        line-height: 1.3;
        color: #1e293b;
    }

    .memo-theme-card-count {
        font-size: 0.75rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .memo-theme-check-mark {
        position: absolute;
        top: -6px;
        right: -6px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #2563eb;
        color: #ffffff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    }

    /* Active States per Theme */
    /* Vehicles (Amber) */
    .memo-theme-card-option.theme-vehicles .memo-theme-card-icon {
        background: #fffbeb;
        color: #d97706;
    }
    .memo-theme-card-option.theme-vehicles.active {
        border-color: #f59e0b;
        background: #fffbeb;
        box-shadow: 0 6px 20px rgba(245, 158, 11, 0.2);
    }
    .memo-theme-card-option.theme-vehicles.active .memo-theme-card-icon {
        background: #f59e0b;
        color: #ffffff;
    }
    .memo-theme-card-option.theme-vehicles.active .memo-theme-check-mark {
        display: flex;
        background: #f59e0b;
    }

    /* Flowers (Rose) */
    .memo-theme-card-option.theme-flowers .memo-theme-card-icon {
        background: #fff1f2;
        color: #e11d48;
    }
    .memo-theme-card-option.theme-flowers.active {
        border-color: #f43f5e;
        background: #fff1f2;
        box-shadow: 0 6px 20px rgba(244, 63, 94, 0.2);
    }
    .memo-theme-card-option.theme-flowers.active .memo-theme-card-icon {
        background: #f43f5e;
        color: #ffffff;
    }
    .memo-theme-card-option.theme-flowers.active .memo-theme-check-mark {
        display: flex;
        background: #f43f5e;
    }

    /* Numbers (Indigo) */
    .memo-theme-card-option.theme-numbers .memo-theme-card-icon {
        background: #eef2ff;
        color: #4f46e5;
    }
    .memo-theme-card-option.theme-numbers.active {
        border-color: #6366f1;
        background: #eef2ff;
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.2);
    }
    .memo-theme-card-option.theme-numbers.active .memo-theme-card-icon {
        background: #6366f1;
        color: #ffffff;
    }
    .memo-theme-card-option.theme-numbers.active .memo-theme-check-mark {
        display: flex;
        background: #6366f1;
    }

    /* Flags (Teal) */
    .memo-theme-card-option.theme-flags .memo-theme-card-icon {
        background: #f0fdfa;
        color: #0d9488;
    }
    .memo-theme-card-option.theme-flags.active {
        border-color: #0d9488;
        background: #f0fdfa;
        box-shadow: 0 6px 20px rgba(13, 148, 136, 0.2);
    }
    .memo-theme-card-option.theme-flags.active .memo-theme-card-icon {
        background: #0d9488;
        color: #ffffff;
    }
    .memo-theme-card-option.theme-flags.active .memo-theme-check-mark {
        display: flex;
        background: #0d9488;
    }

    /* Default / Others */
    .memo-theme-card-option.theme-default .memo-theme-card-icon {
        background: #f0f9ff;
        color: #0284c7;
    }
    .memo-theme-card-option.theme-default.active {
        border-color: #0284c7;
        background: #f0f9ff;
        box-shadow: 0 6px 20px rgba(2, 132, 199, 0.2);
    }
    .memo-theme-card-option.theme-default.active .memo-theme-card-icon {
        background: #0284c7;
        color: #ffffff;
    }
    .memo-theme-card-option.theme-default.active .memo-theme-check-mark {
        display: flex;
        background: #0284c7;
    }

    /* 2. Drag & Drop Upload Zone */
    .memo-dropzone {
        border: 2px dashed #93c5fd;
        border-radius: 16px;
        background: linear-gradient(180deg, #f8fafc 0%, #f0f7ff 100%);
        padding: 36px 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .memo-dropzone:hover {
        border-color: #3b82f6;
        background: #eff6ff;
        box-shadow: 0 8px 25px rgba(37, 99, 235, 0.1);
        transform: translateY(-1px);
    }

    .memo-dropzone.is-dragover {
        border-color: #10b981 !important;
        background: #ecfdf5 !important;
        box-shadow: 0 10px 30px rgba(16, 185, 129, 0.2) !important;
        transform: scale(1.01);
    }

    .memo-dropzone-icon {
        width: 68px;
        height: 68px;
        border-radius: 50%;
        background: #ffffff;
        color: #2563eb;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin-bottom: 16px;
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.18);
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .memo-dropzone:hover .memo-dropzone-icon {
        transform: scale(1.1) translateY(-3px);
    }

    .memo-dropzone.is-dragover .memo-dropzone-icon {
        color: #10b981;
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.25);
    }

    .memo-dropzone-title {
        font-size: 1.05rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 6px;
    }

    .memo-dropzone-sub {
        font-size: 0.84rem;
        color: #64748b;
        margin-bottom: 16px;
    }

    .memo-spec-tag {
        font-size: 0.74rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        background: #ffffff;
        color: #475569;
        border: 1px solid #e2e8f0;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* 3. Live Gallery Grid Preview */
    .memo-gallery-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 16px 20px;
        margin-top: 20px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }

    .memo-gallery-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid #f1f5f9;
        margin-bottom: 14px;
    }

    .memo-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 14px;
        max-height: 460px;
        overflow-y: auto;
        padding: 4px 6px 4px 2px;
    }

    .memo-gallery-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
    }

    .memo-gallery-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.1);
        border-color: #93c5fd;
    }

    .memo-gallery-thumb-wrap {
        width: 100%;
        height: 105px;
        position: relative;
        background: #f8fafc;
        overflow: hidden;
    }

    .memo-gallery-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.25s ease;
    }

    .memo-gallery-item:hover .memo-gallery-thumb {
        transform: scale(1.06);
    }

    .memo-item-remove-btn {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: rgba(239, 68, 68, 0.9);
        color: #ffffff;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.15s ease;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.25);
        z-index: 5;
    }

    .memo-item-remove-btn:hover {
        background: #dc2626;
        transform: scale(1.15);
    }

    .memo-gallery-info {
        padding: 8px 10px;
        background: #ffffff;
        border-top: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }

    .memo-gallery-name {
        font-size: 0.78rem;
        font-weight: 700;
        color: #1e293b;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .memo-gallery-size {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 500;
    }

    /* 4. Side Sticky Box & Tips */
    .memo-side-sticky {
        position: sticky;
        top: 24px;
    }

    .memo-summary-card {
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        margin-bottom: 20px;
    }

    .memo-guide-card {
        border-radius: 16px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 20px;
    }

    .memo-guide-item {
        display: flex;
        gap: 12px;
        margin-bottom: 14px;
        font-size: 0.83rem;
        color: #475569;
        line-height: 1.45;
    }

    .memo-guide-item:last-child {
        margin-bottom: 0;
    }

    .memo-guide-icon {
        width: 28px;
        height: 28px;
        border-radius: 8px;
        background: #e2e8f0;
        color: #334155;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.95rem;
        flex-shrink: 0;
        margin-top: 2px;
    }
</style>
