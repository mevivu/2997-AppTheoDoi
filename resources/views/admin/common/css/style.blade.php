<style>
    /* Card & Shadow Styles */
    .custom-shadow {
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.03) !important;
        border: 1px solid #eef2f6 !important;
        border-radius: 16px !important;
    }

    .card {
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .bg-primary-subtle {
        background-color: #eff6ff !important;
    }

    .border-primary-subtle {
        border-color: #bfdbfe !important;
    }

    /* Page Header Custom Styles */
    .page-header-custom {
        padding: 1.25rem 1.75rem;
        background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 50%, #eef2ff 100%) !important;
        border: 1px solid #cbd5e1;
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        box-shadow: 0 8px 30px -4px rgba(37, 99, 235, 0.08), 0 2px 6px -1px rgba(15, 23, 42, 0.04);
    }

    .card > .page-header-custom {
        border: none !important;
        border-bottom: 1px solid #e2e8f0 !important;
        border-radius: 16px 16px 0 0 !important;
        box-shadow: none !important;
        background: linear-gradient(135deg, #ffffff 0%, #f1f7ff 60%, #eef2ff 100%) !important;
        padding: 1.15rem 1.5rem !important;
    }

    .page-header-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #2563eb 0%, #6366f1 40%, #8b5cf6 70%, #3b82f6 100%);
        background-size: 200% 100%;
        animation: gradientMove 4s linear infinite;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        box-shadow: 0 2px 10px rgba(37, 99, 235, 0.4);
    }

    @keyframes gradientMove {
        0% { background-position: 100% 0; }
        100% { background-position: -100% 0; }
    }

    .ph-icon-box {
        width: 46px !important;
        height: 46px !important;
        border-radius: 14px !important;
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%) !important;
        color: #ffffff !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 1.45rem !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35), inset 0 1px 1px rgba(255, 255, 255, 0.3) !important;
        flex-shrink: 0 !important;
        transition: transform 0.25s ease !important;
    }

    .page-header-custom:hover .ph-icon-box {
        transform: scale(1.05) rotate(-3deg);
    }

    .page-header-custom h2 {
        font-size: 1.25rem !important;
        color: #0f172a !important;
        font-weight: 700 !important;
        letter-spacing: -0.02em !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        position: relative !important;
        padding-left: 14px;
    }

    .page-header-custom h2::before {
        content: '';
        position: absolute;
        left: 0;
        top: 10%;
        width: 5px;
        height: 80%;
        background: linear-gradient(180deg, #2563eb 0%, #4f46e5 100%);
        border-radius: 99px;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.4);
    }

    .page-header-custom.has-icon h2 {
        padding-left: 0 !important;
    }

    .page-header-custom.has-icon h2::before {
        display: none !important;
    }

    .page-header-custom .header-subtitle {
        font-size: 0.86rem !important;
        color: #64748b !important;
        font-weight: 500 !important;
        padding-left: 14px;
        margin: 4px 0 0 0 !important;
    }

    .page-header-custom.has-icon .header-subtitle {
        padding-left: 0 !important;
    }

    .ph-breadcrumbs {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
        font-size: 0.82rem !important;
        color: #64748b !important;
        margin-bottom: 4px !important;
    }

    .ph-breadcrumbs a {
        color: #475569 !important;
        text-decoration: none !important;
        font-weight: 500 !important;
        transition: color 0.15s ease !important;
    }

    .ph-breadcrumbs a:hover {
        color: #2563eb !important;
    }

    .ph-breadcrumbs .sep {
        font-size: 0.7rem !important;
        color: #cbd5e1 !important;
    }

    .ph-breadcrumbs .active {
        color: #0f172a !important;
        font-weight: 600 !important;
    }

    .btn-add-custom {
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%) !important;
        color: #ffffff !important;
        border: none !important;
        padding: 9px 20px !important;
        border-radius: 50px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35) !important;
        text-decoration: none !important;
        letter-spacing: 0.01em !important;
    }

    .btn-add-custom:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #4338ca 100%) !important;
        transform: translateY(-2px) scale(1.02) !important;
        box-shadow: 0 8px 24px rgba(37, 99, 235, 0.45) !important;
        color: #ffffff !important;
    }

    .btn-add-custom i {
        font-size: 1.15rem !important;
    }

    .btn-header-back {
        background: #ffffff !important;
        color: #334155 !important;
        border: 1px solid #cbd5e1 !important;
        padding: 8.5px 18px !important;
        border-radius: 50px !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 7px !important;
        font-size: 0.88rem !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
        text-decoration: none !important;
        box-shadow: 0 2px 6px rgba(15, 23, 42, 0.05) !important;
    }

    .btn-header-back:hover {
        background: #f8fafc !important;
        border-color: #94a3b8 !important;
        color: #0f172a !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08) !important;
    }

    .btn-header-back i {
        font-size: 1.1rem !important;
        transition: transform 0.2s ease !important;
    }

    .btn-header-back:hover i {
        transform: translateX(-3px) !important;
    }

    .page-header-custom .header-content {
        position: relative;
        z-index: 1;
    }

    .page-header-custom::after {
        content: '';
        position: absolute;
        right: -20px;
        bottom: -30px;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.12) 0%, rgba(37, 99, 235, 0.05) 45%, transparent 70%);
        border-radius: 100%;
        pointer-events: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .page-header-custom {
            padding: 1rem;
        }

        .page-header-custom h2 {
            font-size: 1.1rem;
        }
    }

    /* ============================================
       MODERN DATATABLE STYLING (3504-HOMTHU STYLE)
       ============================================ */
    .header-cell-content {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #ffffff;
        padding: 5px 10px;
        border-radius: 6px;
        transition: all 0.2s ease;
        position: relative;
        margin: 2px auto;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
        border: 1px solid #eef2f6;
    }

    .header-cell-content:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .header-cell-content i {
        color: #2563eb;
        font-size: 0.85rem;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        background: rgba(37, 99, 235, 0.08);
        transition: all 0.2s ease;
    }

    .header-cell-content:hover i {
        transform: scale(1.08);
        background: rgba(37, 99, 235, 0.15);
    }

    .header-cell-content span {
        font-weight: 600;
        color: #334155;
        font-size: 0.78rem;
        white-space: nowrap;
        text-transform: none;
        letter-spacing: normal;
    }

    /* Subtle Badge Colors */
    .bg-indigo-subtle {
        background-color: #eef2ff !important;
    }
    .text-indigo {
        color: #4f46e5 !important;
    }
    .border-indigo-subtle {
        border-color: #c7d2fe !important;
    }

    .bg-success-subtle {
        background-color: #f0fdf4 !important;
    }
    .text-success {
        color: #16a34a !important;
    }
    .border-success-subtle {
        border-color: #bbf7d0 !important;
    }

    .bg-warning-subtle {
        background-color: #fffbeb !important;
    }
    .text-warning {
        color: #d97706 !important;
    }
    .border-warning-subtle {
        border-color: #fde68a !important;
    }

    .bg-danger-subtle {
        background-color: #fef2f2 !important;
    }
    .text-danger {
        color: #dc2626 !important;
    }
    .border-danger-subtle {
        border-color: #fecaca !important;
    }

    .bg-secondary-subtle {
        background-color: #f8fafc !important;
    }
    .text-secondary {
        color: #64748b !important;
    }
    .border-secondary-subtle {
        border-color: #e2e8f0 !important;
    }

    /* DataTable Table Polish */
    .table-bordered {
        border: 1px solid #eef2f6 !important;
    }

    .table thead th {
        background-color: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        font-size: 0.8rem;
        vertical-align: middle;
        padding: 8px 6px !important;
    }

    .table tbody tr {
        transition: background-color 0.15s ease;
    }

    .table tbody tr:hover {
        background-color: #f1f5f9 !important;
    }

    .table tbody td {
        vertical-align: middle !important;
        padding: 10px 8px !important;
        font-size: 0.85rem;
    }

    /* DataTable Search Input Controls */
    .table thead input.form-control,
    .table thead select.form-select,
    .dataTables_filter input {
        border-radius: 8px !important;
        font-size: 12.5px !important;
        padding: 5px 10px !important;
        border: 1px solid #cbd5e1 !important;
        background-color: #ffffff !important;
        height: 32px !important;
        transition: all 0.2s ease !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03) !important;
    }

    .table thead input.form-control:focus,
    .table thead select.form-select:focus,
    .dataTables_filter input:focus {
        border-color: #2563eb !important;
        box-shadow: 0 0 0 2px rgba(37, 99, 235, 0.15) !important;
        outline: none !important;
    }

    /* ============================================
       DATATABLE ACTION BUTTONS - Modern Style
       ============================================ */
    .dt-action-group {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        white-space: nowrap;
        flex-wrap: nowrap;
    }


    .dt-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        text-decoration: none !important;
        line-height: 1;
    }

    .dt-action-btn i {
        font-size: 16px;
        line-height: 1;
    }

    .dt-action-btn.dt-action-edit {
        background: #eff6ff;
        color: #2563eb;
        border-color: #bfdbfe;
    }
    .dt-action-btn.dt-action-edit:hover {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .dt-action-btn.dt-action-delete {
        background: #fef2f2;
        color: #dc2626;
        border-color: #fecaca;
    }
    .dt-action-btn.dt-action-delete:hover {
        background: #dc2626;
        color: #ffffff;
        border-color: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    .dt-action-btn.dt-action-force-delete {
        background: #fff1f2;
        color: #e11d48;
        border-color: #fecdd3;
    }
    .dt-action-btn.dt-action-force-delete:hover {
        background: #e11d48;
        color: #ffffff;
        border-color: #e11d48;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(225, 29, 72, 0.35);
    }

    .dt-action-btn.dt-action-deactivate {
        background: #fffbeb;
        color: #d97706;
        border-color: #fde68a;
    }
    .dt-action-btn.dt-action-deactivate:hover {
        background: #d97706;
        color: #ffffff;
        border-color: #d97706;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(217, 119, 6, 0.3);
    }


    .dt-action-btn.dt-action-view {
        background: #f0fdf4;
        color: #16a34a;
        border-color: #bbf7d0;
    }
    .dt-action-btn.dt-action-view:hover {
        background: #16a34a;
        color: #ffffff;
        border-color: #16a34a;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
    }

    /* ============================================
       FLOATING BULK ACTION TOOLBAR
       ============================================ */
    .floating-bulk-actions {
        position: fixed;
        bottom: 28px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050;
        animation: floatingSlideUp 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes floatingSlideUp {
        0% { opacity: 0; transform: translate(-50%, 25px) scale(0.92); }
        100% { opacity: 1; transform: translate(-50%, 0) scale(1); }
    }

    .floating-bulk-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 14px 8px 18px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid #cbd5e1;
        border-radius: 99px;
        box-shadow: 0 16px 36px -6px rgba(15, 23, 42, 0.14), 0 4px 12px rgba(0, 0, 0, 0.06);
        color: #0f172a;
    }

    .bulk-count-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        white-space: nowrap;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        padding: 4px 14px;
        border-radius: 20px;
        color: #1e40af;
    }

    .bulk-count-badge strong {
        color: #2563eb;
        font-size: 0.95rem;
        font-weight: 700;
    }

    .badge-dot {
        width: 8px;
        height: 8px;
        background: #2563eb;
        border-radius: 50%;
        box-shadow: 0 0 8px rgba(37, 99, 235, 0.6);
        animation: pulseDot 2s infinite;
    }

    @keyframes pulseDot {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.4); opacity: 0.6; }
    }

    .bulk-divider {
        width: 1px;
        height: 22px;
        background: #e2e8f0;
    }

    .bulk-controls {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bulk-action-select {
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
        border-radius: 20px !important;
        padding: 6px 32px 6px 14px !important;
        font-size: 0.86rem !important;
        font-weight: 600 !important;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .bulk-action-select:hover,
    .bulk-action-select:focus {
        border-color: #2563eb !important;
        background-color: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
        outline: none !important;
    }

    .btn-bulk-apply {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important;
        border: none !important;
        border-radius: 20px !important;
        padding: 6px 18px !important;
        font-size: 0.86rem !important;
        font-weight: 600 !important;
        color: #ffffff !important;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35) !important;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }

    .btn-bulk-apply:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%) !important;
        transform: translateY(-2px) scale(1.02) !important;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.5) !important;
    }

    .btn-bulk-close {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #64748b;
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-left: 2px;
    }

    .btn-bulk-close:hover {
        background: #fee2e2;
        border-color: #fca5a5;
        color: #ef4444;
        transform: rotate(90deg);
    }

    /* Drag to Scroll */
    .table-responsive.dragging-active {
        cursor: grabbing !important;
        user-select: none !important;
    }

    /* ============================================
       MODERN CMS-STYLE DATATABLE GRID CARDS
       ============================================ */
    .table-responsive.datatable-grid-active,
    .table-responsive.datatable-grid-active table.dataTable,
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr,
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr > td {
        cursor: default !important;
    }

    .table-responsive.datatable-grid-active {
        overflow-x: hidden !important;
        width: 100% !important;
        max-width: 100% !important;
    }

    .table-responsive.datatable-grid-active table.dataTable {
        border-collapse: separate !important;
        border-spacing: 0 16px !important;
        border: none !important;
        width: 100% !important;
        max-width: 100% !important;
        min-width: 0 !important;
        table-layout: fixed !important;
        box-sizing: border-box !important;
        background: transparent !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > thead {
        display: none !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > tbody {
        display: grid !important;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)) !important;
        gap: 16px !important;
        padding: 8px 0 !important;
        border: none !important;
        background: transparent !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    /* DataTables Empty State ở chế độ thẻ Grid */
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr:has(.dataTables_empty),
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr.odd:has(.dataTables_empty),
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr.even:has(.dataTables_empty) {
        grid-column: 1 / -1 !important;
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 48px 24px !important;
        background: #ffffff !important;
        border: 1px dashed #cbd5e1 !important;
        border-radius: 20px !important;
        box-shadow: 0 4px 20px rgba(15, 23, 42, 0.03) !important;
        text-align: center !important;
        width: 100% !important;
        min-height: 240px !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > tbody > tr > td.dataTables_empty {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 0 !important;
        border: none !important;
        width: 100% !important;
        color: #475569 !important;
        font-size: 1rem !important;
        font-weight: 700 !important;
    }

    /* Thẻ Card Phong Cách CMS Hiện Đại */
    .table-responsive.datatable-grid-active table.dataTable,
    .table-responsive.datatable-grid-active table.dataTable > tbody {
        background: transparent !important;
        background-color: transparent !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > tbody > tr,
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr.odd,
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr.even {
        display: flex !important;
        flex-direction: column !important;
        background: #ffffff !important;
        background-color: #ffffff !important;
        --bs-table-accent-bg: transparent !important;
        --bs-table-bg: #ffffff !important;
        --bs-table-bg-type: transparent !important;
        --tblr-table-accent-bg: transparent !important;
        --tblr-table-bg: #ffffff !important;
        --tblr-table-striped-bg: transparent !important;
        border: 1px solid #eef2f6 !important;
        border-radius: 16px !important;
        padding: 16px 20px !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 4px 12px rgba(15, 23, 42, 0.03) !important;
        transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
        height: 100% !important;
        position: relative !important;
        overflow: hidden !important;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > tbody > tr:hover {
        box-shadow: 0 12px 30px -5px rgba(37, 99, 235, 0.12), 0 4px 10px -2px rgba(0, 0, 0, 0.04) !important;
        border-color: #bfdbfe !important;
        transform: translateY(-3px) !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > tbody > tr > td,
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr.odd > td,
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr.even > td,
    .table-responsive.datatable-grid-active table.dataTable.table-bordered > tbody > tr > td,
    .table-responsive.datatable-grid-active table.dataTable.table-striped > tbody > tr > td,
    .table-responsive.datatable-grid-active table.dataTable.table-striped > tbody > tr:nth-of-type(odd) > *,
    .table-responsive.datatable-grid-active table.dataTable.table-striped > tbody > tr:nth-of-type(even) > *,
    .table-responsive.datatable-grid-active table.dataTable > tbody > tr > td:nth-child(n) {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 11px 0 !important;
        margin: 0 !important;
        border: none !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        border-bottom: 1px dotted #e2e8f0 !important;
        outline: none !important;
        box-shadow: none !important;
        background: #ffffff !important;
        background-color: #ffffff !important;
        --bs-table-accent-bg: transparent !important;
        --bs-table-bg: #ffffff !important;
        --bs-table-bg-type: transparent !important;
        --tblr-table-accent-bg: transparent !important;
        --tblr-table-bg: #ffffff !important;
        --tblr-table-striped-bg: transparent !important;
        font-size: 13.5px !important;
        color: #334155 !important;
        word-break: break-word !important;
        overflow-wrap: anywhere !important;
        overflow: hidden !important;
        min-width: 0 !important;
        width: 100% !important;
        box-sizing: border-box !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > tbody > tr > td > *:not(::before) {
        min-width: 0 !important;
        max-width: 100% !important;
        word-break: break-word !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > tbody > tr > td:last-child {
        border-bottom: none !important;
        border-top: 1px solid #f1f5f9 !important;
        margin-top: auto !important;
        padding-top: 14px !important;
        padding-bottom: 0 !important;
        justify-content: flex-end !important;
        gap: 8px !important;
        flex-wrap: wrap !important;
        width: 100% !important;
    }

    .table-responsive.datatable-grid-active table.dataTable > tbody > tr > td[data-label]::before {
        content: attr(data-label);
        font-weight: 700;
        color: #94a3b8;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin-right: 12px;
        flex-shrink: 0;
        max-width: 45%;
        word-break: break-word;
    }

    /* Switcher Button Styling */
    .datatable-view-switcher .btn-datatable-mode.active {
        background-color: #ffffff !important;
        color: #2563eb !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
    }

    .datatable-view-switcher .btn-datatable-mode:not(.active) {
        color: #64748b !important;
        background: transparent !important;
    }

    /* ============================================
       DATATABLE TOP CONTROLS & ACTION BUTTONS
       ============================================ */
    .toggle-columns-table {
        width: 100% !important;
        margin-bottom: 14px !important;
        position: relative !important;
        z-index: 10 !important;
        display: block !important;
        clear: both !important;
    }

    .dt-buttons {
        width: auto !important;
        margin-bottom: 16px !important;
        display: inline-flex !important;
        flex-wrap: wrap !important;
        align-items: center !important;
        gap: 8px !important;
        float: none !important;
        clear: both !important;
    }

    .dt-buttons button.btn,
    .dt-buttons .btn {
        width: fit-content !important;
        flex: unset !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        padding: 7px 16px !important;
        border-radius: 6px !important;
        border: none !important;
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        line-height: 1.4 !important;
    }

    .dt-buttons .buttons-reset {
        background: linear-gradient(135deg, #5a6268 0%, #495057 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(73, 80, 87, 0.25) !important;
    }

    .dt-buttons .buttons-reset:hover {
        background: linear-gradient(135deg, #495057 0%, #343a40 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(73, 80, 87, 0.35) !important;
    }

    .dt-buttons .buttons-reload {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(23, 162, 184, 0.25) !important;
    }

    .dt-buttons .buttons-reload:hover {
        background: linear-gradient(135deg, #138496 0%, #117a8b 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(23, 162, 184, 0.35) !important;
    }

    .dt-buttons .buttons-page-length {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
        color: #ffffff !important;
        box-shadow: 0 2px 6px rgba(0, 123, 255, 0.25) !important;
    }

    .dt-buttons .buttons-page-length:hover {
        background: linear-gradient(135deg, #0056b3 0%, #004085 100%) !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 10px rgba(0, 123, 255, 0.35) !important;
    }

    .dt-buttons .btn:active {
        transform: translateY(0) scale(0.98) !important;
    }
</style>
