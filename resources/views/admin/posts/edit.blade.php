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
                :title="__('Chỉnh sửa Bài viết')"
                :subtitle="$post->title ?? __('Cập nhật thông tin chi tiết bài viết')"
                :back-route="route(RouteAdminSystem::POST_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::POST_UPDATE)" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$post->id" />
                <div class="row g-4 justify-content-center">
                    @include('admin.posts.forms.edit-left')
                    @include('admin.posts.forms.edit-right')
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
