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
                icon="news"
                :title="__('Thêm Bài viết mới')"
                :subtitle="__('Nhập thông tin bài viết, nội dung và chuyên mục tin tức')"
                :back-route="route(RouteAdminSystem::POST_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::POST_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.posts.forms.create-left')
                    @include('admin.posts.forms.create-right')
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
@endpush
