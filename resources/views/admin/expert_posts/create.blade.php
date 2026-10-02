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
                icon="stethoscope"
                :title="__('Thêm Bài viết Chuyên gia mới')"
                :subtitle="__('Nhập nội dung bài viết, chọn bác sĩ cố vấn, nhóm tuổi và trích dẫn lời khuyên')"
                :back-route="route(RouteAdminSystem::EXPERT_POST_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::EXPERT_POST_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.expert_posts.forms.create-left')
                    @include('admin.expert_posts.forms.create-right')
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
