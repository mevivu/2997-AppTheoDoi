<style>

    .btn-warning-enhanced {
        background: linear-gradient(45deg, #ff9800, #ff6f00);
        border: none;
        color: white;
        box-shadow: 0 2px 4px rgba(255, 152, 0, 0.2);
        transition: all 0.3s ease;
    }

    .btn-warning-enhanced:hover {
        background: linear-gradient(45deg, #ff6f00, #e65100);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(255, 152, 0, 0.3);
        color: white;
    }

    .btn-warning-enhanced:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 152, 0, 0.25);
    }

    /* Responsive cho action buttons */
    @media (max-width: 768px) {
        .action-buttons-wrapper {
            flex-direction: column;
            gap: 0.5rem;
        }

        .action-buttons-wrapper .btn {
            width: 100%;
            justify-content: center;
        }
    }
    /* Enhanced Card Header */
    .card-header.justify-content-between {
        background: linear-gradient(135deg, #254165 0%, #1a2f4a 100%);
        border: none;
        padding: 1.5rem 2rem;
        border-radius: 12px 12px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .card-header.justify-content-between::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='4'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E") repeat;
        pointer-events: none;
    }

    .card-header h2 {
        color: white;
        font-weight: 700;
        font-size: 1.5rem;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 0.75rem;
        position: relative;
        z-index: 1;
    }

    .card-header h2::before {
        content: '';
        width: 4px;
        height: 2rem;
        background: rgba(255, 255, 255, 0.8);
        border-radius: 2px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Action Buttons Wrapper */
    .action-buttons-wrapper {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
        position: relative;
        z-index: 1;
    }

    /* Enhanced Button Styles */
    .btn-modern {
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s;
    }

    .btn-modern:hover::before {
        left: 100%;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .btn-modern:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Primary Button */
    .btn-primary-enhanced {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
    }

    .btn-primary-enhanced:hover {
        background: linear-gradient(135deg, #5a67d8, #6b46c1);
        color: white;
    }

    /* Success Button */
    .btn-success-enhanced {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .btn-success-enhanced:hover {
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
    }

    /* Info Button */
    .btn-info-enhanced {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: white;
    }

    .btn-info-enhanced:hover {
        background: linear-gradient(135deg, #0891b2, #0e7490);
        color: white;
    }

    /* Button Icons */
    .btn-icon {
        font-size: 1rem;
        margin-right: 0.25rem;
    }

    /* Card Enhancement */
    .card.custom-shadow {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .card-body {
        padding: 2rem;
        background: white;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .card-header.justify-content-between {
            padding: 1rem 1.5rem;
            flex-direction: column;
            align-items: flex-start;
            gap: 1rem;
        }

        .card-header h2 {
            font-size: 1.25rem;
        }

        .action-buttons-wrapper {
            width: 100%;
            justify-content: flex-start;
            gap: 0.5rem;
        }

        .btn-modern {
            padding: 0.625rem 1rem;
            font-size: 0.8rem;
            flex: 1;
            justify-content: center;
            min-width: auto;
        }

        .card-body {
            padding: 1.5rem;
        }
    }

    @media (max-width: 576px) {
        .action-buttons-wrapper {
            flex-direction: column;
            width: 100%;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }
    }
</style>
