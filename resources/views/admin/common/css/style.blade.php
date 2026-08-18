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
        padding: 1.25rem 1.5rem;
        background: #f8fafc;
        border-bottom: 1px solid #eef2f6;
        border-radius: 16px 16px 0 0;
    }

    .page-header-custom .ph-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    }

    .page-header-custom h2 {
        font-size: 1.25rem;
        font-weight: 700;
        letter-spacing: -0.2px;
    }

    .page-header-custom .header-subtitle {
        font-size: 0.85rem;
        color: #64748b;
    }

    .ph-breadcrumbs {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: #94a3b8;
        margin-bottom: 4px;
    }

    .ph-breadcrumbs a {
        color: #64748b;
        text-decoration: none;
    }

    .ph-breadcrumbs a:hover {
        color: #2563eb;
    }

    .ph-breadcrumbs .sep {
        font-size: 10px;
    }

    .ph-breadcrumbs .active {
        color: #0f172a;
        font-weight: 600;
    }

    .btn-add-custom {
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-header-back {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        border-radius: 10px;
        font-weight: 600;
        padding: 8px 16px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .btn-header-back:hover {
        background: #f1f5f9;
        color: #1e293b;
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
</style>
