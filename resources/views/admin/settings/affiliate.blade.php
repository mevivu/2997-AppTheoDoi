@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
    <style>
        .wrap-loop-input .add-image-ckfinder {
            max-width: 300px;
            display: block;
        }

        /* High-contrast & Premium Typography for Affiliate Settings */
        .affiliate-tier-card {
            background-color: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
            color: #1e293b !important;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .affiliate-tier-card:hover {
            border-color: #cbd5e1 !important;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.06);
        }

        .affiliate-tier-card .card-header {
            background-color: #f8fafc !important;
            border-bottom: 1px solid #f1f5f9 !important;
            padding: 0.85rem 1.25rem;
            border-top-left-radius: 11px;
            border-top-right-radius: 11px;
        }

        .affiliate-tier-card .card-title {
            font-size: 0.95rem;
            font-weight: 700;
            color: #0f172a !important;
        }

        .affiliate-tier-card .card-body {
            padding: 1.25rem;
        }

        /* Form Labels & Controls - Clean, Crisp & High Contrast */
        .affiliate-tier-card .form-label,
        #affiliateTabContent .form-label {
            color: #1e293b !important;
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.4rem;
            display: block;
        }

        .affiliate-tier-card .form-control,
        #affiliateTabContent .form-control {
            color: #0f172a !important;
            background-color: #ffffff !important;
            border: 1px solid #cbd5e1 !important;
            font-weight: 500;
            font-size: 0.9rem;
            border-radius: 8px;
            padding: 0.5rem 0.75rem;
        }

        .affiliate-tier-card .form-control::placeholder,
        #affiliateTabContent .form-control::placeholder {
            color: #94a3b8 !important;
            opacity: 1;
        }

        .affiliate-tier-card .form-control:focus,
        #affiliateTabContent .form-control:focus {
            border-color: #206bc4 !important;
            box-shadow: 0 0 0 3px rgba(32, 107, 196, 0.15) !important;
            color: #0f172a !important;
        }

        .affiliate-tier-card .form-hint,
        #affiliateTabContent .form-hint {
            color: #64748b !important;
            font-size: 0.8rem;
            line-height: 1.45;
            margin-top: 0.35rem;
        }

        /* Divider "HOẶC" cách điệu */
        .affiliate-or-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.25rem 0;
        }

        .affiliate-or-divider::before,
        .affiliate-or-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px dashed #cbd5e1;
        }

        .affiliate-or-badge {
            padding: 3px 12px;
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 0.05em;
            color: #0284c7;
            background-color: #e0f2fe;
            border-radius: 20px;
            margin: 0 10px;
            text-transform: uppercase;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="affiliate"
                :title="__('Cài đặt Affiliate')"
                :subtitle="__('Cấu hình chính sách hoa hồng và phần thưởng khi người dùng giới thiệu thành viên mới')"
                :back-route="route('admin.dashboard')"
            />

            <x-form :action="route('admin.setting.update')" type="put" :validate="true">
                <div class="row g-4 justify-content-center">
                    <div class="col-12 col-lg-8 col-xl-9">
                        @include('admin.settings.forms.affiliate-left')
                    </div>
                    @include('admin.settings.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush

@push('custom-js')
    <script>
        $(document).ready(function() {
            // Tự động điều chỉnh kích thước CKEditor khi mở tab Quy định tham gia
            $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                if (typeof CKEDITOR !== 'undefined') {
                    for (var instanceName in CKEDITOR.instances) {
                        if (CKEDITOR.instances[instanceName]) {
                            CKEDITOR.instances[instanceName].resize('100%');
                        }
                    }
                }
            });

            // Đảm bảo dữ liệu trong CKEditor được sync vào textarea trước khi submit
            $('form').on('submit', function() {
                if (typeof CKEDITOR !== 'undefined') {
                    for (var instanceName in CKEDITOR.instances) {
                        if (CKEDITOR.instances[instanceName]) {
                            CKEDITOR.instances[instanceName].updateElement();
                        }
                    }
                }
            });
        });
    </script>
@endpush
