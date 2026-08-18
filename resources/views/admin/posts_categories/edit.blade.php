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
                icon="category"
                :title="__('Chỉnh sửa Chuyên mục Bài viết')"
                :subtitle="$category->name ?? __('Cập nhật thông tin chi tiết chuyên mục')"
                :back-route="route(RouteAdminSystem::POST_CATEGORY_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::POST_CATEGORY_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$category->id"/>
                <div class="row g-4 justify-content-center">
                    @include('admin.posts_categories.forms.edit-left')
                    @include('admin.posts_categories.forms.edit-right')
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
