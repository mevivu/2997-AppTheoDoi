@php use App\Traits\RouteAdminSystem; @endphp
@extends('admin.layouts.master')

@push('libs-css')
    @include('admin.common.css.style')
    @include('admin.common.css.action')
@endpush

@section('content')
    <div class="page-body">
        <div class="container-fluid">
            <x-admin.page-header
                class="mb-4"
                icon="info-circle"
                :title="__('Thêm bài giới thiệu mới')"
                :subtitle="__('Nhập thông tin tiêu đề, phân loại, tầm nhìn, sứ mệnh hoặc câu chuyện thương hiệu')"
                :back-route="route(RouteAdminSystem::INTRODUCTION_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::INTRODUCTION_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.introductions.forms.create-left')
                    @include('admin.introductions.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    @include('ckfinder::setup')
@endpush

@push('custom-js')
    <script>
        $(document).ready(function() {
            if (typeof CKEDITOR !== 'undefined') {
                if (CKEDITOR.instances['excerpt'] && CKEDITOR.instances['excerpt'].status === 'ready') {
                    CKEDITOR.instances['excerpt'].resize('100%', 200, true);
                }
                CKEDITOR.on('instanceReady', function(evt) {
                    if (evt.editor.name === 'excerpt') {
                        evt.editor.resize('100%', 200, true);
                    }
                });

                $('form').on('submit', function() {
                    for (var name in CKEDITOR.instances) {
                        try {
                            CKEDITOR.instances[name].updateElement();
                        } catch (e) {}
                    }
                });
            }
        });
    </script>
@endpush
