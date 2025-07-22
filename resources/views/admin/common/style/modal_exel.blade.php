<style>
    .action-buttons-wrapper {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 12px;
        padding: 15px 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        display: inline-block;
    }




    .btn-modern:hover:before {
        left: 100%;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .btn-primary-enhanced {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-primary-enhanced:hover {
        background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
        color: white;
    }

    .btn-success-enhanced {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        color: white;
    }

    .btn-success-enhanced:hover {
        background: linear-gradient(135deg, #3d8bfe 0%, #00d4fe 100%);
        color: white;
    }

    .btn-info-enhanced {
        background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        color: white;
    }

    .btn-info-enhanced:hover {
        background: linear-gradient(135deg, #f85a88 0%, #fed12e 100%);
        color: white;
    }

    .btn-icon {
        margin-right: 6px;
        font-size: 16px;
        display: inline-flex;
        align-items: center;
    }

    @media (max-width: 768px) {
        .action-buttons-wrapper {
            padding: 12px 15px;
            text-align: center;
        }

        .btn-modern {
            width: 100%;
            margin-right: 0;
            margin-bottom: 8px;
        }

        .btn-modern:last-child {
            margin-bottom: 0;
        }
    }
    /* Modal Styling */
    .modal-content-enhanced {
        border: none;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .modal-header-enhanced {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-bottom: none;
        padding: 20px 25px;
    }

    .modal-header-enhanced .modal-title {
        color: white;
        font-weight: 600;
        font-size: 18px;
        display: flex;
        align-items: center;
    }

    .modal-header-enhanced .btn-close {
        filter: invert(1);
        opacity: 0.8;
    }

    .modal-header-enhanced .btn-close:hover {
        opacity: 1;
    }

    .modal-body-enhanced {
        padding: 25px;
        background-color: #f8f9fa;
    }

    .form-label-enhanced {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .file-input-wrapper {
        position: relative;
        display: block;
    }

    .form-control-file-enhanced {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background-color: white;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .form-control-file-enhanced:hover {
        border-color: #667eea;
        background-color: #f8f9ff;
    }

    .form-control-file-enhanced.file-selected {
        border-color: #28a745;
        background-color: #f8fff9;
    }

    .file-input-text {
        color: #6c757d;
        font-size: 14px;
    }

    .file-input-text.has-file {
        color: #28a745;
        font-weight: 500;
    }

    .file-input-icon {
        font-size: 32px;
        color: #dee2e6;
        margin-bottom: 10px;
        transition: color 0.3s ease;
    }

    .form-control-file-enhanced:hover .file-input-icon {
        color: #667eea;
    }

    .form-control-file-enhanced.file-selected .file-input-icon {
        color: #28a745;
    }

    .actual-file-input {
        position: absolute;
        opacity: 0;
        width: 100%;
        height: 100%;
        cursor: pointer;
    }

    .modal-footer-enhanced {
        background-color: white;
        border-top: 1px solid #e9ecef;
        padding: 20px 25px;
    }

    .btn-modal-close {
        background-color: #6c757d;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-modal-close:hover {
        background-color: #545b62;
        transform: translateY(-1px);
    }

    .btn-modal-import {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        color: white;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-modal-import:hover {
        background: linear-gradient(135deg, #3d8bfe 0%, #00d4fe 100%);
        color: white;
        transform: translateY(-1px);
    }

    .btn-modal-import:disabled {
        background: #6c757d;
        transform: none;
    }

    .file-info {
        margin-top: 10px;
        padding: 8px 12px;
        background-color: #e7f3ff;
        border-radius: 6px;
        font-size: 13px;
        color: #0066cc;
        display: none;
    }

    .file-info.show {
        display: block;
    }

    /* Loading animation */
    .btn-loading {
        position: relative;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: #ffffff;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
