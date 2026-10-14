@php
    use App\Traits\RouteAdminSystem;
@endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
    <style>
        .iq-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(15, 23, 42, 0.04);
            margin-bottom: 1.5rem;
            overflow: hidden;
            transition: box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .iq-card:hover {
            box-shadow: 0 6px 20px rgba(15, 23, 42, 0.07);
            border-color: #cbd5e1;
        }

        .iq-card-header {
            padding: 1rem 1.35rem;
            border-bottom: 1px solid #f1f5f9;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .iq-card-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .iq-params-well {
            background: linear-gradient(135deg, #f0f7ff 0%, #f8faff 100%);
            border: 1px solid #bfdbfe;
            border-radius: 14px;
            padding: 1.15rem;
            margin-bottom: 1rem;
            position: relative;
        }

        .iq-params-badge {
            position: absolute;
            top: -10px;
            right: 16px;
            background: #2563eb;
            color: #ffffff;
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 2px 10px;
            border-radius: 99px;
            box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3);
        }

        .wechsler-ref-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 12px;
            border-radius: 8px;
            margin-bottom: 4px;
            background: #ffffff;
            border: 1px solid #f1f5f9;
            font-size: 0.82rem;
        }

        .wechsler-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 6px;
            flex-shrink: 0;
        }
    </style>
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid" style="padding-bottom: 90px;">
            <x-admin.page-header
                class="mb-3"
                icon="brain"
                :title="__('Thêm mới Bảng chuẩn IQ Wechsler')"
                :subtitle="__('Thiết lập tham số phân phối chuẩn hóa Mean (μ) và StdDev (σ) cho nhóm độ tuổi mới')"
                :breadcrumbs="$breadcrumbs"
                :back-route="route(RouteAdminSystem::IQ_NORM_INDEX)"
                :back-title="__('Danh sách mốc tuổi')"
            />

            <x-form :action="route(RouteAdminSystem::IQ_NORM_STORE)" type="post" :validate="true" id="iqNormCreateForm">
                <div class="row g-4 justify-content-center">
                    @include('admin.iq_norms.forms.create-left')
                    @include('admin.iq_norms.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
