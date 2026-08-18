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
                icon="book-2"
                :title="__('Thêm Hướng dẫn sử dụng mới')"
                :subtitle="__('Nhập nội dung bài hướng dẫn sử dụng và trợ giúp')"
                :back-route="route(RouteAdminSystem::GUIDE_INDEX)"
            />

            <x-form :action="route(RouteAdminSystem::GUIDE_STORE)" type="post" :validate="true">
                <div class="row g-4 justify-content-center">
                    @include('admin.guide.forms.create-left')
                    @include('admin.guide.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.guide.scripts.script')
@endpush
