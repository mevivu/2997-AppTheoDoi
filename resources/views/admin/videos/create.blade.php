@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@push('custom-css')
    <style>
        .video-create-page { --video-accent: #2563eb; }
        .video-create-page .workflow-strip {
            background: linear-gradient(135deg, #eff6ff 0%, #ffffff 65%);
            border: 1px solid #dbeafe;
            border-radius: 16px;
            padding: 16px 18px;
            margin-bottom: 24px;
        }
        .video-create-page .workflow-step { color: #64748b; min-width: 0; }
        .video-create-page .workflow-step strong { color: #1e293b; display: block; }
        .video-create-page .workflow-number {
            width: 32px; height: 32px; flex: 0 0 32px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            background: #fff; color: var(--video-accent); border: 1px solid #bfdbfe;
            font-weight: 700;
        }
        .video-create-page .card { border: 1px solid #e8edf4 !important; overflow: hidden; }
        .video-create-page .card-header { background: #fff !important; }
        .video-create-page .form-control-lg,
        .video-create-page .form-select-lg { min-height: 48px; }
        .video-create-page .form-control:focus,
        .video-create-page .form-select:focus {
            border-color: #60a5fa; box-shadow: 0 0 0 3px rgba(37, 99, 235, .12);
        }
        .video-create-page .access-choice .form-selectgroup-label { transition: .18s ease; }
        .video-create-page .access-choice .form-selectgroup-label:hover {
            border-color: #93c5fd !important; background: #f8fbff;
        }
        .video-create-page .form-selectgroup-input:checked + .form-selectgroup-label {
            border-color: var(--video-accent) !important;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, .10);
        }
        .video-create-page .settings-column { align-self: flex-start; }
        .video-create-page .thumbnail-empty {
            min-height: 150px; background: #f8fafc; color: #64748b;
            display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .video-create-page .field-counter { font-variant-numeric: tabular-nums; }
        .video-create-page .is-invalid + .invalid-feedback { display: block; }
        .video-create-page .input-group:has(.is-invalid) + .invalid-feedback { display: block; }
        @media (min-width: 1200px) {
            .video-create-page .settings-column { position: sticky; top: 82px; }
        }
        @media (max-width: 767.98px) {
            .video-create-page .workflow-strip { padding: 14px; }
            .video-create-page .workflow-steps { flex-direction: column; align-items: stretch !important; }
            .video-create-page .workflow-divider { display: none; }
            .video-create-page .card-body, .video-create-page .card-header { padding: 16px !important; }
            .video-create-page #btn_fetch_yt span { display: none; }
        }
    </style>
@endpush

@section('content')
    <div class="page-body video-create-page">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="ti ti-video"
                :title="__('Thêm Video giáo dục mới')"
                :subtitle="__('Nhập link YouTube và thông tin phân loại độ tuổi cho video')"
                :back-route="route(RouteAdminSystem::VIDEO_INDEX)"
            />

            <div class="workflow-strip" aria-label="Các bước thêm video">
                <div class="workflow-steps d-flex align-items-center gap-3">
                    <div class="workflow-step d-flex align-items-center gap-2 flex-fill">
                        <span class="workflow-number">1</span>
                        <div><strong>{{ __('Dán link YouTube') }}</strong><small>{{ __('Kiểm tra video và thời lượng') }}</small></div>
                    </div>
                    <i class="workflow-divider ti ti-chevron-right text-blue fs-3"></i>
                    <div class="workflow-step d-flex align-items-center gap-2 flex-fill">
                        <span class="workflow-number">2</span>
                        <div><strong>{{ __('Điền thông tin') }}</strong><small>{{ __('Tiêu đề, danh mục và mô tả') }}</small></div>
                    </div>
                    <i class="workflow-divider ti ti-chevron-right text-blue fs-3"></i>
                    <div class="workflow-step d-flex align-items-center gap-2 flex-fill">
                        <span class="workflow-number">3</span>
                        <div><strong>{{ __('Thiết lập & lưu') }}</strong><small>{{ __('Quyền xem và trạng thái') }}</small></div>
                    </div>
                </div>
            </div>

            <x-form id="video_create_form" :action="route(RouteAdminSystem::VIDEO_STORE)" type="post" :validate="true" :has-file="true" enctype="multipart/form-data">
                <div class="row g-4 justify-content-center">
                    @include('admin.videos.forms.create-left')
                    @include('admin.videos.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
    @include('admin.videos.partials.upload-progress')
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush
